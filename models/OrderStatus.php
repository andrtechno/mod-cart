<?php

namespace panix\mod\cart\models;

class OrderStatus extends \panix\engine\db\ActiveRecord {

    const MODULE_ID = 'cart';

    public static function tableName() {
        return '{{%order_status}}';
    }

    public function rules() {
        return [
            ['name', 'required'],
            ['ordern', 'number'],
            ['name', 'string', 'max' => 100],
            ['color', 'string', 'min' => 7, 'max' => 7],
        ];
    }

    public function countOrders() {
        return Order::find()->where(array('status_id' => $this->id))->count();
    }

}
