<?php

namespace panix\cart\models;

use Yii;
use panix\engine\WebModel;

class ShopPaymentMethod extends WebModel {

    const MODULE_ID = 'cart';

    /**
     * @var string
     */
    public $translateModelName = 'ShopPaymentMethodTranslate';

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $description;

    public function getGridColumns() {
        return array(
            array(
                'name' => 'name',
                'type' => 'raw',
                'value' => 'Html::link(Html::encode($data->name), array("/shop/admin/paymentMethod/update", "id"=>$data->id))',
            ),
            array(
                'name' => 'switch',
                'filter' => array(1 => Yii::t('core', 'YES'), 0 => Yii::t('core', 'NO')),
                'value' => '$data->switch ? Yii::t("core", "YES") : Yii::t("core", "NO")'
            ),
            'DEFAULT_CONTROL' => array(
                'class' => 'ButtonColumn',
                'template' => '{update}{delete}',
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
                        'description' => array(
                            'type' => 'textarea',
                        ),
                        'currency_id' => array(
                            'type' => 'dropdownlist',
                            'items' => Html::listData(ShopCurrency::model()->findAll(), 'id', 'name'),
                        ),
                        'payment_system' => array(
                            'type' => 'dropdownlist',
                            'empty' => '---',
                            'items' => $this->getPaymentSystemsArray(),
                            'rel' => $this->id,
                        ),
                        '<div id="payment_configuration"></div>',
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



    public static function tableName() {
        return '{{%shop_payment_method}}';
    }
    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return array(
            array('name, currency_id', 'required'),
            array('switch, ordern', 'numerical', 'integerOnly' => true),
            array('name', 'length', 'max' => 255),
            array('description', 'safe'),
            array('payment_system', 'safe'),
            // Search
            array('id, name, description, switch', 'safe', 'on' => 'search'),
        );
    }

    public function relations() {
        return array(
            'pm_translate' => array(self::HAS_ONE, $this->translateModelName, 'object_id'),
        );
    }

    /**
     * @return array
     */
    public function behaviors() {
        return array(
            'TranslateBehavior' => array(
                'class' => 'app.behaviors.TranslateBehavior',
                'relationName' => 'pm_translate',
                'translateAttributes' => array(
                    'name',
                    'description',
                ),
            ),
        );
    }

    /**
     * @return array
     */
    public function scopes() {
        $alias = $this->getTableAlias();
        return array(
            'active' => array('order' => $alias . '.switch=1'),
            'orderByPosition' => array('order' => $alias . '.ordern ASC'),
            'orderByPositionDesc' => array('order' => $alias . '.ordern DESC'),
            'orderByName' => array('order' => $alias . '.name ASC'),
            'orderByNameDesc' => array('order' => $alias . '.name DESC'),
        );
    }

    /**
     * @return array of available payment systems. e.g array(id=>name)
     */
    public function getPaymentSystemsArray() {
        // Yii::import('application.modules.shop.components.payment.PaymentSystemManager');
        $result = array();

        $systems = new PaymentSystemManager;

        foreach ($systems->getSystems() as $system) {
            $result[(string) $system->id] = $system->name;
        }

        return $result;
    }

    /**
     * Renders form display on the order view page
     */
    public function renderPaymentForm(Order $order) {
        if ($this->payment_system) {
            $manager = new PaymentSystemManager;
            $system = $manager->getSystemClass($this->payment_system);
            return $system->renderPaymentForm($this, $order);
        }
    }

    /**
     * @return null|BasePaymentSystem
     */
    public function getPaymentSystemClass() {
        if ($this->payment_system) {
            $manager = new PaymentSystemManager;
            return $manager->getSystemClass($this->payment_system);
        }
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return ActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search() {
        $criteria = new CDbCriteria;

        $criteria->with = array('pm_translate');

        $criteria->compare('id', $this->id);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('description', $this->description, true);
        $criteria->compare('switch', $this->switch);

        $sort = new CSort;
        $sort->defaultOrder = $this->getTableAlias() . '.ordern ASC';

        return new ActiveDataProvider($this, array(
                    'criteria' => $criteria,
                    'sort' => $sort
                ));
    }

}