<?php
namespace panix\mod\cart\components\payment;

use Yii;

class BasePaymentSystem extends \yii\base\Component {

    /**
     * @return string
     */
    public function renderSubmit() {
        return '<input type="submit" class="btn btn-success" value="' . Yii::t('app', 'Оплатить') . '">';
    }

    /**
     * @param $paymentMethodId
     * @param $data
     */
    public function setSettings($paymentMethodId, $data) {
        Yii::$app->settings->set($this->getSettingsKey($paymentMethodId), $data);
    }

    /**
     * @param $paymentMethodId
     */
    public function getSettings($paymentMethodId) {
        // die($this->getSettingsKey($paymentMethodId));
        return Yii::$app->settings->get($this->getSettingsKey($paymentMethodId));
    }



}