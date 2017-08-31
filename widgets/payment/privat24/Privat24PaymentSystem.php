<?php

namespace panix\mod\cart\widgets\payment\privat24;

use panix\mod\cart\widgets\payment\privat24\Privat24ConfigurationModel;
use panix\mod\cart\models\PaymentMethod;
use panix\mod\cart\models\Order;
use panix\mod\cart\components\payment\BasePaymentSystem;
/**
 * Privat24 payment system
 */
class Privat24PaymentSystem extends BasePaymentSystem {

    /**
     * This method will be triggered after redirection from payment system site.
     * If payment accepted method must return Order model to make redirection to order view.
     * @param ShopPaymentMethod $method
     * @return boolean|Order
     */
    public function processPaymentRequest(PaymentMethod $method) {
        $request = Yii::app()->request;
        $log = '';
        $log.=' Transaction ID: ' . $payments['ref'].'; ';
        $log.=' Transaction datatime: ' . $payments['date'].'; ';
        $log.=' UserID: ' . (Yii::app()->user->isGuest) ? 0 : Yii::app()->user->id.'; ';
        $log.=' IP: ' . $request->userHostAddress.'; ';
        //$log.=' User-agent: ' . $request->userAgent.';';
        self::log($log);
        die;
        

        if (isset($_POST['payment'])) {
            parse_str($request->getPost('payment'), $payments);
        }

        $order_id = substr($payments['order'], 5);
        $settings = $this->getSettings($method->id);
        $order = Order::model()->findByPk($order_id);

        // if ($order === false)
        //     return false;
        // For first WebMoney pre-request
        //if (!isset($_POST['signature']) && isset($_GET['result']))
        //     die('YES');

        $MERCHANT_ID = $settings['MERCHANT_ID'];
        $MERCHANT_PASS = $settings['MERCHANT_PASS'];


        // Grab WM variables from post.
        // Variables to create signature.
        /* $forHash = array(
          'amt' => '',
          'ccy' => '',
          'details' => '',
          'ext_details' => '',
          'pay_way' => '',
          'order' => '',
          'merchant'=>$MERCHANT_ID
          ); */
        //parse_str(Yii::app()->request->getPost('payment'), $forHash);
        // foreach ($forHash as $key => $val) {
        //     if ($request->getParam($key))
        //         $forHash[$key] = $request->getParam($key);
        // }
        // Check if order is paid.
        if ($order->paid) {
            Yii::log('Order is paid', 'info', 'payment privat24');
            return false;
        }

        // unset($forHash['state'],$forHash['payCountry'],$forHash['ref'],$forHash['date']);
        //parse_str(Yii::app()->request->getPost('payment'), $test);
//print_r($forHash);
        // Check amount.
        if (Yii::app()->currency->active->iso != $payments['ccy']) {
            Yii::log('Currency error', 'info', 'payment privat24');
            return false;
        }



        if (!$request->getParam('payment')) {
            Yii::log('No find post param "payment"', 'info', 'payment privat24');
            return false;
        }

        // Create and check signature.
        $sign = sha1(md5($request->getParam('payment') . $MERCHANT_PASS));

        // If ok make order paid.
        if ($sign != $request->getParam('signature')) {
            Yii::log('signature error', 'info', 'payment privat24');
            return false;
        }


        // Set order paid
        $order->paid = 1;
        $order->save(false);

        $log = '';
        $log.='PayID: ' . $payments['ref'];
        $log.='Datatime: ' . $payments['date'];
        $log.='UserID: ' . (Yii::app()->user->isGuest) ? 0 : Yii::app()->user->id;
        $log.='IP: ' . $request->userHostAddress;
        $log.='User-agent: ' . $request->userAgent;
        $this->log($log);


        return $order;
    }

    public function renderPaymentForm(ShopPaymentMethod $method, Order $order) {
        $html = '
            <form action="https://api.privatbank.ua/p24api/ishop" method="POST" accept-charset="UTF-8">
                <input type="hidden" name="amt" value="{AMOUNT}"/>
                <input type="hidden" name="ccy" value="UAH" />
                <input type="hidden" name="merchant" value="{MERCHANT_ID}" />
                <input type="hidden" name="order" value="{ORDER}" />
                <input type="hidden" name="details" value="{ORDER_TITLE}" />
                <input type="hidden" name="ext_details" value="{ORDER_ID}" />
                <input type="hidden" name="pay_way" value="privat24" />
                <input type="hidden" name="return_url" value="{SUCCESS_URL}" />
                <input type="hidden" name="server_url" value="{RESULT_URL}" />
                {SUBMIT}
            </form>';

        $settings = $this->getSettings($method->id);
        
        $html = strtr($html, array(
            // '{AMOUNT}' => 1,
            '{AMOUNT}' => Yii::app()->currency->convert($order->full_price,$method->currency_id), //, $method->currency_id
            '{ORDER_ID}' => $order->id,
            '{ORDER_TITLE}' => Yii::t('CartModule.default', 'PAYMENT_ORDER', array('{id}' => $order->id)),
            '{MERCHANT_ID}' => $settings['MERCHANT_ID'],
            '{ORDER}' => CMS::gen(5) . $order->id,
            '{SUCCESS_URL}' => Yii::app()->createAbsoluteUrl('/cart/payment/process', array('payment_id' => $method->id)),
            '{RESULT_URL}' => Yii::app()->createAbsoluteUrl('/cart/payment/process', array('payment_id' => $method->id, 'result' => true)),
            '{SUBMIT}' => $this->renderSubmit(),
                ));

        return ($order->paid) ? false : $html;
    }

    /**
     * This method will be triggered after payment method saved in admin panel
     * @param $paymentMethodId
     * @param $postData
     */
    public function saveAdminSettings($paymentMethodId, $postData) {
        $this->setSettings($paymentMethodId, $postData['Privat24ConfigurationModel']);
    }

    /**
     * @param $paymentMethodId
     * @return string
     */
    public function getSettingsKey($paymentMethodId) {
        return $paymentMethodId . '_Privat24PaymentSystem';
    }

    /**
     * Get configuration form to display in admin panel
     * @param string $paymentMethodId
     * @return CForm
     */
    public function getConfigurationFormHtml($paymentMethodId) {
        $model = new Privat24ConfigurationModel;
        $model->attributes = $this->getSettings($paymentMethodId);
        $form = new BasePaymentForm($model->getForm(), $model);
        return $form;
    }

}
