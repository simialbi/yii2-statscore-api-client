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
 * The parameter `data` holds the parsed message
 *
 * @package simialbi\yii2\statscore\events
 */
class AMQPNewEventEvent extends AMQPEvent
{
    /**
     * @var Event the data that is passed to [[Component::on()]] when attaching an event handler.
     * Note that this varies according to which event handler is currently executing.
     */
    public $data;
}