<?php
/**
 * @package yii2-statscore-api-client
 * @author Simon Karlen <simi.albi@gmail.com>
 */

namespace simialbi\yii2\statscore\models;


use yii\base\Model;

class Sport extends Model
{
    /**
     * @var integer Unique identifier for the sport
     */
    public $id;
    /**
     * @var string Name of the sport. Possible translation of the attribute
     */
    public $name;
    /**
     * @var string Name that is used in the url address in API
     */
    public $url;
    /**
     * @var boolean Sports status that shows if the sport is currently supported in API.
     */
    public $active;
    /**
     * @var boolean Defines if sport use timer.
     */
    public $has_timer;
    /**
     * @var string Show information about events participants quantity in sport. Possible values
     */
    public $participant_quantity;
    /**
     * @var string Internal attribute.
     */
    public $template;
    /**
     * @var boolean Defines if sport uses incidents positions.
     */
    public $incidents_positions;
    /**
     * @var integer Information about when the date and time of the record was last updated. Format UNIX_TIMESTAMP
     */
    public $ut;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['id', 'integer'],
            ['name', 'string'],
            ['url', 'string'],
            ['active', 'boolean', 'trueValue' => 'yes', 'falseValue' => 'no'],
            ['has_timer', 'boolean', 'trueValue' => 'yes', 'falseValue' => 'no'],
            ['participant_quantity', 'in', 'range' => ['2', 'more']],
            ['template', 'in', 'range' => ['default', 'fixed_incidents']],
            ['incidents_positions', 'boolean', 'trueValue' => 'yes', 'falseValue' => 'no'],
            ['ut', 'integer']
        ];
    }
}