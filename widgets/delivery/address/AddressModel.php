<?php

namespace panix\mod\cart\widgets\delivery\address;

use Yii;
use yii\base\Model;
use yii\validators\RequiredValidator;

class AddressModel extends Model
{

    public $address;

    public function rules()
    {
        return [
            [['address'], 'required'],
            [['address'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'address' => Yii::t('cart/Delivery', 'ADDRESS'),
        ];
    }

}
