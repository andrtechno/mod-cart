<?php

namespace panix\mod\cart\widgets\delivery\meest;


use Yii;
use panix\mod\cart\models\Delivery;
use panix\mod\cart\components\delivery\BaseDeliverySystem;
use yii\httpclient\Client;
use yii\web\Response;
use panix\mod\cart\widgets\delivery\meest\api\MeestApi;
use panix\mod\cart\widgets\delivery\meest\DeliveryAsset;

/**
 * Meest delivery system
 */
class MeestDeliverySystem extends BaseDeliverySystem
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

        $api = new MeestApi();

        $post = Yii::$app->request->post();
        DeliveryAsset::register(Yii::$app->view);
        if (!$deliveryModel) {
            if (isset($post['MeestModel']['type']) == 'warehouse') {
                $this->model->addRule(['warehouse'], 'required');
            } else {
                $this->model->addRule(['address'], 'required');
            }
            $this->model->load($post);
        } else {
            $this->model = $deliveryModel;
        }

        $render = (Yii::$app->request->isAjax) ? 'renderAjax' : 'render';

        return Yii::$app->view->$render("@cart/widgets/delivery/meest/_view", [
            'model' => $this->model,
            'delivery_id' => $method->id,
            'settings' => $settings,
            'api' => $api,
            'areas'=>$api->getGeoRegions(),
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
                $model->deliveryModel->load(['MeestModel' => $data]);
            }
        }
        $api = new MeestApi();
        $render = (Yii::$app->request->isAjax) ? 'renderAjax' : 'render';


        return Yii::$app->view->$render("@cart/widgets/delivery/meest/_view_admin", [
            'model' => $model->deliveryModel,
            'delivery_id' => $method->id,
            'areas'=>$api->getGeoRegions(),
            'api' => $api
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
        return $deliveryMethodId . '_MeestDeliverySystem';
    }

    public function getModelConfig()
    {
        return new MeestConfigurationModel();
    }

    public function getModel()
    {
        $model = new MeestModel(['type', 'city', 'warehouse', 'area', 'address', 'typesList']);
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
