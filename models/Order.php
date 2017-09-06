<?php

namespace panix\mod\cart\models;

use Yii;
use panix\mod\cart\models\OrderStatus;
use panix\mod\cart\models\OrderProduct;

class Order extends \panix\engine\WebModel {

    const MODULE_ID = 'cart';

    /**
     * @return string the associated database table name
     */
    public static function tableName() {
        return '{{%order}}';
    }
    public static function find() {
        return new query\OrderQuery(get_called_class());
    }
    public function getDeliveryMethod() {
        return $this->hasOne(DeliveryMethod::className(), ['id' => 'delivery_id']);
    }

    public function getPaymentMethod() {
        return $this->hasOne(PaymentMethod::className(), ['id' => 'payment_id']);
    }

    public function getStatus() {
        return $this->hasOne(OrderStatus::className(), ['id' => 'status_id']);
    }
    
    public function getProducts() {
        return $this->hasMany(OrderProduct::className(), ['order_id' => 'id']);
    }

    public function getUrl() {
        return ['/cart/default/view', 'secret_key' => $this->secret_key];
    }

    public function rules() {
        return [
            [['user_name', 'user_email'], 'required'],
            //[['delivery_id','payment_id'], 'required'],
            ['user_email', 'email'],
            [['user_comment', 'admin_comment'], 'string', 'max' => 500],
            [['user_address'], 'string', 'max' => 255],
            [['user_phone'], 'string', 'max' => 30],
            [['user_name', 'user_email', 'discount'], 'string', 'max' => 100],
            ['paid', 'boolean'],
            //   ['delivery_id', 'validateDelivery'],
            //    ['payment_id', 'validatePayment'],
            ['status_id', 'validateStatus'],
        ];
    }

    /**
     * Check if delivery method exists
     */
    public function validateDelivery() {
        if (DeliveryMethod::model()->where(['id' => $this->delivery_id])->count() == 0)
            $this->addError('delivery_id', Yii::t('cart/admin', 'Необходимо выбрать способ доставки.'));
    }

    public function validatePayment() {
        if (PaymentMethod::model()->where(['id' => $this->payment_id])->count() == 0)
            $this->addError('payment_id', Yii::t('cart/admin', 'Необходимо выбрать способ оплаты.'));
    }

    /**
     * Check if status exists
     */
    public function validateStatus() {
        if ($this->status_id && OrderStatus::find()->where(['id' => $this->status_id])->count() == 0)
            $this->addError('status_id', Yii::t('cart/admin', 'Ошибка проверки статуса.'));
    }

    /**
     * @return bool
     */
    public function beforeSave($insert) {


        if ($this->isNewRecord) {
            $this->secret_key = $this->createSecretKey();
            $this->ip_create = Yii::$app->request->getUserIP();


            if (!Yii::$app->user->isGuest)
                $this->user_id = Yii::$app->user->id;
        }


        // Set `New` status
        if (!$this->status_id)
            $this->status_id = 1;

        return parent::beforeSave($insert);
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

        if (Order::find()->where(['secret_key' => $result])->count() > 0)
            $this->createSecretKey($size);

        return $result;
    }

    /**
     * Update total
     */
    public function updateTotalPrice() {

        $this->total_price = 0;
        $products = OrderProduct::find()->where(['order_id' => $this->id])->all();

        foreach ($products as $p) {
            //if($p->currency_id){
            // $currency = ShopCurrency::model()->findByPk($p->currency_id);
            // $this->total_price += $p->price * $currency->rate * $p->quantity;
            // }else{
            $curr_rate = Yii::$app->currency->active->rate;

            $this->total_price += (Yii::$app->settings->get('shop', 'wholesale')) ? $p->price * $p->prd->pcs * $curr_rate * $p->quantity : $p->price * $curr_rate * $p->quantity;


            //  }
        }


        $this->save(false);
    }

    /**
     * @return int
     */
    public function updateDeliveryPrice() {
        if ($this->delivery_id) {
            $result = 0;
            $deliveryMethod = DeliveryMethod::findOne($this->delivery_id);

            if ($deliveryMethod) {
                if ($deliveryMethod->price > 0) {
                    if ($deliveryMethod->free_from > 0 && $this->total_price > $deliveryMethod->free_from)
                        $result = 0;
                    else
                        $result = $deliveryMethod->price;
                }
            }

            $this->delivery_price = $result;
            $this->save(false);
        }
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
        $model = DeliveryMethod::findOne($this->delivery_id);
        if ($model)
            return $model->name;
    }

    public function getPayment_name() {
        $model = PaymentMethod::findOne($this->payment_id);
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
            /*$event = new CModelEvent($this, array(
                'product_model' => $product,
                'ordered_product' => $ordered_product,
                'quantity' => $quantity
            ));
            $this->onProductAdded($event);*/
        }
    }

    /**
     * Delete ordered product from order
     *
     * @param $id
     */
    public function deleteProduct($id) {

        $model = OrderProduct::findOne($id);

        if ($model) {
            $model->delete();

           // $event = new CModelEvent($this, array(
           //     'ordered_product' => $model
           // ));
            //$this->onProductDeleted($event);
        }
    }

    /**
     * @return ActiveDataProvider
     */
    public function getOrderedProducts() {
        $products = new search\OrderProductSearch();
        return $products->search([$products->formName()=>['order_id'=>$this->id]]);
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

    public function getRelativeUrl() {
        return Yii::$app->createUrl('/cart/default/view', array('secret_key' => $this->secret_key));
    }

    public function getAbsoluteUrl() {
        return Yii::$app->createAbsoluteUrl('/cart/default/view', array('secret_key' => $this->secret_key));
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
