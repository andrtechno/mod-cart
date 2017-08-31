<?php
namespace panix\cart\models;

use Yii;
use panix\engine\SettingsModel;


class OrderCreateForm extends SettingsModel {

    
    protected $category = 'cart';
    protected $module = 'cart';
    
    public $name;
    public $email;
    public $phone;
    public $address;
    public $comment;
    public $delivery_id;
    public $payment_id;
    public $registerGuest = true;
    protected $_password;
    protected $_newpassword;


    public function init() {
        if (!Yii::$app->user->isGuest) {
            // NEED CONFINGURE
          //  $this->name = Yii::$app->user->getUsername();
           // $this->phone = Yii::$app->user->phone;
          //  $this->address = Yii::$app->user->address;
           // $this->email = Yii::$app->user->email;
        } else {
          //  $this->_newpassword =CMS::gen((int) Yii::$app->settings->get('users', 'min_password') + 2);
           // $this->_password = User::encodePassword($this->_newpassword);
        }
    }

    /**
     * Validation
     * @return array
     */
    public function rules() {
        return array(
            array('name, email', 'required'),
            array('email', 'email'),
            array('comment', 'length', 'max' => '500'),
            array('address', 'length', 'max' => '255'),
            array('email', 'length', 'max' => '100'),
            array('phone', 'length', 'max' => '30'),
            array('delivery_id', 'validateDelivery'),
            array('payment_id', 'validatePayment'),
            array('registerGuest', 'boolean'),
        );
    }

    public function validateDelivery() {
        if (ShopDeliveryMethod::model()->countByAttributes(array('id' => $this->delivery_id)) == 0)
            $this->addError('delivery_id', $this->t('VALID_DELIVERY'));
    }

    public function validatePayment() {
        if (ShopPaymentMethod::model()->countByAttributes(array('id' => $this->payment_id)) == 0)
            $this->addError('payment_id', $this->t('VALID_PAYMENT'));
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
                Yii::$app->user->setFlash('success_register',Yii::t('app', 'SUCCESS_REGISTER'));
            } else {
                $this->addError('registerGuest', 'Ошибка регистрации');
                Yii::$app->user->setFlash('error_register',Yii::t('CartModule.default', 'ERROR_REGISTER'));
                print_r($user->getErrors());
                die('error register');
            }
        }
    }
    
    private function sendRegisterMail(){
        $mailer = Yii::$app->mail;
        $mailer->From = 'noreply@' . Yii::$app->request->serverName;
        $mailer->FromName = Yii::$app->settings->get('core','site_name');
        $mailer->Subject = 'Вы загеристрованы';
        $mailer->Body = 'Ваш пароль: '.$this->_newpassword;
        $mailer->AddAddress($this->email);
        $mailer->AddReplyTo('noreply@' . Yii::$app->request->serverName);
        $mailer->isHtml(true);
        $mailer->Send();
        $mailer->ClearAddresses();
    }

}
