<?php
/**
 * @package yii2-statscore-api-client
 * @author Simon Karlen <simi.albi@gmail.com>
 */

namespace simialbi\yii2\statscore\models;


use yii\base\Model;

class StandingType extends Model
{
    /**
     * @var integer Unique identifier for the standings type
     */
    public $id;
    /**
     * @var string Name of the standing type
     */
    public $name;
    /**
     * @var integer Information about the date and time of when the record was last updated. Format UNIX_TIMESTAMP
     */
    public $ut;

    /**
     * @var Column[]
     */
    public $columns = [];

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['id', 'integer'],
            ['name', 'string'],
            ['ut', 'integer'],

            [['columns'], 'safe']
        ];
    }
}