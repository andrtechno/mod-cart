<?php

namespace app\system\modules\shop\controllers\admin;

use Yii;
use panix\engine\controllers\AdminController;
use app\system\modules\shop\models\search\ShopProductSearch;
use yii\filters\VerbFilter;

class CategoriesController extends AdminController {


    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
            ],
                ],
        ];
    }

    public function actionIndex() {
        $this->pageName = Yii::t('shop/default', 'MODNAME');
        $this->buttons = [
            [
                'label' => Yii::t('shop/default', 'CREATE_PRODUCT'),
                'url' => ['/admin/shop/default/create'],
                'options' => ['class' => 'btn btn-success']
            ]
        ];

        return $this->render('index', []);
    }

 

}
