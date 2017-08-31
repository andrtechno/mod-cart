<?php

namespace panix\mod\cart\models;
use panix\mod\cart\models\Order;

class OrderProduct extends \panix\engine\WebModel {

    const MODULE_ID = 'cart';

    /**
     * @return string the associated database table name
     */
    public static function tableName() {
        return '{{%order_product}}';
    }

    public function rules() {
        return [
        ];
    }
    public function getOrder() {
        return $this->hasOne(Order::className(), ['id' => 'order_id']);
    }
    /**
     * @return boolean
     */
    public function afterSave($insert, $changedAttributes) {
        $this->order->updateTotalPrice();
        $this->order->updateDeliveryPrice();

        if ($this->isNewRecord) {
            $product = ShopProduct::model()->findByPk($this->product_id);
            $product->decreaseQuantity();
        }

        return parent::afterSave($insert, $changedAttributes);
    }

    public function afterDelete() {
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
    public function getRenderFullName($appendConfigurableName = true) {
        $result = Html::link($this->name, $this->prd->absoluteUrl, array('target' => '_blank'));

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
