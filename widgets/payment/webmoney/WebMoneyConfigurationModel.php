<?php
namespace panix\mod\cart\widgets\payment\webmoney;
class WebMoneyConfigurationModel extends \yii\base\Model {

    public $LMI_PAYEE_PURSE;
    public $LMI_SECRET_KEY;

    public function rules() {
        return array(
            array('LMI_PAYEE_PURSE, LMI_SECRET_KEY', 'type')
        );
    }

    public function attributeNames() {
        return array(
            'LMI_PAYEE_PURSE' => Yii::t('CartModule.payments', 'WEBMONEY_LMI_PAYEE_PURSE'),
            'LMI_SECRET_KEY' => Yii::t('CartModule.payments', 'WEBMONEY_LMI_SECRET_KEY'),
        );
    }

    public function getForm() {
        return array(
            'type' => 'form',
            'elements' => array(
                'LMI_PAYEE_PURSE' => array(
                    'label' => Yii::t('CartModule.payments', 'WEBMONEY_LMI_PAYEE_PURSE'),
                    'type' => 'text',
                ),
                'LMI_SECRET_KEY' => array(
                    'label' => Yii::t('CartModule.payments', 'WEBMONEY_LMI_SECRET_KEY'),
                    'type' => 'text',
                ),
                ));
    }

}
