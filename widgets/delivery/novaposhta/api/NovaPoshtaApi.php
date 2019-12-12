<?php

namespace panix\mod\cart\widgets\delivery\novaposhta\api;

use Yii;
use yii\helpers\ArrayHelper;
use yii\httpclient\Client;

class NovaPoshtaApi
{

    private $api_url = 'https://api.novaposhta.ua/v2.0/json/';
    private $api_key;
    public $options = [];
    //public $properties = [];
    private $response;

    public function __construct($api_key, $options = [])
    {
        $this->api_key = $api_key;
        if (!isset($options['Language'])) {
            $this->options['Language'] = Yii::$app->language;
        }
        //$this->properties['CityName'] = 'Одесса';

        $this->options['apiKey'] = $this->api_key;


    }

    public function getAddressGeneral($options = [])
    {
        $this->options['modelName'] = 'AddressGeneral';
        $this->options['calledMethod'] = 'getWarehouses';
        $result = $this->run($options);
        $res = [];
        if ($result['success']) {
            foreach ($result['data'] as $data) {
                $res[] = $data['DescriptionRu'];
            }
        }
        return $res;
    }

    public function Counterparty($options = [])
    {
        $this->options['modelName'] = 'Counterparty';
        $this->options['calledMethod'] = 'getCounterparties';
        $this->options['CounterpartyProperty'] = 'Sender';

        return $this->run($options);
    }

    public function ScanSheet($options = [])
    {
        $this->options['modelName'] = 'ScanSheet';
        $this->options['calledMethod'] = 'getScanSheetList';
        return $this->run($options);
    }

    public function TrackingDocument($options = [])
    {
        $this->options['modelName'] = 'TrackingDocument';
        $this->options['calledMethod'] = 'getStatusDocuments';
        return $this->run($options);
    }

    public function getCities($options = [])
    {
        $this->options['modelName'] = 'Address';
        $this->options['calledMethod'] = 'getCities';
        $result = $this->run($options);
        $res = [];
        if ($result['success']) {
            foreach ($result['data'] as $data) {
                $res[$data['DescriptionRu']] = $data['DescriptionRu'];
            }
        }
        return $res;
    }

    public function InternetDocument($properties = [])
    {
        $this->options['modelName'] = 'InternetDocument';
        $this->options['calledMethod'] = 'getDocumentList';
        $this->options['GetFullList'] = '1';
        $this->options['DateTimeFrom'] = '21.06.2016';
        $this->options['DateTimeTo'] = '21.06.2019';
        return $this->run($properties);
    }

    public function run($options = [])
    {
        $cacheId = 'cache_novaposhta_' . $this->options['modelName'] . ':' . $this->options['calledMethod'];
        $value = Yii::$app->cache->get($cacheId);
        if ($value === false) {
            $this->options['methodProperties'] = $this->options;
            $this->options = ArrayHelper::merge($this->options, $options);
            $client = new Client(['baseUrl' => $this->api_url]);
            $this->response = $client->createRequest()
                ->setData($this->options)
                ->setFormat(Client::FORMAT_JSON)
                ->send();

            if ($this->response->isOk) {
                $value = $this->response->data;
            }
            Yii::$app->cache->set($cacheId, $value, 86400 * 1);
        }
        return $value;
    }

}