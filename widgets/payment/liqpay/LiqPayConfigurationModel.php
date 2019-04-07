<?php

namespace panix\mod\cart\widgets\payment\liqpay;

use panix\engine\base\Model;
use Yii;

class LiqPayConfigurationModel extends Model
{

    public $public_key;
    public $private_key;

    public function rules()
    {
        return [
            [['public_key', 'private_key'], 'required'],
            [['public_key', 'private_key'], 'string'],
            [['public_key', 'private_key'], 'trim'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'public_key' => Yii::t('cart/widgets/payment/liqpay/LiqPayConfigurationModel', 'test'),
            'private_key' => Yii::t('cart/payments', 'PRIVAT24_MERCHANT_PASS'),
        ];
    }

}
