<?php

namespace panix\mod\cart\models;


use panix\engine\WebModel;

class ShopDeliveryMethod extends WebModel {

    const MODULE_ID = 'cart';


    public static function tableName() {
        return '{{%shop_delivery_method}}';
    }


    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return array(
            array('name', 'required'),
            array('ordern', 'numerical', 'integerOnly' => true),
            array('price, free_from', 'numerical'),
            array('switch', 'boolean'),
            array('payment_methods', 'validatePaymentMethods'),
            array('name', 'length', 'max' => 255),
            array('description', 'type', 'type' => 'string'),
            array('id, name, description, ordern', 'safe', 'on' => 'search'),
        );
    }




    /**
     * Validate payment method exists
     * @param $attr
     * @return mixed
     */
    public function validatePaymentMethods($attr) {
        if (!is_array($this->$attr))
            return;

        foreach ($this->$attr as $id) {
            if (ShopPaymentMethod::model()->countByAttributes(array('id' => $id)) == 0)
                $this->addError($attr, $this->t('ERROR_PAYMENT'));
        }
    }

    /**
     * After save event
     */
    public function afterSave($insert, $changedAttributes) {
        

        // Clear payment relations
        ShopDeliveryPayment::find()->deleteAll(array('delivery_id' => $this->id));

        foreach ($this->payment_methods as $pid) {
            $model = new ShopDeliveryPayment;
            $model->delivery_id = $this->id;
            $model->payment_id = $pid;
            $model->save();
        }

        return parent::afterSave($insert, $changedAttributes);
    }

    /**
     * @param $data array ids of payment methods
     */
    public function setPayment_methods($data) {
        $this->_payment_methods = $data;
    }

    /**
     * @return array
     */
    public function getPayment_methods() {
        if ($this->_payment_methods)
            return $this->_payment_methods;

        $this->_payment_methods = array();
        foreach ($this->categorization as $row)
            $this->_payment_methods[] = $row->payment_id;
        return $this->_payment_methods;
    }

    /**
     * @return string order used delivery method
     */
    public function countOrders() {
        return Order::model()->countByAttributes(array('delivery_id' => $this->id));
    }


}