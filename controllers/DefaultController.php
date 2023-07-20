<?php

namespace panix\mod\cart\controllers;

use panix\engine\bootstrap\ActiveForm;
use panix\engine\CMS;
use panix\mod\cart\CartAsset;
use panix\mod\cart\components\delivery\DeliverySystemManager;
use panix\mod\cart\components\OrderEvent;
use panix\mod\cart\Module;
use panix\mod\cart\widgets\delivery\address\AddressModel;
use panix\mod\novaposhta\models\Cities;
use panix\mod\novaposhta\models\Warehouses;
use panix\mod\shop\models\Attribute;
use panix\mod\user\models\forms\LoginForm;
use Yii;
use yii\base\ActionEvent;
use yii\base\Event;
use yii\base\Exception;
use yii\db\ActiveRecord;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\HttpException;
use panix\engine\controllers\WebController;
use panix\mod\cart\models\forms\OrderCreateForm;
use panix\mod\cart\models\Delivery;
use panix\mod\cart\models\Payment;
use panix\mod\cart\models\Order;
use panix\mod\cart\models\OrderProduct;
use panix\mod\shop\models\Product;
use panix\mod\cart\models\search\OrderSearch;
use panix\mod\shop\models\ProductVariant;
use yii\web\Response;

class DefaultController extends WebController
{

    /**
     * @var OrderCreateForm
     */
    public $form;

    /**
     * @var bool
     */
    protected $_errors = false;
    public $order;
    protected $delivery;

    public function actions()
    {
        return [
            'promoCode' => [
                'class' => 'panix\mod\cart\widgets\promocode\PromoCodeAction',
            ],
            'buyOneClick' => [
                'class' => 'panix\mod\cart\widgets\buyOneClick\actions\BuyOneClickAction',
            ],
        ];
    }

    public function beforeAction($action)
    {

        if (in_array($this->action->id, ['add', 'popup', 'recount'])) {
            $this->enableCsrfValidation = false;
        }
        return parent::beforeAction($action);
    }

    public function actionRecount()
    {
        if (Yii::$app->request->isAjax) {
            if (Yii::$app->request->isPost && !empty($_POST['quantities'])) {
                $params = [];
                $params[Yii::$app->request->post('product_id')] = Yii::$app->request->post('quantities');
                return $this->asJson(Yii::$app->cart->ajaxRecount($params));
            }
        } else {
            throw new ForbiddenHttpException(Yii::t('app/error', '403'));
        }
    }

    public function actionPopup()
    {
        if (Yii::$app->request->isAjax) {
            $cart = Yii::$app->cart;
            $items = $cart->getDataWithModels();

            //$this->module->modalView
            return $this->renderAjax(Yii::$app->getModule('cart')->modalBodyView, [
                'total' => $cart->getTotalPrice(),
                'items' => isset($items['items']) ? $items['items'] : [],
                'popup' => true
            ]);
        } else {
            throw new ForbiddenHttpException(Yii::t('app/error', '403'));
        }
    }

