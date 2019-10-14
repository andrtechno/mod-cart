<?php

namespace panix\mod\cart\models\translate;

use yii\db\ActiveRecord;

/**
 * Class DeliveryTranslate
 * @property string $name
 * @package panix\mod\cart\models\translate
 */
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
