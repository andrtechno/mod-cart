<?php

namespace panix\mod\cart\components\payment;

class BasePaymentForm extends \yii\base\Model {

    public function render() {
        $this->renderBegin();
        $form = $this->renderBody();
        $this->renderEnd();

        return $form;
    }

}