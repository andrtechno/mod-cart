<?php

namespace panix\mod\cart\components\payment;

use yii\base\Model;

class BasePaymentForm extends Model {

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
