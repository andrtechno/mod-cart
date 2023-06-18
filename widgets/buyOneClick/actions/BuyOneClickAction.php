<?php

namespace panix\mod\cart\widgets\buyOneClick\actions;

use Yii;
use yii\web\HttpException;
use yii\web\Response;
use yii\base\Action;
use panix\mod\cart\models\Order;
use panix\mod\cart\models\OrderProduct;
use panix\mod\shop\models\Attribute;
use panix\mod\shop\models\Product;

/**
 * Форма купить в один клик.
 *
 * @author PIXELION CMS development team <dev@pixelion.com.ua>
 * @link http://pixelion.com.ua PIXELION CMS
 * @package modules
 * @subpackage commerce.cart.widgets.buyOneClick.actions
 * @uses CAction
 *
 * @property array $receiverMail Массив почты на которые будут отправлены уведомление
 * @todo Нужно доработать, добавление в админку заказа.
 */
class BuyOneClickAction extends Action
{

    public function run()
    {
        $result['success'] = false;
        $quantity = (int)Yii::$app->request->post('quantity', 1);
        $configurable_id = Yii::$app->request->post('configurable_id');
        if (Yii::$app->request->isAjax) {
            Yii::$app->assetManager->bundles = [
                //'yii\bootstrap\BootstrapPluginAsset' => false,
                //'yii\bootstrap\BootstrapAsset' => false,
                'yii\jui\JuiAsset' => false,
                'yii\web\YiiAsset' => false,
                'yii\web\JqueryAsset' => false,
                //'panix\ext\telinput\Asset' => false,
            ];
            if ($configurable_id) {
                $productModel = Product::findOne($configurable_id);
            } else {
                $productModel = Product::findOne(Yii::$app->request->get('id'));
            }

            if (!$productModel) {
                throw new HttpException(404, '404!');
            }
            //
            $model = new Order();
            $model->setScenario('buyOneClick');

            $post = Yii::$app->request->post();
            if ($model->load($post)) {
                if ($model->validate()) {
                    $order = $this->createOrder($model, $productModel, $quantity, $configurable_id);
//print_r($order);die;
                    if (Yii::$app->settings->get('seo', 'google_tag_manager')) {
                        $result['data']['order_id'] = $order->id;
                        $result['data']['total'] = $order->full_price;
                        foreach ($order->products as $item) {
                            $result['data']['products'][] = [
                                'id' => $item->product_id,
                                'name' => $item->name,
                                'price' => $item->price,
                                'quantity' => $item->quantity
                            ];
                        }
                    }
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    $result['success'] = true;
                    $result['message'] = Yii::t('cart/default', 'SUCCESS_ORDER');
                    return $result;
                }
            }
            $path = Yii::$app->assetManager->getPublishedUrl('@bower/intl-tel-input/build');

            $this->controller->view->registerJsFile($path . '/js/utils.js');
            return $this->controller->render(Yii::$app->getModule('cart')->buyOneClick['skinForm'], [
                'model' => $model,
                'productModel' => $productModel,
                'quantity' => $quantity,
                'configurable_id' => ($configurable_id) ? $configurable_id : 0
            ]);
        } else {
            throw new HttpException(404, 'error!');
        }
    }

    /**
     * @param $model Order
     * @param $productModel Product
     * @param $quantity integer
     * @param $configurable_id integer
     * @return Order
     */
    public function createOrder($model, $productModel, $quantity, $configurable_id)
    {

        $order = new Order();
        $order->setScenario('buyOneClick');
        $user = Yii::$app->user;
        // Set main data
        $order->user_id = Yii::$app->user->isGuest ? null : Yii::$app->user->id;
        $order->user_name = $user->getFirstname();
        $order->user_email = $user->email;
        $order->user_lastname = $user->getLastname();
        $order->user_phone = $model->user_phone;
        $order->status_id = Order::STATUS_NEW;
        $order->buyOneClick = 1;


        if ($order->validate()) {
            $order->save(false);
        } else {
            print_r($order->getErrors());
            die;
            //throw new CHttpException(503, Yii::t('CartModule.default', 'ERROR_CREATE_ORDER'));
        }


        $ordered_product = new OrderProduct();
        $ordered_product->order_id = $order->id;
        $ordered_product->product_id = $productModel->id;
        $ordered_product->currency_id = $productModel->currency_id;
        $ordered_product->supplier_id = $productModel->supplier_id;
        $ordered_product->configurable_id = $configurable_id;
        if ($ordered_product->currency_id)
            $ordered_product->currency_rate = Yii::$app->currency->getById($ordered_product->currency_id)->rate;
        $ordered_product->name = $productModel->name;
        $ordered_product->quantity = $quantity;
        $ordered_product->sku = $productModel->sku;

        if ($productModel->in_box) {
            $ordered_product->price_purchase = Yii::$app->currency->convert($productModel->price_purchase * $productModel->in_box, $ordered_product->currency_id);
        } else {
            $ordered_product->price_purchase = Yii::$app->currency->convert($productModel->price_purchase, $ordered_product->currency_id);
        }


        // if($item['currency_id']){
        //     $currency = Currency::model()->findByPk($item['currency_id']);
        //$ordered_product->price = ShopProduct::calculatePrices($item['model'], $item['variant_models'], $item['configurable_id']) * $currency->rate;
        // }else{
        //

        //  $options = $item['options'];
        if (isset($productModel->hasDiscount)) {

            // $ordered_product->price += $productModel->discountPrice;
        } else {
            // $ordered_product->price += $productModel->price;
        }
        $ordered_product->price = Product::calculatePrices($productModel, [], $configurable_id);


        /* if (isset($productModel) && $productModel instanceof Product) {
             $configurable_data = [];

             $ordered_product->configurable_name = $productModel->name;
             // Use configurable product sku
             $ordered_product->sku = $productModel->sku;
             // Save configurable data

             $attributeModels = Attribute::find()
                 ->where(['id' => $productModel->configurable_attributes])->all();
             //->findAllByPk($item['model']->configurable_attributes);
             foreach ($attributeModels as $attribute) {
                 $method = 'eav_' . $attribute->name;
                 $configurable_data[$attribute->title] = $productModel->$method;
             }
             $ordered_product->configurable_data = serialize($configurable_data);
         }

         // Save selected variants as key/value array
         if (!empty($item['variant_models'])) {
             $variants = [];
             foreach ($item['variant_models'] as $variant)
                 $variants[$variant->productAttribute->title] = $variant->option->value;
             $ordered_product->variants = serialize($variants);
         }*/


        //$ordered_product->price = $price;
        $ordered_product->save();


        $order->refresh();
        $order->updateDeliveryPrice();
        $order->updateTotalPrice();
        $order->sendAdminEmail(explode(',', Yii::$app->settings->get('cart', 'order_emails')));
        //Yii::$app->user->unsetPoints($this->order->points);
        return $order;
    }

}
