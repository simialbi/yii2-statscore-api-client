<?php
/**
 * @package yii2-statscore-api-client
 * @author Simon Karlen <simi.albi@gmail.com>
 */

namespace simialbi\yii2\statscore\models;


use yii\base\Model;

class Zone extends Model
{
    /**
     * @var string
     */
    public $name;
    /**
     * @var string The CSS colour for the zone
     */
    public $colour;

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            ['name', 'string'],
            ['colour', 'string'],
            ['colour', 'match', 'pattern' => '/^#[0-9a-f]{3,6}$/i']
        ];
    }
}
