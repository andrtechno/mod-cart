<?php

class NotifyForm extends CFormModel {

    public $email;

    public function rules() {
        return array(
            array('email', 'email'),
        );
    }

    public function attributeLabels() {
        return array(
            'email' => Yii::t('CartModule.admin', 'Email'),
        );
    }

}
