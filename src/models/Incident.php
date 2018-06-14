<?php
/**
 * @package yii2-statscore-api-client
 * @author Simon Karlen <simi.albi@gmail.com>
 */

namespace simialbi\yii2\statscore\models;


use yii\base\Model;

class Incident extends Model
{
    /**
     * @var integer Incident identifier. Unique for sport.
     */
    public $id;
    /**
     * @var string Describes type of operation.
     */
    public $action;
    /**
     * @var integer Old Id
     * @deprecated
     */
    public $old_id;
    /**
     * @var integer The event identifier
     */
    public $event_id;
    /**
     * @var integer Identifier for event status in which the incident occured
     */
    public $event_status_id;
    /**
     * @var string Name of event status in which the incident occured
     */
    public $event_status_name;
    /**
     * @var string Event minute and second in which the incident occured
     */
    public $event_time;
    /**
     * @var integer Incident id in data.sports.incidents node for event. The
     */
    public $incident_id;
    /**
     * @var string Name of incident e.g. goal, yellow card, red card etc
     */
    public $incident_name;
    /**
     * @var integer Participant id
     */
    public $participant_id;
    /**
     * @var integer Participant team id
     */
    public $participant_team_id;
    /**
     * @var string Participant name.
     */
    public $participant_name;
    /**
     * @var string Friendly url for the participant
     */
    public $participant_slug;
    /**
     * @var integer Unique identifier for the player, who is a member of participant squad e.g. Real Madrid, Barcelona
     */
    public $subparticipant_id;
    /**
     * @var string Subparticipant name
     */
    public $subparticipant_name;
    /**
     * @var string Friendly url for the subparticipant. Possible empty value
     */
    public $subparticipant_slug;
    /**
     * @var string Additional information about the incident
     */
    public $info;
    /**
     * @var integer Status id in data.sports.statuses node for event
     */
    public $status_id;
    /**
     * @var integer Incident time: minute
     */
    public $minute;
    /**
     * @var integer Incident time: second
     */
    public $second;
    /**
     * @var integer Home Score
     */
    public $home_score;
    /**
     * @var integer Away Score
     */
    public $away_score;
    /**
     * @var integer Home Score 1
     */
    public $home_score1;
    /**
     * @var integer Away Score 1
     */
    public $away_score1;
    /**
     * @var integer Home Score 2
     */
    public $home_score2;
    /**
     * @var integer Away Score 2
     */
    public $away_score2;
    /**
     * @var integer Ball position on X axis (percentage)
     */
    public $x_pos;
    /**
     * @var integer Ball position on Y axis (percentage)
     */
    public $y_pos;
    /**
     * @var string Custom information
     */
    public $add_data;
    /**
     * @var string Label for incidents
     */
    public $display_info;
    /**
     * @var integer Subsequent number of the incident type in the event. Example: 5th goal in the match.
     */
    public $counter;
    /**
     * @var integer Created date
     */
    public $ct;
    /**
     * @var integer Last update date
     */
    public $ut;
    /**
     * @var integer Deleted date
     */
    public $dt;
    /**
     * @var integer Old Id
     * @deprecated
     */
    public $old_added_id;
    /**
     * @var string Determines sort order of data
     */
    public $sort_type;
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
            ['action', 'in', 'range' => ['insert', 'update', 'delete']],
            ['old_id', 'integer'],
            ['event_id', 'integer'],
            ['event_status_id', 'integer'],
            ['event_status_name', 'string'],
            ['event_time', 'string'],
            ['incident_id', 'integer'],
            ['incident_name', 'string'],
            ['participant_id', 'integer'],
            ['participant_team_id', 'integer'],
            ['participant_name', 'string'],
            ['participant_slug', 'string'],
            ['subparticipant_id', 'integer'],
            ['subparticipant_name', 'string'],
            ['subparticipant_slug', 'string'],
            ['info', 'string'],
            ['status_id', 'integer'],
            ['minute', 'integer', 'min' => 0],
            ['second', 'integer', 'min' => 0],
            ['home_score', 'integer'],
            ['away_score', 'integer'],
            ['home_score1', 'integer'],
            ['away_score1', 'integer'],
            ['home_score2', 'integer'],
            ['away_score2', 'integer'],
            ['x_pos', 'integer'],
            ['y_pos', 'integer'],
            ['add_data', 'string'],
            ['display_info', 'string'],
            ['counter', 'integer'],
            ['ct', 'integer'],
            ['ut', 'integer'],
            ['dt', 'integer'],
            ['old_added_id', 'integer'],
            ['sort_type', 'in', 'range' => ['id']],
            ['for', 'in', 'range' => ['all', 'own', 'rival', 'none']]
        ];
    }
}