<?php

namespace panix\mod\cart\models\forms;

use panix\mod\cart\models\Order;
use Yii;
use panix\mod\cart\models\Delivery;
use panix\mod\cart\models\Payment;
use panix\engine\base\Model;
use panix\engine\CMS;
use panix\mod\cart\models\PromoCode;
use panix\mod\user\models\User;

/**
 * Class OrderCreateForm
 * @property integer $delivery_id
 * @property integer $payment_id
 * @property string $user_name
 * @property string $user_lastname
 * @property string $user_comment
 * @property string $user_email
 * @property string $user_phone
 * @property string $call_confirm
 * @property integer $promocode_id
 * @property integer $points
 *
 * @package panix\mod\cart\models\forms
 */
class OrderCreateForm extends Model
{

    public static $category = 'cart';
    protected $module = 'cart';
    public $user_name;
    public $user_lastname;
    public $user_email;
    public $user_phone;
    public $delivery_address;
    public $user_comment;
    public $delivery_id;
    public $payment_id;
    public $promocode_id;
    public $register = false;
    public $call_confirm = false;
    //delivery
    public $delivery_city; //for delivery systems;
    public $delivery_warehouse; //for delivery systems;
    public $delivery_type = 'warehouse'; //for delivery systems;
    public $delivery_city_ref;
    public $delivery_warehouse_ref;
    public $points;

    public function init()
    {
        $user = Yii::$app->user;
        if (!$user->isGuest && Yii::$app->controller instanceof \panix\engine\controllers\WebController) {
            // NEED CONFINGURE
            $this->user_name = $user->firstname;
            $this->user_phone = $user->phone;
            //$this->delivery_address = Yii::app()->user->address; //comment for april
            $this->user_email = $user->getEmail();
            $this->user_lastname = $user->lastname;
            $this->points = (isset(Yii::$app->cart->session['cart_data']['bonus'])) ? Yii::$app->cart->session['cart_data']['bonus'] : 0;
        } else {

            //  $this->_password = User::encodePassword(CMS::gen((int) Yii::$app->settings->get('users', 'min_password') + 2));
        }

        parent::init();
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['buyOneClick'] = ['user_phone'];


        $scenarios['guest'] = [
            'register',
            'delivery_id',
            'payment_id',
            'user_lastname',
            'user_name',
            'user_email',
            'user_phone',
            'delivery_address',
            'delivery_city',
            'delivery_type',
            'delivery_city_ref',
            'delivery_warehouse_ref',
            'delivery_warehouse',
            'points',
            'call_confirm',
        ];
        $scenarios['checkout_guest'] = [
            'user_lastname',
            'user_name',
            'user_email',
            'user_phone',
        ];
        $scenarios['checkout_delivery'] = [
            'user_lastname',
            'user_name',
            'user_email',
            'user_phone',
            'delivery_id',
        ];



        return $scenarios;
    }

    public function rules()
    {

        $rules = [];

        $rules[] = [['user_lastname'], 'required', 'on' => 'guest'];
        $rules[] = [['user_name', 'user_email', 'user_phone', 'user_lastname'], 'required'];
        $rules[] = [['delivery_id', 'payment_id'], 'required'];
        $rules[] = [['delivery_id', 'payment_id', 'promocode_id', 'points'], 'integer'];
        $rules[] = ['user_email', 'email'];
        $rules[] = ['user_comment', 'string'];
        $rules[] = [['user_lastname', 'user_name'], 'string', 'max' => 100];
        $rules[] = [['delivery_address', 'delivery_city', 'delivery_type', 'delivery_city_ref', 'delivery_warehouse_ref', 'delivery_warehouse'], 'string'];
        $rules[] = [['user_phone'], 'string', 'max' => 30];
        $rules[] = [['register', 'call_confirm'], 'boolean'];
        $rules[] = ['delivery_id', 'validateDelivery'];
        $rules[] = ['payment_id', 'validatePayment'];

        //$rules[] = ['user_phone', 'panix\ext\telinput\PhoneInputValidator', 'on' => self::SCENARIO_DEFAULT];
        $rules[] = ['user_phone', 'panix\ext\telinput\PhoneInputValidator'];
        // $rules[] = ['user_phone', 'string', 'on' => 'buyOneClick'];


        $rules[] = ['points', 'pointsValidate'];
        if (Yii::$app->user->isGuest) {
            $rules[] = [['register'], 'validateRegisterEmail'];
        }
        return $rules;
    }

