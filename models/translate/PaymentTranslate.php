<?php

namespace panix\mod\cart\models\translate;

use yii\db\ActiveRecord;

class PaymentTranslate extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%order__payment_translate}}';
    }

}