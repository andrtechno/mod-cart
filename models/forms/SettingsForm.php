<?php
namespace panix\mod\cart\models\forms;

use Yii;

class SettingsForm extends \panix\engine\SettingsModel {

    public $category = 'cart';
    public $module = 'cart';
    public $order_emails;
    public $tpl_body_user;
    public $tpl_subject_user;
    public $tpl_subject_admin;
    public $tpl_body_admin;

    public static function defaultSettings() {
        return array(
            'order_emails' => Yii::$app->settings->get('core', 'admin_email'),
            'tpl_body_admin' => '<p><strong>Номер заказ:</strong> #%ORDER_ID%</p>
<p><strong>Способ доставки: </strong>%ORDER_DELIVERY_NAME%</p>
<p><strong>Способ оплаты: </strong>%ORDER_PAYMENT_NAME%</p>
<p>&nbsp;</p>
<p>%LIST%</p>
<p>&nbsp;</p>
<p>Общая стоимость: <strong>%TOTAL_PRICE%</strong> %CURRENT_CURRENCY%</p>
<p>&nbsp;</p>
<p><strong>Контактные данные:</strong></p>
<p>Имя: %USER_NAME%</p>
<p>Телефон: %USER_PHONE%</p>
<p>Почта: %USER_EMAIL%</p>
<p>Адрес: %USER_ADDRESS%</p>
<p>Комментарий: %USER_COMMENT%</p>',
            'tpl_body_user' => '<p>Здравствуйте, <strong>%USER_NAME%</strong></p>
<p>Способ доставки: <strong>%ORDER_DELIVERY_NAME%</strong></p>
<p>Способ оплаты: <strong>%ORDER_PAYMENT_NAME%</strong></p>
<p>&nbsp;</p>
<p>Детали заказа вы можете просмотреть на странице: %LINK_TO_ORDER%</p>
<p><br />%LIST%</p>
<p>Всего к оплате: %FOR_PAYMENY% %CURRENT_CURRENCY%</p>
<p><strong>Контактные данные:</strong></p>
<p>Телефон: %USER_PHONE%</p>
<p>Адрес доставки: %USER_ADDRESS%</p>',
            'tpl_subject_admin' => 'Новый заказ',
            'tpl_subject_user' => 'Вы оформили заказ #%ORDER_ID%',
        );
    }

    public function getForm() {
        Yii::import('ext.BootstrapTagInput');
        Yii::app()->controller->widget('ext.tinymce.TinymceWidget');
        return new TabForm(array('id' => __CLASS__,
                    'showErrorSummary' => false,
                    'attributes' => array(
                        'enctype' => 'multipart/form-data',
                        'class' => 'form-horizontal'
                    ),
                    'elements' => array(
                        'global' => array(
                            'type' => 'form',
                            'title' => Yii::t('core', 'Общие'),
                            'elements' => array(
                                'order_emails' => array(
                                    'type' => 'BootstrapTagInput',
                                ),
                            )
                        ),
                        'tpl_mail_user' => array(
                            'type' => 'form',
                            'title' => Yii::t('core', 'Шаблон письма для покупателя'),
                            'elements' => array(
                                'tpl_subject_user' => array('type' => 'text'),
                                'tpl_body_user' => array(
                                    'type' => 'textarea',
                                    'class' => 'editor',
                                    'hint' => Html::link('Документация', 'javascript:open_manual()')
                                ),
                            )
                        ),
                        'tpl_mail_admin' => array(
                            'type' => 'form',
                            'title' => Yii::t('core', 'Шаблон письма для администратора'),
                            'elements' => array(
                                'tpl_subject_admin' => array('type' => 'text'),
                                'tpl_body_admin' => array(
                                    'type' => 'textarea',
                                    'class' => 'editor',
                                    'hint' => Html::link('Документация', 'javascript:open_manual()')
                                ),
                            )
                        ),
                    ),
                    'buttons' => array(
                        'submit' => array(
                            'type' => 'submit',
                            'class' => 'btn btn-success',
                            'label' => Yii::t('app', 'SAVE')
                        )
                    )
                        ), $this);
    }



    public function rules() {
        return [
            [['order_emails','tpl_body_user','tpl_body_admin','tpl_subject_user','tpl_subject_admin'], 'required'],
            [['tpl_body_user', 'tpl_body_admin', 'tpl_subject_user', 'tpl_subject_admin'], 'string'],
        ];
    }


}
