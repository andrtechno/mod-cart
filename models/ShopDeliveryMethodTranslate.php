<?php

/**
 * Class to access delivery methods translations
 *
 * @property int $id
 * @property int $object_id
 * @property int $language_id
 */
class ShopDeliveryMethodTranslate extends CActiveRecord {

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{shop_delivery_method_translate}}';
    }



}