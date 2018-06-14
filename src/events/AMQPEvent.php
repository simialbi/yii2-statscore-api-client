<?php
/**
 * @package yii2-statscore-api-client
 * @author Simon Karlen <simi.albi@gmail.com>
 */

namespace simialbi\yii2\statscore\events;


use simialbi\yii2\statscore\Client;
use yii\base\Event;

/**
 * AMQPEvent represents the event parameter for all AMQP service based events
 *
 * @package simialbi\yii2\statscore\events
 */
class AMQPEvent extends Event
{
    /**
     * @var Client the sender of this event. If not set, this property will be
     * set as the object whose `trigger()` method is called.
     * This property may also be a `null` when this event is a
     * class-level event which is triggered in a static context.
     */
    public $sender;
}