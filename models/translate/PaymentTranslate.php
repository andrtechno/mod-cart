<?php

namespace panix\mod\cart\models\translate;

class PaymentTranslate extends \yii\db\ActiveRecord {


    public static function tableName() {
        return '{{%order_payment_translate}}';
    }

}