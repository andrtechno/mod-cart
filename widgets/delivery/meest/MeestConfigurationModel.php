<?php

namespace panix\mod\cart\widgets\delivery\meest;

use Yii;
use yii\base\Model;

class MeestConfigurationModel extends Model
{

    public $api_key;
    public $type_warehouse;

    public function rules()
    {
        return [
            [['api_key'], 'required'],
            [['api_key'], 'string'],
            [['api_key'], 'trim'],
            [['type_warehouse'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'api_key' => Yii::t('cart/delivery', 'API key'),
            'type_warehouse' => Yii::t('cart/delivery', 'Types Warehouse'),
        ];
    }


}
