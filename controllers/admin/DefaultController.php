<?php

namespace app\system\modules\shop\controllers\admin;

use Yii;
use panix\engine\controllers\AdminController;
use app\system\modules\shop\models\search\ShopProductSearch;
use yii\filters\VerbFilter;

class DefaultController extends AdminController {
public function actions()
{
    return [
        'moveNode' => [
            'class' => 'panix\engine\behaviors\nestedsets\actions\MoveNodeAction',
            'modelClass' => 'app\system\modules\shop\models\ShopCategory',
        ],
        'deleteNode' => [
            'class' => 'panix\engine\behaviors\nestedsets\actions\DeleteNodeAction',
            'modelClass' => 'app\system\modules\shop\models\ShopCategory',
        ],
        'updateNode' => [
            'class' => 'panix\engine\behaviors\nestedsets\actions\UpdateNodeAction',
            'modelClass' => 'app\system\modules\shop\models\ShopCategory',
        ],
        'createNode' => [
            'class' => 'panix\engine\behaviors\nestedsets\actions\CreateNodeAction',
            'modelClass' => 'app\system\modules\shop\models\ShopCategory',
        ],
    ];
}
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
        $searchModel = new ShopProductSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());
        return $this->render('index', [
                    'dataProvider' => $dataProvider,
                    'searchModel' => $searchModel,
                ]);
    }
    public function actionCreate() {
        
    }
    
    public function actionUpdate($id) {
        $model = $this->findModel($id);
//$model->setScenario("admin");
        $post = Yii::$app->request->post();
        if ($model->load($post) && $model->validate()) {
            $model->save();
            Yii::$app->session->addFlash('success', \Yii::t('app', 'SUCCESS_UPDATE'));
            // return $this->redirect(['index']);
            return Yii::$app->getResponse()->redirect(['/admin/shop/default']);
        }
        return $this->render('update', [
                    'model' => $model,
                ]);
    }
    
        protected function findModel($id) {
        $model = Yii::$app->getModule("shop")->model("ShopProduct");
        if (($model = $model::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
