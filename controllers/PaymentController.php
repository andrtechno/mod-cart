<?php
namespace panix\mod\cart\controllers;
class PaymentController extends Controller {

    public function actionProcess() {
        if (Yii::app()->request->getParam('Shp_pmId'))
            $_GET['payment_id'] = $_GET['Shp_pmId'];

        $payment_id = (int) Yii::app()->request->getParam('payment_id');
        $model = ShopPaymentMethod::model()->findByPk($payment_id);

        if (!$model)
            throw new CHttpException(404, 'Ошибка');

        $system = $model->getPaymentSystemClass();
        if ($system instanceof BasePaymentSystem) {
            $response = $system->processPaymentRequest($model);
            if ($response instanceof Order)
                $this->redirect($this->createUrl('/cart', array('view' => $response->secret_key)));
            else
                throw new CHttpException(404, Yii::t('CartModule.default', 'Возникла ошибка при обработке запроса. <br> {err}', array('{err}' => $response)));
        }
    }

}
