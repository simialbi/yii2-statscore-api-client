<?php
/**
 * @package yii2-statscore-api-client
 * @author Simon Karlen <simi.albi@gmail.com>
 */

namespace simialbi\yii2\statscore\models;


use yii\base\Model;

class Correction extends Model
{
    /**
     * @var integer Identifier for the participant
     */
    public $participant_id;
    /**
     * @var string Name of the participant
     */
    public $participant_name;
    /**
     * @var string Correction value, e.g. `+2`
     */
    public $value;
    /**
     * @var string Determines the reason of corrections
     */
    public $reason;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['participant_id', 'integer'],
            ['participant_name', 'string'],
            ['value', 'string'],
            [
                'reason',
                'in',
                'range' => ['Association decision', 'Disqualified', 'Financial issues', 'Quit', 'Regulations']
            ]
        ];
    }
}