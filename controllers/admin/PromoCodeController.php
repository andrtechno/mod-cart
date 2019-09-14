<?php

namespace panix\mod\cart\controllers\admin;

use Yii;
use panix\engine\controllers\AdminController;
use panix\mod\cart\models\PromoCode;
use panix\mod\cart\models\search\PromoCodeSearch;

class PromoCodeController extends AdminController
{


    public function actionIndex()
    {
        $this->pageName = Yii::t('cart/admin', 'PROMOCODE');
        $this->buttons = [
            [
                'label' => Yii::t('cart/admin', 'CREATE_PROMOCODE'),
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
        $model = PromoCode::findModel($id);
        $this->pageName = Yii::t('cart/admin', 'PROMOCODE');
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
