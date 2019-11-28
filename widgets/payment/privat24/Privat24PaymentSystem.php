<?php

namespace panix\mod\cart\widgets\payment\privat24;

use Yii;
use panix\engine\CMS;
use panix\mod\cart\models\Payment;
use panix\mod\cart\models\Order;
use panix\mod\cart\components\payment\BasePaymentSystem;
use yii\helpers\Url;

/**
 * Privat24 payment system
 */
class Privat24PaymentSystem extends BasePaymentSystem
{

    /**
     * This method will be triggered after redirection from payment system site.
     * If payment accepted method must return Order model to make redirection to order view.
     * @param Payment $method
     * @return boolean|Order
     */
    public function processPaymentRequest(Payment $method)
    {

        $request = Yii::$app->request;
        $log = '';
        // $log.=' Transaction ID: ' . $payments['ref'].'; ';
        // $log .= ' Transaction datatime: ' . $payments['date'] . '; ';
        // $log .= ' UserID: ' . (Yii::$app->user->isGuest) ? 0 : Yii::$app->user->id . '; ';
        //  $log .= ' IP: ' . $request->userHostAddress . '; ';
        //$log.=' User-agent: ' . $request->userAgent.';';
        // self::log($log);
        // die;
        $settings = $this->getSettings($method->id);
        $MERCHANT_ID = $settings->merchant_id;
        $MERCHANT_PASS = $settings->merchant_pass;

        if ($request->post('payment')) {
            parse_str($request->post('payment'), $payments);


            list($gen, $order_id) = explode('_', $payments['order']);


            $order = Order::findOne((int)$order_id);


            if ($order === false)
                return false;

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


            // foreach ($forHash as $key => $val) {
            //     if ($request->getParam($key))
            //         $forHash[$key] = $request->getParam($key);
            // }
            // Check if order is paid.
            if ($order->paid) {
                // Yii::info('Order is paid');
                $this->log('Order is paid');
                return false;
            }


            if (Yii::$app->currency->active['iso'] != $payments['ccy']) {
                $this->log('Currency error');
                return false;
            }


            if (!$request->get('payment_id')) {
                $this->log('No find post param "payment"');
                return false;
            }

            // Create and check signature.
            $sign = sha1(md5($request->post('payment') . $MERCHANT_PASS));

            // If ok make order paid.
            if ($sign != $request->post('signature')) {
                $this->log('signature error');

                return false;
            }


            // Set order paid
            $order->paid = 1;
            $order->save(false);
            if ($order->paid)
                Yii::$app->session->setFlash('success', 'Заказ успешно оплачен');
            $log = '';
            //$log .= 'PayID: ' . $payments['ref'];
            //$log .= 'Datatime: ' . $payments['date'];
            //$log .= 'UserID: ' . (Yii::$app->user->isGuest) ? 0 : Yii::$app->user->id;
            //$log .= 'IP: ' . $request->userHostAddress;
            // $log .= 'User-agent: ' . $request->userAgent;


        } else {
            $this->log('no find pay');
            return false;
        }

        return $order;
    }

    public function renderPaymentForm(Payment $method, Order $order)
    {
        $html = '
            <form action="https://api.privatbank.ua/p24api/ishop" method="POST" accept-charset="UTF-8">
                <input type="hidden" name="amt" value="{amount}"/>
                <input type="hidden" name="ccy" value="UAH" />
                <input type="hidden" name="merchant" value="{merchant_id}" />
                <input type="hidden" name="order" value="{order}" />
                <input type="hidden" name="details" value="{order_title}" />
                <input type="hidden" name="ext_details" value="{order_title}" />
                <input type="hidden" name="pay_way" value="privat24" />
                <input type="hidden" name="return_url" value="{return_url}" />
                <input type="hidden" name="server_url" value="{server_url}" />
                {submit}
            </form>';


        $settings = $this->getSettings($method->id);

        $html = strtr($html, [
            '{amount}' => Yii::$app->currency->convert($order->full_price, $method->currency_id), //, $method->currency_id
            '{order_id}' => $order->id,
            '{order_title}' => Yii::t('cart/default', 'PAYMENT_ORDER', ['id' => $order->id]),
            '{merchant_id}' => $settings->merchant_id,
            '{order}' => CMS::gen(5) . '_' . $order->id, //CMS::gen(5) . '_'.
            '{return_url}' => Url::toRoute(['/cart/payment/process', 'payment_id' => $method->id], true),
            '{server_url}' => Url::toRoute(['/cart/payment/process', 'payment_id' => $method->id, 'result' => true], true),
            '{submit}' => $this->renderSubmit(),
        ]);

        return ($order->paid) ? false : $html;

    }

    /**
     * This method will be triggered after payment method saved in admin panel
     * @param $paymentMethodId
     * @param $postData
     */
    public function saveAdminSettings($paymentMethodId, $postData)
    {
        $this->setSettings($paymentMethodId, $postData['Privat24ConfigurationModel']);
    }

    /**
     * @param $paymentMethodId
     * @return string
     */
    public function getSettingsKey($paymentMethodId)
    {
        return $paymentMethodId . '_Privat24PaymentSystem';
    }

    /**
     * Get configuration form to display in admin panel
     * @param $paymentMethodId
     * @return Privat24ConfigurationModel
     */
    public function getConfigurationFormHtml($paymentMethodId)
    {
        $model = new Privat24ConfigurationModel;
        $model->load([basename(get_class($model)) => (array)$this->getSettings($paymentMethodId)]);

        return $model;
    }

}
