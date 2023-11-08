<?php

namespace panix\mod\cart\controllers\admin;

use panix\engine\bootstrap\ActiveForm;
use panix\engine\CMS;
use panix\mod\cart\components\delivery\BaseDeliverySystem;
use panix\mod\cart\components\delivery\DeliverySystemManager;
use panix\mod\cart\components\events\EventProduct;
use panix\mod\cart\models\Delivery;
use Yii;
use yii\helpers\Json;
use yii\helpers\Markdown;
use yii\helpers\Url;
use yii\web\Response;
use panix\engine\controllers\AdminController;
use panix\mod\shop\models\Product;
use panix\mod\cart\models\Order;
use panix\mod\cart\models\OrderProduct;
use panix\mod\shop\models\search\ProductSearch;
use panix\mod\cart\models\search\OrderSearch;
use Mpdf\Mpdf;

class DefaultController extends AdminController
{
    public function actions()
    {
        return [
            'delete' => [
                'class' => 'panix\engine\actions\DeleteAction',
                'modelClass' => Order::class,
            ],
        ];
    }

    /**
     * Update Quantity products
     *
     * @param $id
     * @return Response
     */
    public function actionQuantity($id)
    {
        $product_id = Yii::$app->request->post('product_id');
        $quantity = Yii::$app->request->post('quantity');
        $result['success'] = false;
        if ($product_id && $quantity) {
            //$product = Yii::$app->request->post();
            //$quantity = $post['quantity'][$key];
            $product = OrderProduct::find()->where(['order_id' => $id, 'product_id' => $product_id])->one();
            $oldQuantity = $product->quantity;
            // $product->quantity = $quantity;
            //$event = new EventProduct([
            //    'product_model' => $product->originalProduct,
            //    'ordered_product' => $product,
            //    'quantity' => $quantity,
            //  'params'=>['new_quantity'=>$quantity]
            // ]);
            // $product->save(false);

            /** @var Order $order */
            $order = Order::findOne($id);
            // $order = $product->order;


            if (Yii::$app->settings->get('cart', 'quantity_convert')) {
                $order->setProductQuantities([$product->id => $quantity * $product->in_box]);
            } else {
                $order->setProductQuantities([$product->id => $quantity]);
            }
            //$order->eventProductQuantityChanged($event);
            $order->updateTotalPrice();

            $diff_price = 0;
            if ($order->diff_price) {
                if ($order->discount) {
                    if ('%' === substr($order->discount, -1, 1)) {
                        $sum = $order->total_price * ((double)$order->discount / 100);
                    } else {
                        $sum = $order->discount;
                    }
                    $diff_price = Yii::$app->currency->number_format($order->diff_price - $sum);
                } else {
                    $diff_price = Yii::$app->currency->number_format($order->diff_price);
                }

            }

            $result['success'] = true;
            $result['total'] = $order->total_price;
            $result['total_formatted'] = Yii::$app->currency->number_format($order->total_price);
            $result['diff_price_formatted'] = $diff_price;
            $result['full_price_formatted'] = Yii::$app->currency->number_format($order->full_price);
            $result['message'] = Yii::t('cart/admin', 'QUANTITY_CHANGED', [$product->name]);

        }
        return $this->asJson($result);
    }

    public function actionRelated()
    {
        $model = Order::findModel(Yii::$app->request->post('order_id'));
        $model->user_id = Yii::$app->request->post('user_id');
        if ($model->save(false)) {
            Yii::$app->session->setFlash('success', 'Пользователь успешно связан с данным заказом');
            return $this->redirect(['update', 'id' => Yii::$app->request->post('order_id')]);
        }
    }

