<?php

class BasePaymentSystem extends CComponent {

    /**
     * @return string
     */
    public function renderSubmit() {
        return '<input type="submit" class="btn btn-success" value="' . Yii::t('core', 'Оплатить') . '">';
    }

    /**
     * @param $paymentMethodId
     * @param $data
     */
    public function setSettings($paymentMethodId, $data) {
        Yii::app()->settings->set($this->getSettingsKey($paymentMethodId), $data);
    }

    /**
     * @param $paymentMethodId
     */
    public function getSettings($paymentMethodId) {
        // die($this->getSettingsKey($paymentMethodId));
        return Yii::app()->settings->get($this->getSettingsKey($paymentMethodId));
    }

    public static function log($message) {
        return Yii::log($message, 'info', 'payment');
    }

}