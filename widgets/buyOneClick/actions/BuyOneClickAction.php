<?php

namespace panix\mod\cart\widgets\buyOneClick\actions;

use panix\engine\CMS;
use panix\mod\cart\models\forms\OrderCreateForm;
use panix\mod\cart\models\Order;
use panix\mod\cart\models\OrderProduct;
use yii\base\Action;
use panix\mod\shop\models\Product;
use Yii;
use yii\web\HttpException;
use yii\web\Response;

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
        $result['success']=false;
        $quantity = Yii::$app->request->post('quantity');
        if (Yii::$app->request->isAjax) {

            $productModel = Product::findOne(Yii::$app->request->get('id'));
            if (!$productModel) {
                throw new HttpException(404);
            }
            //
            $model = new OrderCreateForm();
            $model->setScenario('buyOneClick');
            $post = Yii::$app->request->post();
            if ($model->load($post)) {
                if ($model->validate()) {
                    $order = $this->createOrder($model, $productModel);
                    $order->sendAdminEmail();
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    $result['success'] = true;
                    $result['message'] = Yii::t('cart/default', 'SUCCESS_ORDER');
                    return $result;
                }
            }
            $path = Yii::$app->assetManager->getPublishedUrl('@bower/intl-tel-input/build');

            $this->controller->view->registerJsFile($path.'/js/utils.js');
            return $this->controller->render('@cart/widgets/buyOneClick/views/_form', [
                'model' => $model,
                'productModel' => $productModel,
                'quantity' => (is_numeric($quantity)) ? $quantity : 1
            ]);
        } else {
            throw new HttpException(404);
        }
    }

    /**
     * @param $model OrderCreateForm
     * @param $productModel Product
     * @return Order
     */
    public function createOrder($model, $productModel)
    {

        $order = new Order();
        $order->setScenario('buyOneClick');
        $user = Yii::$app->user;
        // Set main data
        $order->user_id = Yii::$app->user->isGuest ? null : Yii::$app->user->id;
        $order->user_name = $user->getUsername();
        $order->user_email = $user->email;
        $order->user_phone = $model->user_phone;
        $order->status_id = 1;
        $order->buyOneClick = 1;
        //  $order->user_address = $this->form->user_address;


        if ($order->validate()) {
            $order->save(false);
        } else {
            print_r($order->getErrors());
            die;
            //throw new CHttpException(503, Yii::t('CartModule.default', 'ERROR_CREATE_ORDER'));
        }


        $price = 0;
        $ordered_product = new OrderProduct();
        $ordered_product->order_id = $order->id;
        $ordered_product->product_id = $productModel->id;


        $ordered_product->currency_id = $productModel->currency_id;
        $ordered_product->supplier_id = $productModel->supplier_id;
        $ordered_product->name = $productModel->name;
        $ordered_product->quantity = $model->quantity;
        $ordered_product->sku = $productModel->sku;
        // if($item['currency_id']){
        //     $currency = Currency::model()->findByPk($item['currency_id']);
        //$ordered_product->price = ShopProduct::calculatePrices($item['model'], $item['variant_models'], $item['configurable_id']) * $currency->rate;
        // }else{
        // 
        // $category = ShopCategory::model()->findByPk($item['category_id']);
        //  $options = $item['options'];
        if (isset($productModel->hasDiscount)) {

            $price += $productModel->discountPrice;
        } else {
            $price += $productModel->price;
        }


        $ordered_product->price = $price;
        $ordered_product->save();
        return $order;
    }

}
