<?php
namespace panix\mod\cart\widgets\payment\privat24;

class Privat24ConfigurationModel extends \yii\base\Model {

    public $MERCHANT_ID;
    public $MERCHANT_PASS;

    public function rules() {
        return array(
            array('MERCHANT_ID, MERCHANT_PASS', 'type')
        );
    }

    public function attributeNames() {
        return array(
            'MERCHANT_ID' => Yii::t('cart/payments', 'PRIVAT24_MERCHANT_ID'),
            'MERCHANT_PASS' => Yii::t('cart/payments', 'PRIVAT24_MERCHANT_PASS'),
        );
    }

    public function getForm() {
        return array(
            'type' => 'form',
            'elements' => array(
                'MERCHANT_ID' => array(
                    'label' => Yii::t('cart/payments', 'PRIVAT24_MERCHANT_ID'),
                    'type' => 'text',
                ),
                'MERCHANT_PASS' => array(
                    'label' => Yii::t('cart/payments', 'PRIVAT24_MERCHANT_PASS'),
                    'type' => 'text',
                ),
            )
        );
    }

}
