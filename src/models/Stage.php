<?php
/**
 * @package yii2-statscore-api-client
 * @author Simon Karlen <simi.albi@gmail.com>
 */

namespace simialbi\yii2\statscore\models;


use yii\base\Model;

class Stage extends Model
{
    /**
     * @var integer Unique identifier for the stage
     */
    public $id;
    /**
     * @var string Name of the stage. Possible translation of the attribute
     */
    public $name;
    /**
     * @var string Date when stage begins. Format YYYY-MM-DD
     */
    public $start_date;
    /**
     * @var string Date when stage ends. Format YYYY-MM-DD
     */
    public $end_date;
    /**
     * @var boolean Determines if the stage contains standings
     */
    public $show_standings;
    /**
     * @var integer The number of groups that are part of the stage
     */
    public $groups_nr;
    /**
     * @var integer The sort order for the stage. Sorting direction is ascending
     */
    public $sort;
    /**
     * @var boolean True if current stage
     */
    public $is_current;
    /**
     * @var integer Information about the date and time of when the stage record was last updated. Format UNIX_TIMESTAMP
     */
    public $ut;
    /**
     * @var string Old stage id. The attribute will be removed in the next API version
     * @deprecated
     */
    public $old_stage_id;

    /**
     * @var Group[]
     */
    public $groups;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['id', 'integer'],
            ['name', 'string'],
            ['start_date', 'date', 'format' => 'yyyy-MM-dd'],
            ['end_date', 'date', 'format' => 'yyyy-MM-dd'],
            ['show_standings', 'boolean', 'trueValue' => 'yes', 'falseValue' => 'no'],
            ['groups_nr', 'integer'],
            ['sort', 'integer'],
            ['is_current', 'boolean', 'trueValue' => 'yes', 'falseValue' => 'no'],
            ['ut', 'integer'],
            ['old_stage_id', 'integer'],

            [['groups'], 'safe']
        ];
    }
}