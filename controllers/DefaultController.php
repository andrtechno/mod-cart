<?php

namespace panix\mod\cart\controllers;


use panix\engine\bootstrap\ActiveForm;
use panix\engine\CMS;
use panix\mod\cart\CartAsset;
use panix\mod\shop\models\Attribute;
use Yii;
use yii\base\Exception;
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


    public function actions()
    {
        return [
            'promoCode' => [
                'class' => 'panix\mod\cart\widgets\promocode\PromoCodeAction',
            ],
        ];
    }

    public function actionRecount()
    {
        if (Yii::$app->request->isAjax) {
            if (Yii::$app->request->isPost && !empty($_POST['quantities'])) {
                $test = [];
                $test[Yii::$app->request->post('product_id')] = Yii::$app->request->post('quantities');
                return Yii::$app->cart->ajaxRecount($test);
            }
        } else {
            throw new ForbiddenHttpException(Yii::t('app/error', 403));
        }
    }

    /**
     * Display list of product added to cart
     */
    public function actionIndex()
    {
        $this->pageName = Yii::t('cart/default', 'MODULE_NAME');
        $this->view->title = $this->pageName;
        $this->breadcrumbs = [$this->pageName];

        if (Yii::$app->request->isPost && Yii::$app->request->post('recount') && !empty($_POST['quantities'])) {
            $this->processRecount();
        }
        $this->form = new OrderCreateForm(); //['scenario' => 'create-form-order']

        // Make order
        $post = Yii::$app->request->post();

        if ($post) {
            if (Yii::$app->request->isAjax && $this->form->load($post)) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($this->form);
            }
            if ($this->form->load($post) && $this->form->validate()) {
                $this->form->registerGuest();
                $order = $this->createOrder();
                Yii::$app->cart->clear();
                Yii::$app->session->setFlash('success', Yii::t('cart/default', 'SUCCESS_ORDER'));
                return $this->redirect(['view', 'secret_key' => $order->secret_key]);
            }
        }


        $deliveryMethods = Delivery::find()
            ->published()
            ->orderByName()
            ->all();
        // echo($deliveryMethods->prepare(Yii::$app->db->queryBuilder)->createCommand()->rawSql);die;


        $paymentMethods = Payment::find()->all();

        $this->view->registerJs("
            var penny = '" . Yii::$app->currency->active['penny'] . "';
            var separator_thousandth = '" . Yii::$app->currency->active['separator_thousandth'] . "';
            var separator_hundredth = '" . Yii::$app->currency->active['separator_hundredth'] . "';
        ", yii\web\View::POS_HEAD, 'numberformat');

        return $this->render('index', [
            'items' => Yii::$app->cart->getDataWithModels(),
            'totalPrice' => Yii::$app->cart->getTotalPrice(),
            //'totalPrice' => Yii::$app->cart->totalPrice,
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
        $this->breadcrumbs[] = $this->pageName;
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Validate POST data and add product to cart
     * @throws BadRequestHttpException
     */
    public function actionAdd()
    {
        if (!Yii::$app->request->isAjax) {
            throw new BadRequestHttpException(Yii::t('app/default', 'ACCESS_DENIED'));
        }


        $variants = [];

        // Load product model
        $model = Product::findOne(Yii::$app->request->post('product_id', 0));

        // Check product
        if (!isset($model))
            $this->_addError(Yii::t('cart/default', 'ERROR_PRODUCT_NO_FIND'), true);

        // Update counter
        $model->updateCounters(['added_to_cart_count' => 1]);

        // Process variants
        if (!empty($_POST['eav'])) {
            foreach ($_POST['eav'] as $attribute_id => $variant_id) {
                if (!empty($variant_id)) {
                    // Check if attribute/option exists
                    if (!$this->_checkVariantExists($_POST['product_id'], $attribute_id, $variant_id))
                        $this->_addError(Yii::t('cart/default', 'ERROR_VARIANT_NO_FIND'));
                    else
                        array_push($variants, $variant_id);
                }
            }
        }

        // Process configurable products
        if ($model->use_configurations) {
            // Get last configurable item
            $configurable_id = Yii::$app->request->post('configurable_id', 0);

            if (!$configurable_id || !in_array($configurable_id, $model->configurations))
                $this->_addError(Yii::t('cart/default', 'ERROR_SELECT_VARIANT'), true);
        } else
            $configurable_id = 0;


        Yii::$app->cart->add([
            'product_id' => $model->id,
            'variants' => $variants,
            'attributes_data' => json_encode([
                'data' => $model->eavData['data'],
                'attributes' => $model->eavAttributes
            ]),
            'currency_id' => $model->currency_id,
            'supplier_id' => $model->supplier_id,
            'weight' => $model->weight,
            'height' => $model->height,
            'length' => $model->length,
            'width' => $model->width,
            'weight_class_id' => $model->weight_class_id,
            'length_class_id' => $model->length_class_id,
            'configurable_id' => $configurable_id,
            'quantity' => (int)Yii::$app->request->post('quantity', 1),
            'price' => $model->price,
        ]);

        $this->_finish($model->name);
    }

    /**
     * Remove product from cart and redirect
     * @param $id
     * @return array|Response
     */
    public function actionRemove($id)
    {
        Yii::$app->cart->remove($id);
        if (!Yii::$app->request->isAjax || !Yii::$app->cart->countItems()) {
            return $this->redirect(['index']);
        } else {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'id' => $id,
                'success' => true,
                'total_price' => Yii::$app->currency->number_format(Yii::$app->cart->totalPrice),
                'message' => Yii::t('cart/default', 'SUCCESS_PRODUCT_CART_DELETE')
            ];
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
        if (Yii::$app->cart->countItems() == 0)
            return false;

        $order = new Order;

        // Set main data
        $order->user_id = Yii::$app->user->isGuest ? null : Yii::$app->user->id;
        $order->user_name = $this->form->user_name;
        $order->user_email = $this->form->user_email;
        $order->user_phone = $this->form->user_phone;
        $order->user_address = $this->form->user_address;
        $order->user_comment = $this->form->user_comment;
        $order->delivery_id = $this->form->delivery_id;
        $order->payment_id = $this->form->payment_id;
        $order->promocode_id = $this->form->promocode_id;
        $order->status_id = 1;
        if ($order->validate()) {
            $order->save();

        } else {
            print_r($order->getErrors());
            die;
            throw new Exception(503, Yii::t('cart/default', 'ERROR_CREATE_ORDER'));
        }

        // Process products
        $productsCount = 0;
        foreach (Yii::$app->cart->getDataWithModels() as $item) {


            $ordered_product = new OrderProduct;
            $ordered_product->order_id = $order->id;
            $ordered_product->product_id = $item['model']->id;
            $ordered_product->configurable_id = $item['configurable_id'];
            $ordered_product->currency_id = $item['model']->currency_id;
            $ordered_product->supplier_id = $item['model']->supplier_id;
            $ordered_product->name = $item['model']->name;
            $ordered_product->quantity = $item['quantity'];
            $ordered_product->sku = $item['model']->sku;
            $ordered_product->price_purchase = $item['model']->price_purchase;
            $ordered_product->attributes_data = json_encode($item['attributes_data']);
            $ordered_product->weight = $item['weight'];
            $ordered_product->height = $item['height'];
            $ordered_product->length = $item['length'];
            $ordered_product->width = $item['width'];
            $ordered_product->weight_class_id = $item['weight_class_id'];
            $ordered_product->length_class_id = $item['length_class_id'];


            // if($item['currency_id']){
            //     $currency = Currency::model()->findByPk($item['currency_id']);
            //$ordered_product->price = Product::calculatePrices($item['model'], $item['variant_models'], $item['configurable_id']) * $currency->rate;
            // }else{
            $ordered_product->price = Product::calculatePrices($item['model'], $item['variant_models'], $item['configurable_id']);
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
        $order->refresh(); //@todo panix text email tpl
        // All products added. Update delivery price.
        $order->updateDeliveryPrice();
        $text = (Yii::$app->user->isGuest) ? 'NOTIFICATION_GUEST_TEXT' : 'NOTIFICATION_USER_TEXT';
        $order->attachBehavior('notification', [
            'class' => 'panix\engine\behaviors\NotificationBehavior',
            'type' => 'success',
            'url' => Url::to($order->getUpdateUrl()),
            'sound' => CartAsset::register($this->view)->baseUrl . '/notification_new-order.mp3',
            'text' => Yii::t('cart/default', $text, [
                'num' => $productsCount,
                'total' => Yii::$app->currency->number_format($order->total_price),
                'currency' => Yii::$app->currency->active['symbol'],
                'username' => Yii::$app->user->isGuest ? $order->user_name : Yii::$app->user->getDisplayName()
            ])
        ]);

        // Send email to user.
        $order->sendClientEmail();
        // Send email to admin.
        $order->sendAdminEmail();
        // $order->detachBehavior('notification');


        //\machour\yii2\notifications\components\Notification::notify(\machour\yii2\notifications\components\Notification::KEY_NEW_ORDER, 1,$order->primaryKey);
        return $order;
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
            'url' => Url::to(['/cart/default/index'])
        ];
        return $this->asJson($data);
    }

    /**
     * @param Order $order
     * @return \yii\mail\MailerInterface
     */
    private function sendAdminEmail(Order $order)
    {

        $mailer = Yii::$app->mailer;
        $mailer->compose(['html' => Yii::$app->getModule('cart')->mailPath.'/order.tpl'], ['order' => $order])
            ->setFrom(['noreply@' . Yii::$app->request->serverName => Yii::$app->name . ' robot'])
            ->setTo([Yii::$app->settings->get('app', 'email') => Yii::$app->name])
            ->setSubject(Yii::t('cart/default', 'MAIL_ADMIN_SUBJECT', ['id' => $order->id]))
            ->send();
        return $mailer;
    }

    /**
     * @param Order $order
     * @return \yii\mail\MailerInterface
     */
    private function sendClientEmail(Order $order)
    {
        $mailer = Yii::$app->mailer;
        $mailer->htmlLayout = Yii::$app->getModule('cart')->mailPath.'/layouts/client';
        $mailer->compose(Yii::$app->getModule('cart')->mailPath.'/order.tpl', ['order' => $order])
            ->setFrom('noreply@' . Yii::$app->request->serverName)
            ->setTo($order->user_email)
            ->setSubject(Yii::t('cart/default', 'MAIL_CLIENT_SUBJECT', ['id' => $order->id]))
            ->send();

        return $mailer;
    }

    /**
     * Display user orders
     */
    public function actionOrders()
    {
        if (!Yii::$app->user->isGuest) {
            $searchModel = new OrderSearch();

            $this->pageName = Yii::t('cart/default', 'MY_ORDERS');
            $this->breadcrumbs[] = $this->pageName;

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
