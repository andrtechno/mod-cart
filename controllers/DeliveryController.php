<?php

namespace panix\mod\cart\controllers;

use Yii;
use panix\mod\cart\components\delivery\BaseDeliverySystem;
use panix\mod\cart\models\Order;
use panix\mod\cart\models\Delivery;
use panix\engine\controllers\WebController;
use yii\web\Controller;

class DeliveryController extends WebController
{
    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function actionProcess($id)
    {
       // $payment_id = (int) Yii::$app->request->get('id');
        $model = Delivery::findOne($id);

        if (!$model)
            $this->error404();



        $system = $model->getDeliverySystemClass();

        if ($system instanceof BaseDeliverySystem) {
            $response = $system->processRequest($model);
            if ($response instanceof Order)
                return $this->redirect($response->getUrl());
            else
                $this->error404(Yii::t('cart/default', 'Возникла ошибка при обработке запроса. <br/> {err}', ['err' => $response]));
        }
    }

}
