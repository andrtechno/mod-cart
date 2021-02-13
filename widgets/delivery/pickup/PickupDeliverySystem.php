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


        return $result;

    }


    public function renderDeliveryFormHtml($model)
    {
        return Yii::$app->view->renderAjax("@cart/widgets/delivery/{$model->deliveryMethod->system}/_view", [
            'model' => $model
        ]);
    }

    public function getModel()
    {
        return new PickupConfigurationModel();
    }

    public function getModelName()
    {
        return (new \ReflectionClass($this->getModel()))->getShortName();
    }
}
