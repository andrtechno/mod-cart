<?php

namespace panix\mod\cart\widgets\delivery\meest\api;

use panix\engine\CMS;
use Yii;
use yii\helpers\ArrayHelper;
use yii\httpclient\Client;

class MeestApi
{

    private $api_url = 'https://publicapi.meest.com/';

    public $options = [];

    private $response;

    public function __construct()
    {


    }

    public function getGeoRegions($options = [])
    {
        return $this->run('geo_regions', $options);
    }

    public function getGeoLocalities($options = [])
    {
        return $this->run('geo_localities', $options);
    }

    public function getGeoDistricts($options = [])
    {
        return $this->run('geo_districts', $options);
    }
    public function getGeoStreets($options = [])
    {
        return $this->run('geo_streets', $options);
    }
    public function getBranches($options = [])
    {
        return $this->run('branches', $options);
    }

    public function getBranchesById($id, $options = [])
    {
        return $this->run('branches/' . $id, $options);
    }

    public function run($method, $options = [])
    {

        $client = new Client(['baseUrl' => $this->api_url]);
        $response = $client->get($method, $options)->send();
        if ($response->isOk) {
            if ($response->data['status']) {
                return $response->data['result'];
            }
        }
        return [];
    }

}
