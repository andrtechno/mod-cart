<?php

Yii::import('mod.cart.widgets.buyOneClick.BuyOneClickForm');
Yii::import('mod.cart.widgets.buyOneClick.BuyOneClickWidget');

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
class BuyOneClickAction extends CAction {

    public $receiverMail = array('notify@pixelion.com.ua');

    public function run() {

        $quantity = Yii::$app->request->getParam('quantity');
        if (Yii::$app->request->isAjax) {
            $productModel = Product::model()->findByPk(Yii::$app->request->getParam('id'));
            if (!$productModel) {
                throw new CHttpException(404);
            }

            $model = new BuyOneClickForm();
            $sended = false;
            if (isset($_POST['BuyOneClickForm'])) {
                $model->attributes = $_POST['BuyOneClickForm'];
                if ($model->validate()) {
                    $sended = true;
                    $this->sendMessage($model, $productModel);
                    $this->createOrder($model, $productModel);
                    $model->unsetAttributes();
                }
            }
            $this->controller->render('mod.cart.widgets.buyOneClick.views._form', array(
                'model' => $model,
                'sended' => $sended,
                'productModel' => $productModel,
                'quantity' => (is_numeric($quantity)) ? $quantity : 1
            ),false,true);
        } else {
            throw new CHttpException(403);
        }
    }

    public function createOrder($model, $productModel) {
        Yii::import('mod.cart.models.Order');
        Yii::import('mod.cart.models.OrderProduct');
        $order = new Order();

        $user = Yii::$app->user;
        // Set main data
        $order->user_id = Yii::$app->user->isGuest ? null : Yii::$app->user->id;
        $order->user_name = $user->getUsername();
        $order->user_email = $user->email;
        $order->user_phone = $model->phone;
        $order->status_id = 1;
        $order->buyOneClick = 1;
        //  $order->user_address = $this->form->user_address;


        if ($order->validate(false)) {
            $order->save();
        } else {
            print_r($order->getErrors());
            die;
            //throw new CHttpException(503, Yii::t('CartModule.default', 'ERROR_CREATE_ORDER'));
        }


        $price = 0;
        $ordered_product = new OrderProduct;
        $ordered_product->order_id = $order->id;
        $ordered_product->product_id = $productModel->id;
        //$ordered_product->category_id = $item['category_id'];




        $ordered_product->currency_id = $productModel->currency_id;
        $ordered_product->supplier_id = $productModel->supplier_id;
        $ordered_product->name = $productModel->name;
        $ordered_product->quantity = $model->quantity;
        $ordered_product->sku = $productModel->sku;
        $ordered_product->date_create = $order->date_create;
        // if($item['currency_id']){
        //     $currency = Currency::model()->findByPk($item['currency_id']);
        //$ordered_product->price = ShopProduct::calculatePrices($item['model'], $item['variant_models'], $item['configurable_id']) * $currency->rate;
        // }else{
        // 
        // $category = ShopCategory::model()->findByPk($item['category_id']);
        //  $options = $item['options'];
        if (isset($productModel->hasDiscount)) {

            $price += $productModel->toCurrentCurrency('discountPrice');
        } else {
            $price += $productModel->priceRange();
        }





        $ordered_product->price = $price;
        $ordered_product->save();
    }

    /**
     * Оптравка письма на почту получателей.
     * @param ByOnClickForm $model
     */
    private function sendMessage($model, $productModel) {
        $currency = Yii::$app->currency->active['symbol'];
        $request = Yii::$app->request;



        $params = array();

        $params['th_name'] = Yii::t('CartModule.default', 'TABLE_TH_MAIL_NAME');
        $params['th_quantity'] = Yii::t('CartModule.default', 'TABLE_TH_MAIL_QUANTITY', 1);
        $params['th_price'] = Yii::t('CartModule.default', 'TABLE_TH_MAIL_PRICE');


        $params['quantity'] = $model->quantity;
        $params['phone'] = $model->phone;
        $params['name'] = $productModel->name;
        $params['image'] = Yii::$app->controller->createAbsoluteUrl($productModel->getMainImageUrl('50x50'));
        $params['url'] = $productModel->getAbsoluteUrl();
        $params['price'] = $productModel->priceRange();
        $params['currency'] = $currency;
        if (isset($productModel->originalPrice)) {
            $params['originalPrice'] = Yii::$app->currency->number_format($productModel->toCurrentCurrency('originalPrice'));
        } else {
            $params['originalPrice'] = false;
        }
        if (isset($productModel->hasDiscount)) {
            $params['hasDiscount'] = $productModel->hasDiscount;
        } else {
            $params['hasDiscount'] = false;
        }

        $config = Yii::$app->settings->get('app');
        $mailer = Yii::$app->mail;
        $mailer->From = 'noreply@' . $request->serverName;
        $mailer->FromName = $config['site_name'];
        $mailer->Subject = Yii::t('BuyOneClickWidget.default', 'MAIL_SUBJECT');
        $mailer->Body = Yii::$app->etpl->template_path($params, Yii::getPathOfAlias('mod.cart.widgets.buyOneClick.views') . DS . '_email_template.tpl');

        $configCart = Yii::$app->settings->get('cart');
        $this->receiverMail = explode(',', $configCart['order_emails']);

        foreach ($this->receiverMail as $mail) {
            $mailer->AddAddress($mail);
        }
        $mailer->AddReplyTo('noreply@' . $request->serverName);
        $mailer->isHtml(true);
        $mailer->Send();
        $mailer->ClearAddresses();
    }

}
