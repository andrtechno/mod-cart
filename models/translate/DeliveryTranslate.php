<?php

namespace panix\mod\cart\models\translate;

class DeliveryTranslate extends \yii\db\ActiveRecord {

    public static function tableName() {
        return '{{%order_delivery_translate}}';
    }

}
