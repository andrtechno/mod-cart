<?php

namespace panix\mod\cart\controllers;

use Yii;
use panix\mod\cart\components\payment\BasePaymentSystem;
use panix\mod\cart\models\Order;
use panix\mod\cart\models\Payment;
use panix\engine\controllers\WebController;
use yii\web\Controller;

class PaymentController extends WebController
{
    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function actionProcess()
    {

        $payment_id = (int) Yii::$app->request->get('payment_id');
        $model = Payment::findOne($payment_id);

        if (!$model)
            $this->error404('Ошибка');

        $system = $model->getPaymentSystemClass();



        if ($system instanceof BasePaymentSystem) {
            $response = $system->processPaymentRequest($model);
            if ($response instanceof Order)
                return $this->redirect($response->getUrl());
            else
                $this->error404(Yii::t('cart/default', 'Возникла ошибка при обработке запроса. <br> {err}', ['err' => $response]));
        }
    }

}
