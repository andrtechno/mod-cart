<?php

namespace panix\mod\cart\controllers\admin;


use panix\mod\cart\models\Order;
use Yii;
use yii\web\NotFoundHttpException;
use panix\engine\controllers\AdminController;
use panix\engine\pdf\Pdf;
use panix\mod\shop\models\Product;
use panix\mod\cart\models\OrderProduct;
use panix\mod\shop\models\search\ProductSearch;
use panix\mod\cart\models\search\OrderSearch;

class DefaultController extends AdminController
{

    public function actionPrint($id)
    {
        $model = $this->findModel($id);

        $content = $this->renderPartial('_pdf_order', ['model' => $model]);


        $pdf = new Pdf([
            'content' => $content,
            'methods' => [
                //'SetHeader' => [Yii::$app->name],
                'SetHeader' => $this->renderPartial('@theme/views/pdf/header', []),
                'SetFooter' => ['Страница: {PAGENO}'],
            ]
        ]);

        return $pdf->render();
    }

    public function actionIndex()
    {
        $this->pageName = Yii::t('cart/admin', 'ORDERS');
        $this->buttons = [
            [
                'label' => Yii::t('cart/default', 'CREATE_ORDER'),
                'url' => ['/admin/cart/default/create'],
                'options' => ['class' => 'btn btn-success']
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

    public function actionUpdate($id=false)
    {
        $model = Order::findModel($id, Yii::t('cart/admin', 'ORDER_NOT_FOUND'));
        $this->pageName = Yii::t('cart/admin', 'ORDERS');
        $this->breadcrumbs = [
            $this->pageName
        ];
        \panix\mod\cart\assets\admin\OrderAsset::register($this->view);
        $this->view->registerJs('
            var deleteQuestion = "' . Yii::t('cart/admin', 'Вы действительно удалить запись?') . '";
            var productSuccessAddedToOrder = "' . Yii::t('cart/admin', 'Продукт успешно добавлен к заказу.') . '";', \yii\web\View::POS_HEAD, 'myid'
        );

        $post = Yii::$app->request->post();
        if ($model->load($post) && $model->validate()) {
            $model->save();

            if (sizeof(Yii::$app->request->post('quantity', [])))
                $model->setProductQuantities(Yii::$app->request->post('quantity'));

            Yii::$app->session->setFlash('success', \Yii::t('app', 'SUCCESS_UPDATE'));
            // return $this->redirect(['index']);
            //return Yii::$app->getResponse()->redirect(['/cart/default']);
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
        $request = Yii::$app->request;
        if ($request->isPost) {
            if ($request->isAjax) {
                $order = Order::findModel($request->post('order_id'), Yii::t('cart/admin', 'ORDER_NOT_FOUND'));

                $product = Product::findOne($request->post('product_id'));

                $find = OrderProduct::find()->where(array('order_id' => $order->id, 'product_id' => $product->id))->one();

                if ($find) {
                    if ($request->isAjax) {
                        echo \yii\helpers\Json::encode(array(
                            'success' => false,
                            'message' => Yii::t('cart/admin', 'ERR_ORDER_PRODUCT_EXISTS'),
                        ));
                        die;
                    } else {
                        //throw new CHttpException(400, Yii::t('CartModule.admin', 'ERR_ORDER_PRODUCT_EXISTS'));
                    }
                }
                if (!$product) {
                    if ($request->isAjax) {
                        echo \yii\helpers\Json::encode(array(
                            'success' => false,
                            'message' => Yii::t('cart/default', 'ERROR_PRODUCT_NO_FIND'),
                        ));
                        die;
                    } else {
                        $this->error404(Yii::t('CartModule.default', 'ERROR_PRODUCT_NO_FIND'));
                    }
                }

                $order->addProduct($product, $request->post('quantity'), $request->post('price'));
                echo \yii\helpers\Json::encode(array(
                    'success' => true,
                    'message' => Yii::t('cart/admin', 'SUCCESS_ADD_PRODUCT_ORDER'),
                ));
                die;
            } else {
                //throw new CHttpException(500, Yii::t('error', '500'));
            }
        } else {
            //throw new CHttpException(500, Yii::t('error', '500'));
        }
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
        return $this->renderAjax('_orderedProducts', array(
            'model' => $this->findModel($order_id)
        ));
    }


}
