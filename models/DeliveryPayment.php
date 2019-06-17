<?php

namespace panix\mod\cart\models;

use yii\db\ActiveRecord;

class DeliveryPayment extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%order__delivery_payment}}';
    }

}