    public function actionPreCheckout()
    {
        $this->pageName = Yii::t('cart/default', 'MODULE_NAME');
        $this->view->title = $this->pageName;
        $this->view->params['breadcrumbs'] = [$this->pageName];

        if (Yii::$app->request->isPost && Yii::$app->request->post('recount') && !empty($_POST['quantities'])) {
            $this->processRecount();
        }

        $this->view->registerJs("
            var penny = '" . Yii::$app->currency->active['penny'] . "';
            var separator_thousandth = '" . Yii::$app->currency->active['separator_thousandth'] . "';
            var separator_hundredth = '" . Yii::$app->currency->active['separator_hundredth'] . "';
        ", yii\web\View::POS_HEAD, 'numberformat');

        return $this->render('pre-chekout', [
            'items' => Yii::$app->cart->getDataWithModels(),
            'totalPrice' => Yii::$app->cart->getTotalPrice(),
        ]);
    }

    /**
     * Display list of product added to cart
     */
    public function actionIndex()
    {
        $this->pageName = Yii::t('cart/default', 'MODULE_NAME');
        $this->view->title = $this->pageName;
        $this->view->params['breadcrumbs'] = [$this->pageName];

        if (Yii::$app->request->isPost && Yii::$app->request->post('recount') && !empty($_POST['quantities'])) {
            $this->processRecount();
        }
        //$this->form = new OrderCreateForm(); //['scenario' => 'create-form-order']
        $this->order = new Order;

        $user = Yii::$app->user;
        if (!$user->isGuest) {
            // NEED CONFINGURE
            $this->order->user_name = $user->firstname;
            $this->order->user_phone = $user->phone;
            //$this->delivery_address = Yii::app()->user->address; //comment for april
            $this->order->user_email = $user->getEmail();
            $this->order->user_lastname = $user->lastname;
            $this->order->points = (isset(Yii::$app->cart->session['cart_data']['bonus'])) ? Yii::$app->cart->session['cart_data']['bonus'] : 0;
        }


        $this->order->setScenario('create_order');
        if (Yii::$app->user->isGuest) {
            //$this->form->setScenario('guest');
            $this->order->setScenario('create_order_guest');
        }

        $post = Yii::$app->request->post();
        if ($this->order->isNewRecord && !$post) {
            $this->order->delivery_id = Yii::$app->settings->get('cart', 'delivery_id');
        }
        /*if (Yii::$app->user->isGuest) {
             $modelLogin = new LoginForm();
             $config = Yii::$app->settings->get('user');

             if (Yii::$app->request->isAjax) {
                 Yii::$app->response->format = Response::FORMAT_JSON;
                 return ActiveForm::validate($modelLogin);
             }

             if ($modelLogin->load(Yii::$app->request->post()) && $modelLogin->login($config->login_duration * 86400)) {
                 return $this->goBack(['/cart/default/index']);
             }
         }*/


        if ($this->order->load($post)) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($this->order);
            }
            $this->delivery = Delivery::findOne($this->order->delivery_id);
            if ($this->delivery->system) {
                $manager = new DeliverySystemManager();
                $system = $manager->getSystemClass($this->delivery->system);
                $deliveryModel = $system->getModel();

                if ($this->delivery->system == 'novaposhta') {
                    if (isset($post['DynamicModel'])) {

                        if ($deliveryModel->load($post)) {
                            // CMS::dump($deliveryModel);
                            //die;
                            //if (isset($post['DynamicModel']['type'])) {
                            if ($deliveryModel->type == 'warehouse') {
                                //if ($post['DynamicModel']['type'] == 'warehouse') {
                                $deliveryModel->addRule(['warehouse'], 'required');
                            } else {
                                $deliveryModel->addRule(['address'], 'required');
                            }
                            //}
                            if (Yii::$app->request->isAjax) {
                                Yii::$app->response->format = Response::FORMAT_JSON;
                                return ActiveForm::validate($deliveryModel);
                            }
                            $this->order->deliveryModel = $deliveryModel;
                        }
                    }
                } elseif ($this->delivery->system == 'pickup') {
                    if (isset($post['DynamicModel'])) {
                        if ($deliveryModel->load($post)) {
                            if (Yii::$app->request->isAjax) {
                                Yii::$app->response->format = Response::FORMAT_JSON;
                                return ActiveForm::validate($deliveryModel);
                            }
                            $this->order->deliveryModel = $deliveryModel;
                        }
                    }
                } elseif ($this->delivery->system == 'address') {
                    if (isset($post['AddressModel'])) {
                        //$deliveryModel = new AddressModel();
                        if ($deliveryModel->load($post)) {
                            if (Yii::$app->request->isAjax) {
                                Yii::$app->response->format = Response::FORMAT_JSON;
                                return ActiveForm::validate($deliveryModel);
                            }
                            $this->order->deliveryModel = $deliveryModel;
                        }
                    }
                }
            }


            $order = $this->createOrder();

            if ($order instanceof Order) {
                Yii::$app->cart->clear();
                Yii::$app->session->setFlash('success', Yii::t('cart/default', 'SUCCESS_ORDER'));
                return $this->redirect(['view', 'secret_key' => $order->secret_key]);
            } elseif ($order == 'error') {

                //return $this->refresh();
            } else {
                //return $this->redirect(['index']);
                return $this->refresh();
            }
        } else {
            // print_r($this->form->errors);die;
        }


