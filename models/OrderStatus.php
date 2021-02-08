<?php

namespace panix\mod\cart\models;

use panix\engine\db\ActiveRecord;

/**
 * Class OrderStatus
 *
 * @property int $id
 * @property string $name
 * @property string $color
 * @property boolean $use_in_stats
 *
 * @package panix\mod\cart\models
 */
class OrderStatus extends ActiveRecord
{

    const MODULE_ID = 'cart';

    public $disallow_delete = [
        Order::STATUS_NEW,
        Order::STATUS_DELETE,
        Order::STATUS_SUBMITTED,
        Order::STATUS_RETURN
    ];
    const route = '/admin/cart/statuses';

    public static function tableName()
    {
        return '{{%order__status}}';
    }

    public static function find()
    {
        return new query\OrderStatusesQuery(get_called_class());
    }

    public function rules()
    {
        return [
            ['name', 'required'],
            ['ordern', 'number'],
            ['name', 'string', 'max' => 100],
            ['color', 'string', 'min' => 7, 'max' => 7],
            [['use_in_stats'], 'boolean']
        ];
    }

    public function getOrdersCount()
    {
        return $this->hasMany(Order::class, ['status_id' => 'id'])->count();
    }
}
