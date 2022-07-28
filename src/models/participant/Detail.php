<?php
/**
 * @package yii2-statscore-api-client
 * @author Simon Karlen <simi.albi@gmail.com>
 */

namespace simialbi\yii2\statscore\models\participant;


use yii\base\Model;

class Detail extends Model
{
    /**
     * @var string Date when the team was founded
     */
    public $founded;
    /**
     * @var string Phone number
     */
    public $phone;
    /**
     * @var string Email address
     */
    public $email;
    /**
     * @var string Address where the team is located
     */
    public $address;
    /**
     * @var integer Unique identifier of the venue in which the team plays home games
     */
    public $venue_id;
    /**
     * @var string Name of the venue in which the team plays home games
     */
    public $venue_name;
    /**
     * @var string Participants weight
     */
    public $weight;
    /**
     * @var string Participants height
     */
    public $height;
    /**
     * @var string Participants nickname
     */
    public $nickname;
    /**
     * @var integer Unique identifier of the participants position
     */
    public $position_id;
    /**
     * @var string Name of the participants position
     */
    public $position_name;
    /**
     * @var string Participants birthday
     */
    public $birthdate;
    /**
     * @var string Participants place of birth
     */
    public $born_place;
    /**
     * @var boolean Determines if the participant has retired.
     */
    public $is_retired;
    /**
     * @var string Participant subtype name.
     */
    public $subtype;

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            ['founded', 'string'],
            ['phone', 'string'],
            ['email', 'email', 'enableIDN' => extension_loaded('intl')],
            ['address', 'string'],
            ['venue_id', 'integer'],
            ['venue_name', 'string'],
            ['weight', 'integer'],
            ['height', 'integer'],
            ['nickname', 'string'],
            ['position_id', 'integer'],
            ['position_name', 'string'],
            ['birthdate', 'date', 'format' => 'yyyy-MM-dd'],
            ['born_place', 'string'],
            ['is_retired', 'boolean', 'trueValue' => 'yes', 'falseValue' => 'no'],
            ['subtype', 'in', 'range' => ['athlete', 'coach', 'referee', 'director']],
        ];
    }
}
