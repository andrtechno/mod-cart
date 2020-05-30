<?php

namespace panix\mod\cart\controllers\admin;

use panix\engine\Html;
use Yii;
use panix\engine\controllers\AdminController;
use panix\mod\cart\models\OrderStatus;
use panix\mod\cart\models\search\OrderStatusSearch;
use yii\web\HttpException;

class StatusesController extends AdminController
{
    public function actions()
    {
        return [
            'delete' => [
                'class' => 'panix\engine\actions\DeleteAction',
                'modelClass' => OrderStatus::class,
            ],
        ];
    }

    /**
     * Display statuses list
     */
    public function actionIndex()
    {
        $this->pageName = Yii::t('cart/admin', 'STATUSES');
        $this->breadcrumbs[] = [
            'label' => Yii::t('cart/admin', 'ORDERS'),
            'url' => ['/admin/cart']
        ];
        $this->breadcrumbs[] = $this->pageName;
        if (Yii::$app->user->can("/{$this->module->id}/{$this->id}/*") || Yii::$app->user->can("/{$this->module->id}/{$this->id}/create")) {
            $this->buttons = [
                [
                    'label' => Yii::t('app/default', 'CREATE'),
                    'url' => ['create'],
                    'icon' => 'add',
                    'options' => ['class' => 'btn btn-success']
                ]
            ];
        }
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
    public function actionUpdate($id = false)
    {

        $model = OrderStatus::findModel($id, Yii::t('cart/admin', 'NO_STATUSES'));


        $title = ($model->isNewRecord) ? Yii::t('cart/admin', 'CREATE_STATUSES') :
            Yii::t('cart/admin', 'UPDATE_STATUSES');

        $this->pageName = $title;

        $this->breadcrumbs[] = [
            'label' => Yii::t('cart/admin', 'ORDERS'),
            'url' => ['/admin/cart']
        ];

        $this->breadcrumbs[] = [
            'label' => Yii::t('cart/admin', 'STATUSES'),
            'url' => ['index']
        ];

        $this->breadcrumbs[] = $this->pageName;

        $post = Yii::$app->request->post();

        $isNew = $model->isNewRecord;
        if ($model->load($post) && $model->validate()) {
            $model->save();
            return $this->redirectPage($isNew, $post);
        }


        return $this->render('update', array(
            'model' => $model,
        ));
    }

    /**
     * Delete status
     *
     * @param array $id
     * @return \yii\web\Response
     * @throws HttpException
     */
    public function actionDelete($id = [])
    {
        if (Yii::$app->request->isPost) {
            $model = OrderStatus::find()->where(['id' => $_REQUEST['id']])->all();

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
    public function getAddonsMenu22()
    {
        return array(
            array(
                'label' => Yii::t('cart/admin', 'ORDER', 0),
                'url' => ['/admin/cart'],
                'icon' => Html::icon('cart'),

            ),
            array(
                'label' => Yii::t('cart/admin', 'STATS'),
                'url' => ['/admin/cart/statistics'],
                'icon' => Html::icon('stats'),

            ),
            array(
                'label' => Yii::t('cart/admin', 'HISTORY'),
                'url' => ['/admin/cart/history'],
                'icon' => Html::icon('history'),

            ),
        );
    }

    public function actionCreate()
    {
        return $this->actionUpdate(false);
    }
}
