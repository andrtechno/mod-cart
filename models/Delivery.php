<?php

namespace panix\mod\cart\models;

use panix\mod\cart\components\delivery\DeliverySystemManager;
use yii\helpers\ArrayHelper;
use panix\engine\behaviors\TranslateBehavior;
use panix\mod\cart\models\translate\DeliveryTranslate;
use panix\engine\db\ActiveRecord;

class Delivery extends ActiveRecord {

    const MODULE_ID = 'cart';

    public $_payment_methods;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%order__delivery}}';
    }
    public static function find() {
        return new query\DeliveryQuery(get_called_class());
    }
    
    public function getTranslations() {
        return $this->hasMany(DeliveryTranslate::class, ['object_id' => 'id']);
    }


    public function getCategorization() {
        return $this->hasMany(DeliveryPayment::class, ['delivery_id' => 'id']);
    }

    public function getPaymentMethods() {
        return $this->hasMany(Payment::class, ['id' => 'payment_id'])->via('categorization');
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
            [['description','price','free_from','system'], 'string'],
        ];
    }

    public function behaviors() {
        return ArrayHelper::merge([
                    'translate' => [
                        'class' => TranslateBehavior::class,
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

       foreach ($this->getPayment_methods() as $pid) {
      //  if($this->getPayment_methods()){
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
    public function getDeliverySystemsArray() {

        $result = [];

        $systems = new DeliverySystemManager();

        foreach ($systems->getSystems() as $system) {
            $result[$system['id']] = $system['name'];
        }

        return $result;
    }
    /**
     * @return array
     */
    public function getPayment_methods()
    {


        if ($this->_payment_methods)
            return $this->_payment_methods;

        $this->_payment_methods = [];
        foreach ($this->categorization as $row)
            $this->_payment_methods[] = $row->payment_id;


        return $this->_payment_methods;
    }

    /**
     * @return string order used delivery method
     */
    public function countOrders() {
        return Order::find()->where(['delivery_id' => $this->id])->count();
    }

}
