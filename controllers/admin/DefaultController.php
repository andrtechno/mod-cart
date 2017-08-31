<?php

namespace panix\mod\cart\controllers\admin;

use Yii;
use panix\engine\controllers\AdminController;
use panix\mod\cart\models\search\OrderSearch;
use yii\web\NotFoundHttpException;
class DefaultController extends AdminController {

    public function actionIndex() {
        $this->pageName = Yii::t('cart/default', 'MODULE_NAME');
        $this->buttons = [
            [
                'label' => Yii::t('cart/default', 'CREATE_PRODUCT'),
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
//$model->setScenario("admin");
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

    protected function findModel($id) {
        $model = new \panix\mod\cart\models\Order;
        if (($model = $model::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
