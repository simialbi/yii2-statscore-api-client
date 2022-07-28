<?php
/**
 * @package yii2-statscore-api-client
 * @author Simon Karlen <simi.albi@gmail.com>
 */

namespace simialbi\yii2\statscore\models;

use yii\base\Model;

class Result extends Model
{
    /**
     * @var integer Unique identifier for the result
     */
    public $id;
    /**
     * @var string Abbreviated name of the result.
     */
    public $short_name;
    /**
     * @var string Name of the result. Possible values are different depending on the sport
     */
    public $name;
    /**
     * @var string Attribute for internal purpose
     */
    public $code;
    /**
     * @var mixed Value related to the result
     */
    public $value;
    /**
     * @var string Defines type of the result.
     */
    public $type;
    /**
     * @var string Defines type of field generated on front (internal purpose only)
     */
    public $data_type;

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            ['id', 'integer'],
            ['short_name', 'string'],
            ['name', 'string'],
            ['code', 'string'],
            ['value', 'safe'],
            ['type', 'in', 'range' => ['team', 'person', 'team_and_person']],
            ['data_type', 'in', 'range' => ['integer', 'decimal', 'binary_text', 'binary', 'text', 'signed_integer']]
        ];
    }
}
