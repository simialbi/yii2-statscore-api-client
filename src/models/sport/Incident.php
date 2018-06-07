<?php
/**
 * @package yii2-statscore-api-client
 * @author Simon Karlen <simi.albi@gmail.com>
 */

namespace simialbi\yii2\statscore\models\sport;


use yii\base\Model;

class Incident extends Model
{
    /**
     * @var integer Unique identifier for the incident
     */
    public $id;
    /**
     * @var string Name of the incident available in all events for the selected sport
     */
    public $name;
    /**
     * @var boolean Determines if the incident is important e.g. goals, cards, substitutions
     */
    public $important;
    /**
     * @var boolean Determines if incident is important for traders
     */
    public $important_for_trader;
    /**
     * @var integer Unique identifier for the sport in which a event is played
     */
    public $sport_id;
    /**
     * @var string Type of incident
     */
    public $type;
    /**
     * @var string A group which incident belongs to
     */
    public $group;
    /**
     * @var integer Information about the date and time of when the record was last updated. Format UNIX_TIMESTAMP
     */
    public $ut;
    /**
     * @var string Attribute for internal purpose
     */
    public $code;
    /**
     * @var string Determine what the participant includes incident
     */
    public $for;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['id', 'integer'],
            ['name', 'string'],
            [['important', 'important_for_trader'], 'boolean', 'trueValue' => 'yes', 'falseValue' => 'no'],
            ['sport_id', 'integer'],
            ['type', 'in', 'range' => ['team', 'event']],
            ['group', 'string'],
            ['ut', 'integer'],
            ['code', 'string'],
            ['for', 'in', 'range' => ['all', 'own', 'rival', 'none']]
        ];
    }
}