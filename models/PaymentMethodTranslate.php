<?php

/**
 * Class to access payment method translations
 *
 * @property int $id
 * @property int $object_id
 * @property int $language_id
 */
class PaymentMethodTranslate extends yii\db\ActiveRecord {


    public function tableName() {
        return '{{%shop_payment_method_translate}}';
    }

}