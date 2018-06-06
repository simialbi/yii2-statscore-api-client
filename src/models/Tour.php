<?php
/**
 * @package yii2-statscore-api-client
 * @author Simon Karlen <simi.albi@gmail.com>
 */

namespace simialbi\yii2\statscore\models;


use yii\base\Model;

class Tour extends Model
{
    /**
     * @var integer Unique identifier for the tour
     */
    public $id;
    /**
     * @var string Name of the tour. Possible translation of the attribute
     */
    public $name;
    /**
     * @var integer Identifier for sport
     */
    public $sport_id;
    /**
     * @var integer Attribute used to sort tours, ascending
     */
    public $sport_order;
    /**
     * @var integer Information about the date and time of when the tour record was last updated. Format UNIX_TIMESTAMP
     */
    public $ut;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['id', 'integer'],
            ['name', 'string'],
            ['sport_id', 'integer'],
            ['sport_order', 'integer'],
            ['ut', 'integer']
        ];
    }
}