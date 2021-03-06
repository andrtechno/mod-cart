<?php

namespace panix\mod\cart\widgets\delivery\novaposhta;

use panix\mod\cart\models\forms\OrderCreateForm;
use panix\mod\cart\widgets\delivery\novaposhta\api\NovaPoshtaApi;
use panix\mod\novaposhta\models\Cities;
use panix\mod\novaposhta\models\Warehouses;
use Yii;
use panix\engine\CMS;
use panix\mod\cart\models\Delivery;
use panix\mod\cart\models\Order;
use panix\mod\cart\components\delivery\BaseDeliverySystem;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\httpclient\Client;
use yii\web\Response;

/**
 * NovaPoshta delivery system
 */
class NovaPoshtaDeliverySystem extends BaseDeliverySystem
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
        $result['field']['delivery_city_ref']['items'] = array_merge([NULL => html_entity_decode('&mdash; Выберите город &mdash;')], Cities::getList());
        if ($form->delivery_city_ref) {
            $result['field']['delivery_city_ref']['value'] = $form->delivery_city_ref;
        }

        $result['field']['delivery_city_ref']['id'] = Html::getInputId($form, 'delivery_city_ref');
        $result['field']['delivery_city_ref']['error'] = 'Необходимо выбрать город';
        $result['field']['delivery_city_ref']['name'] = Html::getInputName($form, 'delivery_city_ref');
        $result['field']['delivery_city_ref']['jsOptions'] = [
            'liveSearch' => true,
            'liveSearchPlaceholder' => 'Найти город',
            //'dropupAuto'=>false,
            'dropdownAlignRight' => 'auto',
            'size' => '300px',
            'width' => '100%',
            //'container'=>'#'.Html::getInputId($form, 'delivery_city_ref')
        ];
        if ($form->delivery_city_ref) {
            $result['field']['delivery_type']['type'] = 'dropdownlist';
            //$result['field']['delivery_type']['error'] = 'Необходимо выбрать город';
            $result['field']['delivery_type']['id'] = Html::getInputId($form, 'delivery_type');
            $result['field']['delivery_type']['items'] = ['warehouse' => 'Доставка на отделение', 'address' => 'Доставка на адрес'];
            $result['field']['delivery_type']['value'] = $form->delivery_type;
            $result['field']['delivery_type']['name'] = Html::getInputName($form, 'delivery_type');
            $result['field']['delivery_type']['jsOptions'] = [];
        }
        if ($form->delivery_city_ref && ($form->delivery_type == 'warehouse')) {


            $result['field']['delivery_warehouse_ref']['type'] = 'dropdownlist';
            $result['field']['delivery_warehouse_ref']['items'] = array_merge([NULL => html_entity_decode('&mdash; Выберите отделение &mdash;')], Warehouses::getList($form->delivery_city_ref));
            if ($form->delivery_warehouse) {
                $result['field']['delivery_warehouse_ref']['value'] = $form->delivery_warehouse;
            }
            $result['field']['delivery_warehouse_ref']['error'] = 'Необходимо выбрать отделение';
            $result['field']['delivery_warehouse_ref']['id'] = Html::getInputId($form, 'delivery_warehouse');
            $result['field']['delivery_warehouse_ref']['name'] = Html::getInputName($form, 'delivery_warehouse');
            $result['field']['delivery_warehouse_ref']['jsOptions'] = [
                'liveSearch' => true,
                'liveSearchPlaceholder' => 'Найти отделение',
                //'dropupAuto'=>false,
                'dropdownAlignRight' => 'auto',
                'size' => '300px',
                'width' => '100%'
            ];
        }


        /*if($form->delivery_type == 'warehouse'){
            $result['warehouse']['type'] = 'dropdownlist';
            $result['warehouse']['items'] = array_merge([NULL=>html_entity_decode('&mdash; Выберите отделение &mdash;')],Warehouses::getList($form->delivery_city_ref));
            if($form->delivery_warehouse){
                $result['warehouse']['value'] = $form->delivery_warehouse;
            }

            $result['warehouse']['id'] = Html::getInputId($form, 'user_address');
            $result['warehouse']['name'] = Html::getInputName($form, 'user_address');
            $result['warehouse']['jsOptions'] = [
                'liveSearch' => true,
                'liveSearchPlaceholder'=>'Найти отделение',
                //'dropupAuto'=>false,
                'dropdownAlignRight'=>'auto',
                'size'=>'300px',
                'width' => '100%'
            ];



        }else {
            $result['warehouse']['type'] = 'text';
            $result['warehouse']['id'] = Html::getInputId($form, 'user_address');
            $result['warehouse']['name'] = Html::getInputName($form, 'user_address');

        }*/


        $result['attributes'] = $form->attributes;
        $result['show_address'] = false;
        if ($form->delivery_type == 'address') {
            $result['show_address'] = true;
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $result;

    }


    public function renderDeliveryFormHtml($model)
    {

        return Yii::$app->view->renderAjax("@cart/widgets/delivery/{$model->deliveryMethod->system}/_view", [
            'model' => $model
        ]);
    }

    public function renderDeliveryForm2(Delivery $method)
    {
        $setting = $this->getSettings($method->id);
        $postApi = new NovaPoshtaApi($setting->api_key);

        return $postApi->getCities();

    }

    public function cities($method)
    {


        $cacheIdCities = 'cache_novaposhta_cities';
        $value = Yii::$app->cache->get($cacheIdCities);
        if ($method->system) {
            if ($value === false) {
                $response = $this->connect($method, ["modelName" => "Address", "calledMethod" => "getCities"]);

                foreach ($response as $data) {
                    $value[$data['DescriptionRu']] = $data['DescriptionRu'];
                }

                Yii::$app->cache->set($cacheIdCities, $value, 86400 * 346);
            }
        }
        return $value;

    }


    private function connect($method, $config = [])
    {
        $settings = $this->getSettings($method->id);
        $client = new Client();
        $response = $client->createRequest()
            ->setMethod('POST')
            ->setUrl('https://api.novaposhta.ua/v2.0/json/')
            ->setData(ArrayHelper::merge([
                'apiKey' => $settings->api_key,
                'Language' => 'ru',
            ], $config))
            ->setOptions([
                CURLOPT_CONNECTTIMEOUT => 5, // connection timeout
                CURLOPT_TIMEOUT => 10, // data receiving timeout
            ])
            ->setFormat(Client::FORMAT_JSON)
            ->addHeaders(['content-type' => 'application/json'])
            ->send();

        if ($response->isOk) {
            if ($response->data['success']) {
                return $response->data['data'];
            }
        }
    }


    public function getSettingsKey2($paymentMethodId)
    {
        return $paymentMethodId . '_NovaPoshtaDeliverySystem';
    }

    public function getModel()
    {
        return new NovaPoshtaConfigurationModel();
    }
}
