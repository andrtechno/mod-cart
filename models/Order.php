<?php

/**
 * This is the model class for table "Order".
 *
 * The followings are the available columns in table 'Order':
 * @property integer $id
 * @property integer $user_id
 * @property string $secret_key
 * @property integer $delivery_id
 * @property integer $payment_id
 * @property float $delivery_price
 * @property float $total_price Sum of ordered products
 * @property float $full_price Total price + delivery price
 * @property integer $status_id
 * @property integer $paid
 * @property string $user_name
 * @property string $user_email
 * @property string $user_address
 * @property string $user_phone
 * @property string $user_comment
 * @property string $admin_comment
 * @property string $ip_address
 * @property string $date_create
 * @property string $date_update
 * @property string $discount
 */
Yii::import('mod.shop.ShopModule');

class Order extends ActiveRecord {

    const MODULE_ID = 'cart';

    public function getStatusByCssClass() {
        if ($this->status == 1) {
            return 'success';
        } elseif ($this->status == 2) {
            return 'default';
        } else {
            return 'danger';
        }
    }

    public function getStatusByHtml() {
        $css = (!empty($this->status->color)) ? 'color:#777777;background:#' . $this->status->color . ';' : '';
        return Html::tag('span', array('class' => 'label label-lg label-default', 'style' => $css), $this->status->name, true);
    }

