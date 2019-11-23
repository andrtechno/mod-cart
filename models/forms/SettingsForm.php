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



    public function rules()
    {
        return [
            [['order_emails'], 'required'], //, 'tpl_body_user', 'tpl_body_admin', 'tpl_subject_user', 'tpl_subject_admin'
            //[['tpl_body_user', 'tpl_body_admin', 'tpl_subject_user', 'tpl_subject_admin'], 'string'],
        ];
    }


}
