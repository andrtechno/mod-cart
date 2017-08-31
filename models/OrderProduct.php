<?php

/**
 * This is the model class for table "OrderProduct".
 *
 * The followings are the available columns in table 'OrderProduct':
 * @property integer $id
 * @property integer $order_id
 * @property integer $product_id
 * @property integer $configurable_id
 * @property string $name
 * @property string $configurable_name Name of configurable product
 * @property string $configurable_data same as $variants but shop attr=>value for configurable product
 * @property string $variants Key/value array of selected variants. E.g: Color/Green, Size/Large
 * @property integer $quantity
 * @property string $sku
 * @property float $price
 */
class OrderProduct extends ActiveRecord {

    const MODULE_ID = 'cart';

    public function getGridColumns() {
        return array(
            array(
                'class' => 'SGridIdColumn',
                'name' => 'image',
                'type' => 'html',
                'htmlOptions' => array('class' => 'image'),
                'value' => '(!empty($data->prd->mainImage))?Html::link(Html::image($data->prd->mainImage->getUrl("50x50"),""),$data->prd->mainImage->getUrl("500x500")):"no image"'
            ),
            array(
                'name' => 'renderFullName',
                'type' => 'raw',
                'htmlOptions' => array(),
                'header' => Yii::t('CartModule.OrderProduct', 'NAME'),
                'value' => 'Html::link($data->renderFullName,array("/admin/shop/products/update", "id"=>$data->product_id),array("target"=>"_blank"))'
            ),
            array(
// 'class' => 'OrderQuantityColumn',
                'name' => 'quantity',
                'htmlOptions' => array('class' => 'quantity'),
                'header' => Yii::t('CartModule.admin', 'WHOLESALE_LITE',Yii::app()->settings->get('shop', 'wholesale'))
            ),
            array(
                'name' => 'price',
                'value' => 'ShopProduct::formatPrice($data->price)'
            ),
            array(
                'header' => 'Пар в ящ.',
                'value' => '$data->prd->pcs',
                'visible' => (Yii::app()->settings->get('shop', 'wholesale')) ? true : false
            ),
            'DEFAULT_CONTROL' => array(
                'type' => 'raw',
                'htmlOptions' => array('class' => 'text-center'),
                'value' => 'Html::link("<i class=\"flaticon-delete\"></i>", "#", array("class"=>"btn btn-danger btn-sm", "onclick"=>"deleteOrderedProduct($data->id, $data->order_id, \"' . Yii::app()->request->csrfToken . '\")"))',
            ),
                // 'DEFAULT_COLUMNS' => array(
                //     array('class' => 'CheckBoxColumn')
                // ),
        );
    }

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return OrderProduct the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return '{{order_product}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return array(
            //array('supplier_id', 'numerical'),
            //array('name', 'type', 'type' => 'string'),
            array('id, order_id, product_id, currency_id, supplier_id, configurable_id, name, quantity, sku, price, date_create', 'safe', 'on' => 'search'),
            array('id, order_id, product_id, currency_id, supplier_id, configurable_id, name, quantity, sku, price, date_create', 'safe', 'on' => 'search_pdf'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        return array(
            'categorization' => array(self::HAS_MANY, 'ShopProductCategoryRef', 'product'),
            'categories' => array(self::HAS_MANY, 'ShopCategory', array('category' => 'id'), 'through' => 'categorization'),
            'mainCategory' => array(self::HAS_ONE, 'ShopCategory', array('category' => 'id'), 'through' => 'categorization', 'condition' => 'categorization.is_main = 1', 'scopes' => 'applyTranslateCriteria'),
            'order' => array(self::BELONGS_TO, 'Order', 'order_id'),
            //'order2' => array(self::BELONGS_TO, 'Order', 'product_id'),
            'supplier' => array(self::BELONGS_TO, 'ShopSuppliers', 'supplier_id'),
            'mainImage' => array(self::BELONGS_TO, 'ShopProductImage', 'product_id', 'condition' => 'is_main=1'), //for pdf
            'prd' => array(self::BELONGS_TO, 'ShopProduct', 'product_id'),
        );
    }

    /**
     * @return boolean
     */
    public function afterSave() {
        $this->order->updateTotalPrice();
        $this->order->updateDeliveryPrice();

        if ($this->isNewRecord) {
            $product = ShopProduct::model()->findByPk($this->product_id);
            $product->decreaseQuantity();
        }

        return parent::afterSave();
    }

