<?php
/**
 * @package yii2-statscore-api-client
 * @author Simon Karlen <simi.albi@gmail.com>
 */

namespace simialbi\yii2\statscore\models;


use yii\base\Model;

class Area extends Model
{
    /**
     * @var integer Unique identifier for the area
     */
    public $id;
    /**
     * @var string Three character area code e.g. GER, POL, FRA
     */
    public $area_code;
    /**
     * @var string Name of the area. Possible translation of the attribute
     */
    public $name;
    /**
     * @var integer Identifier for the parent area e.g. parent area for Poland is Europe and parent area for Europe is World
     */
    public $parent_area_id;
    /**
     * @var integer Information about the date and time of when the record was last updated. Format UNIX_TIMESTAMP
     */
    public $ut;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['id', 'integer'].
            ['area_code', 'string', 'length' => 3],
            ['name', 'string'],
            ['parent_area_id', 'integer'],
            ['ut', 'integer']
        ];
    }
}