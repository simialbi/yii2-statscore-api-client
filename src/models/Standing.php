<?php
/**
 * @package yii2-statscore-api-client
 * @author Simon Karlen <simi.albi@gmail.com>
 */

namespace simialbi\yii2\statscore\models;


use yii\base\Model;

class Standing extends Model
{
    /**
     * @var integer Unique identifier for the standings
     */
    public $id;
    /**
     * @var string Name of the standings
     */
    public $name;
    /**
     * @var integer Unique identifier for the sport in which the standings occurres
     */
    public $sport_id;
    /**
     * @var string Name of the sport
     */
    public $sport_name;
    /**
     * @var integer Unique identifier for the standings type. e.g. Away standings, World Cup standings,
     * Event's final result
     */
    public $type_id;
    /**
     * @var string Name of standings type. e.g. Standings table, Top scorers, Cards
     */
    public $type_name;
    /**
     * @var string Name of subtype of standings
     */
    public $subtype;
    /**
     * @var integer Unique identifier for object selected in the object_type
     */
    public $object_id;
    /**
     * @var string Type of object (kind of scope) for which data is generated.
     */
    public $object_type;
    /**
     * @var string Name of selected object e.g. European Championship (competition_season), Premier League
     * (competition_season), FC Barcelona (team), Relegation Round (stage), FC Barcelona - Manchester United (event)
     */
    public $object_name;
    /**
     * @var string Describes status of the item (record)
     */
    public $item_status;
    /**
     * @var boolean Describes type of rank in groups.
     */
    public $reset_group_rank;
    /**
     * @var integer Information about the date and time of when the record was last updated. Format UNIX_TIMESTAMP
     */
    public $ut;

    /**
     * @var Group[]
     */
    public $groups = [];

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['id', 'integer'],
            ['name', 'string'],
            ['sport_id', 'integer'],
            ['sport_name', 'string'],
            ['type_id', 'integer'],
            ['type_name', 'string'],
            ['subtype', 'in', 'range' => ['standings', 'under_over', 'form', 'overall_stats']],
            ['object_id', 'integer'],
            ['object_type', 'in', 'range' => ['sport', 'season', 'stage']],
            ['object_name', 'string'],
            ['item_status', 'in', 'range' => ['active', 'deleted']],
            ['reset_group_rank', 'boolean', 'trueValue' => 'yes', 'falseValue' => 'no'],
            ['ut', 'integer'],

            [['groups'], 'safe']
        ];
    }
}