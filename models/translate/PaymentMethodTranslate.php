<?php

namespace panix\mod\cart\models\translate;

class PaymentMethodTranslate extends yii\db\ActiveRecord {


    public function tableName() {
        return '{{%shop_payment_method_translate}}';
    }

}