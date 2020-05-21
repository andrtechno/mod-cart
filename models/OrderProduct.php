<?php

namespace panix\mod\cart\models;

use panix\mod\shop\models\Product;
use panix\engine\db\ActiveRecord;

/**
 * Class OrderProduct
 *
 * @property integer $order_id
 * @property integer $product_id
 * @property integer $configurable_id
 * @property integer $currency_id
 * @property integer $supplier_id
 * @property string $name
 * @property string $configurable_name
 * @property integer $quantity Quantity products
 * @property float $price Products price
 * @property float $price_purchase
 * @property string $configurable_data
 * @property string $sku Article product
 * @property string $variants
 * @property Product $originalProduct
 * @property Order $order
 * @property string $attributes_data
 *
 * @package panix\mod\cart\models
 */
class OrderProduct extends ActiveRecord
{

    const MODULE_ID = 'cart';

    /**
     * @return string the associated database table name
     */
    public static function tableName()
    {
        return '{{%order__product}}';
    }

    public static function find()
    {
        return new query\OrderProductQuery(get_called_class());
    }

    public function getOrder()
    {
        return $this->hasOne(Order::class, ['id' => 'order_id']);
    }

    public function getOriginalProduct()
    {
        return $this->hasOne(Product::class, ['id' => 'product_id']);
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        $this->order->updateTotalPrice();
        $this->order->updateDeliveryPrice();

        if ($this->isNewRecord) {
            $product = Product::findOne($this->product_id);

            if ($product->added_to_cart_count == Yii::$app->settings->get('shop', 'added_to_cart_count')) {
                $product->added_to_cart_date = time();
                $product->save(false);
            }

            $product->decreaseQuantity();

        }

        return parent::afterSave($insert, $changedAttributes);
    }


    public function afterDelete()
    {
        if ($this->order) {
            $this->order->updateTotalPrice();
            $this->order->updateDeliveryPrice();
        }

        return parent::afterDelete();
    }

    /**
     * Render full name to present product on order view
     *
     * @param bool $appendConfigurableName
     * @return string
     */
    public function getRenderFullName($appendConfigurableName = true)
    {

        if ($this->originalProduct) {
            $result = \yii\helpers\Html::a($this->name, $this->originalProduct->getUrl(), ['target' => '_blank']);
        } else {
            $result = $this->name;
        }


        if (!empty($this->configurable_name) && $appendConfigurableName)
            $result .= '<br/>' . $this->configurable_name;

        $variants = unserialize($this->variants);

        if ($this->configurable_data !== '' && is_string($this->configurable_data))
            $this->configurable_data = unserialize($this->configurable_data);

        if (!is_array($variants))
            $variants = [];

        if (!is_array($this->configurable_data))
            $this->configurable_data = [];

        $variants = array_merge($variants, $this->configurable_data);

        if (!empty($variants)) {
            foreach ($variants as $key => $value)
                $result .= "<br/> - {$key}: {$value}";
        }

        return $result;
    }

    public function getCategories()
    {
        $content = array();
        foreach ($this->originalProduct->categories as $c) {
            $content[] = $c->name;
        }
        return implode(', ', $content);
    }


    public function getProductAttributes()
    {
        return json_decode($this->attributes_data);
    }

}
