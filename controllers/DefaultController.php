<?php

namespace panix\mod\cart\controllers;

use Yii;
use yii\helpers\Url;
use panix\engine\Html;
use yii\helpers\Json;
use panix\engine\controllers\WebController;
use panix\mod\cart\models\forms\OrderCreateForm;
use panix\mod\cart\models\Delivery;
use panix\mod\cart\models\Payment;
use panix\mod\cart\models\Order;
use panix\mod\cart\models\OrderProduct;
use panix\mod\shop\models\Product;

class DefaultController extends WebController {

    /**
     * @var OrderCreateForm
     */
    public $form;

    /**
     * @var bool
     */
    protected $_errors = false;

    public function actionRecount() {
        if (Yii::$app->request->isAjax) {
            if (Yii::$app->request->isPost && !empty($_POST['quantities'])) {
                $test = array();
                $test[Yii::$app->request->post('product_id')] = Yii::$app->request->post('quantities');
                Yii::$app->cart->ajaxRecount($test);
            }
        }
    }
    public $pageTitle;
    /**
     * Display list of product added to cart
     */
    public function actionIndex() {
        $this->pageName = Yii::t('cart/default', 'MODULE_NAME');
        $this->title = $this->pageName;
        $this->breadcrumbs = [$this->pageName];
        
        if (Yii::$app->request->isPost && Yii::$app->request->post('recount') && !empty($_POST['quantities'])) {
            $this->processRecount();
        }
        $this->form = new OrderCreateForm;

        // Make order
        $post = Yii::$app->request->post();

        if ($post) {
            if ($this->form->load($post) && $this->form->validate()) {
                $this->form->registerGuest();
                $order = $this->createOrder();
                //Yii::$app->cart->clear();
              // Yii::$app->session->setFlash('success', Yii::t('cart/default', 'SUCCESS_ORDER'));
                //return $this->redirect(['view', 'secret_key' => $order->secret_key]);
            }
        }


        $deliveryMethods = Delivery::find()
                ->published()
                ->orderByName()
                ->all();
        // echo($deliveryMethods->prepare(Yii::$app->db->queryBuilder)->createCommand()->rawSql);die;



        $paymenyMethods = Payment::find()->all();

        return $this->render('index', array(
                    'items' => Yii::$app->cart->getDataWithModels(),
                    'totalPrice' => Yii::$app->currency->convert(Yii::$app->cart->getTotalPrice()),
                    'deliveryMethods' => $deliveryMethods,
                    'paymenyMethods' => $paymenyMethods,
        ));
    }

    public function actionPayment() {
        if (isset($_POST)) {
            $this->form = Payment::find()->all();
            echo $this->render('_payment', array('model' => $this->form));
        }
    }

    /**
     * Find order by secret_key and display.
     * @throws CHttpException
     */
    public function actionView() {
        $secret_key = Yii::$app->request->get('secret_key');
        $model = Order::find()->where('secret_key=:key', array(':key' => $secret_key))->one();
        if (!$model)
            throw new \yii\web\NotFoundHttpException(Yii::t('cart/default', 'ERROR_ORDER_NO_FIND'));

        $this->pageName = Yii::t('cart/default', 'VIEW_ORDER', ['id' => $model->id]);
        $this->breadcrumbs[] = $this->pageName;
        return $this->render('view', array(
                    'model' => $model,
        ));
    }

    /**
     * Validate POST data and add product to cart
     */
    public function actionAdd() {
        if (!Yii::$app->request->isAjax) {
            throw new \yii\web\BadRequestHttpException(Yii::t('app', 'ACCESS_DENIED'));
        }


        $variants = array();

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


        Yii::$app->cart->add(array(
            'product_id' => $model->id,
            'variants' => $variants,
            'currency_id' => $model->currency_id,
            'supplier_id' => $model->supplier_id,
            'configurable_id' => $configurable_id,
            'quantity' => (int) Yii::$app->request->post('quantity', 1),
            'price' => $model->price,
        ));

        $this->_finish($model->name);
    }

    /**
     * Remove product from cart and redirect
     */
    public function actionRemove($id) {
        Yii::$app->cart->remove($id);
        if (!Yii::$app->request->isAjax) {
            return $this->redirect(['index']);
        }
    }

    /**
     * Clear cart
     */
    public function actionClear() {
        Yii::$app->cart->clear();
        if (!Yii::$app->request->isAjax)
            return $this->redirect(['index']);
    }

