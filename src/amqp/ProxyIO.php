<?php
/**
 * @package yii2-statscore-api-client
 * @author Simon Karlen <simi.albi@gmail.com>
 */

namespace simialbi\yii2\statscore\amqp;

use PhpAmqpLib\Exception\AMQPConnectionClosedException;
use PhpAmqpLib\Exception\AMQPIOException;
use PhpAmqpLib\Exception\AMQPSocketException;
use PhpAmqpLib\Exception\AMQPTimeoutException;
use PhpAmqpLib\Helper\MiscHelper;
use PhpAmqpLib\Helper\SocketConstants;
use PhpAmqpLib\Wire\IO\AbstractIO;

class ProxyIO extends AbstractIO
{
    /**
     * @var string the host name or ip of the proxy
     */
    protected $proxyHost;

    /**
     * @var integer the port of the proxy
     */
    protected $proxyPort;

    /**
     * @var string the hostname or ip of the AMQP host
     */
    protected $host;

    /**
     * @var integer the port of the AMQP host
     */
    protected $port;

    /**
     * @var integer the interval to send the heartbeat
     */
    protected $heartbeat;

    /**
     * @var boolean whether or not to keep alive the connection
     */
    protected $keepAlive;

    /**
     * @var resource The socket connection to the proxy
     */
    private $_sock;

    /**
     * ProxyIO constructor.
     *
     * @param string $proxyHost the host name or ip of the proxy
     * @param integer $proxyPort the port of the proxy
     * @param string $host the hostname or ip of the AMQP host
     * @param integer $port the port of the AMQP host
     * @param float $readTimeout the timeout for reading
     * @param boolean $keepAlive whether or not to keep the connection alive
     * @param null $writeTimeOut if null defaults to read timeout
     * @param int $heartbeat how often to send heartbeat. 0 means off
     */
    public function __construct(
        $proxyHost,
        $proxyPort,
        $host,
        $port,
        $readTimeout,
        $keepAlive = true,
        $writeTimeOut = null,
        $heartbeat = 0
    )
    {
        $this->proxyHost = $proxyHost;
        $this->proxyPort = $proxyPort;
        $this->host = $host;
        $this->port = $port;
        $this->read_timeout = $readTimeout;
        $this->keepAlive = $keepAlive;
        $this->write_timeout = $writeTimeOut ?: $readTimeout;
        $this->heartbeat = $heartbeat;
    }

    /**
     * The read function
     * {@inheritDoc}
     * @throws AMQPIOException
     */
    public function read($len)
    {
        if (is_null($this->_sock)) {
            throw new AMQPSocketException(sprintf(
                'Socket was null! Last SocketError was: %s',
                socket_strerror(socket_last_error())
            ));
        }

        $this->check_heartbeat();

        list($timeoutSec, $timeoutUSec) = MiscHelper::splitSecondsMicroseconds($this->read_timeout);
        $readStart = microtime(true);
        $read = 0;
        $rsp = '';
        while ($read < $len) {
            $buffer = null;
            $result = socket_recv($this->_sock, $buffer, $len - $read, 0);
            if ($result === 0) {
                // From linux recv() manual:
                // When a stream socket peer has performed an orderly shutdown,
                // the return value will be 0 (the traditional "end-of-file" return).
                // http://php.net/manual/en/function.socket-recv.php#47182
                /*
                $this->close();
                throw new AMQPConnectionClosedException('Broken pipe or closed connection');
                /*/
                $this->reconnect();
                //*/
            }

            if (empty($buffer)) {
                $readNow = microtime(true);
                $tRead = $readNow - $readStart;
                if ($tRead > $this->read_timeout) {
                    if (!empty($rsp)) {
                        break;
                    }
                    throw new AMQPTimeoutException('Too many read attempts detected in SocketIO');
                }
                $this->select($timeoutSec, $timeoutUSec);
                continue;
            }

            $read += mb_strlen($buffer, 'ASCII');
            $rsp .= $buffer;
        }

        if (mb_strlen($rsp, 'ASCII') > $len) {
            throw new AMQPIOException(sprintf(
                'Error reading data. Received %s instead of expected %s bytes',
                mb_strlen($rsp, 'ASCII'),
                $len
            ));
        }

        $this->last_read = microtime(true);

        return $rsp;
    }

