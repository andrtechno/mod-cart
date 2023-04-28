<?php

namespace panix\mod\cart\widgets\delivery\novaposhta;

use panix\mod\cart\models\forms\OrderCreateForm;
use panix\mod\cart\widgets\delivery\novaposhta\api\NovaPoshtaApi;
use panix\mod\cart\widgets\delivery\novaposhta\DeliveryAsset;
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
            if (isset($post['DynamicModel']['type']) == 'warehouse') {
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
        $resultWarehouses = [];
        if ($this->model->city) {
            $queryWarehouse = \panix\mod\novaposhta\models\Warehouses::find();

            $queryWarehouse->where(['CityRef' => $this->model->city]);
            if ($settings->type_warehouse) {
                $queryWarehouse->andWhere(['TypeOfWarehouse' => $settings->type_warehouse]);
            }
            //$queryWarehouse->cache(8600 * 7);
            $queryWarehouse->orderBy(['number' => SORT_ASC]);
            $resultWarehouses = $queryWarehouse->all();
            if ($resultWarehouses) {
                //$this->model->type = 'warehouse';
                /*$this->model->typesList = [
                    'warehouse' => Yii::t('cart/Delivery', 'DELIVERY_WAREHOUSE'),
                    'address' => Yii::t('cart/Delivery', 'DELIVERY_ADDRESS')
                ];*/
            } else {
                //$this->model->type = 'address';
                /*$this->model->typesList = [
                    'address' => Yii::t('cart/Delivery', 'DELIVERY_ADDRESS')
                ];*/
            }
        }



        //CMS::dump($test);die;
        return Yii::$app->view->$render("@cart/widgets/delivery/novaposhta/_view", [
            'model' => $this->model,
            'delivery_id' => $method->id,
            'settings' => $settings,
            'resultWarehouses' => $resultWarehouses
        ]);
    }

    public function processRequestAdmin(Delivery $method)
    {
        $post = Yii::$app->request->post();
        if (isset($post['DynamicModel']['type']) == 'warehouse') {
            $this->model->addRule(['warehouse'], 'required');
        } else {
            $this->model->addRule(['address'], 'required');
        }
        $this->model->load($post);
//CMS::dump($this->model);
        $render = (Yii::$app->request->isAjax) ? 'renderAjax' : 'render';

        return Yii::$app->view->$render("@cart/widgets/delivery/novaposhta/_view_admin", [
            'model' => $this->model,
            'delivery_id' => $method->id,
            //'order_id' => $order_id
        ]);
    }

    public function processRequestAdmin2(Delivery $method, $model = null)
    {
        $post = Yii::$app->request->post();
        if ($post) {
            $model->deliveryModel->load($post);
        } else {
            $data = $model->getDeliveryData();
            if ($data) {
                $model->deliveryModel->load(['DynamicModel' => $data]);
            }
        }

        $render = (Yii::$app->request->isAjax) ? 'renderAjax' : 'render';

        return Yii::$app->view->$render("@cart/widgets/delivery/novaposhta/_view_admin", [
            'model' => $model->deliveryModel,
            'delivery_id' => $method->id,
            //'order_id' => $order_id
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
        $model = new \yii\base\DynamicModel(['type', 'city', 'warehouse', 'area', 'address', 'typesList']);
        $model->addRule(['type', 'address', 'city', 'warehouse', 'area'], 'safe');
        $model->addRule(['type', 'address', 'city', 'warehouse', 'area'], 'default');
        $model->addRule(['city', 'area', 'type'], 'required');
        $model->typesList = [
            'warehouse' => Yii::t('cart/Delivery', 'DELIVERY_WAREHOUSE'),
            'address' => Yii::t('cart/Delivery', 'DELIVERY_ADDRESS')
        ];
        $model->setAttributeLabels([
            'type' => Yii::t('cart/Delivery', 'Тип доставки'),
            'address' => Yii::t('cart/Delivery', 'TYPE_ADDRESS'),
            'city' => Yii::t('cart/Delivery', 'CITY'),
            'warehouse' => Yii::t('cart/Delivery', 'WAREHOUSE'),
            'area' => Yii::t('cart/Delivery', 'AREA')
        ]);

        $model->type = array_keys($model->typesList)[0]; //to default first item

        return $model;
    }


}
