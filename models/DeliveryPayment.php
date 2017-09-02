<?php

namespace panix\mod\cart\models;

class DeliveryPayment extends \yii\db\ActiveRecord {


    public static function tableName() {
        return '{{%shop_delivery_payment}}';
    }

}
