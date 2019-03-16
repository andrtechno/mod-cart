<?php

namespace panix\mod\cart\models;

use panix\engine\behaviors\TranslateBehavior;
use yii\helpers\ArrayHelper;
use panix\mod\cart\models\translate\PaymentTranslate;
use panix\mod\cart\components\payment\PaymentSystemManager;

class Payment extends \panix\engine\db\ActiveRecord {

    const MODULE_ID = 'cart';

    public static function tableName() {
        return '{{%order__payment}}';
    }

    public static function find() {
        return new query\DeliveryPaymentQuery(get_called_class());
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules22() {
        return array(
            array('switch, ordern', 'numerical', 'integerOnly' => true),
            array('description', 'safe'),
            array('payment_system', 'safe'),
        );
    }

    public function getTranslations() {
        return $this->hasMany(PaymentTranslate::class, ['object_id' => 'id']);
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

    public function rules() {
        return [
            [['name', 'currency_id'], 'required'],
            [['name'], 'trim'],
            [['name'], 'string', 'max' => 255],
            [['id, name, description, switch', 'safe'], 'safe'],
        ];
    }

    public function getPaymentSystemsArray() {
        // Yii::import('application.modules.shop.components.payment.PaymentSystemManager');
        $result = array();

        $systems = new PaymentSystemManager();

        foreach ($systems->getSystems() as $system) {
            $result[(string) $system->id] = $system->name;
        }

        return $result;
    }

    /**
     * Renders form display on the order view page
     */
    public function renderPaymentForm(Order $order) {
        if ($this->payment_system) {
            $manager = new PaymentSystemManager;
            $system = $manager->getSystemClass($this->payment_system);
            return $system->renderPaymentForm($this, $order);
        }
    }

    /**
     * @return null|BasePaymentSystem
     */
    public function getPaymentSystemClass() {
        if ($this->payment_system) {
            $manager = new PaymentSystemManager;
            return $manager->getSystemClass($this->payment_system);
        }
    }

}
