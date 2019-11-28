<?php

namespace panix\mod\cart\models;

use panix\engine\behaviors\TranslateBehavior;
use yii\helpers\ArrayHelper;
use panix\mod\cart\models\translate\PaymentTranslate;
use panix\mod\cart\components\payment\PaymentSystemManager;
use panix\engine\db\ActiveRecord;

/**
 * Class Payment
 * @package panix\mod\cart\models
 */
class Payment extends ActiveRecord
{

    const MODULE_ID = 'cart';
    public $translationClass = PaymentTranslate::class;

    public static function tableName()
    {
        return '{{%order__payment}}';
    }

    public static function find()
    {
        return new query\DeliveryPaymentQuery(get_called_class());
    }


    public function rules()
    {
        return [
            [['name', 'currency_id'], 'required'],
            [['name'], 'trim'],
            [['name', 'payment_system'], 'string', 'max' => 255],
            [['id', 'name', 'description', 'switch'], 'safe'],
        ];
    }

    public function getPaymentSystemsArray()
    {
        // Yii::import('application.modules.shop.components.payment.PaymentSystemManager');
        $result = array();

        $systems = new PaymentSystemManager();

        foreach ($systems->getSystems() as $system) {
            $result[(string)$system->id] = $system->name;
        }

        return $result;
    }

    /**
     * Renders form display on the order view page
     */
    public function renderPaymentForm(Order $order)
    {
        if ($this->payment_system) {
            $manager = new PaymentSystemManager;
            $system = $manager->getSystemClass($this->payment_system);
            return $system->renderPaymentForm($this, $order);
        }
    }

    /**
     * @return null|BasePaymentSystem
     */
    public function getPaymentSystemClass()
    {
        if ($this->payment_system) {
            $manager = new PaymentSystemManager;
            return $manager->getSystemClass($this->payment_system);
        }
    }

}
