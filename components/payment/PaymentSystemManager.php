<?php

namespace panix\mod\cart\components\payment;





class PaymentSystemManager extends \yii\base\Component {

    /**
     * @var array
     */
    private $_systems = array();

    /**
     * Find all payment systems installed
     * @return array
     */
    public function getSystems() {
        $pattern = \Yii::getAlias('@cart/widgets/payment') . DIRECTORY_SEPARATOR . '*' . DIRECTORY_SEPARATOR . 'config.xml';

        foreach (glob($pattern, GLOB_BRACE) as $file) {
            $config = simplexml_load_file($file);
            $this->_systems[(string) $config->id] = $config;
        }
        return $this->_systems;
    }

    /**
     * Read and return system config.xml
     * @param $name
     */
    public function getSystemInfo($name) {
        return $this->systems[$name];
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getSystemClass($id) {
        $systemInfo = $this->getSystemInfo($id);
        $className = (string) $systemInfo->class;
        
        $systemArray = $this->getDefaultModelClasses();
        
        //Yii::import("mod.cart.widgets.payment.{$systemInfo->id}.{$className}");
        return new $systemArray[$className];
    }
    
    protected function getDefaultModelClasses() {
        return [
            'QiwiPaymentSystem' => 'panix\mod\cart\widgets\payment\qiwi\QiwiPaymentSystem',
        ];
    }
}
