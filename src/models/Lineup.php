<?php
/**
 * @package yii2-statscore-api-client
 * @author Simon Karlen <simi.albi@gmail.com>
 */

namespace simialbi\yii2\statscore\models;


use yii\base\Model;

class Lineup extends Model
{
    /**
     * @var integer Unique identifier for lineup related to event
     */
    public $id;
    /**
     * @var string Type of participant in lineup.
     */
    public $type;
    /**
     * @var boolean Determines if participant (player) is in the starting line up for the event
     */
    public $bench;
    /**
     * @var integer Unique identifier for the participant (player, coach)
     */
    public $participant_id;
    /**
     * @var string Name of the participant (player, coach)
     */
    public $participant_name;
    /**
     * @var integer Identifier for area represented by the participant
     */
    public $participant_area_id;
    /**
     * @var string Shirt number. Could be empty value
     */
    public $shirt_nr;
    /**
     * @var string Friendly url for the participant
     */
    public $participant_slug;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['id', 'integer'],
            ['type', 'in', 'range' => ['player', 'coach']],
            ['bench', 'boolean', 'trueValue' => 'yes', 'falseValue' => ''],
            ['participant_id', 'integer'],
            ['participant_name', 'string'],
            ['participant_area_id', 'integer'],
            ['shirt_nr', 'string'],
            ['participant_slug', 'string']
        ];
    }
}