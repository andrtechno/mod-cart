<?php

namespace panix\mod\cart\components\delivery;

use Yii;
use panix\engine\Html;
use yii\base\Component;
// extends Component
class BaseDeliverySystem extends Component
{

    /**
     * @return string
     */
    public function renderSubmit($options = [])
    {
        // return '<input type="submit" class="btn btn-success" value="' . Yii::t('app/default', 'Оплатить') . '">';
        if (!isset($options['class'])) {
            $options['class'] = 'btn btn-success';
        }
        return Html::submitButton(Yii::t('app/default', 'Оплатить'), $options);
    }

    /**
     * @param $paymentMethodId
     * @param $data
     */
    public function setSettings($paymentMethodId, $data)
    {
        Yii::$app->settings->set($this->getSettingsKey($paymentMethodId), $data);
    }

    /**
     * @param $paymentMethodId
     * @return mixed
     */
    public function getSettings($paymentMethodId)
    {
        return Yii::$app->settings->get($this->getSettingsKey($paymentMethodId));
    }

    /**
     * @param $message string
     */
    public function log($message)
    {
        Yii::info($this->getSettingsKey(basename(get_class($this))) . ': ' . $message);
    }


}