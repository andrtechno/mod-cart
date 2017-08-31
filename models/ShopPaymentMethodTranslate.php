<?php

/**
 * Class to access payment method translations
 *
 * @property int $id
 * @property int $object_id
 * @property int $language_id
 */
class ShopPaymentMethodTranslate extends CActiveRecord {

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{shop_payment_method_translate}}';
    }

}