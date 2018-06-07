<?php
/**
 * @package yii2-statscore-api-client
 * @author Simon Karlen <simi.albi@gmail.com>
 */

namespace simialbi\yii2\statscore\models\sport;


use yii\base\Model;

class Detail extends Model
{
    /**
     * @var integer Unique identifier for the detail
     */
    public $id;
    /**
     * @var string Name of the detail available in all events for the selected sport
     */
    public $name;
    /**
     * @var string Attribute for internal purpose
     */
    public $code;
    /**
     * @var string Defines type of field generated on front (internal purpose only)
     */
    public $data_type;
    /**
     * @var string Description note of detail
     */
    public $description;
    /**
     * @var string Input name e.g. select/input etc
     */
    public $input;
    /**
     * @var string Type attribute of input
     */
    public $type;
    /**
     * @var array Possible selection values for input types like "select" or "radio".
     */
    public $possible_values = [];
    /**
     * @var string Statuses related to detail
     */
    public $related_statuses;
    /**
     * @var string Input format e.g. for date: "Y-m-d H:i:s"
     */
    public $format;
    /**
     * @var boolean Determines if input can be sended with null value
     */
    public $nullable;
    /**
     * @var mixed Determines default value
     */
    public $default;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return  [
            ['id', 'integer'],
            ['name', 'string'],
            ['code', 'string'],
            ['data_type', 'in', 'range' => ['integer', 'decimal', 'binary_text', 'binary', 'text', 'singed_integer']],
            ['description', 'string'],
            ['input', 'string'],
            ['type', 'string'],
            ['possible_values', 'safe'],
            ['related_statuses', 'each', 'rule' => ['integer']],
            ['format', 'string'],
            ['nullable', 'boolean', 'trueValue' => 'yes', 'falseValue' => 'no'],
            ['default', 'safe']
        ];
    }
}