        $deliveryMethods = Delivery::find()
            ->published()
            ->all();
        // echo($deliveryMethods->prepare(Yii::$app->db->queryBuilder)->createCommand()->rawSql);die;


        $paymentMethods = Payment::find()->all();

        $this->view->registerJs("
            var penny = '" . Yii::$app->currency->active['penny'] . "';
            var separator_thousandth = '" . Yii::$app->currency->active['separator_thousandth'] . "';
            var separator_hundredth = '" . Yii::$app->currency->active['separator_hundredth'] . "';
        ", yii\web\View::POS_HEAD, 'numberformat');

        $items = Yii::$app->cart->getDataWithModels();
        $totalPrice = Yii::$app->cart->getTotalPrice();
        $counter = Yii::$app->cart->countItems();
        if (Yii::$app->settings->get('seo', 'google_tag_manager') && isset($items['items'])) {
            $dataLayer['ecomm_pagetype'] = 'conversionintent';
            $dataLayer['ecomm_totalvalue'] = (string)$totalPrice;
            foreach ($items['items'] as $item) {
                $dataLayer['ecomm_prodid'][] = $item['product_id'];
            }
            $this->view->params['gtm_ecomm'] = $dataLayer;

        }

        return $this->render('index', [
            'items' => isset($items['items']) ? $items['items'] : [],
            'totalPrice' => $totalPrice,
            'counter' => $counter,
            'deliveryMethods' => $deliveryMethods,
            'paymentMethods' => $paymentMethods,
        ]);
    }

    public function actionPayment()
    {
        if (isset($_POST)) {
            $this->form = Payment::find()->all();
            echo $this->render('_payment', ['model' => $this->form]);
        }
    }

