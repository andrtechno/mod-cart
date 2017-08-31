<?php

namespace panix\mod\cart\models\forms;

use Yii;

class OrderCreateForm extends \panix\engine\base\Model {

    protected $category = 'cart';
    protected $module = 'cart';
    public $name;
    public $email;
    public $phone;
    public $address;
    public $comment;
    public $delivery_id;
    public $payment_id;
    public $registerGuest = false;

    public function rules() {
        return [
            [['name', 'email'], 'required'],
           // [['delivery_id','payment_id'], 'required'],
            ['email', 'email'],
            [['comment'], 'string', 'max' => 500],
            [['address'], 'string', 'max' => 255],
            [['phone'], 'string', 'max' => 30],
            ['registerGuest', 'boolean'],
            ['delivery_id', 'validateDelivery'],
            ['payment_id', 'validatePayment'],
        ];
    }

    public function validateDelivery() {
        if (DeliveryMethod::find()->count(['id' => $this->delivery_id]) == 0)
            $this->addError('delivery_id', 'VALID_DELIVERY');
    }

    public function validatePayment() {
        if (PaymentMethod::find()->count(['id' => $this->payment_id]) == 0)
            $this->addError('payment_id', 'VALID_PAYMENT');
    }

    public function registerGuest() {
        if (Yii::$app->user->isGuest && $this->registerGuest) {
            $user = new User('registerFast');
            $user->password = $this->_password;
            $user->username = $this->name;
            $user->email = $this->email;
            $user->login = $this->email;
            $user->address = $this->address;
            $user->phone = $this->phone;
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
