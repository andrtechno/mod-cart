<?php

namespace panix\mod\cart\models\forms;

use Yii;
use panix\mod\cart\models\Delivery;
use panix\mod\cart\models\Payment;

class OrderCreateForm extends \panix\engine\base\Model {

    protected $category = 'cart';
    protected $module = 'cart';
    public $user_name;
    public $user_email;
    public $user_phone;
    public $user_address;
    public $user_comment;
    public $delivery_id;
    public $payment_id;
    public $registerGuest = false;
    public function init() {
        $user = Yii::$app->user;
        if (!$user->isGuest && Yii::$app->controller instanceof \panix\engine\controllers\WebController) {
            // NEED CONFINGURE
            $this->user_name = $user->getDisplayName();
            $this->user_phone = $user->phone;
            //$this->user_address = Yii::app()->user->address; //comment for april
            $this->user_email = $user->getEmail();
 
        } else {
          //  $this->_password = User::encodePassword(CMS::gen((int) Yii::$app->settings->get('users', 'min_password') + 2));
        }

        parent::init();
    }
    public function rules() {
        return [
            [['user_name', 'user_email'], 'required'],
            [['delivery_id','payment_id'], 'required'],
            [['delivery_id','payment_id'], 'integer'],
            ['user_email', 'email'],
            [['user_comment'], 'string', 'max' => 500],
            [['user_address'], 'string', 'max' => 255],
            [['user_phone'], 'string', 'max' => 30],
            ['registerGuest', 'boolean'],
            ['delivery_id', 'validateDelivery'],
            ['payment_id', 'validatePayment'],
        ];
    }

    public function validateDelivery() {
        if (Delivery::find()->where(['id' => $this->delivery_id])->count() == 0)
            $this->addError('delivery_id', Yii::t('cart/OrderCreateForm','VALID_DELIVERY'.$this->delivery_id));
    }

    public function validatePayment() {
        if (Payment::find()->where(['id' => $this->payment_id])->count() == 0)
            $this->addError('payment_id', Yii::t('cart/OrderCreateForm','VALID_PAYMENT'.$this->payment_id));
    }

    public function registerGuest() {
        if (Yii::$app->user->isGuest && $this->registerGuest) {
            $user = new User('registerFast');
            $user->password = $this->_password;
            $user->username = $this->user_name;
            $user->email = $this->user_email;
            $user->login = $this->user_email;
            $user->address = $this->user_address;
            $user->phone = $this->user_phone;
            $user->group_id = 2;
            if ($user->validate()) {
                $user->save();
                $this->sendRegisterMail();
                Yii::$app->user->setFlash('success_register', Yii::t('app', 'SUCCESS_REGISTER'));
            } else {
                $this->addError('registerGuest', 'Ошибка регистрации');
                Yii::$app->user->setFlash('error_register', Yii::t('CartModule.default', 'ERROR_REGISTER'));
                print_r($user->getErrors());
                die('error register');
            }
        }
    }

    private function sendRegisterMail() {
        $mailer = Yii::$app->mail;
        $mailer->From = 'noreply@' . Yii::$app->request->serverName;
        $mailer->FromName = Yii::$app->settings->get('core', 'site_name');
        $mailer->Subject = 'Вы загеристрованы';
        $mailer->Body = 'Ваш пароль: ' . $this->_newpassword;
        $mailer->AddAddress($this->email);
        $mailer->AddReplyTo('noreply@' . Yii::$app->request->serverName);
        $mailer->isHtml(true);
        $mailer->Send();
        $mailer->ClearAddresses();
    }

}
