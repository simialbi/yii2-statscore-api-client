<?php
/**
 * @package yii2-statscore-api-client
 * @author Simon Karlen <simi.albi@gmail.com>
 */

namespace simialbi\yii2\statscore\models;


use yii\base\Model;

class Detail extends Model
{
    /**
     * @var integer Unique identifier for the detail
     */
    public $id;
    /**
     * @var string Name of the detail. Possible values are different depending on the sport
     */
    public $name;
    /**
     * @var mixed Value related to the detail
     */
    public $value;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['id', 'integer'],
            ['name', 'string'],
            ['value', 'safe']
        ];
    }
}