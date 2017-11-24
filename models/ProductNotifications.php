<?php

namespace panix\mod\cart\models;

use Yii;
use panix\mod\shop\models\Product;
/**
 * This is the model class for table "notifications".
 *
 * The followings are the available columns in table 'notifications':
 * @property integer $id
 * @property integer $product_id
 * @property string $email
 */
class ProductNotifications extends \panix\engine\db\ActiveRecord {

    const MODULE_ID = 'cart';

    /**
     * @return string the associated database table name
     */
    public static function tableName() {
        return '{{%order_product_notify}}';
    }

    public static function find() {
        return new query\ProductNotificationsQuery(get_called_class());
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return [
            ['email', 'required'],
            ['email', 'string', 'max' => 255],
            ['email', 'email'],
        ];
    }

    /**
     * @return array relational rules.
     */
    public function getProduct() {
        return $this->hasOne(Product::className(), ['id' => 'product_id']);
    }
    public static function getSort() {
        return new \yii\data\Sort([
            'attributes' => [
                //'totalEmails',
                'product.quantity' => [
                    'asc' => ['quantity' => SORT_ASC],
                    'desc' => ['quantity' => SORT_DESC],
                ],
                'product.availability' => [
                    'asc' => ['availability' => SORT_ASC],
                    'desc' => ['availability' => SORT_DESC],
                ],

            ],
        ]);//
    }
    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'product_id' => Yii::t('app', 'Продукт'),
            'product' => Yii::t('app', 'Продукт'),

            'name' => Yii::t('app', 'Название'),
            'email' => Yii::t('app', 'Email'),
            'totalEmails' => Yii::t('app', 'Количество подписчиков')
        );
    }

    public function getTotalEmails() {
        return ProductNotifications::find()->where(['product_id' => $this->product_id])->count();
    }



    /**
     * Check if email exists in list for current product
     */
    public function hasEmail() {
        return ProductNotifications::find([
                            'email' => $this->email,
                            'product_id' => $this->product_id])
                        ->where()->count() > 0;
    }

}