    public function afterDelete() {
        if ($this->order) {
            $this->order->updateTotalPrice();
            $this->order->updateDeliveryPrice();
        }

        return parent::afterDelete();
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return ActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search($params = array()) {
        $criteria = new CDbCriteria;
        /**
         * Выводи записи с даты "Y-m-d" по дату "Y-m-d"
         */
        $get = $_GET['OrderProductHistoryForm'];
        if (isset($get['from_date']) && isset($get['to_date'])) {
            $criteria->addBetweenCondition('t.date_create', date('Y-m-d H:i:s', strtotime($get['from_date'])), date('Y-m-d H:i:s', strtotime($get['to_date']) + 86400));
        }

        $criteria->compare('id', $this->id);
        $criteria->compare('order_id', $this->order_id);
        $criteria->compare('product_id', $this->product_id);
        $criteria->compare('supplier_id', $this->supplier_id);
        $criteria->compare('configurable_id', $this->configurable_id);
        $criteria->compare('currency_id', $this->currency_id);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('quantity', $this->quantity);
        $criteria->compare('sku', $this->sku, true);
        $criteria->compare('price', $this->price);

        /**
         * Для истории заказов.
         */
        if (isset($params['history'])) {
            $criteria->with = array('order');
            $criteria->addInCondition('`order`.`status_id`', array(3, 4));
        }

        return new ActiveDataProvider($this, array(
                    'criteria' => $criteria,
                    'pagination' => null
                ));
    }

    public function search_pdf($params = array()) {
        $params['history'] = true;
        $criteria = new CDbCriteria;
        $get = $_GET['OrderProductHistoryForm'];
        if (isset($get['from_date']) && isset($get['to_date'])) {
            $criteria->addBetweenCondition('t.date_create', date('Y-m-d H:i:s', strtotime($get['from_date'])), date('Y-m-d H:i:s', strtotime($get['to_date']) + 86400));
        }


        $criteria->compare('id', $this->id);
        $criteria->compare('order_id', $this->order_id);
        $criteria->compare('product_id', $this->product_id);
        $criteria->compare('supplier_id', $this->supplier_id);
        $criteria->compare('configurable_id', $this->configurable_id);
        $criteria->compare('currency_id', $this->currency_id);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('quantity', $this->quantity);
        $criteria->compare('sku', $this->sku, true);
        $criteria->compare('price', $this->price);

        if (isset($params['history'])) {
            $criteria->with = array('order');
            $criteria->addInCondition('`order`.`status_id`', array(3, 4));
        }
        if (isset($_GET['orderid'])) {
            $criteria->condition = '`order`.`id`=' . $_GET['orderid'];
        }
        return new ActiveDataProvider($this, array('criteria' => $criteria, 'pagination' => null));
    }

    public function scopes() {
        $alias = $this->getTableAlias(true);
        return array(
            'ordersMade1' => array(
                //  'condition' => '`order`.`status_id`="3"',
                'with' => array('order')
            ),
            'ordersMade2' => array(
                'condition' => '`order`.`status_id`="4"',
                'with' => array('order')
            ),
        );
    }

    /**
     * Render full name to present product on order view
     *
     * @param bool $appendConfigurableName
     * @return string
     */
    public function getRenderFullName($appendConfigurableName = true) {
        $result = Html::link($this->name,$this->prd->absoluteUrl,array('target'=>'_blank'));

        if (!empty($this->configurable_name) && $appendConfigurableName)
            $result .= '<br/>' . $this->configurable_name;

        $variants = unserialize($this->variants);

        if ($this->configurable_data !== '' && is_string($this->configurable_data))
            $this->configurable_data = unserialize($this->configurable_data);

        if (!is_array($variants))
            $variants = array();

        if (!is_array($this->configurable_data))
            $this->configurable_data = array();

        $variants = array_merge($variants, $this->configurable_data);

        if (!empty($variants)) {
            foreach ($variants as $key => $value)
                $result .= "<br/> - {$key}: {$value}";
        }

        return $result;
    }

    public function getCategories() {
        $content = array();
        foreach ($this->prd->categories as $c) {
            $content[] = $c->name;
        }
        return implode(', ', $content);
    }

}