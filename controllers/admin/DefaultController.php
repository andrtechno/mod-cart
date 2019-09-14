<?php

namespace panix\mod\cart\controllers\admin;

use panix\engine\CMS;
use Yii;
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

    public function actionPrint($id)
    {
        $currentDate = CMS::date(date('Y-m-d H:i:s'));
        $model = Order::findModel($id);
        $title = $model::t('NEW_ORDER_ID', ['id' => $model->getNumberId()]);
        $mpdf = new Mpdf([
            // 'debug' => true,
            //'mode' => 'utf-8',
            'default_font_size' => 9,
            'default_font' => 'times',
            'margin_top' => 25,
            'margin_bottom' => 9,
            'margin_left' => 5,
            'margin_right' => 5,
            'margin_footer' => 5,
            'margin_header' => 5,
        ]);
        $mpdf->SetCreator('My Creator');
        $mpdf->SetAuthor('My Name');

        //$mpdf->SetProtection(['copy','print'], 'asdsad', 'MyPassword');
        $mpdf->SetTitle($title);
        $mpdf->SetHTMLFooter($this->renderPartial('@theme/views/pdf/footer',['currentDate'=>$currentDate]));
        $mpdf->SetHTMLHeader($this->renderPartial('@theme/views/pdf/header', ['title' => '№'.$model->getNumberId()]));
        $mpdf->WriteHTML(file_get_contents(Yii::getAlias('@vendor/panix/engine/pdf/assets/mpdf-bootstrap.min.css')), 1);
        $mpdf->WriteHTML($this->renderPartial('_pdf_order', ['model' => $model]), 2);
        return $mpdf->Output($model::t('NEW_ORDER_ID', ['id' => $model->id]) . ".pdf", 'I');

    }

    public function actionIndex()
    {
        $this->pageName = Yii::t('cart/admin', 'ORDERS');
        $this->buttons = [
            [
                'label' => Yii::t('cart/default', 'CREATE_ORDER'),
                'url' => ['create'],
                'options' => ['class' => 'btn btn-success', 'target' => '_blank']
            ]
        ];

        $this->breadcrumbs[] = $this->pageName;

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
        $this->pageName = Yii::t('cart/admin', 'ORDERS');
        $this->breadcrumbs = [
            $this->pageName
        ];
        \panix\mod\cart\OrderAsset::register($this->view);
        $this->view->registerJs('
            var deleteQuestion = "' . Yii::t('cart/admin', 'Вы действительно удалить запись?') . '";
            var productSuccessAddedToOrder = "' . Yii::t('cart/admin', 'Продукт успешно добавлен к заказу.') . '";', \yii\web\View::POS_HEAD, 'myid'
        );


        $this->buttons = [
            [
                'label' => Yii::t('cart/admin', 'PRINT'),
                'icon' => 'print',
                'url' => ['print', 'id' => $model->id],
                'options' => ['class' => 'btn btn-success']
            ]
        ];
        $isNew = $model->isNewRecord;
        $post = Yii::$app->request->post();
        if ($model->load($post) && $model->validate()) {
            $model->save();

            if (sizeof(Yii::$app->request->post('quantity', [])))
                $model->setProductQuantities(Yii::$app->request->post('quantity'));

            $this->redirectPage($isNew, $post);
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionAddProductList()
    {

        $request = Yii::$app->request;
        $order_id = $request->post('id');

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
        Yii::$app->response->format = Response::FORMAT_JSON;
        $result = [];
        $request = Yii::$app->request;
        if ($request->isPost) {
            if ($request->isAjax) {
                $order = Order::findModel($request->post('order_id'), Yii::t('cart/admin', 'ORDER_NOT_FOUND'));

                $product = Product::findModel($request->post('product_id'));

                $find = OrderProduct::find()->where(['order_id' => $order->id, 'product_id' => $product->id])->one();

                if ($find) {
                    if ($request->isAjax) {
                        $result = [
                            'success' => false,
                            'message' => Yii::t('cart/admin', 'ERR_ORDER_PRODUCT_EXISTS'),
                        ];

                    }
                }

                if ($request->isAjax) {
                    $result = [
                        'success' => false,
                        'message' => Yii::t('cart/default', 'ERROR_PRODUCT_NO_FIND'),
                    ];

                }


                $order->addProduct($product, $request->post('quantity'), $request->post('price'));
                $result = [
                    'success' => true,
                    'message' => Yii::t('cart/admin', 'SUCCESS_ADD_PRODUCT_ORDER'),
                ];
            } else {
                //throw new CHttpException(500, Yii::t('error', '500'));
            }
        } else {
            //throw new CHttpException(500, Yii::t('error', '500'));
        }

        return $result;
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


}
