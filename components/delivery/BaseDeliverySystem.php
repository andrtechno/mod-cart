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
        if ($this->getModelConfig()) {
            $this->_system_name = basename(get_class($this->getModelConfig()));
            $this->_system_name2 = basename(get_class($this));
        }
        parent::init();
    }

    /**
     * @param $deliveryMethodId
     * @param $data
     */
    public function setSettings($deliveryMethodId, $data)
    {
        Yii::$app->settings->set($this->getSettingsKey($deliveryMethodId), $data);
    }

    /**
     * @param $deliveryMethodId
     * @return mixed
     */
    public function getSettings($deliveryMethodId)
    {

        return Yii::$app->settings->get($this->getSettingsKey($deliveryMethodId));
    }

    /**
     * @param $message string
     */
    public function log($message)
    {
        Yii::info($this->getSettingsKey(basename(get_class($this))) . ': ' . $message);
    }


    public function getSettingsKey($id)
    {
        return $id . '_' . $this->getShortName();
    }

    public function getShortName()
    {
        $reflect = new \ReflectionClass($this);
        return $reflect->getShortName();
    }

    public function getModelName()
    {
        return (new \ReflectionClass($this->getModelConfig()))->getShortName();
    }
}