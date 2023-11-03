<?php

namespace panix\mod\cart\widgets\delivery\meest;

use Yii;
use yii\base\Model;

class MeestConfigurationModel extends Model
{

    public $type_warehouse;

    public function rules()
    {
        return [
            [['type_warehouse'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'type_warehouse' => Yii::t('cart/delivery', 'Types Warehouse'),
        ];
    }


}
