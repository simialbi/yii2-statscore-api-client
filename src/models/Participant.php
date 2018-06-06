<?php
/**
 * @package yii2-statscore-api-client
 * @author Simon Karlen <simi.albi@gmail.com>
 */

namespace simialbi\yii2\statscore\models;


use yii\base\Model;

class Participant extends Model
{
    /**
     * @var integer Unique identifier of the participant
     */
    public $id;
    /**
     * @var string Participant type.
     */
    public $type;
    /**
     * @var string Participants name
     */
    public $name;
    /**
     * @var string Participants abbreviated name, max length: 20 characters
     */
    public $short_name;
    /**
     * @var string Max 3 characters in length mini name, e.g. BAR, REA
     */
    public $acronym;
    /**
     * @var string Participants gender.
     */
    public $gender;
    /**
     * @var integer Unique identifier for the area represented by the participant.
     */
    public $area_id;
    /**
     * @var string Area name represented by the participant.
     */
    public $area_name;
    /**
     * @var string Max 3 characters in length for the abbreviated area name e.g. GER, POL, FRA.
     */
    public $area_code;
    /**
     * @var integer Unique identifier for the sport in which the participant is involved.
     */
    public $sport_id;
    /**
     * @var string Name of the sport in which the participant is involved.
     */
    public $sport_name;
    /**
     * @var boolean Determines if participant is a national team
     */
    public $national;
    /**
     * @var string Participants official website
     */
    public $website;
    /**
     * @var integer Information about when the date and time of the record was last updated. Format UNIX_TIMESTAMP
     */
    public $ut;
    /**
     * @var string Old participant id. The attribute will be removed in the next API version
     * @deprecated
     */
    public $old_participant_id;
    /**
     * @var string friendly url for participant
     */
    public $slug;
    /**
     * @var string Attribute for internal purpose
     */
    public $logo;
    /**
     * @var boolean Determines if the participant is real player / team or virtual.
     */
    public $virtual;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['id', 'integer'],
            ['type', 'in', 'range' => ['team', 'person']],
            ['name', 'string'],
            ['short_name', 'string', 'max' => 20],
            ['acronym', 'string', 'max' => 3],
            ['gender', 'in', 'range' => ['male', 'female', 'mixed']],
            ['area_id', 'integer'],
            ['area_name', 'string'],
            ['area_code', 'string', 'max' => 3],
            ['sport_id', 'integer'],
            ['sport_name', 'string'],
            ['national', 'boolean', 'trueValue' => 'yes', 'falseValue' => 'no'],
            ['website', 'string'],
            ['ut', 'integer'],
            ['old_participant_id', 'integer'],
            ['slug', 'string'],
            ['logo', 'string'],
            ['virtual', 'boolean', 'trueValue' => 'yes', 'falseValue' => 'no']
        ];
    }
}