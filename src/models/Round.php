<?php
/**
 * @package yii2-statscore-api-client
 * @author Simon Karlen <simi.albi@gmail.com>
 */

namespace simialbi\yii2\statscore\models;


use yii\base\Model;

class Round extends Model
{
    /**
     * @var integer Unique identifier for the round
     */
    public $id;
    /**
     * @var string Name of the the round. Possible translation of the attribute
     */
    public $name;
    /**
     * @var integer Order of round. Sorting direction is ascending
     */
    public $sort;
    /**
     * @var integer Information about when the date and time of the record was last updated. Format UNIX_TIMESTAMP
     */
    public $ut;

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            ['id', 'integer'],
            ['name', 'string'],
            ['sort', 'integer'],
            ['ut', 'integer']
        ];
    }
}
