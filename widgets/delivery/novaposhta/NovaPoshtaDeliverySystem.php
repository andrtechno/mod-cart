<?php

namespace panix\mod\cart\widgets\delivery\novaposhta;

use Yii;
use panix\engine\CMS;
use panix\mod\cart\models\Delivery;
use panix\mod\cart\models\Order;
use panix\mod\cart\components\delivery\BaseDeliverySystem;
use yii\helpers\Url;
use yii\httpclient\Client;

/**
 * NovaPoshta delivery system
 */
class NovaPoshtaDeliverySystem extends BaseDeliverySystem
{

    /**
     * This method will be triggered after redirection from payment system site.
     * If payment accepted method must return Order model to make redirection to order view.
     * @param Delivery $method
     * @return boolean|Order
     */
    public function processRequest(Delivery $method)
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





        $client = new Client();
        $response = $client->createRequest()
            ->setMethod('POST')
            ->setUrl('https://api.novaposhta.ua/v2.0/json/')
            ->setData([
                'apiKey' => $settings->api_key,
                'Language'=>'ru',
               // "modelName" => "Address",
               // "calledMethod" => "getCities",

          //      "modelName"=> "AddressGeneral",
    //"calledMethod"=> "getWarehouses",

                "modelName" => "Address",
                "calledMethod" => "getCities",
            ])
            ->setFormat(Client::FORMAT_JSON)
            ->addHeaders(['content-type' => 'application/json'])
            ->send();
        if ($response->isOk) {
            if($response->data['success']){
                //CMS::dump($response->data['data']);
                print_r($response->data['data']);die;
            }

        }
die;

        return $order;
    }

    public function renderPaymentForm(Delivery $method, Order $order)
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
            // '{AMOUNT}' => 1,
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
        $this->setSettings($paymentMethodId, $postData['NovaPoshtaConfigurationModel']);
    }

    /**
     * @param $paymentMethodId
     * @return string
     */
    public function getSettingsKey($paymentMethodId)
    {
        return $paymentMethodId . '_NovaPoshtaDeliverySystem';
    }

    /**
     * Get configuration form to display in admin panel
     * @param $paymentMethodId
     * @return NovaPoshtaConfigurationModel
     */
    public function getConfigurationFormHtml($paymentMethodId)
    {
        $model = new NovaPoshtaConfigurationModel;
        $model->load([basename(get_class($model)) => (array)$this->getSettings($paymentMethodId)]);

        return $model;
    }

}