    /**
     * Render data to display in theme header.
     */
    public function actionRenderSmallCart() {
        if (!Yii::$app->request->isAjax) {
            throw new \yii\web\BadRequestHttpException(Yii::t('app', 'ACCESS_DENIED'));
        }
        echo \panix\mod\cart\widgets\cart\CartWidget::widget(['skin'=>Yii::$app->request->post('skin')]);
    }

    /**
     * Create new order
     * @return Order
     */
    public function createOrder() {
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

        if ($order->validate()) {
            $order->save();
        } else {
            throw new CHttpException(503, Yii::t('cart/default', 'ERROR_CREATE_ORDER'));
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
            // if($item['currency_id']){
            //     $currency = Currency::model()->findByPk($item['currency_id']);
            //$ordered_product->price = Product::calculatePrices($item['model'], $item['variant_models'], $item['configurable_id']) * $currency->rate;
            // }else{
            $ordered_product->price = Product::calculatePrices($item['model'], $item['variant_models'], $item['configurable_id']);
            // }

            $ordered_product->save();
$productsCount++;
        }

        // Reload order data.
        $order->refresh(); //@todo panix text email tpl
        // All products added. Update delivery price.
        $order->updateDeliveryPrice();
        $text = (Yii::$app->user->isGuest) ? 'NOTIFACTION_GUEST_TEXT':'NOTIFACTION_USER_TEXT';
                $order->attachBehavior('notifaction', [
            'class' => \panix\engine\behaviors\NotifactionBehavior::class,
            'type' => 'success',
            'text' => Yii::t('cart/default', $text, [
                'num' => $productsCount,
                'total' => $order->total_price,
                'currency' => Yii::$app->currency->active->symbol,
                'username' => Yii::$app->user->isGuest ? $order->user_name : Yii::$app->user->getDisplayName()
            ])
        ]);
                
        
        // Send email to user.
        //$this->sendClientEmail($order);
        // Send email to admin.
        $this->sendAdminEmail($order);




        // $order->detachBehavior('notifaction');
        return $order;
    }

    /**
     * Check if product variantion exists
     * @param $product_id
     * @param $attribute_id
     * @param $variant_id
     * @return string
     */
    protected function _checkVariantExists($product_id, $attribute_id, $variant_id) {
        return ProductVariant::find()->where([
                    'id' => $variant_id,
                    'product_id' => $product_id,
                    'attribute_id' => $attribute_id
                ])->count();
    }

    /**
     * Recount product quantity and redirect
     */
    public function processRecount() {
        Yii::$app->cart->recount(Yii::$app->request->post('quantities'));

        if (!Yii::$app->request->isAjax)
            Yii::$app->request->redirect($this->createUrl('index'));
    }

    /**
     * Add message to errors array.
     * @param string $message
     * @param bool $fatal finish request
     */
    protected function _addError($message, $fatal = false) {
        if ($this->_errors === false)
            $this->_errors = array();

        array_push($this->_errors, $message);

        if ($fatal === true)
            $this->_finish();
    }

    /**
     * Process result and exit!
     */
    protected function _finish($product = null) {

        echo Json::encode(array(
            'errors' => $this->_errors,
            'message' => Yii::t('cart/default', 'SUCCESS_ADDCART', [
                'cart' => \yii\helpers\BaseHtml::a(Yii::t('cart/default', 'IN_CART'), '/cart'),
                'product_name' => $product
            ]),
        ));
        exit;
    }

    private function sendAdminEmail(Order $order) {
        Yii::$app->mailer->htmlLayout = "layouts/admin";
        Yii::$app->mailer
                ->compose()
                ->setFrom(['noreply@' . Yii::$app->request->serverName => Yii::$app->name . ' robot'])
                ->setTo([Yii::$app->settings->get('app', 'email') => Yii::$app->name])
                //->setCc(Yii::$app->settings->get('app','email')) //copy
                //->setBcc(Yii::$app->settings->get('app','email')) //hidden copy
                 ->setHtmlBody($this->renderPartial('@cart/mail/admin.tpl', ['order' => $order,'test'=>'1111']))
                ->setSubject(Yii::t('cart/default', 'MAIL_ADMIN_SUBJECT', ['id' => $order->id]))
                ->send();
    }

    private function sendClientEmail(Order $order) {
        Yii::$app->mailer
                ->compose('@cart/mail/admin', ['order' => $order])
                ->setFrom('noreply@' . Yii::$app->request->serverName)
                ->setTo($order->user_email)
                ->setSubject(Yii::t('cart/default', 'MAIL_CLIENT_SUBJECT', ['id' => $order->id]))
                ->send();
    }

}
