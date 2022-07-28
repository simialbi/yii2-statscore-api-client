<?php
/**
 * @package yii2-statscore-api-client
 * @author Simon Karlen <simi.albi@gmail.com>
 */

namespace simialbi\yii2\statscore\models;


use yii\base\Model;

class Status extends Model
{
    /**
     * @var integer Unique identifier for the status
     */
    public $id;
    /**
     * @var string Name of the status. Possible translation of the attribute
     */
    public $name;
    /**
     * @var string Abbreviated name of the status. Max 10 characters. Possible translation of the attribute
     */
    public $short_name;
    /**
     * @var string Type of status.
     */
    public $type;
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
            ['short_name', 'string', 'max' => 10],
            ['type', 'in', 'range' => ['live', 'scheduled', 'finished', 'cancelled', 'interrupted', 'deleted', 'other']],
            ['ut', 'integer']
        ];
    }
}
