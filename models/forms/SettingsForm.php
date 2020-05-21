<?php

namespace panix\mod\cart\models\forms;

use panix\engine\Html;
use Yii;

class SettingsForm extends \panix\engine\SettingsModel
{

    public static $category = 'cart';
    public $module = 'cart';
    public $order_emails;
    public $mail_tpl_order;
    public $notify_changed_status;

    public static function defaultSettings()
    {
        return [
            'order_emails' => Yii::$app->settings->get('app', 'admin_email'),
            'mail_tpl_order' => file_get_contents(Yii::getAlias(Yii::$app->getModule('cart')->mailPath) . '/order.dist.tpl'),
        ];
    }


    public function rules()
    {
        return [
            [['notify_changed_status'], 'boolean'],
            [['order_emails', 'mail_tpl_order'], 'required'],
            [['mail_tpl_order'], 'string'],
        ];
    }

    public function init()
    {
        parent::init();
        $this->mail_tpl_order = file_get_contents(Yii::getAlias(Yii::$app->getModule('cart')->mailPath) . '/order.tpl');
    }


    public function save()
    {

        parent::save();
        $this->mail_tpl_order = file_put_contents(Yii::getAlias(Yii::$app->getModule('cart')->mailPath) . '/order.tpl', $this->mail_tpl_order);
    }
}