    /**
     * Find order by secret_key and display.
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionView()
    {
        $secret_key = Yii::$app->request->get('secret_key');
        $model = Order::find()->where(['secret_key' => $secret_key])->one();
        if (!$model)
            $this->error404(Yii::t('cart/default', 'ERROR_ORDER_NO_FIND'));


        if (!Yii::$app->user->can('admin')) {
            if ((Yii::$app->user->id != $model->user_id) || (Yii::$app->request->remoteIP != $model->ip_create)) {
                return $this->redirect(['/site/index']);
            }
        }

        $post = Yii::$app->request->post();
        if ($post) {
            if ($model->load($post)) {
                if ($model->validate()) {
                    //$model->save();
                    $model->updateTotalPrice();
                    $model->updateDeliveryPrice();
                    //Yii::$app->session->setFlash('success-promocode','YAhhoo');
                    //Yii::$app->session->addFlash('success-promocode','YAhhoo');
                    $this->refresh();
                }
            }
            // print_r($post);
            //  die;
        }

        $this->pageName = Yii::t('cart/default', 'VIEW_ORDER', ['id' => CMS::idToNumber($model->id)]);
        $this->view->params['breadcrumbs'][] = $this->pageName;


        $items = $model->getOrderedProducts()->getModels();
        if (Yii::$app->settings->get('seo', 'google_tag_manager')) {
            $dataLayer['ecomm_pagetype'] = 'conversion';
            $dataLayer['ecomm_totalvalue'] = $model->full_price;

            $transaction['event'] = 'transaction';
            $transaction['transactionId'] = $model->id;
            $transaction['transactionAffiliation'] = Yii::$app->settings->get('app', 'sitename');
            $transaction['transactionTotal'] = $model->full_price;

            foreach ($items as $item) {
                $dataLayer['ecomm_prodid'][] = $item->product_id;
                $transaction['transactionProducts'][] = [
                    'sku' => $item->product_id,
                    'name' => $item->name,
                    'price' => $item->price,
                    'quantity' => $item->quantity
                ];
            }
            $this->view->params['gtm_ecomm'] = $dataLayer;
            $transaction = Json::encode($transaction);


            $this->view->registerJs("
            window.dataLayer = window.dataLayer || [];
            dataLayer.push($transaction);", $this->view::POS_BEGIN, 'dataLayer_transaction');

        }

        return $this->render('view', [
            'model' => $model,
            'items' => $items
        ]);
    }

    /**
     * Validate POST data and add product to cart
     * @throws BadRequestHttpException
     */
    public function actionAdd()
    {
        $product_id = Yii::$app->request->post('product_id', 0);
        $cart = Yii::$app->cart;
        if ($cart->hasIndex($product_id)) {
            $data = [
                'errors' => true,
                'status' => 'already_exists',
                'message' => 'Товар уже в корзине.',
                'url' => Url::to($this->module->homeUrl)
            ];
            return $this->asJson($data);
        }
        if (!Yii::$app->request->isAjax) {
            throw new BadRequestHttpException(Yii::t('app/default', 'ACCESS_DENIED'));
        }


        $variants = [];

        $productClass = Yii::$app->getModule('shop')->model('Product');
        // Load product model
        /** @var Product $model */
        $model = $productClass::findOne($product_id);

        // Check product
        if (!isset($model))
            return $this->_addError(Yii::t('cart/default', 'ERROR_PRODUCT_NO_FIND'), true);


        // Process variants
        if (!empty($_POST['eav'])) {
            foreach ($_POST['eav'] as $attribute_id => $variant_id) {
                if (!empty($variant_id)) {
                    // Check if attribute/option exists
                    if (!$this->_checkVariantExists($_POST['product_id'], $attribute_id, $variant_id))
                        return $this->_addError(Yii::t('cart/default', 'ERROR_VARIANT_NO_FIND'));
                    else
                        array_push($variants, $variant_id);
                }
            }
        }

        // Process configurable products
        //  if ($model->use_configurations) {
        // Get last configurable item
        $configurable_id = Yii::$app->request->post('configurable_id', 0);

//if($configurable_id != $model->id){
        // if (!$configurable_id || !in_array($configurable_id, $model->configurations))
        //     return $this->_addError(Yii::t('cart/default', 'ERROR_SELECT_VARIANT'), true);
//}
        //  } else
        //      $configurable_id = 0;


        // Update counter
        $model->updateCounters(['added_to_cart_count' => 1]);


        // print_r($items);die;
        $cart->add([
            'product_id' => $model->id,
            'variants' => $variants,
            'attributes_data' => json_encode([
                'data' => $model->eavData['data'],
                'attributes' => $model->eavAttributes
            ], JSON_UNESCAPED_UNICODE),
            'currency_id' => $model->currency_id,
            'supplier_id' => $model->supplier_id,
            'in_box' => $model->in_box,
            'weight' => $model->weight,
            'height' => $model->height,
            'length' => $model->length,
            'width' => $model->width,
            'weight_class_id' => $model->weight_class_id,
            'length_class_id' => $model->length_class_id,
            'configurable_id' => $configurable_id,
            'quantity' => (int)Yii::$app->request->post('quantity', (Yii::$app->settings->get('cart', 'quantity_convert')) ? $model->in_box : 1),
            'price' => $model->price
        ]);
        $totalPrice = $cart->getTotalPrice();
        $items = $cart->getDataWithModels();

        $data = [
            'errors' => $this->_errors,
            'message' => Yii::t('cart/default', 'SUCCESS_ADDCART', [
                'product_name' => $model->name
            ]),
            'status' => 'success',
            /*'html' => $this->renderAjax('popup', [
                'totalPrice' => $totalPrice,
                'items' => $items,
            ]),*/
            'total_price' => $totalPrice,
            'total_price_format' => Yii::$app->currency->number_format($totalPrice),
            'countItems' => $cart->countItems(),
            'buttonText' => Yii::t('cart/default', 'BUTTON_ALREADY_CART'),
            'url' => Url::to($this->module->homeUrl)
        ];
        return $this->asJson($data);


    }

