<?php

namespace panix\mod\cart\models\translate;

class PaymentMethodTranslate extends \yii\db\ActiveRecord {


    public static function tableName() {
        return '{{%shop_payment_method_translate}}';
    }

}