    public function actionPrint($id)
    {
        $currentDate = CMS::date(time());
        $model = Order::findModel($id);
        $config = Yii::$app->settings->get('cart', 'pdf_tpl_order');
        $title = $model::t('NEW_ORDER_ID', ['id' => CMS::idToNumber($model->id)]);
        $mpdf = new Mpdf([
            // 'debug' => true,
            //'mode' => 'utf-8',
            'default_font_size' => 9,
            'default_font' => 'times',
            'margin_top' => 5,
            'margin_bottom' => 9,
            'margin_left' => 5,
            'margin_right' => 5,
            'margin_footer' => 5,
            'margin_header' => 5,
        ]);

        $mpdf->SetCreator(Yii::$app->name);
        $mpdf->SetAuthor(Yii::$app->user->getDisplayName());

        //$mpdf->SetProtection(['copy','print'], 'asdsad', 'MyPassword');
        $mpdf->SetTitle($title);
        $mpdf->SetHTMLFooter($this->renderPartial('@theme/views/pdf/footer', ['currentDate' => $currentDate]));
        /*$mpdf->SetHTMLHeader($this->renderPartial('pdf/_header_order', [
            'model' => $model
        ]));*/
        $mpdf->WriteHTML(file_get_contents(Yii::getAlias('@vendor/panix/engine/pdf/assets/mpdf-bootstrap.min.css')), 1);
        //$mpdf->WriteHTML($this->renderPartial('_pdf_order', ['model' => $model]), 2);
        $mpdf->WriteHTML($this->renderPartial($config, ['model' => $model]), 2);
        echo $mpdf->Output($model::t('NEW_ORDER_ID', ['id' => CMS::idToNumber($model->id)]) . ".pdf", 'I');
        die;
    }

