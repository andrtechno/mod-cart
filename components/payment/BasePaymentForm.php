<?php

namespace panix\mod\cart\components\payment;

class BasePaymentForm extends \yii\base\Object {

    public $_config;

    public function __construct($config, $model = null) {
     //   print_r($model);
        $this->_config = $config;
        foreach ($this->_config as $element) {
           
        }
         return parent::__construct($config);
    }
    public function init(){
        return 'zzz';
    }

    /*   public function render() {
      $this->renderBegin();
      $form = $this->renderBody();
      $this->renderEnd();

      return $form;
      } */
}
