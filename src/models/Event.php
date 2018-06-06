<?php
/**
 * @package yii2-statscore-api-client
 * @author Simon Karlen <simi.albi@gmail.com>
 */

namespace simialbi\yii2\statscore\models;


use yii\base\Model;

class Event extends Model
{
    /**
     * @var integer Unique identifier for the event
     */
    public $id;
    /**
     * @var integer Unique event identifier for the client.
     */
    public $client_event_id;
    /**
     * @var boolean True if result is booked in selected product
     */
    public $booked;
    /**
     * @var integer When the value is 0 then event was autobooked.
     */
    public $booked_by;
    /**
     * @var string Name of the event e.g. Spain - Italy
     */
    public $name;
    /**
     * @var string Date when the event starts in the format DATE_TIME (yyyy-MM-dd HH:mm)
     */
    public $start_date;
    /**
     * @var boolean Determines when the result/score of the event is updated.
     */
    public $ft_only;
    /**
     * @var string|array Type of event coverage. Can be comma separated string.
     */
    public $coverage_type;
    /**
     * @var boolean Information concerning scouts coverage.
     */
    public $scoutsfeed;
    /**
     * @var integer Unique identifier of the event status
     */
    public $status_id;
    /**
     * @var string Name of the event status
     */
    public $status_name;
    /**
     * @var string Type of status.
     */
    public $status_type;
    /**
     * @var string How many seconds have passed in the current event period. Possible empty value
     */
    public $clock_time;
    /**
     * @var string Clock status.
     */
    public $clock_status;
    /**
     * @var string Bet status
     */
    public $bet_status;
    /**
     * @var string Relation status. Attribute for internal purposes.
     */
    public $relation_status;
    /**
     * @var string Identificator of scout related to the event. Attribute for internal purposes
     */
    public $source;
    /**
     * @var integer Who is the winner of the match
     */
    public $winner_id;
    /**
     * @var integer Who advanced to the next round
     */
    public $progress_id;
    /**
     * @var string Day of the season
     */
    public $day;
    /**
     * @var integer Unique identifier for the sport in which event is played.
     */
    public $sport_id;
    /**
     * @var string Name of the sport in which event is played
     */
    public $sport_name;
    /**
     * @var integer Unique identifier for the round in which event is played. Possible null value.
     */
    public $round_id;
    /**
     * @var string Name of the round
     */
    public $round_name;
    /**
     * @var boolean Determines if the event is played at a neutral venue.
     */
    public $neutral_venue;
    /**
     * @var string Describes status of the item (record).
     */
    public $item_status;
    /**
     * @var boolean Is the result verified?
     */
    public $verified_result;
    /**
     * @var integer Information about when the date and time of the event record was last updated. Format UNIX_TIMESTAMP
     */
    public $ut;
    /**
     * @var integer Old event id. The attribute will be removed in the next API version
     * @deprecated
     */
    public $old_event_id;
    /**
     * @var string friendly url for event
     */
    public $slug;
    /**
     * @var integer Unique identifier for the competition
     */
    public $competition_id;
    /**
     * @var string Abbreviated name of the competition
     */
    public $competition_short_name;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['id', 'integer'],
            ['client_event_id', 'integer'],
            ['booked', 'boolean', 'trueValue' => 'yes', 'falseValue' => 'no'],
            ['booked_by', 'integer'],
            ['name', 'string'],
            ['start_date', 'datetime', 'format' => 'yyyy-MM-dd HH:mm'],
            ['ft_only', 'boolean', 'trueValue' => 'yes', 'falseValue' => 'no'],
            ['coverage_type', 'in', 'range' => ['from_venue', 'from_tv', 'basic'], 'allowArray' => true],
            ['scoutsfeed', 'boolean', 'trueValue' => 'yes', 'falseValue' => 'no'],
            ['status_id', 'integer'],
            ['status_name', 'string'],
            ['status_type', 'in', 'range' => ['live', 'scheduled', 'finished', 'cancelled', 'interrupted', 'deleted', 'other']],
            ['clock_time', 'integer'],
            ['clock_status', 'in', 'range' => ['running', 'stopped']],
            ['bet_status', 'in', 'range' => ['active', 'suspended']],
            ['relation_status', 'in', 'range' => ['not_started', 'in_progress', 'finished', '30_min_left', '5_min_left']],
            ['source', 'integer'],
            ['winner_id', 'integer'],
            ['progress_id', 'integer'],
            ['day', 'string'],
            ['sport_id', 'integer'],
            ['sport_name', 'string'],
            ['round_id', 'integer'],
            ['round_name', 'string'],
            ['neutral_venue', 'boolean', 'trueValue' => 'yes', 'falseValue' => 'no'],
            ['item_status', 'in', 'range' => ['active', 'deleted']],
            ['verified_result', 'boolean', 'trueValue' => 'yes', 'falseValue' => 'no'],
            ['ut', 'integer'],
            ['old_event_id', 'integer'],
            ['slug', 'string'],
            ['competition_id', 'integer'],
            ['competition_short_name', 'string', 'max' => 20]
        ];
    }
}