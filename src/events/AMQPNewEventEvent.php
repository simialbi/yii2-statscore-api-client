<?php
/**
 * @package yii2-statscore-api-client
 * @author Simon Karlen <simi.albi@gmail.com>
 */

namespace simialbi\yii2\statscore\events;

use simialbi\yii2\statscore\models\Event;

/**
 * AMQPNewEventEvent represents a **new message** from AMQP service event
 *
 * The parameter `event` holds the parsed message
 *
 * @package simialbi\yii2\statscore\events
 */
class AMQPNewEventEvent extends AMQPEvent
{
    /**
     * @var Event the parsed event message data
     */
    public $event;
}
