<?php

namespace panix\mod\cart\models;

class DeliveryMethodTranslate extends \yii\db\ActiveRecord {

    public static function tableName() {
        return '{{%shop_delivery_method_translate}}';
    }

}
