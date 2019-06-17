<?php

namespace panix\mod\cart\models\translate;

use yii\db\ActiveRecord;

class DeliveryTranslate extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%order__delivery_translate}}';
    }

}