    public function validateRegisterEmail($attribute)
    {
        if ($this->{$attribute}) {
            $find = User::find()->where(['username' => $this->user_email])->count();
            if ($find) {
                $this->addError($attribute, 'Ошибка регистрации, данный E-mail уже зарегистрирован');
            }
        }

    }

    public function pointsValidate($attribute)
    {
        //if ($this->{$attribute} <= Yii::$app->user->identity->points) {
        $total = Yii::$app->cart->getTotalPrice();
        $config = Yii::$app->settings->get('user');
        $profit = (($total - $this->{$attribute}) / $total) * 100;
        if ($profit >= (int)$config->bonus_max_use_order) {
            // $bonusData['message'] = "Вы успешно применили {$points2} бонусов";
            // $bonusData['success'] = true;
            // $total -= $this->{$attribute};
            return true;
        } else {
            $this->addError($attribute, 'У Вас недостаточно бонусов');
        }
        // return true;
        // } else {
        //    $this->addError($attribute, 'У Вас недостаточно бонусов');
        // }
    }

    public function beforeValidate()
    {
        $p = PromoCode::find()->where(['code' => $this->promocode_id])->one();
        if ($p) {
            $this->promocode_id = $p->id;
        }
        return parent::beforeValidate();
    }

    public function validatePromoCode()
    {


    }

    public function validateDelivery()
    {
        if (Delivery::find()->where(['id' => $this->delivery_id])->count() == 0)
            $this->addError('delivery_id', Yii::t('cart/OrderCreateForm', 'VALID_DELIVERY'));
    }

    public function validatePayment()
    {
        if (Payment::find()->where(['id' => $this->payment_id])->count() == 0)
            $this->addError('payment_id', Yii::t('cart/OrderCreateForm', 'VALID_PAYMENT'));
    }

    public function registerGuest(Order $order)
    {
        if (Yii::$app->user->isGuest && $this->register) {
            $pass = mb_strtoupper(CMS::gen(3)) . rand(1000, 9999);
            $user = new User(['scenario' => 'register_fast']);
            $user->password = $pass;
            $user->username = $this->user_email;
            $user->first_name = $this->user_name;
            $user->email = $this->user_email;
            $user->phone = $this->user_phone;
            // $user->group_id = 2;
            if ($user->validate()) {
                $user->save();
                $this->sendRegisterEmail($order, $user, $pass);
                Yii::$app->session->addFlash('success', Yii::t('cart/default', 'SUCCESS_REGISTER'));
            } else {
                $this->addError('register', 'Ошибка регистрации');
                Yii::$app->session->addFlash('error', Yii::t('cart/default', 'ERROR_REGISTER'));
                // print_r($user->getErrors());
                // die('error register');
            }
        }
    }

    private function sendRegisterEmail(Order $order, User $user, $password)
    {
        $mailer = Yii::$app->mailer;
        $mailer->compose(['html' => Yii::$app->getModule('cart')->mailPath . '/register.tpl'], [
            'user' => $user,
            'order' => $order,
            'password' => $password,
            'form' => $this,
        ])
            //->setFrom(['noreply@' . Yii::$app->request->serverName => Yii::$app->name . ' robot'])
            ->setTo($this->user_email)
            ->setSubject(Yii::t('cart/default', 'Вы загеристрованы'))
            ->send();
    }

}
