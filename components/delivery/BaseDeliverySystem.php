<?php

namespace panix\mod\cart\components\delivery;

use panix\engine\CMS;
use Yii;
use panix\engine\Html;
use yii\base\Component;

class BaseDeliverySystem extends Component
{
    private $_system_name;
    private $_system_name2;
    public function init()
    {
        $this->_system_name = basename(get_class($this->getModel()));
        $this->_system_name2 = basename(get_class($this));

       // CMS::dump($this->_system_name);
       // CMS::dump($this->_system_name2);die;
        parent::init(); // TODO: Change the autogenerated stub
    }

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
        //echo basename(get_class($this));


       // CMS::dump($data);die;
        Yii::$app->settings->set($paymentMethodId.'_'.$this->_system_name2, $data);
    }

    /**
     * @param $paymentMethodId
     * @return mixed
     */
    public function getSettings($paymentMethodId)
    {
        return Yii::$app->settings->get($paymentMethodId.'_'.$this->_system_name2);
    }

    /**
     * @param $message string
     */
    public function log($message)
    {
        Yii::info($this->getSettingsKey(basename(get_class($this))) . ': ' . $message);
    }


    public function getSettingsKey2($id)
    {
       return $id.'_'.basename(get_class($this));
    }


}