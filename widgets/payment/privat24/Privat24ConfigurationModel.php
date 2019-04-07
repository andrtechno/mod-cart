<?php

namespace panix\mod\cart\widgets\payment\privat24;

use Yii;

class Privat24ConfigurationModel extends \yii\base\Model
{

    public $merchant_id;
    public $merchant_pass;

    public function rules()
    {
        return [
            [['merchant_id', 'merchant_pass'], 'required'],
            [['merchant_id', 'merchant_pass'], 'string'],
            [['merchant_id', 'merchant_pass'], 'trim'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'merchant_id' => Yii::t('cart/payments', 'PRIVAT24_MERCHANT_ID'),
            'merchant_pass' => Yii::t('cart/payments', 'PRIVAT24_MERCHANT_PASS'),
        ];
    }

}
