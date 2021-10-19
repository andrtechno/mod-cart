<?php

namespace panix\mod\cart\widgets\delivery\pickup;

use panix\mod\cart\models\forms\OrderCreateForm;
use Yii;
use panix\engine\CMS;
use panix\mod\cart\models\Delivery;
use panix\mod\cart\models\Order;
use panix\mod\cart\components\delivery\BaseDeliverySystem;
use yii\base\DynamicModel;
use yii\helpers\Html;

/**
 * Pickup delivery system
 */
class PickupDeliverySystem extends BaseDeliverySystem
{

    public $json = [];
    public function model(Delivery $method)
    {
        $settings = $this->getSettings($method->id);
        $model = new DynamicModel(['name']);

        $model->addRule(['name'], 'string', ['max' => 128])
            ->addRule('name', 'required');
           // ->validate();

        return $model;
    }
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

        $fileds=[];
        foreach ($settings->address['name'] as $key=>$value){

        }
        $fileds[]=[
            'type'=>'checkboxlist',
            'id'=>$key,
            'items'=>$settings->address['name'],
            'rules'=>[
                [['name'], 'string', ['max' => 128]],
                ['name', 'required']
            ]

        ];
        $result['field'] = $fileds;
        $result['delivery_id']=$method->id;

        //return $result;
        return Yii::$app->view->renderAjax("@cart/widgets/delivery/pickup/_view", [
            'model' => $form
        ]);
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