    public function actionIndex()
    {
        $this->pageName = Yii::t('cart/admin', 'ORDERS');
        if (Yii::$app->user->can("/{$this->module->id}/{$this->id}/*") || Yii::$app->user->can("/{$this->module->id}/{$this->id}/create")) {
            $this->buttons = [
                [
                    'label' => Yii::t('cart/admin', 'CREATE_ORDER'),
                    'url' => ['create'],
                    'icon' => 'add',
                    'options' => ['class' => 'btn btn-success']
                ]
            ];
        }
        $this->view->params['breadcrumbs'][] = $this->pageName;

        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    public function actionUpdate($id = false)
    {
        $model = Order::findModel($id, Yii::t('cart/admin', 'ORDER_NOT_FOUND'));
        $isNew = $model->isNewRecord;
        $this->pageName = ($isNew) ? $model::t('CREATE_ORDER') : $model::t('NEW_ORDER_ID', ['id' => CMS::idToNumber($model->id)]);
        $this->view->params['breadcrumbs'] = [
            [
                'label' => Yii::t('cart/admin', 'ORDERS'),
                'url' => ['index']
            ],
            $this->pageName
        ];
        \panix\mod\cart\OrderAsset::register($this->view);
        $this->view->registerJs('
            var deleteQuestion = "' . Yii::t('cart/admin', 'Вы действительно удалить запись?') . '";
            var productSuccessAddedToOrder = "' . Yii::t('cart/admin', 'Продукт успешно добавлен к заказу.') . '";', \yii\web\View::POS_HEAD, 'myid'
        );


        $this->buttons[] = [
            'label' => Yii::t('cart/admin', 'ORDER_VIEW'),
            'icon' => 'eye',
            'url' => $model->getUrl(),
            'options' => ['class' => 'btn btn-primary', 'target' => '_blank']
        ];

        if (Yii::$app->user->can("/{$this->module->id}/{$this->id}/*") || Yii::$app->user->can("/{$this->module->id}/{$this->id}/print")) {
            $this->buttons[] = [
                'label' => Yii::t('cart/admin', 'PRINT_PDF'),
                'icon' => 'print',
                'url' => ['print', 'id' => $model->id],
                'options' => ['class' => 'btn btn-primary', 'target' => '_blank']
            ];
        }
        $old = $model->oldAttributes;
        $post = Yii::$app->request->post();


        //LOAD
        if (!$post && !$model->isNewRecord && $model->delivery_id) {
            $delivery = Delivery::findOne($model->delivery_id);
            if ($delivery->system) {
                $manager = new DeliverySystemManager();
                $system = $manager->getSystemClass($delivery->system);
                $model->deliveryModel = $system->getModel();
            }

        }

        if ($model->load($post)) {

            //POST
            $delivery = Delivery::findOne($model->delivery_id);
            if ($delivery->system) {
                $manager = new DeliverySystemManager();
                $system = $manager->getSystemClass($delivery->system);
                $model->deliveryModel = $system->getModel();
                $model->deliveryModel->load($post);
                if (isset($model->deliveryModel->type)) {
                    if ($model->deliveryModel->type == 'warehouse') {
                        $model->deliveryModel->addRule(['warehouse'], 'required');
                    } else {
                        $model->deliveryModel->addRule(['address'], 'required');
                    }
                }
                //$model->deliveryModel->validate();
            }


            /*if (Yii::$app->request->post('onChangeDelivery')) {
                //   print_r($model);
                if ($model->deliveryMethod) {
                    // if($model->deliveryMethod->system){
                    $system = $model->deliveryMethod->getDeliverySystemClass();
                    if ($system instanceof BaseDeliverySystem) {
                        return $system->renderDeliveryFormHtml($model);
                    }

                    // }
                }
                return false;
            }*/


            if ($model->validate()) {

                $model->save();
                $model->updateDeliveryPrice();
                /*if (isset($old['status_id'])) {
                    if (Yii::$app->settings->get('cart', 'notify_changed_status') && $old['status_id'] != $model->status_id) {
                        if ($model->user_email) {
                            $mailer = Yii::$app->mailer;
                            $mailer->htmlLayout = Yii::$app->getModule('cart')->mailPath . '/layouts/client';
                            $mailer->compose(['html' => Yii::$app->getModule('cart')->mailPath . '/changed_status.tpl'], ['order' => $model])
                                ->setTo([$model->user_email])
                                ->setSubject(Yii::t('cart/default', 'MAIL_CHANGE_STATUS_SUBJECT', CMS::idToNumber($model->id)))
                                ->send();
                        }
                    }
                }*/

                /*if (isset($old['ttn']) != $model->ttn && !empty($model->ttn)) {
                    if ($model->user_email) {
                        $mailer = Yii::$app->mailer;
                        $mailer->htmlLayout = Yii::$app->getModule('cart')->mailPath . '/layouts/client';
                        $mailer->compose(['html' => Yii::$app->getModule('cart')->mailPath . '/ttn.tpl'], ['order' => $model])
                            ->setTo([$model->user_email])
                            ->setSubject(Yii::t('cart/default', 'MAIL_TTN_SUBJECT', CMS::idToNumber($model->id)))
                            ->send();
                    }
                }*/


                if (sizeof(Yii::$app->request->post('quantity', [])))
                    $model->setProductQuantities(Yii::$app->request->post('quantity'));

                return $this->redirectPage($isNew, $post);
            } else {
                //CMS::dump($model->getErrors());
                //die;
            }
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionAddProductList()
    {

        $request = Yii::$app->request;
        $order_id = $request->get('id');

        $model = Order::findModel($order_id, Yii::t('cart/admin', 'ORDER_NOT_FOUND'));

        if ($order_id) {
            if (!$request->isAjax) {
                return $this->redirect(['/admin/cart/default/update', 'id' => $order_id]);
            }
        }
        if (!$request->isAjax) {
            return $this->redirect(['/admin/cart/default/index']);
        }


        $searchModel = new ProductSearch();
        $dataProvider = $searchModel->search($request->getQueryParams());


        return $this->renderAjax('_addProduct', [
            'dataProvider' => $dataProvider,
            'order_id' => $order_id,
            'model' => $model,
        ]);
    }

    /**
     * Add product to order
     */
    public function actionAddProduct()
    {
        $result = [];
        $result['success'] = false;
        $request = Yii::$app->request;
        if ($request->isPost) {
            if ($request->isAjax) {
                $order = Order::findModel($request->post('order_id'), Yii::t('cart/admin', 'ORDER_NOT_FOUND'));
                $product = Product::findModel($request->post('product_id'));

                $find = OrderProduct::find()->where(['order_id' => $order->id, 'product_id' => $product->id])->one();

                if ($find) {
                    $result['message'] = Yii::t('cart/admin', 'ERR_ORDER_PRODUCT_EXISTS');
                } else {
                    $price = $request->post('price');
                    if ($product->discount) {
                        if ('%' === substr($product->discount, -1, 1)) {
                            $price = $price * ((double)$product->discount) / 100;
                        } else {
                            $price = $price - $product->discount;
                        }
                    }
                    //@Todo Вообше лучше сделать чтобы записывалась оригинальная цена, а не уже конвертирущая
                    if ($product->currency_id) {
                        $product->price_purchase = Yii::$app->currency->convert($product->price_purchase, $product->currency_id);
                        $price = Yii::$app->currency->convert($price, $product->currency_id);
                    }
                    $order->addProduct($product, $request->post('quantity'), $price);
                    $result['success'] = true;
                    $result['message'] = Yii::t('cart/admin', 'SUCCESS_ADD_PRODUCT_ORDER');
                }
            }
        }

        return $this->asJson($result);
    }

    /**
     * Delete product from order
     */
    public function actionDeleteProduct()
    {
        $order = Order::findModel(Yii::$app->request->post('order_id'), Yii::t('cart/admin', 'ORDER_NOT_FOUND'));

        //if ($order->is_deleted)
        //    throw new NotFoundHttpException(Yii::t('cart/admin', 'ORDER_ISDELETED'));

        $order->deleteProduct(Yii::$app->request->post('id'));
    }

    public function actionRenderOrderedProducts($order_id)
    {
        $this->pageName = Yii::t('cart/default', 'MODULE_NAME');
        return $this->renderAjax('_order-products', array(
            'model' => Order::findModel($order_id)
        ));
    }

    public function actionPdfOrders($start, $end, $render = 'delivery', $type = 0)
    {


        $selection = Yii::$app->request->get('selection');
        $dateStart = strtotime($start);

        $dateEnd = strtotime($end) + 86400;
        $mpdf = new Mpdf([
            // 'debug' => true,
            //'mode' => 'utf-8',
            'default_font_size' => 9,
            'default_font' => 'times',
            'margin_top' => 5,
            'margin_bottom' => 10,
            'margin_left' => 5,
            'margin_right' => 5,
            'margin_footer' => 5,
            'margin_header' => 5,
        ]);
        if ($type) {
            /*Yii::import('ext.tcpdf.TCPDF');
            $contact = Yii::app()->settings->get('contacts');
            $phones = explode(',', $contact['phone']);
            $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetHeaderData("", "", Yii::app()->settings->get('app', 'site_name'), $phones[0].', '.$phones[1].', 3 konteynernaya, rolet 460');
            //$pdf->SetHeaderData("", "", Yii::app()->settings->get('app', 'site_name'), "phone " . $phones[0]);
            $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
            $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
            $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
            $pdf->SetMargins(0, PDF_MARGIN_TOP, 0); //PDF_MARGIN_TOP
            $pdf->SetMargins(10, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
            $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
            $pdf->SetFooterMargin(0); //PDF_MARGIN_FOOTER
            $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM); //PDF_MARGIN_BOTTOM
            $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
            $pdf->setJPEGQuality(100);
            $pdf->AddPage();
            $pdf->setFontSubsetting(true);
            $pdf->SetFont('freeserif', '', 12);
            $pdf->Write(0, '', '', 0, 'L', true, 0, false, false, 0);*/

            $mpdf->use_kwt = true;
            $mpdf->SetCreator(Yii::$app->name);
            $mpdf->SetAuthor(Yii::$app->user->getDisplayName());

            // $mpdf->SetProtection(['print','copy'], 'admin', '111');
            //$mpdf->SetTitle($title);
            $mpdf->SetHTMLFooter($this->renderPartial('@cart/views/admin/default/pdf/_footer_delivery', ['currentDate' => 'dsadsa']));

            $mpdf->WriteHTML(file_get_contents(Yii::getAlias('@vendor/panix/engine/pdf/assets/mpdf-bootstrap.min.css')), 1);
            // $mpdf->WriteHTML($this->renderPartial('_pdf_order', ['model' => $model]), 2);
            // return $mpdf->Output($model::t('NEW_ORDER_ID', ['id' => CMS::idToNumber($model->id)]) . ".pdf", 'I');

        }


        /* $model = Order::find()->with([
          'products' => function (\yii\db\ActiveQuery $query) {
               $query->andWhere(['not', ['brand_id' => null]]);
           },
       ]);*/

        $statuses = Yii::$app->request->get('status_id');
        $query = Order::find()->where(['buyOneClick' => 0])->andWhere(['status_id' => $statuses]);
        if ($selection) {
            $query->andWhere([Order::tableName() . '.id' => $selection]);
        }
        if (!$selection && $dateStart && $dateEnd) {
            $query->between($dateStart, $dateEnd);
        }
        // $model->where(['status_id' => 1]);
        if ($render == 'delivery') {
            $view = 'pdf/delivery';
            $query->orderBy(['delivery_id' => SORT_DESC]);
            $mpdf->SetHTMLHeader($this->renderPartial('pdf/_header_delivery', [
                'start_date' => CMS::date($dateStart, false),
                'end_date' => CMS::date($dateEnd, false),
            ]));
        } else {
            $query->joinWith(['products p']);
            if ($render == 'brand') {
                //$view = 'pdf/brand';
                $view = Yii::$app->settings->get('cart', 'pdf_tpl_brand');
                $query->andWhere(['not', ['p.brand_id' => null]]);
                $query->orderBy(['p.brand_id' => SORT_DESC]);
            } elseif ($render == 'supplier') {
                //$view = 'pdf/supplier';
                $view = Yii::$app->settings->get('cart', 'pdf_tpl_supplier');
                $query->andWhere(['not', ['p.supplier_id' => null]]);
                $query->orderBy(['p.supplier_id' => SORT_DESC]);
            } else {
                $this->error404();
            }

            /*$mpdf->SetHTMLHeader($this->renderPartial('pdf/_header_products', [
                'start_date' => CMS::date($dateStart, false),
                'end_date' => CMS::date($dateEnd, false),
            ]));*/
        }
        $model = $query->all();


        $array = [];

        if ($type) {
            $mpdf->WriteHTML($this->renderPartial($view, [
                'array' => $array,
                'model' => $model,
                'dateStart' => $dateStart,
                //'dateStart' => date('Y-m-d', $dateStart),
                'dateEnd' => $dateEnd - 86400

            ]), 2);
            $mpdf->Ln();
            echo $mpdf->Output($this->action->id . ".pdf", 'I');
            die;
        } else {
            $this->layout = '@admin/views/admin/print';
            return $this->render($view, [
                'array' => $array,
                'model' => $model,
                'dateStart' => date('Y-m-d', strtotime($dateStart)),
                'dateEnd' => date('Y-m-d', strtotime($dateEnd) - 86400)
            ]);
        }

    }

    public function titleSort($a, $b)
    {
        return strnatcmp($a['title'], $b['title']);
    }


    public function actionCreate()
    {
        return $this->actionUpdate(false);
    }

    public function actionReadDocFile($file)
    {
        if (file_exists(Yii::getAlias($file))) {
            $result = Markdown::process(file_get_contents(Yii::getAlias($file)), 'gfm');
        }
        return $this->render('@cart/guide/MAIL_TEMPLATE.md');
        //  return $result;
    }

    public function actionOrderSendEmail($id)
    {
        $model = Order::findOne($id);
        $model->sendAdminEmail(['andrew.panix@gmail.com']);
        $model->sendClientEmail('andrew.panix@gmail.com');
    }
}
