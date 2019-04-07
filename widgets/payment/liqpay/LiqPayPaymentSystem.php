<?php

namespace panix\mod\cart\widgets\payment\liqpay;

use panix\engine\Html;
use Yii;
use panix\engine\CMS;
use panix\mod\cart\models\Payment;
use panix\mod\cart\models\Order;
use panix\mod\cart\components\payment\BasePaymentSystem;
use yii\helpers\Url;

/**
 * Class LiqPayPaymentSystem
 * @package panix\mod\cart\widgets\payment\liqpay
 */
class LiqPayPaymentSystem extends BasePaymentSystem
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

        $settings = $this->getSettings($method->id);


        if ($request->post('data')) {
            parse_str($request->post('data'), $payments);


            list($gen, $order_id) = explode('_', $payments['order']);


            $order = Order::findOne((int)$order_id);


            if ($order === false)
                return false;


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


            if (Yii::$app->currency->active->iso != $payments['ccy']) {
                $this->log('Currency error');
                return false;
            }


            if (!$request->get('payment_id')) {
                $this->log('No find post param "payment"');
                return false;
            }

            // Create and check signature.
            $sign = base64_encode(sha1($settings->private_key . $request->post('data') . $settings->private_key,1));

            // If ok make order paid.
            if ($sign !== $request->post('signature')) {
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
        $settings = $this->getSettings($method->id);
        $liqpay = new LiqPay($settings->public_key, $settings->private_key);
        $html = Html::beginForm('https://www.liqpay.ua/api/3/checkout', 'POST', [
            'csrf' => false,
            'accept-charset' => 'utf-8'
        ]);


        $data = [
            'public_key' => $settings->public_key,
            'action' => 'pay',
            'language' => Yii::$app->language, //Язык клиента ru, uk, en
            // 'amount' => Yii::$app->currency->convert($order->full_price, $method->currency_id),
            'amount' => 1,
            'currency' => 'UAH',
            'description' => Yii::t('cart/default', 'PAYMENT_ORDER', ['id' => $order->id]),
            'order_id' => CMS::gen(5) . '_' . $order->id,
            'sandbox' => '1', //1 test mode
            'server_url' => Url::toRoute(['/cart/payment/process', 'payment_id' => $method->id, 'result' => true], true),
            'result_url' => Url::toRoute(['/cart/payment/process', 'payment_id' => $method->id], true),
            'version' => '3'
        ];

        $html .= Html::hiddenInput('data', base64_encode(json_encode($data)));
        $html .= Html::hiddenInput('signature', $liqpay->cnb_signature($liqpay->cnb_params($data)));
        $html .= $this->renderSubmit(['name' => 'btn_text']);
        $html .= Html::endForm();

        return ($order->paid) ? false : $html;
    }

    /**
     * This method will be triggered after payment method saved in admin panel
     * @param $paymentMethodId
     * @param $postData
     */
    public function saveAdminSettings($paymentMethodId, $postData)
    {
        $this->setSettings($paymentMethodId, $postData['LiqPayConfigurationModel']);
    }

    /**
     * @param $paymentMethodId
     * @return string
     */
    public function getSettingsKey($paymentMethodId)
    {
        return $paymentMethodId . '_LiqPayPaymentSystem';
    }

    /**
     * Get configuration form to display in admin panel
     * @param $paymentMethodId
     * @return LiqPayConfigurationModel
     */
    public function getConfigurationFormHtml($paymentMethodId)
    {
        $model = new LiqPayConfigurationModel;
        $model->load([basename(get_class($model)) => (array)$this->getSettings($paymentMethodId)]);

        return $model;
    }

}
