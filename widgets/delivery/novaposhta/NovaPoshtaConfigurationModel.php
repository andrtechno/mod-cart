<?php

namespace panix\mod\cart\widgets\delivery\novaposhta;

use Yii;
use yii\base\Model;

class NovaPoshtaConfigurationModel extends Model
{

    public $api_key;

    public function rules()
    {
        return [
            [['api_key'], 'required'],
            [['api_key'], 'string'],
            [['api_key'], 'trim'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'api_key' => Yii::t('cart/delivery', 'API key'),
        ];
    }

}
