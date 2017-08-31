<?php

class OrderProductHistroy extends ActiveRecord {

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return '{{order_product_history}}';
    }

    public function relations() {
        Yii::import('mod.shop.models.*');
        return array(
            'supplier' => array(self::BELONGS_TO, 'ShopSuppliers', 'supplier_id'),
            'images' => array(self::HAS_MANY, 'ShopProductImage', 'product_id'),
            'mainImage' => array(self::HAS_ONE, 'ShopProductImage', 'product_id', 'condition' => 'is_main=1'),
            'imagesNoMain' => array(self::HAS_MANY, 'ShopProductImage', 'product_id', 'condition' => 'is_main=0'),
            'manufacturer' => array(self::BELONGS_TO, 'ShopManufacturer', 'manufacturer_id', 'scopes' => 'applyTranslateCriteria'),
            //'productsCount'   => array(self::STAT, 'ShopProduct', 'manufacturer_id', 'select'=>'count(t.id)'),
            'type' => array(self::BELONGS_TO, 'ShopProductType', 'type_id'),
            // Product variation
            'product' => array(self::BELONGS_TO, 'ShopProduct', 'product_id'),
            'order' => array(self::BELONGS_TO, 'Order', 'order_id'),
        );
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return array(
            array('product_id', 'required'),
            array('date_create', 'date', 'format' => 'yyyy-M-d H:m:s'),
            array('id, product_id, date_create', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        $this->_attrLabels = array(
            'id' => 'ID',
            'name' => Yii::t('CartModule.core', 'Название'),
            'sum' => Yii::t('CartModule.core', 'Скидка'),
        );
        return CMap::mergeArray($this->_attrLabels, parent::attributeLabels());
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return ActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search() {
        $criteria = new CDbCriteria;
        $criteria->with = array('product');
        $criteria->compare('t.id', $this->id);
        $criteria->compare('t.product_id', $this->product_id);
        $criteria->compare('t.order_id', $this->order_id);
        $criteria->compare('t.date_create', $this->date_create, true);

        return new ActiveDataProvider($this, array(
                    'criteria' => $criteria,
                ));
    }

}