<?php
namespace panix\mod\cart\controllers\admin;

use Yii;
use panix\mod\cart\models\search\DeliveryMethodSearch;
use panix\mod\cart\models\DeliveryMethod;
use panix\engine\grid\sortable\SortableGridAction;
class DeliveryController extends \panix\engine\controllers\AdminController {
    
    public function actions() {
        return [
            'dnd_sort' => [
                'class' => SortableGridAction::className(),
                'modelName' => DeliveryMethod::className(),
            ],
            'delete' => [
                'class' => 'panix\engine\grid\actions\DeleteAction',
                'modelClass' => DeliveryMethod::className(),
            ],
        ];
    }
/*
    public function actions() {
        return array(
            'order' => array(
                'class' => 'ext.adminList.actions.SortingAction',
            ),
            'switch' => array(
                'class' => 'ext.adminList.actions.SwitchAction',
            ),
            'sortable' => array(
                'class' => 'ext.sortable.SortableAction',
                'model' => ShopDeliveryMethod::model(),
            )
        );
    }
*/
    public function actionIndex() {
        $this->pageName = Yii::t('cart/admin', 'DELIVERY');
        $this->buttons = [
            [
                'icon' => 'icon-add',
                'label' => Yii::t('cart/admin', 'CREATE_DELIVERY'),
                'url' => ['create'],
                'options' => ['class' => 'btn btn-success']
            ]
        ];
        $this->breadcrumbs[] = [
            'label'=>Yii::t('cart/default', 'MODULE_NAME'),
            'url'=>['/admin/cart']
        ];
        $this->breadcrumbs[]=$this->pageName;
            
        $searchModel = new DeliveryMethodSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render('index', [
                    'dataProvider' => $dataProvider,
                    'searchModel' => $searchModel,
        ]);
    }


    public function actionUpdate($id = false) {


        if ($id === true) {
            $model = new DeliveryMethod();
        } else {
            $model = $this->findModel($id);
        }


        $this->pageName = Yii::t('cart/default', 'MODULE_NAME');
        $this->buttons = [
            [
                'icon' => 'icon-add',
                'label' => Yii::t('cart/admin', 'CREATE_DELIVERY'),
                'url' => ['create'],
                'options' => ['class' => 'btn btn-success']
            ]
        ];
        $this->breadcrumbs[] = [
            'label' => $this->pageName,
            'url' => ['index']
        ];
        $this->breadcrumbs[] = [
            'label' => Yii::t('cart/admin', 'DELIVERY'),
            'url' => ['index']
        ];
        $this->breadcrumbs[] = Yii::t('app', 'UPDATE');



        //$model->setScenario("admin");
        $post = Yii::$app->request->post();


        if ($model->load($post) && $model->validate()) {
            $model->save();
            Yii::$app->session->addFlash('success', \Yii::t('app', 'SUCCESS_CREATE'));
            if ($model->isNewRecord) {
                return Yii::$app->getResponse()->redirect(['/admin/cart/delivery']);
            } else {
                return Yii::$app->getResponse()->redirect(['/admin/cart/delivery/update', 'id' => $model->id]);
            }
        }

        echo $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Delete method
     * @param array $id
     */
    public function actionDelete($id = array()) {
        if (Yii::app()->request->isPostRequest) {
            $model = DeliveryMethod::model()->findAllByPk($_REQUEST['id']);

            if (!empty($model)) {
                foreach ($model as $m) {
                    if ($m->countOrders() == 0)
                        $m->delete();
                    else
                        throw new CHttpException(409, Yii::t('CartModule.admin', 'ERR_DEL_DELIVERY'));
                }
            }

            if (!Yii::app()->request->isAjaxRequest)
                $this->redirect('index');
        }
    }
    protected function findModel($id) {
        $model = new DeliveryMethod();
        if (($model = $model::findOne($id)) !== null) {
            return $model;
        } else {
            throw new \yii\web\NotFoundHttpException('The requested page does not exist.');
        }
    }
}