    /**
     * Remove product from cart and redirect
     * @param $id
     * @return array|Response
     */
    public function actionRemove()
    {
        $id = Yii::$app->request->get('id');
        $popup = Yii::$app->request->get('popup');
        $cart = Yii::$app->cart;

        if (!Yii::$app->request->isAjax) {
            return $this->redirect(Yii::$app->homeUrl);

        } else {
            if ($cart->hasIndex($id)) {
                $cart->remove($id);
                $result['success'] = true;
                $result['message'] = Yii::t('cart/default', 'SUCCESS_PRODUCT_CART_DELETE');
            } else {
                $result['success'] = false;
                $result['message'] = Yii::t('cart/default', 'ERROR_PRODUCT_NO_FIND');
            }
            $countItems = $cart->countItems();

            if (!$popup && !$countItems['quantity']) {
                //return $this->redirect(Yii::$app->homeUrl);
                return $this->redirect(['/cart/default/index']);
            }
            $total = $cart->getTotalPrice();
            $result['id'] = $id;

            $result['button_text_already'] = Yii::t('cart/default', 'BUTTON_ALREADY_CART');
            $result['button_text_add'] = Yii::t('cart/default', 'BUY');
            $result['total_price'] = Yii::$app->currency->number_format($total);
            $result['countItems'] = $countItems;
            $result['reload'] = ($total) ? false : true;
            if(!$total){
                $result['emptyHtml'] = $this->render(Yii::$app->getModule('cart')->emptyView);
            }


            return $this->asJson($result);
        }
    }

    /**
     * Clear cart
     */
    public function actionClear()
    {
        Yii::$app->cart->clear();
        if (!Yii::$app->request->isAjax)
            return $this->redirect(['index']);
    }

    /**
     * Render data to display in theme header.
     * @throws BadRequestHttpException
     */
    public function actionRenderSmallCart()
    {
        if (!Yii::$app->request->isAjax) {
            throw new BadRequestHttpException(Yii::t('app/default', 'ACCESS_DENIED'));
        }
        return \panix\mod\cart\widgets\cart\CartWidget::widget(['skin' => Yii::$app->request->post('skin')]);
    }

