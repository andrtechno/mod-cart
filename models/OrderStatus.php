<?php

namespace panix\mod\cart\models;

class OrderStatus extends \panix\engine\WebModel {

    const MODULE_ID = 'cart';

    public static function tableName() {
        return '{{%order_status}}';
    }

    public function rules() {
        return [
            ['name', 'required'],
            ['ordern', 'number'],
            ['name', 'string', 'max' => 255],
            ['color', 'string', 'min' => 6, 'max' => 6],
        ];
    }

    public function countOrders() {
        return Order::find()->where(array('status_id' => $this->id))->count();
    }

}
