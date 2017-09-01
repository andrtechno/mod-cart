<?php

namespace panix\mod\cart\models;
/**
 * This is the model class for table "notifications".
 *
 * The followings are the available columns in table 'notifications':
 * @property integer $id
 * @property integer $product_id
 * @property string $email
 */
class ProductNotifications extends \yii\db\ActiveRecord {


    /**
     * @return string the associated database table name
     */
    public static function tableName() {
        return '{{%shop_notifications}}';
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
        return $this->hasOne(ShopProduct::className(), ['id' => 'product_id']);
    }
    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'product_id' => Yii::t('core', 'Продукт'),
            'product' => Yii::t('core', 'Продукт'),
            'product_quantity' => Yii::t('core', 'Количество'),
            'product_availability' => Yii::t('core', 'Доступность'),
            'name' => Yii::t('core', 'Название'),
            'email' => Yii::t('core', 'Email'),
            'totalEmails' => Yii::t('core', 'Количество подписчиков')
        );
    }

    public function getTotalEmails() {
        return ProductNotifications::find()->where(['product_id' => $this->product_id])->count();
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return ActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search() {
        $criteria = new CDbCriteria;
        $criteria->group = 'product_id';
        $criteria->with = 'product';

        $criteria->compare('id', $this->id);
        $criteria->compare('product_id', $this->product_id);
        $criteria->compare('email', $this->email, true);

        return new ActiveDataProvider($this, array(
                    'criteria' => $criteria,
                ));
    }

    /**
     * Check if email exists in list for current product
     */
    public function hasEmail() {
        return ProductNotifications::model()->countByAttributes(array(
                    'email' => $this->email,
                    'product_id' => $this->product_id
                )) > 0;
    }

}