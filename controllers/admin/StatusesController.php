<?php

namespace panix\mod\cart\controllers\admin;


use Yii;
use panix\engine\controllers\AdminController;
use panix\mod\cart\models\OrderStatus;
use panix\mod\cart\models\search\OrderStatusSearch;
use yii\web\HttpException;

class StatusesController extends AdminController {

    /**
     * Display statuses list
     */
    public function actionIndex() {
        $this->pageName = Yii::t('cart/admin', 'STATUSES');
        $this->breadcrumbs[] = [
            'label'=>Yii::t('cart/admin', 'ORDERS'),
            'url'=>['/admin/cart']
        ];
        $this->breadcrumbs[]=$this->pageName;

        $this->buttons = [
            [
                'label' => Yii::t('cart/default', 'CREATE_STATUS'),
                'url' => ['/cart/statuses/create'],
                'options' => ['class' => 'btn btn-success']
            ]
        ];

        $searchModel = new OrderStatusSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render('index', [
                    'dataProvider' => $dataProvider,
                    'searchModel' => $searchModel,
        ]);
    }

    /**
     * Update status
     * @param bool $id
     * @return string|\yii\web\Response
     */
    public function actionUpdate($id = false) {
        if ($id === true) {
            $model = new OrderStatus;
        } else
            $model = OrderStatus::findOne($id);

        if (!$model)
            $this->error404(Yii::t('cart/admin', 'NO_STATUSES'));

        
        $title = ($model->isNewRecord) ? Yii::t('cart/admin', 'CREATE_STATUSES') :
                Yii::t('cart/admin', 'UPDATE_STATUSES');

        
        $this->breadcrumbs[] = [
            'label'=>Yii::t('cart/admin', 'ORDERS'),
            'url'=>['/admin/cart']
        ];
        
        $this->breadcrumbs[] = [
            'label'=>Yii::t('cart/admin', 'STATUSES'),
            'url'=>['index']
        ];
        
        $this->breadcrumbs[] = $title;

        $this->pageName = $title;

        $post = Yii::$app->request->post();


        if ($model->load($post) && $model->validate()) {
                $model->save();
                return $this->redirect('index');
            }


        return $this->render('update', array(
            'model' => $model,
        ));
    }

    /**
     * Delete status
     * @param array $id
     */
    public function actionDelete($id = array()) {
        if (Yii::$app->request->isPost) {
            $model = OrderStatus::model()->findAllByPk($_REQUEST['id']);

            if (!empty($model)) {
                foreach ($model as $m) {
                    if ($m->countOrders() == 0 && $m->id != 1)
                        $m->delete();
                    else
                        throw new HttpException(409, Yii::t('cart/admin', 'ERR_DELETE_STATUS'));
                }
            }

            if (!Yii::$app->request->isAjax)
                return $this->redirect('index');
        }
    }

    /**
     * Дополнительное меню Контроллера.
     * @return array
     */
    public function getAddonsMenu22() {
        return array(
            array(
                'label' => Yii::t('cart/admin', 'ORDER', 0),
                'url' => array('/admin/cart'),
                'icon' => Html::icon('icon-cart'),
           
            ),
            array(
                'label' => Yii::t('cart/admin', 'STATS'),
                'url' => array('/admin/cart/statistics'),
                'icon' => Html::icon('icon-stats'),
             
            ),
            array(
                'label' => Yii::t('cart/admin', 'HISTORY'),
                'url' => array('/admin/cart/history'),
                'icon' => Html::icon('icon-history'),
             
            ),
        );
    }

}
