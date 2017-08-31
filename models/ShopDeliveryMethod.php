<?php

namespace panix\cart\models;

use Yii;
use panix\engine\WebModel;

class ShopDeliveryMethod extends WebModel {

    const MODULE_ID = 'cart';

    /**
     * @var array
     */
    public $_payment_methods;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $description;

    /**
     * @var string
     */
    public $translateModelName = 'ShopDeliveryMethodTranslate';

    public function init() {
        parent::init();
        $this->_attrLabels['payment_methods'] = $this->t('PAYMENT_METHODS');
    }

    public function getGridColumns() {
        return array(
            array(
                'name' => 'name',
                'type' => 'raw',
                'value' => 'Html::link(Html::encode($data->name), array("/shop/admin/delivery/update", "id"=>$data->id))',
            ),
            array(
                'name' => 'price',
                'value' => '$data->price'
            ),
            array(
                'name' => 'free_from',
                'value' => '$data->free_from'
            ),
            'DEFAULT_CONTROL' => array(
                'class' => 'ButtonColumn',
                'template' => '{switch}{update}{delete}',
            ),
        );
    }

    public function getForm() {
        return new CMSForm(array('id' => __CLASS__,
                    'attributes' => array(
                        'class' => 'form-horizontal'
                    ),
                    'elements' => array(
                        'name' => array(
                            'type' => 'text',
                        ),
                        'switch' => array(
                            'type' => 'dropdownlist',
                            'items' => array(
                                1 => Yii::t('app', 'YES'),
                                0 => Yii::t('app', 'NO')
                            ),
                        ),
                        'price' => array(
                            'type' => 'text',
                        ),
                        'free_from' => array(
                            'type' => 'text',
                        ),
                        'description' => array(
                            'type' => 'textarea',
                        ),
                        'payment_methods' => array(
                            'type' => 'checkboxlist',
                            'items' => Html::listData(ShopPaymentMethod::model()->findAll(), 'id', 'name'),
                        ),
                        '<div id="payment_configuration"></div>'
                    ),
                    'buttons' => array(
                        'submit' => array(
                            'type' => 'submit',
                            'class' => 'btn btn-success',
                            'label' => ($this->isNewRecord) ? Yii::t('app', 'CREATE', 0) : Yii::t('app', 'SAVE')
                        )
                    )
                        ), $this);
    }



    /**
     * @return string the associated database table name
     */

    public static function tableName() {
        return '{{%shop_delivery_method}}';
    }
    /**
     * @return array
     */
    public function scopes() {
        $alias = $this->getTableAlias();
        return array(
            'active' => array('condition' => $alias . '.switch=1'),
            'orderByPosition' => array('order' => $alias . '.ordern ASC'),
            'orderByPositionDesc' => array('order' => $alias . '.ordern DESC'),
            'orderByName' => array(
                'with' => 'dm_translate',
                'order' => 'dm_translate.name ASC'
            ),
            'orderByNameDesc' => array(
                'with' => 'dm_translate',
                'order' => 'dm_translate.name DESC'
            ),
        );
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return array(
            array('name', 'required'),
            array('ordern', 'numerical', 'integerOnly' => true),
            array('price, free_from', 'numerical'),
            array('switch', 'boolean'),
            array('payment_methods', 'validatePaymentMethods'),
            array('name', 'length', 'max' => 255),
            array('description', 'type', 'type' => 'string'),
            array('id, name, description, ordern', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array
     */
    public function relations() {
        return array(
            'categorization' => array(self::HAS_MANY, 'ShopDeliveryPayment', 'delivery_id'),
            'paymentMethods' => array(self::HAS_MANY, 'ShopPaymentMethod', array('payment_id' => 'id'), 'through' => 'categorization', 'order' => 'paymentMethods.ordern'),
            'dm_translate' => array(self::HAS_ONE, 'ShopDeliveryMethodTranslate', 'object_id'),
        );
    }

    /**
     * @return array
     */
    public function behaviors() {
        return array(
            'TranslateBehavior' => array(
                'class' => 'app.behaviors.TranslateBehavior',
                'relationName' => 'dm_translate',
                'translateAttributes' => array(
                    'name',
                    'description',
                ),
            ),
        );
    }

    /**
     * Validate payment method exists
     * @param $attr
     * @return mixed
     */
    public function validatePaymentMethods($attr) {
        if (!is_array($this->$attr))
            return;

        foreach ($this->$attr as $id) {
            if (ShopPaymentMethod::model()->countByAttributes(array('id' => $id)) == 0)
                $this->addError($attr, $this->t('ERROR_PAYMENT'));
        }
    }

    /**
     * After save event
     */
    public function afterSave() {
        // Clear payment relations
        ShopDeliveryPayment::model()->deleteAllByAttributes(array('delivery_id' => $this->id));

        foreach ($this->payment_methods as $pid) {
            $model = new ShopDeliveryPayment;
            $model->delivery_id = $this->id;
            $model->payment_id = $pid;
            $model->save(false);
        }

        return parent::afterSave();
    }

    /**
     * @param $data array ids of payment methods
     */
    public function setPayment_methods($data) {
        $this->_payment_methods = $data;
    }

    /**
     * @return array
     */
    public function getPayment_methods() {
        if ($this->_payment_methods)
            return $this->_payment_methods;

        $this->_payment_methods = array();
        foreach ($this->categorization as $row)
            $this->_payment_methods[] = $row->payment_id;
        return $this->_payment_methods;
    }

    /**
     * @return string order used delivery method
     */
    public function countOrders() {
        Yii::import('shop.models.orders.Order');
        return Order::model()->countByAttributes(array('delivery_id' => $this->id));
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search() {
        $criteria = new CDbCriteria;

        $criteria->with = array('dm_translate');

        $criteria->compare('t.id', $this->id);
        $criteria->compare('dm_translate.name', $this->name, true);
        $criteria->compare('dm_translate.description', $this->description, true);
        $criteria->compare('t.ordern', $this->ordern);
        $criteria->compare('t.switch', $this->switch);

        $sort = new CSort;
        $sort->defaultOrder = $this->getTableAlias() . '.ordern ASC';

        return new ActiveDataProvider($this, array(
                    'criteria' => $criteria,
                    'sort' => $sort,
                ));
    }

}