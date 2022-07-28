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
     * @var string AMQP: This attribute describes the type of action taken for any new data
     */
    public $action;
    /**
     * @var boolean True if result is booked in selected product
     */
    public $booked;
    /**
     * @var integer When the value is 0 then event was autobooked.
     */
    public $booked_by;
    /**
     * @var integer Unique identifier of the venue in which the event occurs
     */
    public $venue_id;
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
     * @var integer TV coverage channels id
     */
    public $channel_id;
    /**
     * @var string TV coverage channels name
     */
    public $channel_name;
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
     * @var string Bet card status
     */
    public $bet_cards;
    /**
     * @var string Bet corners status
     */
    public $bet_corners;
    /**
     * @var string Relation status. Attribute for internal purposes.
     */
    public $relation_status;
    /**
     * @var string Identifier of scout related to the event. Attribute for internal purposes
     */
    public $source;
    /**
     * @var boolean Is the source disconnected?
     */
    public $source_dc;
    /**
     * @var string Sources supervisor
     */
    public $source_super;
    /**
     * @var integer Who is the winner of the match
     */
    public $winner_id;
    /**
     * @var integer Who advanced to the next round
     */
    public $progress_id;
    /**
     * @var integer Which group the event is pointing to
     */
    public $group_id;
    /**
     * @var string Day of the season
     */
    public $day;
    /**
     * @var integer Unique identifier for area in which competitions are played.
     */
    public $area_id;
    /**
     * @var integer Unique identifier for the season.
     */
    public $season_id;
    /**
     * @var integer Unique identifier for the stage
     */
    public $stage_id;
    /**
     * @var integer Unique identifier for the sport in which event is played.
     */
    public $sport_id;
    /**
     * @var string Name of the sport in which event is played
     */
    public $sport_name;
    /**
     * @var integer|null Unique identifier for the tour in which the competition is played.
     */
    public $tour_id;
    /**
     * @var integer Unique identifier for the round in which event is played. Possible null value.
     */
    public $round_id;
    /**
     * @var string Name of the round
     */
    public $round_name;
    /**
     * @var string Event gender.
     */
    public $gender;
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
     * @var string If result is verified, by whom?
     */
    public $result_verified_by;
    /**
     * @var string If result is verified, at?
     */
    public $result_verified_at;
    /**
     * @var boolean Is the protocol verified?
     */
    public $is_protocol_verified;
    /**
     * @var string If protocol is verified, by whom?
     */
    public $protocol_verified_by;
    /**
     * @var string If protocol is verified, at?
     */
    public $protocol_verified_at;
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
     * @var boolean Inverted participants
     */
    public $inverted_participants;
    /**
     * @var boolean BFS
     */
    public $bfs;
    /**
     * @var string Stats LVL
     */
    public $event_stats_lvl;

    /**
     * @var Detail[]
     */
    public $details = [];
    /**
     * @var Participant[]
     */
    public $participants = [];
    /**
     * @var Incident[]
     */
    public $incidents = [];

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            ['id', 'integer'],
            ['client_event_id', 'integer'],
            ['action', 'in', 'range' => ['insert', 'update', 'delete']],
            ['booked', 'boolean', 'trueValue' => 'yes', 'falseValue' => 'no'],
            ['booked_by', 'integer'],
            ['venue_id', 'integer'],
            ['name', 'string'],
            ['start_date', 'datetime', 'format' => 'yyyy-MM-dd HH:mm'],
            ['ft_only', 'boolean', 'trueValue' => 'yes', 'falseValue' => 'no'],
            ['coverage_type', 'in', 'range' => ['from_venue', 'from_tv', 'basic'], 'allowArray' => true],
            ['channel_id', 'integer'],
            ['channel_name', 'string'],
            ['scoutsfeed', 'boolean', 'trueValue' => 'yes', 'falseValue' => 'no'],
            ['status_id', 'integer'],
            ['status_name', 'string'],
            ['status_type', 'in', 'range' => ['live', 'scheduled', 'finished', 'cancelled', 'interrupted', 'deleted', 'other']],
            ['clock_time', 'integer'],
            ['clock_status', 'in', 'range' => ['running', 'stopped']],
            [['bet_status', 'bet_cards', 'bet_corners'], 'in', 'range' => ['active', 'suspended']],
            ['relation_status', 'in', 'range' => ['not_started', 'in_progress', 'finished', '30_min_left', '5_min_left']],
            ['source', 'integer'],
            ['source_dc', 'boolean', 'trueValue' => 'yes', 'falseValue' => 'no'],
            ['source_super', 'string'],
            ['winner_id', 'integer'],
            ['progress_id', 'integer'],
            ['group_id', 'integer'],
            ['day', 'string'],
            ['area_id', 'integer'],
            ['season_id', 'integer'],
            ['stage_id', 'integer'],
            ['sport_id', 'integer'],
            ['sport_name', 'string'],
            ['tour_id', 'integer'],
            ['round_id', 'integer'],
            ['round_name', 'string'],
            ['gender', 'in', 'range' => ['male', 'female', 'mixed']],
            ['neutral_venue', 'boolean', 'trueValue' => 'yes', 'falseValue' => 'no'],
            ['item_status', 'in', 'range' => ['active', 'deleted']],
            ['verified_result', 'boolean', 'trueValue' => 'yes', 'falseValue' => 'no'],
            ['result_verified_by', 'string'],
            ['result_verified_at', 'datetime', 'format' => 'yyyy-MM-dd HH:mm'],
            ['is_protocol_verified', 'boolean', 'trueValue' => 'yes', 'falseValue' => 'no'],
            ['protocol_verified_by', 'string'],
            ['protocol_verified_at', 'datetime', 'format' => 'yyyy-MM-dd HH:mm'],
            ['ut', 'integer'],
            ['old_event_id', 'integer'],
            ['slug', 'string'],
            ['competition_id', 'integer'],
            ['competition_short_name', 'string', 'max' => 20],
            ['inverted_participants', 'boolean', 'trueValue' => 'yes', 'falseValue' => 'no'],
            ['bfs', 'boolean', 'trueValue' => 'yes', 'falseValue' => 'no'],
            ['event_stats_lvl', 'in', 'range' => ['gold', 'vip']],

            [['details', 'participants', 'incidents'], 'safe']
        ];
    }
}
