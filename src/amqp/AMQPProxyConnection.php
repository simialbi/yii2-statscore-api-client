<?php
/**
 * @package yii2-statscore-api-client
 * @author Simon Karlen <simi.albi@gmail.com>
 */

namespace simialbi\yii2\statscore\amqp;

use PhpAmqpLib\Connection\AbstractConnection;
use PhpAmqpLib\Connection\AMQPSocketConnection;
use PhpAmqpLib\Wire\AMQPWriter;
use yii\helpers\ArrayHelper;

/**
 * Class AMQPProxyConnection
 * @package simialbi\yii2\statscore\amqp
 */
class AMQPProxyConnection extends AbstractConnection
{
    /**
     * AMQPProxyConnection constructor.
     *
     * @param string $proxyHost
     * @param integer $proxyPort
     * @param string $host
     * @param integer $port
     * @param string $user
     * @param string $password
     * @param string $vhost
     * @param false $insist
     * @param string $loginMethod
     * @param AMQPWriter|null $loginResponse
     * @param string $locale
     * @param integer $readTimeout
     * @param false $keepalive
     * @param integer $writeTimeout
     * @param integer $heartbeat
     * @param integer $connectionTimeout
     *
     * @throws \Exception
     */
    public function __construct(
        $proxyHost,
        $proxyPort,
        $host,
        $port,
        $user,
        $password,
        $vhost = '/',
        $insist = false,
        $loginMethod = 'AMQPLAIN',
        $loginResponse = null,
        $locale = 'en_US',
        $readTimeout = 3,
        $keepalive = true,
        $writeTimeout = 3,
        $heartbeat = 0,
        $connectionTimeout = 0
    ) {
        $io = new ProxyIO($proxyHost, $proxyPort, $host, $port, $readTimeout, $keepalive, $writeTimeout, $heartbeat);

        parent::__construct(
            $user,
            $password,
            $vhost,
            $insist,
            $loginMethod,
            $loginResponse,
            $locale,
            $io,
            $heartbeat,
            $connectionTimeout
        );
    }

    /**
     * Try to create a connection
     *
     * @param string $host
     * @param string $port
     * @param string $user
     * @param string $password
     * @param string $vhost
     * @param array $options
     *
     * @return AbstractConnection
     * @throws \Exception
     */
    protected static function try_create_connection($host, $port, $user, $password, $vhost, $options)
    {
        if (!isset($options['proxyHost']) || !isset($options['proxyPort'])) {
            return new AMQPSocketConnection(
                $host,
                $port,
                $user,
                $password,
                $vhost,
                ArrayHelper::getValue($options, 'insist', false),
                ArrayHelper::getValue($options, 'login_method', 'AMQPLAIN'),
                ArrayHelper::getValue($options, 'login_response', null),
                ArrayHelper::getValue($options, 'locale', 'en_US'),
                ArrayHelper::getValue($options, 'read_timeout', 3),
                ArrayHelper::getValue($options, 'keepalive', false),
                ArrayHelper::getValue($options, 'write_timeout', 3),
                ArrayHelper::getValue($options, 'heartbeat', 0)
            );
        }

        return new static(
            ArrayHelper::getValue($options, 'proxyHost'),
            ArrayHelper::getValue($options, 'proxyPort'),
            $host,
            $port,
            $user,
            $password,
            $vhost,
            ArrayHelper::getValue($options, 'insist', false),
            ArrayHelper::getValue($options, 'login_method', 'AMQPLAIN'),
            ArrayHelper::getValue($options, 'login_response', null),
            ArrayHelper::getValue($options, 'locale', 'en_US'),
            ArrayHelper::getValue($options, 'read_timeout', 3),
            ArrayHelper::getValue($options, 'keepalive', false),
            ArrayHelper::getValue($options, 'write_timeout', 3),
            ArrayHelper::getValue($options, 'heartbeat', 0)
        );
    }
}
