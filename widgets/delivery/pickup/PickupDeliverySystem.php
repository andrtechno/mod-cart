<?php

namespace panix\mod\cart\widgets\delivery\pickup;

use panix\mod\cart\models\forms\OrderCreateForm;
use Yii;
use panix\engine\CMS;
use panix\mod\cart\models\Delivery;
use panix\mod\cart\models\Order;
use panix\mod\cart\components\delivery\BaseDeliverySystem;
use yii\helpers\Html;
use yii\web\Response;

/**
 * Pickup delivery system
 */
class PickupDeliverySystem extends BaseDeliverySystem
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
        $list = $this->getList($settings);
        if ($this->model->load($post)) {
            $this->model->validate($post);
        }

        $render = (Yii::$app->request->isAjax) ? 'renderAjax' : 'render';
        return Yii::$app->view->$render("@cart/widgets/delivery/pickup/_view", [
            'model' => $this->model,
            'deliveryModel' => $this->model,
            'list' => $list
        ]);
    }

    public function processRequestAdmin2222(Delivery $method, $data = null)
    {

        $settings = $this->getSettings($method->id);
        $post = Yii::$app->request->post();

        $render = (Yii::$app->request->isAjax) ? 'renderAjax' : 'render';
        return Yii::$app->view->$render("@cart/widgets/delivery/pickup/_view_admin", [
            'model' => $this->model,
            'list' => $this->getList($settings),
            'activeIndex' => 0,
        ]);
    }

    public function processRequestAdmin(Delivery $method, $model = null)
    {
        $settings = $this->getSettings($method->id);
        $post = Yii::$app->request->post();
        $activeIndex = 0;

        if ($model) {
            $this->model->address = 1;
            $data = $model->getDeliveryData();
            if ($data) {
                if (isset($data['address'])) {
                    $activeIndex = $data['address'];
                }
            }
        }
        $this->model->load($post);
        $render = (Yii::$app->request->isAjax) ? 'renderAjax' : 'render';
        return Yii::$app->view->$render("@cart/widgets/delivery/pickup/_view_admin", [
            'model' => $this->model,
            //'deliveryModel' => $model->deliveryModel,
            'activeIndex' => $activeIndex,
            'list' => $this->getList($settings)
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
        return new PickupConfigurationModel();
    }

    public function getList($settings)
    {
        $list = [];
        if (isset($settings->address)) {
            foreach ($settings->address as $address) {
                $html = '';
                if ($address['from']) {
                    $html .= '<small class="text-muted">(c ' . $address['from'];
                }
                if ($address['to']) {
                    $html .= ' по ' . $address['to'] . ')</small>';
                }
                $list[] = $address['name'] . ' ' . $html;
            }
        }
        return $list;
    }

    public function getModel()
    {
        $model = new \yii\base\DynamicModel(['address']);
        $model->addRule(['address'], 'required');
        $model->addRule(['address'], 'safe');
        $model->setAttributeLabels(['address' => Yii::t('cart/Delivery', 'PICKUP_FROM_ADDRESS')]);
        return $model;
    }

}
