<?php

namespace panix\mod\cart\widgets\delivery\pickup;

use Yii;
use yii\base\Model;

class PickupConfigurationModel extends Model
{

    public $address;

    public function rules()
    {
        return [
            [['address'], 'required'],
            [['address'], 'string'],
            [['address'], 'trim'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'address' => Yii::t('cart/payments', 'API key'),
        ];
    }

}
