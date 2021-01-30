<?php

namespace panix\mod\cart\models\forms;

use panix\engine\Html;
use Yii;
use yii\helpers\FileHelper;

class SettingsForm extends \panix\engine\SettingsModel
{

    public static $category = 'cart';
    public $module = 'cart';
    public $order_emails;
    public $mail_tpl_order;
    public $pdf_tpl_order;
    protected $_pdf_tpl_order;
    protected $_mail_tpl_order;
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
            [['order_emails', 'mail_tpl_order', 'pdf_tpl_order'], 'required'],
            [['order_emails'], '\panix\engine\validators\EmailListValidator'],
            [['mail_tpl_order'], 'string'],
            [['pdf_tpl_order'], 'string'],
        ];
    }

    public function init()
    {
        parent::init();
        if (file_exists(Yii::getAlias($this->mail_tpl_order))) {
            $this->_mail_tpl_order = file_get_contents(Yii::getAlias('@app/mail') . '/order.tpl');
        } else {
            $this->_mail_tpl_order = file_get_contents(Yii::getAlias('@cart/mail') . '/order.dist.tpl');
        }
        if (file_exists(Yii::getAlias($this->pdf_tpl_order))) {
            $this->_pdf_tpl_order = file_get_contents(Yii::getAlias($this->pdf_tpl_order));
        } else {
            $this->_pdf_tpl_order = file_get_contents(Yii::getAlias('@cart') . '/pdf-order.dist.tpl');

        }

    }


    public function save()
    {


        FileHelper::createDirectory(Yii::getAlias('@app/views/mail'));
        $this->mail_tpl_order = file_put_contents(Yii::getAlias('@app/views/mail') . '/order.tpl', $this->_mail_tpl_order);

        FileHelper::createDirectory(Yii::getAlias('@app/views/pdf'));
        //if(!file_exists(Yii::getAlias('@theme/order') . '/pdf-order.tpl')){
        file_put_contents(Yii::getAlias('@app/views/pdf') . '/pdf-order.tpl', $this->_pdf_tpl_order);
        $this->pdf_tpl_order = '@app/views/pdf/pdf-order.tpl';
        $this->mail_tpl_order = '@app/mail/order.tpl';
        //}
        parent::save();
    }
}
