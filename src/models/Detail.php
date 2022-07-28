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
     * @var string Description of the venue detail
     */
    public $description;
    /**
     * @var mixed Value related to the detail
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
            ['description', 'string'],
            ['value', 'safe']
        ];
    }
}