    /**
     * Create new order
     * @return Order|boolean
     * @throws Exception
     */
    public function createOrder()
    {
        /** @var $form OrderCreateForm */
        if (Yii::$app->cart->countItems() == 0) {

            return false;
        }


        /*$order = new Order;

        // Set main data
        $order->user_id = Yii::$app->user->isGuest ? null : Yii::$app->user->id;
        $order->user_name = $this->form->user_name;
        $order->user_email = $this->form->user_email;
        $order->user_lastname = $this->form->user_lastname;
        $order->user_phone = $this->form->user_phone;
        $order->delivery_address = $this->form->delivery_address;
        $order->user_comment = $this->form->user_comment;
        $order->delivery_id = $this->form->delivery_id;
        $order->payment_id = $this->form->payment_id;
        $order->promocode_id = $this->form->promocode_id;
        $order->call_confirm = $this->form->call_confirm;
        $order->points = $this->form->points;*/

        //if (isset($this->order->delivery_type))
        //    $this->order->delivery_type = $this->order->delivery_type;
        //$order->status_id = 1; //set New status


        //$delivery = Delivery::findOne($this->order->delivery_id);

        $cartItems = Yii::$app->cart->getDataWithModels();
        $this->order->status_id = Order::STATUS_NEW;
        if ($this->order->validate()) {

            if ($this->order->points > 0) {
                $this->order->discount = $this->order->points;
            }
            $this->order->save();
            if (Yii::$app->getModule('cart')->hasEventHandlers(Module::EVENT_ORDER_CREATE)) {
                $event = new OrderEvent();
                $event->order = $this->order;
                Yii::$app->getModule('cart')->trigger(Module::EVENT_ORDER_CREATE, $event);
            }
        } else {
            //print_r($this->order->getErrors());die;
            Yii::$app->session->setFlash('error', Yii::t('cart/default', 'SUCCESS_ORDER'));
            return 'error';
            //echo 'ERRR';
            //print_r($this->order->getErrors());
            //die;
            //throw new Exception(503, Yii::t('cart/default', 'ERROR_CREATE_ORDER'));
        }

        // Process products
        $productsCount = 0;

        foreach ($cartItems['items'] as $item) {

            $ordered_product = new OrderProduct;
            $ordered_product->order_id = $this->order->id;
            $ordered_product->user_id = $this->order->user_id;
            $ordered_product->product_id = $item['model']->id;
            $ordered_product->type_id = $item['model']->type_id;
            $ordered_product->configurable_id = $item['configurable_id'];
            $ordered_product->currency_id = $item['model']->currency_id;
            $ordered_product->supplier_id = $item['model']->supplier_id;
            $ordered_product->brand_id = $item['model']->brand_id;
            $ordered_product->discount = $item['model']->discount;
            $ordered_product->in_box = $item['model']->in_box;
            $ordered_product->unit = $item['model']->unit;
            if ($ordered_product->currency_id)
                $ordered_product->currency_rate = Yii::$app->currency->getById($ordered_product->currency_id)->rate;


            $ordered_product->price_purchase = Yii::$app->currency->convert($item['model']->price_purchase, $ordered_product->currency_id);
            $ordered_product->name = $item['model']->name;
            $ordered_product->quantity = $item['quantity'];
            $ordered_product->sku = $item['model']->sku;
            $ordered_product->attributes_data = json_encode($item['attributes_data'], JSON_UNESCAPED_UNICODE);
            $ordered_product->weight = $item['weight'];
            $ordered_product->height = $item['height'];
            $ordered_product->length = $item['length'];
            $ordered_product->width = $item['width'];
            $ordered_product->weight_class_id = $item['weight_class_id'];
            $ordered_product->length_class_id = $item['length_class_id'];

            $productClass = Yii::$app->getModule('shop')->model('Product');
            // if($item['currency_id']){
            //     $currency = Currency::model()->findByPk($item['currency_id']);
            //$ordered_product->price = Product::calculatePrices($item['model'], $item['variant_models'], $item['configurable_id']) * $currency->rate;
            // }else{
            $ordered_product->price = $productClass::calculatePrices($item['model'], $item['variant_models'], $item['configurable_id']);
            // $ordered_product->price = $item['model']->price;
            // }


            if (isset($item['configurable_model']) && $item['configurable_model'] instanceof Product) {
                $configurable_data = [];

                $ordered_product->configurable_name = $item['configurable_model']->name;
                // Use configurable product sku
                $ordered_product->sku = $item['configurable_model']->sku;
                // Save configurable data

                $attributeModels = Attribute::find()
                    ->where(['id' => $item['model']->configurable_attributes])->all();
                //->findAllByPk($item['model']->configurable_attributes);
                foreach ($attributeModels as $attribute) {
                    $method = 'eav_' . $attribute->name;
                    $configurable_data[$attribute->title] = $item['configurable_model']->$method;
                }
                $ordered_product->configurable_data = serialize($configurable_data);
            }

            // Save selected variants as key/value array
            if (!empty($item['variant_models'])) {
                $variants = [];
                foreach ($item['variant_models'] as $variant)
                    $variants[$variant->productAttribute->title] = $variant->option->value;
                $ordered_product->variants = serialize($variants);
            }


            $ordered_product->save();
            $productsCount++;
        }

        // Reload order data.
        $this->order->refresh(); //@todo panix text email tpl
        // All products added. Update delivery price.
        $this->order->updateDeliveryPrice();
        //$order->updateTotalPrice();
        $text = (Yii::$app->user->isGuest) ? 'NOTIFICATION_GUEST_TEXT' : 'NOTIFICATION_USER_TEXT';
        $this->order->attachBehavior('notification', [
            'class' => 'panix\engine\behaviors\NotificationBehavior',
            'type' => 'success',
            'url' => Url::to($this->order->getUpdateUrl()),
            'sound' => CartAsset::register($this->view)->baseUrl . '/notification_new-order.mp3',
            'text' => Yii::t('cart/default', $text, [
                'num' => $productsCount,
                'total' => Yii::$app->currency->number_format($this->order->total_price),
                'currency' => Yii::$app->currency->active['symbol'],
                'username' => Yii::$app->user->isGuest ? $this->order->user_name : Yii::$app->user->getDisplayName()
            ])
        ]);

        // Send email to user.
        $this->order->sendClientEmail();
        // Send email to admin.
        $this->order->sendAdminEmail(explode(',', Yii::$app->settings->get('cart', 'order_emails')));
        // $order->detachBehavior('notification');

        Yii::$app->user->unsetPoints($this->order->points);
        //\machour\yii2\notifications\components\Notification::notify(\machour\yii2\notifications\components\Notification::KEY_NEW_ORDER, 1,$order->primaryKey);
        return $this->order;
    }

