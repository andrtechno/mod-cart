<?php

namespace panix\mod\cart\models;

use Yii;
use panix\engine\WebModel;

class ShopPaymentMethod extends WebModel {

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

    public function rules() {
        return [
            [['name', 'currency_id'], 'required'],
            [['name'], 'trim'],
            [['is_main','is_default'], 'boolean'],
            [['name'], 'string', 'max' => 255],
            [['ordern'], 'integer'],
            [['rate'], 'number'],
            [['id, name, description, switch', 'safe'], 'safe'],
        ];
    }



}