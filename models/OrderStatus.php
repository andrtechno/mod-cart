<?php

namespace panix\mod\cart\models;

use panix\engine\db\ActiveRecord;

class OrderStatus extends ActiveRecord
{

    const MODULE_ID = 'cart';
    public $disallow_delete = [1, 2, 3];
    const route = '/admin/cart/statuses';

    public static function tableName()
    {
        return '{{%order__status}}';
    }

    public function rules()
    {
        return [
            ['name', 'required'],
            ['ordern', 'number'],
            ['name', 'string', 'max' => 100],
            ['color', 'string', 'min' => 7, 'max' => 7],
            ['use_in_stats', 'boolean']
        ];
    }

    public function getOrdersCount()
    {
        return $this->hasMany(Order::class, ['status_id' => 'id'])->count();
    }
}