    /**
     * The write function
     * {@inheritDoc}
     * @throws AMQPIOException
     */
    public function write($data)
    {
        // Null sockets are invalid, throw exception
        if (is_null($this->_sock)) {
            throw new AMQPSocketException(sprintf(
                'Socket was null! Last SocketError was: %s',
                socket_strerror(socket_last_error())
            ));
        }

        $this->checkBrokerHeartbeat();

        $written = 0;
        $len = mb_strlen($data, 'ASCII');
        $writeStart = microtime(true);

        while ($written < $len) {
            $this->set_error_handler();
            try {
                $this->selectWrite();
                $buffer = mb_substr($data, $written, self::BUFFER_SIZE, 'ASCII');
                $result = socket_write($this->_sock, $buffer, self::BUFFER_SIZE);
                $this->cleanup_error_handler();
            } catch (\ErrorException $e) {
                $code = socket_last_error($this->_sock);
                $constants = SocketConstants::getInstance();
                switch ($code) {
                    case $constants->SOCKET_EPIPE:
                    case $constants->SOCKET_ENETDOWN:
                    case $constants->SOCKET_ENETUNREACH:
                    case $constants->SOCKET_ENETRESET:
                    case $constants->SOCKET_ECONNABORTED:
                    case $constants->SOCKET_ECONNRESET:
                    case $constants->SOCKET_ECONNREFUSED:
                    case $constants->SOCKET_ETIMEDOUT:
                        $this->close();
                        throw new AMQPConnectionClosedException(socket_strerror($code), $code, $e);
                    default:
                        throw new AMQPIOException(sprintf(
                            'Error sending data. Last SocketError: %s',
                            socket_strerror($code)
                        ), $code, $e);
                }
            }

            if ($result === false) {
                throw new AMQPIOException(sprintf(
                    'Error sending data. Last SocketError: %s',
                    socket_strerror(socket_last_error($this->_sock))
                ));
            }

            $now = microtime(true);
            if ($result > 0) {
                $this->last_write = $writeStart = $now;
                $written += $result;
            } else {
                if (($now - $writeStart) > $this->write_timeout) {
                    throw AMQPTimeoutException::writeTimeout($this->write_timeout);
                }
            }
        }

        $this->last_write = microtime(true);
    }

    /**
     * Close the connection to the socket
     * {@inheritDoc}
     */
    public function close()
    {
        $this->disableHeartbeat();
        if (is_resource($this->_sock) || is_a($this->_sock, \Socket::class)) {
            socket_close($this->_sock);
        }
        $this->_sock = null;
        $this->last_read = null;
        $this->last_write = null;
    }

    /**
     * {@inheritDoc}
     * @throws AMQPIOException
     */
    public function connect()
    {
        $this->_sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

        list($sec, $uSec) = MiscHelper::splitSecondsMicroseconds($this->write_timeout);
        socket_set_option($this->_sock, SOL_SOCKET, SO_SNDTIMEO, ['sec' => $sec, 'usec' => $uSec]);
        list($sec, $uSec) = MiscHelper::splitSecondsMicroseconds($this->read_timeout);
        socket_set_option($this->_sock, SOL_SOCKET, SO_RCVTIMEO, ['sec' => $sec, 'usec' => $uSec]);

        $this->set_error_handler();
        try {
            $connected = socket_connect($this->_sock, $this->proxyHost, $this->proxyPort);
            $this->cleanup_error_handler();
        } catch (\ErrorException $e) {
            $connected = false;
        }
        if (!$connected) {
            $errno = socket_last_error($this->_sock);
            $errStr = socket_strerror($errno);
            throw new AMQPIOException(sprintf(
                'Error Connecting to server (%s): %s',
                $errno,
                $errStr
            ), $errno);
        }

        socket_set_block($this->_sock);
        socket_set_option($this->_sock, SOL_TCP, TCP_NODELAY, 1);

        if ($this->keepAlive) {
            socket_set_option($this->_sock, SOL_SOCKET, SO_KEEPALIVE, 1);
        }

        $this->write("CONNECT {$this->host}:{$this->port} HTTP/1.1\r\n\r\n");
        $rsp = $this->read(1024);
        if (!preg_match('#^HTTP/\d\.\d 200#', $rsp)) {
            throw new AMQPSocketException('Connection to AMQP host denied by proxy by unknown reason');
        }
    }

    /**
     * Returns the socket instance
     *
     * @return resource|\Socket
     */
    public function getSocket()
    {
        return $this->_sock;
    }

    /**
     * {@inheritDoc}
     */
    public function error_handler($errno, $errstr, $errfile, $errline, $errcontext = null)
    {
        $constants = SocketConstants::getInstance();
        // socket_select warning that it has been interrupted by a signal - EINTR
        if (isset($constants->SOCKET_EINTR) && false !== strrpos($errstr, socket_strerror($constants->SOCKET_EINTR))) {
            // it's allowed while processing signals
            return;
        }

        parent::error_handler($errno, $errstr, $errfile, $errline, $errcontext);
    }

    /**
     * Reconnect socket
     * @throws AMQPIOException
     */
    protected function reconnect()
    {
        $this->close();
        $this->connect();
    }

    /**
     * @return int|bool
     */
    protected function selectWrite()
    {
        $read = $except = null;
        $write = [$this->_sock];

        return socket_select($read, $write, $except, 0, 100000);
    }

    /**
     * {@inheritDoc}
     */
    protected function do_select($sec, $usec)
    {
        if (!is_resource($this->_sock) && !is_a($this->_sock, \Socket::class)) {
            $this->_sock = null;
            throw new AMQPConnectionClosedException('Broken pipe or closed connection', 0);
        }

        $read = [$this->_sock];
        $write = null;
        $except = null;

        return socket_select($read, $write, $except, $sec, $usec);
    }
}
