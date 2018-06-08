<?php
/**
 * @package yii2-statscore-api-client
 * @author Simon Karlen <simi.albi@gmail.com>
 */

namespace simialbi\yii2\statscore\models;


use yii\base\Model;

class Season extends Model
{
    /**
     * @var integer Unique identifier for the season.
     */
    public $id;
    /**
     * @var string Name of the season e.g. Premiership 2014/15
     */
    public $name;
    /**
     * @var string Determines the season year
     */
    public $year;
    /**
     * @var boolean Determines if the actual season
     */
    public $actual;
    /**
     * @var integer Range level
     */
    public $range_level;
    /**
     * @var integer Information about when the date and time of the record was last updated. Format UNIX_TIMESTAMP
     */
    public $ut;
    /**
     * @var integer Old season id. The attribute will be removed in the next API version
     * @deprecated
     */
    public $old_season_id;

    /**
     * @var Stage[]
     */
    public $stages = [];

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['id', 'integer'],
            ['name', 'string'],
            ['year', 'string'],
            ['actual', 'boolean', 'trueValue' => 'yes', 'falseValue' => 'no'],
            ['range_level', 'integer'],
            ['ut', 'integer'],
            ['old_season_id', 'integer'],

            [['stages'], 'safe']
        ];
    }
}