<?php

namespace panix\mod\cart\models;

use panix\engine\behaviors\TranslateBehavior;
use yii\helpers\ArrayHelper;
use panix\mod\cart\models\translate\PaymentMethodTranslate;

class PaymentMethod extends \panix\engine\WebModel {

    const MODULE_ID = 'cart';

    public static function tableName() {
        return '{{%shop_payment_method}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules22() {
        return array(
            array('switch, ordern', 'numerical', 'integerOnly' => true),
            array('description', 'safe'),
            array('payment_system', 'safe'),
        );
    }

    public function getTranslations() {
        return $this->hasMany(PaymentMethodTranslate::className(), ['object_id' => 'id']);
    }

    public function behaviors() {
        return ArrayHelper::merge([
                    'translate' => [
                        'class' => TranslateBehavior::className(),
                        'translationAttributes' => [
                            'name',
                            'description'
                        ]
                    ],
                        ], parent::behaviors());
    }

    public function rules() {
        return [
            [['name', 'currency_id'], 'required'],
            [['name'], 'trim'],
            [['is_main', 'is_default'], 'boolean'],
            [['name'], 'string', 'max' => 255],
            [['ordern'], 'integer'],
            [['rate'], 'number'],
            [['id, name, description, switch', 'safe'], 'safe'],
        ];
    }

}