    /**
     * Check if product variantion exists
     * @param $product_id
     * @param $attribute_id
     * @param $variant_id
     * @return string
     */
    protected function _checkVariantExists($product_id, $attribute_id, $variant_id)
    {
        return ProductVariant::find()->where([
            'id' => $variant_id,
            'product_id' => $product_id,
            'attribute_id' => $attribute_id
        ])->count();
    }

    /**
     * Recount product quantity and redirect
     */
    public function processRecount()
    {
        Yii::$app->cart->recount(Yii::$app->request->post('quantities'));

        if (!Yii::$app->request->isAjax)
            return $this->redirect($this->createUrl('index'));
    }


    public function actionAcceptPoints()
    {
        $cart = Yii::$app->cart;
        $result = [];
        $result['success'] = false;
        $config = Yii::$app->settings->get('user');
        $totalPrice = 100000;
        $points = 5000;
        $pc = ($points * (int)$config->bonus_value);


        // $profit = round((($totalPrice-$pc)/$totalPrice)*100,2);
        $profit = (($totalPrice - $pc) / $totalPrice) * 100;
        if ($profit >= (int)$config->bonus_max_use_order) {
            $points2 = Yii::$app->request->post('bonus');
            $cart->acceptPoint($points2);
            $result['success'] = true;
        } else {
            $cart->acceptPoint(0);
        }

        return $this->asJson($result);
    }

    /**
     * Add message to errors array.
     * @param string $message
     * @param bool $fatal finish request
     */
    protected function _addError($message, $fatal = false)
    {
        if ($this->_errors === false)
            $this->_errors = array();

        array_push($this->_errors, $message);

        if ($fatal === true)
            $this->_finish();
    }

    /**
     * Process result
     * @param null $product
     * @return Response
     */
    protected function _finish($product = null)
    {
        $data = [
            'errors' => $this->_errors,
            'message' => Yii::t('cart/default', 'SUCCESS_ADDCART', [
                'product_name' => $product
            ]),
            'html' => $this->renderAjax('popup', [
                'totalPrice' => Yii::$app->cart->getTotalPrice(),
                'items' => Yii::$app->cart->getDataWithModels(),
            ]),
            'url' => Url::to($this->module->homeUrl)
        ];
        return $this->asJson($data);
    }

    /**
     * Display user orders
     */
    public function actionOrders()
    {
        if (!Yii::$app->user->isGuest) {
            $searchModel = new OrderSearch();

            $this->pageName = Yii::t('cart/default', 'MY_ORDERS');
            $this->view->params['breadcrumbs'][] = $this->pageName;

            //Yii::$app->request->getQueryParams()
            $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());
            $dataProvider->query->andWhere(['user_id' => Yii::$app->user->id]);

            $this->view->title = $this->pageName;
            return $this->render('user_orders', [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
            ]);
        } else {
            $this->error404();
        }
    }


}
