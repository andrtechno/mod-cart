<?php

/**
 * Saves relations between delivery and payment methods.
 * This is the model class for table "ShopDeliveryPayments".
 *
 * The followings are the available columns in table 'ShopDeliveryPayments':
 * @property integer $id
 * @property integer $delivery_id
 * @property integer $payment_id
 */
class ShopDeliveryPayment extends ActiveRecord {

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return ShopDeliveryPayment the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return '{{shop_delivery_payment}}';
    }

}