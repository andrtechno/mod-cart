<?php

namespace panix\mod\cart\controllers\admin;

use Yii;
use panix\engine\controllers\AdminController;
use panix\mod\cart\models\search\PromoCodeSearch;

class PromoCodeController extends AdminController
{


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

        $searchModel = new PromoCodeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    public function actionUpdate($id = false)
    {
        $model = PromoCodeSearch::findModel($id, Yii::t('cart/admin', 'ORDER_NOT_FOUND'));
        $this->pageName = Yii::t('cart/admin', 'ORDERS');
        $this->breadcrumbs = [
            $this->pageName
        ];

        /*  $this->buttons = [
              [
                  'label' => Yii::t('cart/admin', 'PRINT'),
                  'icon' => 'print',
                  'url' => ['print', 'id' => $model->id],
                  'options' => ['class' => 'btn btn-success']
              ]
          ];*/
        $isNew = $model->isNewRecord;
        $post = Yii::$app->request->post();
        if ($model->load($post) && $model->validate()) {
            $model->save();
            $this->redirectPage($isNew, $post);
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }


}
