<?php
/**
 * @package yii2-statscore-api-client
 * @author Simon Karlen <simi.albi@gmail.com>
 */

namespace simialbi\yii2\statscore\models;


use yii\base\Model;

class Column extends Model
{
    /**
     * @var integer Unique identifier for the column
     */
    public $id;
    /**
     * @var string Name of the column
     */
    public $name;
    /**
     * @var string Abbreviated name of the column
     */
    public $short_name;
    /**
     * @var string Attribute for internal purpose
     */
    public $code;
    /**
     * @var mixed
     */
    public $value;

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            ['id', 'integer'],
            ['name', 'string'],
            ['short_name', 'string'],
            ['code', 'string'],
            ['value', 'safe']
        ];
    }
}