    public function getGridColumns() {
        Yii::import('mod.shop.components.SProductsPreviewColumn');
        return array(
            array(
                'name' => 'status',
                'type' => 'raw',
                'value' => '$data->getStatusByHtml()',
                'htmlOptions' => array('style' => 'width:30px', 'class' => 'text-center')
            ),
            array(
                'name' => 'user_name',
                'type' => 'raw',
                'value' => 'Html::link(Html::encode($data->user_name), array("/admin/cart/default/update", "id"=>$data->id))',
            ),
            array(
                'name' => 'user_phone',
                'value' => '$data->user_phone',
            ),
            array(
                'name' => 'admin_comment',
                'value' => '$data->admin_comment',
            ),
            array(
                'name' => 'delivery_id',
                'filter' => Html::listData(ShopDeliveryMethod::model()->orderByPosition()->findAll(), 'id', 'name'),
                'value' => '$data->delivery_name'
            ),
            array(
                'name' => 'payment_id',
                'filter' => Html::listData(ShopPaymentMethod::model()->findAll(), 'id', 'name'),
                'value' => '$data->payment_name'
            ),
            array(
                'class' => 'ProductsPreviewColumn'
            ),
            array(
                'name' => 'full_price',
                'header' => Yii::t('CartModule.Order', 'FULL_PRICE'),
                'value' => 'ShopProduct::formatPrice($data->full_price)',
            ),
            array(
                'name' => 'date_create',
                // 'filter' => Html::listData(ShopDeliveryMethod::model()->orderByPosition()->findAll(), 'id', 'name'),
                'value' => 'CMS::date($data->date_create)'
            ),
            'DEFAULT_CONTROL' => array(
                'class' => 'ButtonColumn',
                'template' => '{update}{delete}',
            ),
            'DEFAULT_COLUMNS' => array(
                array('class' => 'CCheckBoxColumn')
            ),
        );
    }

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Order the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return '{{order}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return array(
            array('user_name, user_email, delivery_id, payment_id', 'required'), //, payment_id
            array('user_name, user_email, discount', 'length', 'max' => 100),
            array('user_phone', 'length', 'max' => 30),
            array('user_email', 'email'),
            array('user_comment, admin_comment', 'length', 'max' => 500),
            array('user_address', 'length', 'max' => 255),
            array('delivery_id', 'validateDelivery'),
            array('payment_id', 'validatePayment'),
            array('status_id', 'validateStatus'),
            array('paid', 'boolean'),
            // Search
            array('id, user_id, delivery_id, payment_id, delivery_price, total_price, status_id, paid, user_name, user_email, user_address, user_phone, user_comment, ip_address, date_create, date_update', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array
     */
    public function relations() {
        return array(
            'products' => array(self::HAS_MANY, 'OrderProduct', 'order_id'),
            'status' => array(self::BELONGS_TO, 'OrderStatus', 'status_id'),
            'product' => array(self::BELONGS_TO, 'OrderProduct', 'id'),
            'deliveryMethod' => array(self::BELONGS_TO, 'ShopDeliveryMethod', 'delivery_id'),
            'paymentMethod' => array(self::BELONGS_TO, 'ShopPaymentMethod', 'payment_id'),
        );
    }

    /**
     * @return array
     */
    public function scopes() {
        $alias = $this->getTableAlias(true);
        return array(
            'new' => array('condition' => $alias . '.status_id=1'),
        );
    }

    /**
     * @return array
     */
    public function behaviors() {
        return array(
            'historical' => array(
                'class' => 'mod.cart.behaviors.HistoricalBehavior',
            )
        );
    }

    /**
     * Check if delivery method exists
     */
    public function validateDelivery() {
        if (ShopDeliveryMethod::model()->countByAttributes(array('id' => $this->delivery_id)) == 0)
            $this->addError('delivery_id', Yii::t('CartModule.core', 'Необходимо выбрать способ доставки.'));
    }

    public function validatePayment() {
        if (ShopPaymentMethod::model()->countByAttributes(array('id' => $this->payment_id)) == 0)
            $this->addError('payment_id', Yii::t('CartModule.core', 'Необходимо выбрать способ оплаты.'));
    }

    /**
     * Check if status exists
     */
    public function validateStatus() {
        if ($this->status_id && OrderStatus::model()->countByAttributes(array('id' => $this->status_id)) == 0)
            $this->addError('status_id', Yii::t('CartModule.core', 'Ошибка проверки статуса.'));
    }

    /**
     * @return bool
     */
    public function beforeSave() {
        if ($this->isNewRecord) {
            $this->secret_key = $this->createSecretKey();
            $this->ip_address = Yii::app()->request->userHostAddress;


            if (!Yii::app()->user->isGuest)
                $this->user_id = Yii::app()->user->id;
        }


        // Set `New` status
        if (!$this->status_id)
            $this->status_id = 1;

        return parent::beforeSave();
    }

    /**
     * @return bool
     */
    public function afterDelete() {
        foreach ($this->products as $ordered_product)
            $ordered_product->delete();

        return parent::afterDelete();
    }

    /**
     * Create unique key to view orders
     * @param int $size
     * @return string
     */
    public function createSecretKey($size = 10) {

        $result = '';
        $chars = '1234567890qweasdzxcrtyfghvbnuioplkjnm';
        while (mb_strlen($result, 'utf8') < $size) {
            $result .= mb_substr($chars, rand(0, mb_strlen($chars, 'utf8')), 1);
        }

        if (Order::model()->countByAttributes(array('secret_key' => $result)) > 0)
            $this->createSecretKey($size);

        return $result;
    }

    /**
     * Update total
     */
    public function updateTotalPrice() {

        $this->total_price = 0;
        $products = OrderProduct::model()->findAllByAttributes(array('order_id' => $this->id));

        foreach ($products as $p) {
            //if($p->currency_id){
            // $currency = ShopCurrency::model()->findByPk($p->currency_id);
            // $this->total_price += $p->price * $currency->rate * $p->quantity;
            // }else{
            $curr_rate = Yii::app()->currency->active->rate;

            $this->total_price += (Yii::app()->settings->get('shop', 'wholesale')) ? $p->price * $p->prd->pcs * $curr_rate * $p->quantity : $p->price * $curr_rate * $p->quantity;


            //  }
        }


        $this->save(false, false, false);
    }

    /**
     * @return int
     */
    public function updateDeliveryPrice() {
        $result = 0;
        $deliveryMethod = ShopDeliveryMethod::model()->findByPk($this->delivery_id);

        if ($deliveryMethod) {
            if ($deliveryMethod->price > 0) {
                if ($deliveryMethod->free_from > 0 && $this->total_price > $deliveryMethod->free_from)
                    $result = 0;
                else
                    $result = $deliveryMethod->price;
            }
        }

        $this->delivery_price = $result;
        $this->save(false, false, false);
    }

    /**
     * @return mixed
     */
    public function getStatus_name() {
        if ($this->status)
            return $this->status->name;
    }

    public function getStatus_color() {
        if ($this->status)
            return $this->status->color;
    }

    /**
     * @return mixed
     */
    public function getDelivery_name() {
        $model = ShopDeliveryMethod::model()->findByPk($this->delivery_id);
        if ($model)
            return $model->name;
    }

    public function getPayment_name() {
        $model = ShopPaymentMethod::model()->findByPk($this->payment_id);
        if ($model)
            return $model->name;
    }

    /**
     * @return mixed
     */
    public function getFull_price() {
        if (!$this->isNewRecord) {
            $result = $this->total_price + $this->delivery_price;
            if ($this->discount) {
                $sum = $this->discount;
                if ('%' === substr($this->discount, -1, 1))
                    $sum = $result * (int) $this->discount / 100;
                $result -= $sum;
            }
            return $result;
        }
    }

    /**
     * Add product to existing order
     *
     * @param ShopProduct $product
     * @param integer $quantity
     * @param float $price
     */
    public function addProduct($product, $quantity, $price) {

        if (!$this->isNewRecord) {
            $ordered_product = new OrderProduct;
            $ordered_product->order_id = $this->id;
            $ordered_product->product_id = $product->id;
            $ordered_product->currency_id = $product->currency_id;
            $ordered_product->name = $product->name;
            $ordered_product->quantity = $quantity;
            $ordered_product->sku = $product->sku;
            $ordered_product->price = $price;
            $ordered_product->save();

            // Raise event
            $event = new CModelEvent($this, array(
                        'product_model' => $product,
                        'ordered_product' => $ordered_product,
                        'quantity' => $quantity
                    ));
            $this->onProductAdded($event);
        }
    }

    /**
     * Delete ordered product from order
     *
     * @param $id
     */
    public function deleteProduct($id) {

        $model = OrderProduct::model()->findByPk($id);

        if ($model) {
            $model->delete();

            $event = new CModelEvent($this, array(
                        'ordered_product' => $model
                    ));
            $this->onProductDeleted($event);
        }
    }

    /**
     * @param $event
     */
    public function onProductAdded($event) {
        $this->raiseEvent('onProductAdded', $event);
    }

    /**
     * @param $event
     */
    public function onProductDeleted($event) {
        $this->raiseEvent('onProductDeleted', $event);
    }

    /**
     * @param $event
     */
    public function onProductQuantityChanged($event) {
        $this->raiseEvent('onProductQuantityChanged', $event);
    }

    /**
     * @return ActiveDataProvider
     */
    public function getOrderedProducts() {

        $products = new OrderProduct;
        $products->order_id = $this->id;

        return $products->search();
    }

    /**
     * @param array $data
     */
    public function setProductQuantities(array $data) {
        foreach ($this->products as $product) {
            if (isset($data[$product->id])) {
                if ((int) $product->quantity !== (int) $data[$product->id]) {
                    $event = new CModelEvent($this, array(
                                'ordered_product' => $product,
                                'new_quantity' => (int) $data[$product->id]
                            ));
                    $this->onProductQuantityChanged($event);
                }

                $product->quantity = (int) $data[$product->id];
                $product->save(false, false);
            }
        }
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return ActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search() {
        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('user_id', $this->user_id);
        $criteria->compare('delivery_id', $this->delivery_id);
        $criteria->compare('payment_id', $this->payment_id);
        $criteria->compare('delivery_price', $this->delivery_price);
        $criteria->compare('total_price', $this->total_price);
        $criteria->compare('status_id', $this->status_id);
        $criteria->compare('paid', $this->paid);
        $criteria->compare('user_name', $this->user_name, true);
        $criteria->compare('user_email', $this->user_email, true);
        $criteria->compare('user_address', $this->user_address, true);
        $criteria->compare('user_phone', $this->user_phone, true);
        $criteria->compare('user_comment', $this->user_comment, true);
        $criteria->compare('ip_address', $this->ip_address, true);
        $criteria->compare('date_create', $this->date_create, true);
        $criteria->compare('date_update', $this->date_update, true);

        $sort = new CSort;
        $sort->defaultOrder = $this->getTableAlias() . '.date_create DESC';
        return new ActiveDataProvider($this, array(
                    'criteria' => $criteria,
                    'sort' => $sort
                ));
    }

    public function getRelativeUrl() {
        return Yii::app()->createUrl('/cart/default/view', array('secret_key' => $this->secret_key));
    }

    public function getAbsoluteUrl() {
        return Yii::app()->createAbsoluteUrl('/cart/default/view', array('secret_key' => $this->secret_key));
    }

    /**
     * Load history
     *
     * @return array
     */
    public function getHistory() {
        $cr = new CDbCriteria;
        $cr->order = 'date_create ASC';

        return OrderHistory::model()->findAllByAttributes(array('order_id' => $this->id), $cr);
    }

}