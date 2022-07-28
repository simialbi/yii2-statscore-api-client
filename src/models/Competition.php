<?php
/**
 * @package yii2-statscore-api-client
 * @author Simon Karlen <simi.albi@gmail.com>
 */

namespace simialbi\yii2\statscore\models;

use yii\base\Model;

class Competition extends Model
{
    /**
     * @var integer Unique identifier for the competition
     */
    public $id;
    /**
     * @var string Name of the the competition e.g. U19 World Champ. Possible translation of the attribute
     */
    public $name;
    /**
     * @var string Abbreviated name of the competition e.g. U19 WCH, Max 20 characters.
     * Possible translation of the attribute
     */
    public $short_name;
    /**
     * @var string Mini name of the competition. Max 3 characters in length e.g. U19
     */
    public $mini_name;
    /**
     * @var string Competition gender.
     */
    public $gender;
    /**
     * @var string Competitions type.
     */
    public $type;
    /**
     * @var integer Unique identifier for area in which competitions are played.
     */
    public $area_id;
    /**
     * @var string Name of area in which competitions are played. Possible translation of the attribute
     */
    public $area_name;
    /**
     * @var string Type of area in which competitions are played.
     */
    public $area_type;
    /**
     * @var integer The parameter used to sort competitions in a country e.g. Premier League is 1st and
     * the Championship is 2nd in English competitions. Sorting direction is ascending
     */
    public $area_sort;
    /**
     * @var string Max 3 characters, area in which competitions are played
     */
    public $area_code;
    /**
     * @var integer The parameter used to sort competitions. Sorting direction is ascending
     */
    public $overall_sort;
    /**
     * @var integer Unique identifier for the sport in which the competition is played.
     */
    public $sport_id;
    /**
     * @var string Name of the sport in which the competition is played. Possible translation of the attribute
     */
    public $sport_name;
    /**
     * @var integer|null Unique identifier for the tour in which thecompetition is played.
     */
    public $tour_id;
    /**
     * @var string|null Name of the tour in which the competition is played
     */
    public $tour_name;
    /**
     * @var integer Information about when the date and time of the competition record was last updated.
     * Format UNIX_TIMESTAMP
     */
    public $ut;
    /**
     * @var integer The attribute will be removed in the next API version
     * @deprecated
     */
    public $old_competition_id;
    /**
     * @var string friendly url for competition
     */
    public $slug;
    /**
     * @var string
     */
    public $stats_lvl;
    /**
     * @var boolean
     */
    public $generate_season_stats;
    /**
     * @var string
     */
    public $status;

    /**
     * @var Season[]
     */
    public $seasons = [];

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            ['id', 'integer'],
            ['name', 'string'],
            ['short_name', 'string', 'max' => 20],
            ['mini_name', 'string', 'max' => 3],
            ['gender', 'in', 'range' => ['male', 'female', 'mixed']],
            [
                'type',
                'in',
                'range' => [
                    'country_league',
                    'country_cups',
                    'international',
                    'international_clubs',
                    'friendly',
                    'individual',
                    'team',
                    'single',
                    'double',
                    'mixed',
                    'undefined'
                ]
            ],
            ['area_id', 'integer'],
            ['area_name', 'string'],
            ['area_type', 'in', 'range' => ['country', 'international']],
            ['area_sort', 'integer'],
            ['area_code', 'string', 'max' => 3],
            ['overall_sort', 'integer'],
            ['sport_id', 'integer'],
            ['sport_name', 'string'],
            ['tour_id', 'integer'],
            ['tour_name', 'string'],
            ['ut', 'integer'],
            ['old_competition_id', 'integer'],
            ['slug', 'string'],
            ['stats_lvl', 'in', 'range' => ['gold', 'vip']],
            ['generate_season_stats', 'boolean'],
            ['status', 'string'],

            [['seasons'], 'safe']
        ];
    }
}
