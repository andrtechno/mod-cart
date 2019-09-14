<?php

namespace panix\mod\cart\models;

use panix\engine\db\ActiveRecord;

class PromoCode extends ActiveRecord
{

    const MODULE_ID = 'cart';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%order__promocode}}';
    }

    public static function find()
    {
        return new query\PromoCodeQuery(get_called_class());
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return [
            [['code', 'discount', 'max_use'], 'required'],
            [['max_use', 'used'], 'number'],
            ['code', 'string', 'max' => 50],
            ['discount', 'string', 'max' => 10],
            //[['code'], 'string'],
        ];
    }

}
