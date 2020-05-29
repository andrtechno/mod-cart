<?php

namespace panix\mod\cart\widgets\delivery\novaposhta;

use panix\mod\cart\models\forms\OrderCreateForm;
use panix\mod\cart\widgets\delivery\novaposhta\api\NovaPoshtaApi;
use panix\mod\novaposhta\models\Cities;
use Yii;
use panix\engine\CMS;
use panix\mod\cart\models\Delivery;
use panix\mod\cart\models\Order;
use panix\mod\cart\components\delivery\BaseDeliverySystem;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\httpclient\Client;
use yii\web\Response;

/**
 * NovaPoshta delivery system
 */
class NovaPoshtaDeliverySystem extends BaseDeliverySystem
{


    /**
     * This method will be triggered after redirection from payment system site.
     * If payment accepted method must return Order model to make redirection to order view.
     * @param Delivery $method
     * @return boolean|Order
     */
    public function processRequest(Delivery $method)
    {

        $request = Yii::$app->request;
        $log = '';
        // $log.=' Transaction ID: ' . $payments['ref'].'; ';
        // $log .= ' Transaction datatime: ' . $payments['date'] . '; ';
        // $log .= ' UserID: ' . (Yii::$app->user->isGuest) ? 0 : Yii::$app->user->id . '; ';
        //  $log .= ' IP: ' . $request->userHostAddress . '; ';
        //$log.=' User-agent: ' . $request->userAgent.';';
        // self::log($log);
        // die;
        $settings = $this->getSettings($method->id);


        /* $value=[];

         $client = new Client();
         $response = $client->createRequest()
             ->setMethod('POST')
             ->setUrl('https://api.novaposhta.ua/v2.0/json/')
             ->setData([
                 'apiKey' => $settings->api_key,
                 'Language' => 'ru',
                 "modelName"=> "AddressGeneral",
                 "calledMethod"=>  "getWarehouseTypes",
                 "methodProperties" => [
                     'Language' => 'ru',
                  ]
             ])
             ->setOptions([
                 CURLOPT_CONNECTTIMEOUT => 5, // connection timeout
                 CURLOPT_TIMEOUT => 10, // data receiving timeout
             ])
             ->setFormat(Client::FORMAT_JSON)
             ->addHeaders(['content-type' => 'application/json'])
             ->send();

         if ($response->isOk) {
             if ($response->data['success']) {
                 foreach ($response->data['data'] as $data) {
                     $value[$data['Ref']] = $data['Description'];
                 }
                 // die;
                 //CMS::dump($response->data['data']);
                 // print_r($response->data['data']);die;
             }

         }*/


        /*$cacheIdCities2 = 'cache_novaposhta_cities2';
        $value2 = Yii::$app->cache->get($cacheIdCities2);
        if ($value2 === false) {
            $client = new Client();
            $response = $client->createRequest()
                ->setMethod('POST')
                ->setUrl('https://api.novaposhta.ua/v2.0/json/')
                ->setData([
                    'apiKey' => $settings->api_key,
                    'Language' => 'ru',
                    "modelName" => "Address",
                    "calledMethod" => "getWarehouses",
                    "methodProperties" => [
                        'TypeOfWarehouseRef' => '841339c7-591a-42e2-8233-7a0a00f0ed6f',
                    ]
                ])
                ->setOptions([
                    CURLOPT_CONNECTTIMEOUT => 5, // connection timeout
                    CURLOPT_TIMEOUT => 10, // data receiving timeout
                ])
                ->setFormat(Client::FORMAT_JSON)
                ->addHeaders(['content-type' => 'application/json'])
                ->send();

            if ($response->isOk) {
                if ($response->data['success']) {
                    foreach ($response->data['data'] as $data) {

                        $value2[$data['Ref']] = $data['DescriptionRu'];
                    }
                    // die;
                    //CMS::dump($response->data['data']);
                    // print_r($response->data['data']);die;
                }

            }
            Yii::$app->cache->set($cacheIdCities2, $value2, 86400 * 24);
        }
        CMS::dump($value2);
        die;*/


        $cacheIdCities = 'cache_novaposhta_cities';
        $value = Yii::$app->cache->get($cacheIdCities);
        if ($value === false) {
            $client = new Client();
            $response = $client->createRequest()
                ->setMethod('POST')
                ->setUrl('https://api.novaposhta.ua/v2.0/json/')
                ->setData([
                    'apiKey' => $settings->api_key,
                    'Language' => 'ru',

                    //"modelName"=> "AddressGeneral",
                    //"calledMethod"=> "getWarehouses",

                    "modelName" => "Address",
                    "calledMethod" => "getCities",


                    //    "modelName"=> "AddressGeneral",
                    // "calledMethod"=> "getSettlements",

                    //"methodProperties" => [
                    //"FindByString" => "Бровари"
                    //  'Warehouse'=>1,
                    // ]
//841339c7-591a-42e2-8233-7a0a00f0ed6f
                    // "modelName"=> "Address",
//"calledMethod"=> "getAreas",
                ])
                ->setOptions([
                    CURLOPT_CONNECTTIMEOUT => 5, // connection timeout
                    CURLOPT_TIMEOUT => 10, // data receiving timeout
                ])
                ->setFormat(Client::FORMAT_JSON)
                ->addHeaders(['content-type' => 'application/json'])
                ->send();

            if ($response->isOk) {
                if ($response->data['success']) {
                    foreach ($response->data['data'] as $data) {
                        //   CMS::dump($data);
                        $value[] = $data['DescriptionRu'];
                    }
                }
            }
            Yii::$app->cache->set($cacheIdCities, $value, 86400 * 24);
        }
        CMS::dump($value);
        die;

        return $order;
    }

    public function renderDeliveryForm2222(Delivery $method)
    {
        $setting = $this->getSettings($method->id);
        //  $postApi = new NovaPoshtaApi($setting->api_key);

        $address = [];
        if (Yii::$app->request->post('city')) {
            $address = \panix\mod\novaposhta\models\Warehouses::getList(Yii::$app->request->post('city'));
        }

        return Yii::$app->controller->asJson([
            'cities' => Cities::getList(),
            'address' => $address,
        ]);
    }


    public function renderDeliveryForm(Delivery $method)
    {
        $setting = $this->getSettings($method->id);
        //  $postApi = new NovaPoshtaApi($setting->api_key);


        return Yii::$app->view->renderAjax("@cart/widgets/delivery/{$method->system}/_view", [
            // 'form'=>$form,
            /*'cities' => $postApi->getCities(),
            'address' => $postApi->getAddressGeneral([
                "methodProperties" => [
                    "CityName" => Yii::$app->request->post('city')
                ],
            ]),*/
            'cities' => Cities::getList(),
            'address' => [],
            'method' => $method
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


    public function getSettingsKey($paymentMethodId)
    {
        return $paymentMethodId . '_NovaPoshtaDeliverySystem';
    }

    public function getModel()
    {
        return new NovaPoshtaConfigurationModel();
    }
}
