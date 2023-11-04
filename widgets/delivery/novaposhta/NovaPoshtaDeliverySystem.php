<?php

namespace panix\mod\cart\widgets\delivery\novaposhta;

use panix\mod\cart\models\forms\OrderCreateForm;
use panix\mod\cart\widgets\delivery\novaposhta\api\NovaPoshtaApi;
use panix\mod\cart\widgets\delivery\novaposhta\DeliveryAsset;
use panix\mod\novaposhta\models\Cities;
use panix\mod\novaposhta\models\RecipientDynamicModel;
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

    public $model;

    public function __construct($config = [])
    {
        $this->model = $this->getModel();
        parent::__construct($config);
    }


    /**
     * This method will be triggered after redirection from delivery system site.
     * @param Delivery $method
     */
    public function processRequest(Delivery $method, $deliveryModel = null)
    {
        $settings = $this->getSettings($method->id);

        $post = Yii::$app->request->post();
        DeliveryAsset::register(Yii::$app->view);
        if (!$deliveryModel) {
            if (isset($post['NovaposhtaModel']['type']) == 'warehouse') {
                $this->model->addRule(['warehouse'], 'required');
            } else {
                $this->model->addRule(['address'], 'required');
            }
            $this->model->load($post);
        } else {
            $this->model = $deliveryModel;
        }

        $render = (Yii::$app->request->isAjax) ? 'renderAjax' : 'render';
        $sd = [];
        $streets = [];
        $warehouses = false;
        if ($this->model->city) {
            $result = Yii::$app->novaposhta->getWarehouses($this->model->city, 0, 9999);
            $warehouses = $result['data'];
        }

        return Yii::$app->view->$render("@cart/widgets/delivery/novaposhta/_view", [
            'model' => $this->model,
            'delivery_id' => $method->id,
            'settings' => $settings,
            'warehouses' => $warehouses
        ]);
    }


    public function processRequestAdmin(Delivery $method, $model = null)
    {
        DeliveryAdminAsset::register(Yii::$app->view);
        $post = Yii::$app->request->post();
        if ($post) {
            if (isset($post['NovaposhtaModel']['type']) == 'warehouse') {
                $this->model->addRule(['warehouse'], 'required');
            } else {
                $this->model->addRule(['address'], 'required');
            }
        } else {
            if ($model) {
                $data = $model->getDeliveryData();
                if ($data) {
                    $post = ['NovaposhtaModel' => $data];
                }
            }
        }


        $this->model->load($post);


        $render = (Yii::$app->request->isAjax) ? 'renderAjax' : 'render';
        $result = Yii::$app->novaposhta->getWarehouses($this->model->city, 0, 9999);
        $warehouses = $result['data'];

        return Yii::$app->view->$render("@cart/widgets/delivery/novaposhta/_view_admin", [
            'model' => $this->model,
            'delivery_id' => $method->id,
            'warehouses' => $warehouses
        ]);
    }

    public function processRequestSender()
    {
        $config = Yii::$app->settings->get('novaposhta');
        $post = Yii::$app->request->post();
        if ($post) {

            $this->model->load($post);
        } else {
            if (isset($config->sender_area)) {
                $this->model->area = $config->sender_area;
            }
            if (isset($config->sender_city)) {
                $this->model->city = $config->sender_city;
            }
            if (isset($config->sender_warehouse)) {
                $this->model->warehouse = $config->sender_warehouse;
            }
        }

        $render = (Yii::$app->request->isAjax) ? 'renderAjax' : 'render';

        /*$warehouses = Yii::$app->cache->get("warehouses-{$this->model->city}");
        if ($this->model->city) {
            if ($warehouses === false) {
                $np = Yii::$app->novaposhta->model('Address')->method('getWarehouses');
                $result = $np->params(['CityRef' => $this->model->city])->execute();
                if ($result['success']) {
                    Yii::$app->cache->set("warehouses-{$this->model->city}", $result['data'], 86400);
                    $warehouses = $result['data'];
                }
            }
        }*/

        $warehouses = false;
        if ($this->model->city) {
            $result = Yii::$app->novaposhta->getWarehouses($this->model->city, 0, 9999);
            $warehouses = $result['data'];
        }


        return Yii::$app->view->$render("@novaposhta/views/admin/default/_config_sender", [
            'model' => $this->model,
            'warehouses' => $warehouses
        ]);
    }


    public function processRequestRecipient($modelep = null)
    {
        $config = Yii::$app->settings->get('novaposhta');
        $post = Yii::$app->request->post();


        $model = new RecipientDynamicModel(['type', 'city', 'warehouse', 'area', 'address', 'typesList']);
        $model->addRule(['type', 'address', 'city', 'warehouse', 'area'], 'safe');
        $model->addRule(['type', 'address', 'city', 'warehouse', 'area'], 'default');
        $model->addRule(['city', 'area', 'type'], 'required');
        $model->typesList = [
            'warehouse' => Yii::t('cart/Delivery', 'DELIVERY_WAREHOUSE'),
            'address' => Yii::t('cart/Delivery', 'DELIVERY_ADDRESS')
        ];
        $model->setAttributeLabels([
            'type' => Yii::t('cart/Delivery', 'TYPE_DELIVERY'),
            'address' => Yii::t('cart/Delivery', 'TYPE_ADDRESS'),
            'city' => Yii::t('cart/Delivery', 'CITY'),
            'warehouse' => Yii::t('cart/Delivery', 'WAREHOUSE'),
            'area' => Yii::t('cart/Delivery', 'AREA')
        ]);

        $model->type = array_keys($model->typesList)[0]; //to default first item


        if ($post) {
            $model->load($post);
        } else {
            if (isset($modelep->RecipientRegionRef)) {
                $model->area = $modelep->RecipientRegionRef;
            }
            if (isset($modelep->CityRecipientRef)) {
                $model->city = $modelep->CityRecipientRef;
            }
            if (isset($modelep->RecipientAddressRef)) {
                $model->warehouse = $modelep->RecipientAddressRef;
            }
        }

        $render = (Yii::$app->request->isAjax) ? 'renderAjax' : 'render';

        /*$warehouses = Yii::$app->cache->get("warehouses-{$model->city}");
        if ($model->city) {
            if ($warehouses === false) {
                $np = Yii::$app->novaposhta->model('Address')->method('getWarehouses');
                $result = $np->params(['CityRef' => $model->city])->execute();
                if ($result['success']) {
                    Yii::$app->cache->set("warehouses-{$model->city}", $result['data'], 86400);
                    $warehouses = $result['data'];
                }
            }
        }*/


        $warehouses = false;
        if ($model->city) {
            $result = Yii::$app->novaposhta->getWarehouses($model->city, 0, 9999);
            $warehouses = $result['data'];
        }


        return Yii::$app->view->$render("@novaposhta/views/admin/default/_config_recipient", [
            'model' => $model,
            'warehouses' => $warehouses
        ]);
    }


    public function renderDeliveryFormHtml($model)
    {
        return Yii::$app->view->renderAjax("@cart/widgets/delivery/{$model->deliveryMethod->system}/_view", [
            'model' => $model
        ]);
    }


    public function getSettingsKey2($deliveryMethodId)
    {
        return $deliveryMethodId . '_NovaPoshtaDeliverySystem';
    }

    public function getModelConfig()
    {
        return new NovaPoshtaConfigurationModel();
    }

    public function getModel()
    {
        $model = new NovaposhtaModel(['type', 'city', 'warehouse', 'area', 'address', 'typesList']);
        $model->addRule(['type', 'address', 'city', 'warehouse', 'area'], 'safe');
        $model->addRule(['type', 'address', 'city', 'warehouse', 'area'], 'default');
        $model->addRule(['city', 'area', 'type'], 'required');
        $model->typesList = [
            'warehouse' => Yii::t('cart/Delivery', 'DELIVERY_WAREHOUSE'),
            'address' => Yii::t('cart/Delivery', 'DELIVERY_ADDRESS')
        ];
        $model->setAttributeLabels([
            'type' => Yii::t('cart/Delivery', 'TYPE_DELIVERY'),
            'address' => Yii::t('cart/Delivery', 'TYPE_ADDRESS'),
            'city' => Yii::t('cart/Delivery', 'CITY'),
            'warehouse' => Yii::t('cart/Delivery', 'WAREHOUSE'),
            'area' => Yii::t('cart/Delivery', 'AREA')
        ]);

        $model->type = array_keys($model->typesList)[0]; //to default first item

        return $model;
    }


}
