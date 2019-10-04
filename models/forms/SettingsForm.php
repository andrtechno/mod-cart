<?php

namespace panix\mod\cart\models\forms;

use Yii;

class SettingsForm extends \panix\engine\SettingsModel
{

    public static $category = 'cart';
    public $module = 'cart';
    public $order_emails;
    public $tpl_body_user;
    public $tpl_subject_user;
    public $tpl_subject_admin;
    public $tpl_body_admin;

    public static function defaultSettings()
    {
        return [
            'order_emails' => Yii::$app->settings->get('app', 'admin_email'),
            'tpl_body_admin' => '<p><strong>Номер заказ:</strong> #{order_id}</p>
<p><strong>Способ доставки: </strong>{order_delivery_name}</p>
<p><strong>Способ оплаты: </strong>{order_payment_name}</p>
<p>&nbsp;</p>
<p>{list}</p>
<p>&nbsp;</p>
<p>Общая стоимость: <strong>{total_price}</strong> {current_currency}</p>
<p>&nbsp;</p>
<p><strong>Контактные данные:</strong></p>
<p>Имя: {user_name}</p>
<p>Телефон: {user_phone}</p>
<p>Почта: {user_email}</p>
<p>Адрес: {user_address}</p>
<p>Комментарий: {user_comment}</p>',
            'tpl_body_user' => '<p>Здравствуйте, <strong>{user_name}</strong></p>
<p>Способ доставки: <strong>{order_delivery_name}</strong></p>
<p>Способ оплаты: <strong>{order_payment_name}</strong></p>
<p>&nbsp;</p>
<p>Детали заказа вы можете просмотреть на странице: {link_to_order}</p>
<p><br />{list}</p>
<p>Всего к оплате: {for_payment} {current_currency}</p>
<p><strong>Контактные данные:</strong></p>
<p>Телефон: {user_phone}</p>
<p>Адрес доставки: {user_address}</p>',
            'tpl_subject_admin' => 'Новый заказ',
            'tpl_subject_user' => 'Вы оформили заказ #{order_id}',
        ];
    }

    public function getForm()
    {
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


    public function rules()
    {
        return [
            [['order_emails', 'tpl_body_user', 'tpl_body_admin', 'tpl_subject_user', 'tpl_subject_admin'], 'required'],
            [['tpl_body_user', 'tpl_body_admin', 'tpl_subject_user', 'tpl_subject_admin'], 'string'],
        ];
    }


}
