<?php

namespace panix\mod\cart\widgets\delivery\address;

use panix\mod\cart\models\forms\OrderCreateForm;
use Yii;
use panix\engine\CMS;
use panix\mod\cart\models\Delivery;
use panix\mod\cart\models\Order;
use panix\mod\cart\components\delivery\BaseDeliverySystem;
use yii\helpers\Html;
use yii\web\Response;

/**
 * Address delivery system
 */
class AddressDeliverySystem extends BaseDeliverySystem
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
        if($this->model->load($post)){
            $this->model->validate();
        }

        $render = (Yii::$app->request->isAjax) ? 'renderAjax' : 'render';
        return Yii::$app->view->$render("@cart/widgets/delivery/address/_view", [
            'model' => $this->model,
        ]);
    }

    public function processRequestAdmin(Delivery $method, $data = null)
    {

        $settings = $this->getSettings($method->id);
        $post = Yii::$app->request->post();

        $render = (Yii::$app->request->isAjax) ? 'renderAjax' : 'render';
        return Yii::$app->view->$render("@cart/widgets/delivery/address/_view_admin", [
            'model' => $this->model,
        ]);
    }

    public function processRequestAdmin2(Delivery $method, $model = null)
    {
        $settings = $this->getSettings($method->id);
        $post = Yii::$app->request->post();
        $data = $model->getDeliveryData();
        if ($data) {
            if(isset($data['address'])){
                $model->deliveryModel->address = $data['address'];
            }
        }
        $render = (Yii::$app->request->isAjax) ? 'renderAjax' : 'render';
        return Yii::$app->view->$render("@cart/widgets/delivery/address/_view_admin", [
            'model' => $model->deliveryModel,
        ]);
    }

    public function renderDeliveryFormHtml($model)
    {
        return Yii::$app->view->renderAjax("@cart/widgets/delivery/{$model->deliveryMethod->system}/_view", [
            'model' => $model
        ]);
    }

    public function getModelConfig()
    {
        return false;
    }
    public function getModel()
    {
        return new AddressModel;
    }

}
