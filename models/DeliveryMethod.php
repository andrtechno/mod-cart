<?php

namespace panix\mod\cart\models;

use yii\helpers\ArrayHelper;
use panix\engine\behaviors\TranslateBehavior;
use panix\mod\cart\models\translate\DeliveryMethodTranslate;
use panix\mod\cart\models\DeliveryPayment;

class DeliveryMethod extends \panix\engine\WebModel {

    const MODULE_ID = 'cart';
    public $_payment_methods;

    public static function tableName() {
        return '{{%shop_delivery_method}}';
    }
    public function getPaymentMethods() {
        return $this->hasMany(PaymentMethod::className(), ['payment_id' => 'id']);
    }
    public function getTranslations() {
        return $this->hasMany(DeliveryMethodTranslate::className(), ['object_id' => 'id']);
    }
    public function getCategorization() {
        return $this->hasMany(DeliveryPayment::className(), ['delivery_id' => 'id']);
    }
    

             
    
    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return [
            ['name', 'required'],
          //  ['ordern', 'numerical', 'integerOnly' => true],
          //  ['price, free_from', 'numerical'],
          //  ['switch', 'boolean'],
            ['payment_methods', 'validatePaymentMethods'],
            ['name', 'string', 'max' => 255],
            ['description', 'string'],

        ];
    }

    public function behaviors() {
        return ArrayHelper::merge([
                    'translate' => [
                        'class' => TranslateBehavior::className(),
                        'translationAttributes' => [
                            'name',
                            'description'
                        ]
                    ],
                        ], parent::behaviors());
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
            if (ShopPaymentMethod::find()->where(array('id' => $id))->count() == 0)
                $this->addError($attr, $this->t('ERROR_PAYMENT'));
        }
    }

    /**
     * After save event
     */
    public function afterSave($insert, $changedAttributes) {
        

        // Clear payment relations
        DeliveryPayment::deleteAll(['delivery_id' => $this->id]);

        foreach ($this->payment_methods as $pid) {
            $model = new DeliveryPayment;
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
        return Order::find()->where(array('delivery_id' => $this->id))->count();
    }


}