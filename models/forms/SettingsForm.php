<?php

namespace panix\mod\cart\models\forms;

use panix\engine\Html;
use Yii;
use yii\helpers\FileHelper;

class SettingsForm extends \panix\engine\SettingsModel
{

    public static $category = 'cart';
    protected $module = 'cart';
    public $order_emails;
    public $mail_tpl_order;
    public $pdf_tpl_order;
    public $print_tpl_brand;
    public $print_tpl_supplier;

    protected $_pdf_tpl_order;
    protected $_mail_tpl_order;
    protected $_print_tpl_brand;
    protected $_print_tpl_supplier;

    protected $_pdf_tpl_order_path;
    protected $_mail_tpl_order_path;
    protected $_print_tpl_brand_path;
    protected $_print_tpl_supplier_path;

    public $notify_changed_status;

    public static function defaultSettings()
    {
        return [
            'order_emails' => Yii::$app->settings->get('app', 'admin_email'),
            'mail_tpl_order' => '@cart/mail/order.dist.tpl',
            'pdf_tpl_order' => '@cart/pdf-order.dist.tpl',
            'print_tpl_brand' => '@cart/print_brand.dist.tpl',
            'print_tpl_supplier' => '@cart/print_supplier.dist.tpl',
        ];
    }


    public function rules()
    {
        return [
            [['notify_changed_status'], 'boolean'],
            [['order_emails', 'mail_tpl_order', 'pdf_tpl_order', 'print_tpl_brand'], 'required'],
            [['order_emails'], '\panix\engine\validators\EmailListValidator'],
            [['mail_tpl_order', 'pdf_tpl_order', 'print_tpl_brand','print_tpl_supplier'], 'string'],
        ];
    }

    public function init()
    {
        parent::init();
        if (file_exists(Yii::getAlias($this->mail_tpl_order))) {
            $this->_mail_tpl_order_path = Yii::getAlias($this->mail_tpl_order);
            $this->_mail_tpl_order = file_get_contents($this->_mail_tpl_order_path);
        } else {
            $this->_mail_tpl_order_path = Yii::getAlias('@cart/mail') . '/order.dist.tpl';
            $this->_mail_tpl_order = file_get_contents($this->_mail_tpl_order_path);
        }
        if (file_exists(Yii::getAlias($this->pdf_tpl_order))) {
            $this->_pdf_tpl_order_path = Yii::getAlias($this->pdf_tpl_order);
            $this->_pdf_tpl_order = file_get_contents($this->_pdf_tpl_order_path);
        } else {
            $this->_pdf_tpl_order_path = Yii::getAlias('@cart') . '/pdf-order.dist.tpl';
            $this->_pdf_tpl_order = file_get_contents($this->_pdf_tpl_order_path);
        }

        if (file_exists(Yii::getAlias($this->print_tpl_brand))) {
            $this->_print_tpl_brand_path = Yii::getAlias($this->print_tpl_brand);
            $this->_print_tpl_brand = file_get_contents($this->_print_tpl_brand_path);
        } else {
            $this->_print_tpl_brand_path = Yii::getAlias('@cart') . '/print_brand.dist.tpl';
            $this->_print_tpl_brand = file_get_contents($this->_print_tpl_brand_path);
        }

        if (file_exists(Yii::getAlias($this->print_tpl_supplier))) {
            $this->_print_tpl_supplier_path = Yii::getAlias($this->print_tpl_supplier);
            $this->_print_tpl_supplier = file_get_contents($this->_print_tpl_supplier_path);
        } else {
            $this->_print_tpl_supplier_path = Yii::getAlias('@cart') . '/print_supplier.dist.tpl';
            $this->_print_tpl_supplier = file_get_contents($this->_print_tpl_supplier_path);
        }
    }

    public function save()
    {


        FileHelper::createDirectory(Yii::getAlias('@app/views/pdf'));

        file_put_contents(Yii::getAlias('@app/mail') . '/order.tpl', $this->mail_tpl_order);
        file_put_contents(Yii::getAlias('@app/views/pdf') . '/pdf-order.tpl', $this->pdf_tpl_order);
        file_put_contents(Yii::getAlias('@app/views/pdf') . '/print_brand.tpl', $this->print_tpl_brand);
        file_put_contents(Yii::getAlias('@app/views/pdf') . '/print_supplier.tpl', $this->print_tpl_supplier);


        $this->pdf_tpl_order = '@app/views/pdf/pdf-order.tpl';
        $this->mail_tpl_order = '@app/mail/order.tpl';
        $this->print_tpl_brand = '@app/views/pdf/print_brand.tpl';
        $this->print_tpl_supplier = '@app/views/pdf/print_supplier.tpl';
        //}
        parent::save();
    }
}
