<?php

namespace panix\mod\cart\models;

use yii\helpers\ArrayHelper;
use panix\engine\behaviors\TranslateBehavior;
use panix\mod\cart\models\translate\DeliveryTranslate;
use panix\mod\cart\models\DeliveryPayment;

class Delivery extends \panix\engine\WebModel {

    const MODULE_ID = 'cart';

    public $_payment_methods;

    public static function tableName() {
        return '{{%order_delivery}}';
    }
    public static function find() {
        return new query\DeliveryQuery(get_called_class());
    }
    
    public function getTranslations() {
        return $this->hasMany(DeliveryTranslate::className(), ['object_id' => 'id']);
    }


    public function getCategorization() {
        return $this->hasMany(DeliveryPayment::className(), ['delivery_id' => 'id']);
    }

    public function getPaymentMethods() {
        return $this->hasMany(Payment::className(), ['payment_id' => 'id'])->via('categorization');
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return [
            ['name', 'required'],

           // ['price, free_from', 'number'],

            ['payment_methods', 'validatePaymentMethods'],
            ['name', 'string', 'max' => 255],
            [['description','price','free_from'], 'string'],
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
            if (Payment::find()->where(array('id' => $id))->count() == 0)
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
            $model->save(false);
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
