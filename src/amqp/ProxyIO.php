<?php
/**
 * @package yii2-statscore-api-client
 * @author Simon Karlen <simi.albi@gmail.com>
 */

namespace simialbi\yii2\statscore\amqp;

use PhpAmqpLib\Exception\AMQPHeartbeatMissedException;
use PhpAmqpLib\Exception\AMQPIOException;
use PhpAmqpLib\Exception\AMQPSocketException;
use PhpAmqpLib\Helper\MiscHelper;
use PhpAmqpLib\Wire\AMQPWriter;
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
     * @var float the timeout for reading
     */
    protected $readTimeout;

    /**
     * @var float the timeout for writing
     */
    protected $writeTimeout;

    /**
     * @var integer the interval to send the heartbeat
     */
    protected $heartbeat;

    /**
     * @var float the unix timestamp when the last read happened
     */
    protected $lastRead;

    /**
     * @var float the unix timestamp whe the last write happened
     */
    protected $lastWrite;

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
    ) {
        $this->proxyHost = $proxyHost;
        $this->proxyPort = $proxyPort;
        $this->host = $host;
        $this->port = $port;
        $this->readTimeout = $readTimeout;
        $this->keepAlive = $keepAlive;
        $this->writeTimeout = $writeTimeOut ?: $readTimeout;
        $this->heartbeat = $heartbeat;
    }

    /**
     * The read function
     * {@inheritDoc}
     * @throws AMQPIOException
     */
    public function read($n)
    {

        if (is_null($this->_sock)) {
            throw new AMQPSocketException(sprintf(
                'Socket was null! Last SocketError was: %s',
                socket_strerror(socket_last_error())
            ));
        }
        $rsp = '';
        $read = 0;
        $buf = socket_read($this->_sock, $n);
        while ($read < $n && $buf !== '' && $buf !== false) {
            $this->check_heartbeat();

            $read += mb_strlen($buf, 'ASCII');
            $rsp .= $buf;
            $buf = socket_read($this->_sock, $n - $read);
        }

        if (mb_strlen($rsp, 'ASCII') != $n) {
            throw new AMQPIOException(sprintf(
                'Error reading data. Received %s instead of expected %s bytes',
                mb_strlen($rsp, 'ASCII'),
                $n
            ));
        }

        $this->lastRead = microtime(true);

        return $rsp;
    }

    /**
     * The write function
     * {@inheritDoc}
     * @throws AMQPIOException
     */
    public function write($data)
    {

        $len = mb_strlen($data, 'ASCII');

        while (true) {
            // Null sockets are invalid, throw exception
            if (is_null($this->_sock)) {
                throw new AMQPSocketException(sprintf(
                    'Socket was null! Last SocketError was: %s',
                    socket_strerror(socket_last_error())
                ));
            }

            $sent = socket_write($this->_sock, $data, $len);
            if ($sent === false) {
                throw new AMQPIOException(sprintf(
                    'Error sending data. Last SocketError: %s',
                    socket_strerror(socket_last_error())
                ));
            }

            // Check if the entire message has been sent
            if ($sent < $len) {
                // If not sent the entire message.
                // Get the part of the message that has not yet been sent as message
                $data = mb_substr($data, $sent, mb_strlen($data, 'ASCII') - $sent, 'ASCII');
                // Get the length of the not sent part
                $len -= $sent;
            } else {
                break;
            }
        }

        $this->lastWrite = microtime(true);
    }

    /**
     * Close the connection to the socket
     * {@inheritDoc}
     */
    public function close()
    {
        if (is_resource($this->_sock)) {
            socket_close($this->_sock);
        }
        $this->_sock = null;
        $this->lastRead = null;
        $this->lastWrite = null;
    }

    /**
     * {@inheritDoc}
     */
    public function select($sec, $uSec)
    {
        $read = [$this->_sock];
        $write = null;
        $except = null;

        return socket_select($read, $write, $except, $sec, $uSec);
    }

    /**
     * {@inheritDoc}
     * @throws AMQPIOException
     */
    public function connect()
    {
        $this->_sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

        list($sec, $uSec) = MiscHelper::splitSecondsMicroseconds($this->writeTimeout);
        socket_set_option($this->_sock, SOL_SOCKET, SO_SNDTIMEO, ['sec' => $sec, 'usec' => $uSec]);
        list($sec, $uSec) = MiscHelper::splitSecondsMicroseconds($this->readTimeout);
        socket_set_option($this->_sock, SOL_SOCKET, SO_RCVTIMEO, ['sec' => $sec, 'usec' => $uSec]);

        if (!socket_connect($this->_sock, $this->proxyHost, $this->proxyPort)) {
            $errno = socket_last_error($this->_sock);
            $errStr = socket_strerror($errno);
            throw new AMQPIOException(sprintf(
                'Error Connecting to proxy (%s): %s',
                $errno,
                $errStr
            ), $errno);
        }

        socket_set_block($this->_sock);
        socket_set_option($this->_sock, SOL_TCP, TCP_NODELAY, 1);

        if ($this->keepAlive) {
            socket_set_option($this->_sock, SOL_SOCKET, SO_KEEPALIVE, 1);
        }

        $this->write("\05\01\00");
        $rsp = $this->read(2);
        if ($rsp === "\05\00") {
            $host = gethostbyname($this->host);
            $req = "\05\01\00\01" . inet_pton($host) . pack('n', $this->port);
            $this->write($req);
            $rsp = $this->read(10);
            if ($rsp[1] !== "\00") {
                $this->close();
                throw new AMQPSocketException(sprintf(
                    'Connection to AMQP host denied by proxy: Code %s',
                    ord($rsp[1])
                ));
            }
        } else {
            throw new AMQPSocketException('Connection to AMQP host denied by proxy by unknown reason');
        }
    }

    /**
     * Reconnect socket
     * {@inheritDoc}
     * @throws AMQPIOException
     */
    public function reconnect()
    {
        $this->close();
        $this->connect();
    }

    /**
     * {@inheritDoc}
     */
    public function getSocket()
    {
        return $this->_sock;
    }

    /**
     * {@inheritDoc}
     * @throws AMQPIOException
     */
    public function check_heartbeat()
    {
        // ignore unless heartbeat interval is set
        if ($this->heartbeat !== 0 && $this->lastRead && $this->lastWrite) {
            $t = microtime(true);
            $tRead = round($t - $this->lastRead);
            $tWrite = round($t - $this->lastWrite);

            // server has gone away
            if (($this->heartbeat * 2) < $tRead) {
                $this->close();
                throw new AMQPHeartbeatMissedException('Missed server heartbeat');
            }

            // time for client to send a heartbeat
            if (($this->heartbeat / 2) < $tWrite) {
                $this->writeHeartbeat();
            }
        }
    }

    /**
     * Sends a heartbeat message
     * @throws AMQPIOException
     */
    protected function writeHeartbeat()
    {
        $pkt = new AMQPWriter();
        $pkt->write_octet(8);
        $pkt->write_short(0);
        $pkt->write_long(0);
        $pkt->write_octet(0xCE);
        $this->write($pkt->getvalue());
    }
}
