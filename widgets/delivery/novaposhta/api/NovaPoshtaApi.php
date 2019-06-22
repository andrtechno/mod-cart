<?php

namespace panix\mod\cart\widgets\delivery\novaposhta\api;

use Yii;
use yii\httpclient\Client;

class NovaPoshtaApi
{

    private $api_url = 'https://api.novaposhta.ua/v2.0/json/';
    private $api_key;
    public $options = [];
    public $properties = [];
    private $response;

    public function __construct($api_key, $properties = array())
    {
        $this->api_key = $api_key;
        if (!isset($properties['Language'])) {
            $this->properties['Language'] = Yii::$app->language;
        }
        $this->properties['CityName'] = 'Одесса';

        $this->options['apiKey'] = $this->api_key;




    }

    public function AddressGeneral()
    {
        $this->options['modelName'] = 'AddressGeneral';
        $this->options['calledMethod'] = 'getWarehouses';
        return $this->run();
    }

    public function Counterparty()
    {
        $this->options['modelName'] = 'Counterparty';
        $this->options['calledMethod'] = 'getCounterparties';
        $this->properties['CounterpartyProperty']='Sender';

        return $this->run();
    }

    public function ScanSheet()
    {
        $this->options['modelName'] = 'ScanSheet';
        $this->options['calledMethod'] = 'getScanSheetList';
        return $this->run();
    }

    public function TrackingDocument()
    {
        $this->options['modelName'] = 'TrackingDocument';
        $this->options['calledMethod'] = 'getStatusDocuments';
        return $this->run();
    }


    public function InternetDocument()
    {
        $this->options['modelName'] = 'InternetDocument';
        $this->options['calledMethod'] = 'getDocumentList';
        $this->properties['GetFullList']='1';
        $this->properties['DateTimeFrom']='21.06.2016';
        $this->properties['DateTimeTo']='21.06.2019';
        return $this->run();
    }

    public function run()
    {
        $this->options['methodProperties'] = $this->properties;
        $client = new Client(['baseUrl' => $this->api_url]);
        $this->response = $client->createRequest()
            ->setData($this->options)
            ->setFormat(Client::FORMAT_JSON)
            ->send();

        if ($this->response->isOk) {

            \yii\helpers\VarDumper::dump($this->response->data, 10, true);

        } else {
            return false;
        }
    }

}