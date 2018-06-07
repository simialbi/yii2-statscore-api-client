<?php
/**
 * @package yii2-statscore-api-client
 * @author Simon Karlen <simi.albi@gmail.com>
 */

namespace simialbi\yii2\statscore\models;


use yii\base\Model;

class Stat extends Model
{
    /**
     * @var integer Unique identifier for the statistic
     */
    public $id;
    /**
     * @var string Abbreviated name of the statistic.
     */
    public $short_name;
    /**
     * @var string Name of the statistic. Possible values are different depending on the sport
     */
    public $name;
    /**
     * @var string Attribute for internal purpose
     */
    public $code;
    /**
     * @var mixed Value related to the statistic
     */
    public $value;
    /**
     * @var string Defines type of field generated on front (internal purpose only)
     */
    public $data_type;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['id', 'integer'],
            ['short_name', 'string'],
            ['name', 'string'],
            ['code', 'string'],
            ['value', 'safe'],
            ['data_type', 'in', 'range' => ['integer', 'decimal', 'binary_text', 'binary', 'text', 'signed_integer']]
        ];
    }
}