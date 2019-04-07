<?php
namespace panix\mod\cart\components\payment;

use Yii;
use panix\engine\Html;
class BasePaymentSystem extends \yii\base\Component {

    /**
     * @return string
     */
    public function renderSubmit() {
       // return '<input type="submit" class="btn btn-success" value="' . Yii::t('app', 'Оплатить') . '">';
        return Html::submitButton(Yii::t('app', 'Оплатить'), ['class' => 'btn btn-success']);
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
     * @return mixed
     */
    public function getSettings($paymentMethodId) {
        return Yii::$app->settings->get($this->getSettingsKey($paymentMethodId));
    }

    /**
     * @param $message string
     */
    public function log($message){
        Yii::info($this->getSettingsKey(basename(get_class($this))).': '.$message);
    }


}