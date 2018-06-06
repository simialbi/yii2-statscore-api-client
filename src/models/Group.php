<?php
/**
 * @package yii2-statscore-api-client
 * @author Simon Karlen <simi.albi@gmail.com>
 */

namespace simialbi\yii2\statscore\models;


use yii\base\Model;

class Group extends Model
{
    /**
     * @var integer Unique identifier for the group
     */
    public $id;
    /**
     * @var string Name of the group
     */
    public $name;
    /**
     * @var integer Information about the date and time of when the group record was last updated. Format UNIX_TIMESTAMP
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
            ['ut', 'integer']
        ];
    }
}