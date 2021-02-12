<?php

namespace panix\mod\cart\widgets\delivery\pickup;

use panix\mod\cart\models\forms\OrderCreateForm;
use Yii;
use panix\engine\CMS;
use panix\mod\cart\models\Delivery;
use panix\mod\cart\models\Order;
use panix\mod\cart\components\delivery\BaseDeliverySystem;
use yii\helpers\Html;

/**
 * Pickup delivery system
 */
class PickupDeliverySystem extends BaseDeliverySystem
{

    public $json = [];

    /**
     * This method will be triggered after redirection from payment system site.
     * If payment accepted method must return Order model to make redirection to order view.
     * @param Delivery $method
     * @return boolean|Order
     */

    public function processRequest(Delivery $method)
    {
        $settings = $this->getSettings($method->id);
        $result = [];
            $form = new OrderCreateForm;
            $post = Yii::$app->request->post();
        $form->load($post);
        //return $post;
        $result['field'] = [];


        $result['field']['delivery_city_ref']['type'] = 'dropdownlist';
        $result['field']['delivery_city_ref']['items'] = array_merge([NULL=>html_entity_decode('&mdash; Выберите город &mdash;')],Cities::getList());
        if($form->delivery_city_ref){
            $result['field']['delivery_city_ref']['value'] = $form->delivery_city_ref;
        }

        $result['field']['delivery_city_ref']['id'] = Html::getInputId($form, 'delivery_city_ref');
        $result['field']['delivery_city_ref']['error'] = 'Необходимо выбрать город';
        $result['field']['delivery_city_ref']['name'] = Html::getInputName($form, 'delivery_city_ref');
        $result['field']['delivery_city_ref']['jsOptions'] = [
            'liveSearch' => true,
            'liveSearchPlaceholder'=>'Найти город',
            //'dropupAuto'=>false,
            'dropdownAlignRight'=>'auto',
            'size'=>'300px',
            'width' => '100%',
            //'container'=>'#'.Html::getInputId($form, 'delivery_city_ref')
        ];
        if($form->delivery_city_ref){
        $result['field']['delivery_type']['type'] = 'dropdownlist';
        //$result['field']['delivery_type']['error'] = 'Необходимо выбрать город';
        $result['field']['delivery_type']['id'] = Html::getInputId($form, 'delivery_type');
        $result['field']['delivery_type']['items'] = ['warehouse' => 'Доставка на отделение', 'address' => 'Доставка на адрес'];
        $result['field']['delivery_type']['value'] = $form->delivery_type;
        $result['field']['delivery_type']['name'] = Html::getInputName($form, 'delivery_type');
        $result['field']['delivery_type']['jsOptions'] = [];
        }
        if($form->delivery_city_ref && ($form->delivery_type == 'warehouse')) {



            $result['field']['delivery_warehouse_ref']['type'] = 'dropdownlist';
            $result['field']['delivery_warehouse_ref']['items'] = array_merge([NULL=>html_entity_decode('&mdash; Выберите отделение &mdash;')],Warehouses::getList($form->delivery_city_ref));
            if($form->delivery_warehouse){
                $result['field']['delivery_warehouse_ref']['value'] = $form->delivery_warehouse;
            }
            $result['field']['delivery_warehouse_ref']['error'] = 'Необходимо выбрать отделение';
            $result['field']['delivery_warehouse_ref']['id'] = Html::getInputId($form, 'delivery_warehouse');
            $result['field']['delivery_warehouse_ref']['name'] = Html::getInputName($form, 'delivery_warehouse');
            $result['field']['delivery_warehouse_ref']['jsOptions'] = [
                'liveSearch' => true,
                'liveSearchPlaceholder'=>'Найти отделение',
                //'dropupAuto'=>false,
                'dropdownAlignRight'=>'auto',
                'size'=>'300px',
                'width' => '100%'
            ];
        }


        $result['attributes'] = $form->attributes;
        $result['show_address'] = false;
        if ($form->delivery_type == 'address') {
            $result['show_address'] = true;
        }

        return $result;

    }


    public function renderDeliveryFormHtml($model)
    {
        return Yii::$app->view->renderAjax("@cart/widgets/delivery/{$model->deliveryMethod->system}/_view", [
            'model' => $model
        ]);
    }



    public function getSettingsKey2($paymentMethodId)
    {
        return $paymentMethodId . '_PickupDeliverySystem';
    }

    public function getModel()
    {
        return new PickupConfigurationModel();
    }
}
