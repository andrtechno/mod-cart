<?php

namespace panix\mod\cart\controllers\admin;

use Yii;
use panix\engine\controllers\AdminController;
use panix\mod\cart\models\search\OrderSearch;
use yii\web\NotFoundHttpException;
use panix\engine\pdf\Pdf;
use panix\mod\shop\models\ShopProduct;
use panix\mod\cart\models\OrderProduct;

class DefaultController extends AdminController {

    public function actionPrint($id) {
        $model = $this->findModel($id);

        $content = $this->renderPartial('_pdf_order', ['model' => $model]);


        $pdf = new Pdf([
            'content' => $content,
            'methods' => [
                'SetHeader' => ['CORNER CMS'],
                'SetFooter' => ['{PAGENO}'],
            ]
        ]);

        return $pdf->render();
    }

    public function actionIndex() {
        $this->pageName = Yii::t('cart/default', 'MODULE_NAME');
        $this->buttons = [
            [
                'label' => Yii::t('cart/default', 'CREATE_ORDER'),
                'url' => ['/admin/cart/default/create'],
                'options' => ['class' => 'btn btn-success']
            ]
        ];
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());
        return $this->render('index', [
                    'dataProvider' => $dataProvider,
                    'searchModel' => $searchModel,
        ]);
    }

    public function actionUpdate($id) {


        $model = $this->findModel($id);

        \panix\mod\cart\assets\admin\OrderAsset::register($this->view);
        $this->view->registerJs('
             var deleteQuestion = "' . Yii::t('cart/admin', 'Вы действительно удалить запись?') . '";
          var productSuccessAddedToOrder = "' . Yii::t('cart/admin', 'Продукт успешно добавлен к заказу.') . '";', \yii\web\View::POS_HEAD, 'myid'
        );

        $post = Yii::$app->request->post();
        if ($model->load($post) && $model->validate()) {
            $model->save();
            Yii::$app->session->addFlash('success', \Yii::t('app', 'SUCCESS_UPDATE'));
            // return $this->redirect(['index']);
            return Yii::$app->getResponse()->redirect(['/admin/cart/default']);
        }
        return $this->render('update', [
                    'model' => $model,
        ]);
    }

    public function actionAddProductList() {

        $order_id = Yii::$app->request->get('id');
        $model = $this->findModel($order_id);
        if ($order_id) {
            if (!Yii::$app->request->isAjax) {
                return $this->redirect(array('/admin/cart/default/update', 'id' => $order_id));
            }
        }
        if (!Yii::app()->request->isAjax) {
            return $this->redirect(array('/admin/cart/default/index'));
        }


        $searchModel = new ShopProduct();
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());


        echo $this->renderPartial('_addProduct', array(
            'dataProvider' => $dataProvider,
            'order_id' => $order_id,
            'model' => $model,
        ));
        die;
    }

    /**
     * Add product to order
     * @throws CHttpException
     */
    public function actionAddProduct() {
        if (Yii::$app->request->isPost) {
            if (Yii::$app->request->isAjax) {
                $order = $this->findModel($_POST['order_id']);
                $product = ShopProduct::findOne($_POST['product_id']);

                $find = OrderProduct::find()->where(array('order_id' => $order->id, 'product_id' => $product->id))->one();

                if ($find) {
                    if (Yii::$app->request->isAjax) {
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
                    if (Yii::$app->request->isAjax) {
                        echo \yii\helpers\Json::encode(array(
                            'success' => false,
                            'message' => Yii::t('cart/default', 'ERROR_PRODUCT_NO_FIND'),
                        ));
                        die;
                    } else {
                        throw new NotFoundHttpException(Yii::t('CartModule.default', 'ERROR_PRODUCT_NO_FIND'));
                    }
                }

                $order->addProduct($product, Yii::$app->request->post('quantity'), Yii::$app->request->post('price'));
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

    public function error404() {
        throw new NotFoundHttpException(Yii::t('cart/admin', 'ORDER_NOT_FOUND'));
    }

    /**
     * Delete product from order
     */
    public function actionDeleteProduct() {
        $order = $this->findModel(Yii::$app->request->post('order_id'));

        if (!$order)
            $this->error404();

        //if ($order->is_deleted)
        //    throw new NotFoundHttpException(Yii::t('cart/admin', 'ORDER_ISDELETED'));

        $order->deleteProduct(Yii::$app->request->post('id'));
    }
    public function actionRenderOrderedProducts($order_id) {
       echo $this->renderPartial('_orderedProducts', array(
            'model' => $this->findModel($order_id)
        ));
    }
    protected function findModel($id) {
        $model = new \panix\mod\cart\models\Order;
        if (($model = $model::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', '404'));
        }
    }

}
