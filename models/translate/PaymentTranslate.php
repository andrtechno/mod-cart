<?php

namespace panix\mod\cart\models\translate;

use yii\db\ActiveRecord;

/**
 * Class PaymentTranslate
 *
 * @property string $name
 * @property string $description
 * @package panix\mod\cart\models\translate
 */
class PaymentTranslate extends ActiveRecord
{

    public static $translationAttributes = ['name', 'description'];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%order__payment_translate}}';
    }

}