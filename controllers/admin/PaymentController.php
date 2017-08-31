<?php

namespace panix\mod\cart\controllers\admin;

use Yii;
use panix\mod\cart\models\search\PaymentMethodSearch;
use panix\mod\cart\models\PaymentMethod;
use panix\engine\grid\sortable\SortableGridAction;

class PaymentController extends \panix\engine\controllers\AdminController {

    public $icon = 'icon-creditcard';
    public function actions() {
        return [
            'dnd_sort' => [
                'class' => SortableGridAction::className(),
                'modelName' => PaymentMethod::className(),
            ],
            'delete' => [
                'class' => 'panix\engine\grid\actions\DeleteAction',
                'modelClass' => PaymentMethod::className(),
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
      'model' => DeliveryMethod::model(),
      )
      );
      }
     */

    public function actionIndex() {
        $this->pageName = Yii::t('cart/admin', 'PAYMENTS');
        $this->buttons = [
            [
                'icon' => 'icon-add',
                'label' => Yii::t('cart/admin', 'CREATE_PAYMENT'),
                'url' => ['create'],
                'options' => ['class' => 'btn btn-success']
            ]
        ];
        $this->breadcrumbs[] = [
            'label' => Yii::t('cart/default', 'MODULE_NAME'),
            'url' => ['/admin/cart']
        ];
        $this->breadcrumbs[] = $this->pageName;

        $searchModel = new PaymentMethodSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render('index', [
                    'dataProvider' => $dataProvider,
                    'searchModel' => $searchModel,
        ]);
    }

    public function actionUpdate($id = false) {


        if ($id === true) {
            $model = new PaymentMethod();
        } else {
            $model = $this->findModel($id);
        }


        $this->pageName = Yii::t('cart/default', 'MODULE_NAME');
        $this->buttons = [
            [
                'icon' => 'icon-add',
                'label' => Yii::t('cart/admin', 'CREATE_PAYMENT'),
                'url' => ['create'],
                'options' => ['class' => 'btn btn-success']
            ]
        ];
        $this->breadcrumbs[] = [
            'label' => $this->pageName,
            'url' => ['index']
        ];
        $this->breadcrumbs[] = [
            'label' => Yii::t('cart/admin', 'PAYMENTS'),
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


    protected function findModel($id) {
        $model = new PaymentMethod();
        if (($model = $model::findOne($id)) !== null) {
            return $model;
        } else {
            throw new \yii\web\NotFoundHttpException('The requested page does not exist.');
        }
    }
    /**
     * Renders payment system configuration form
     */
    public function actionRenderConfigurationForm() {
        Yii::import('mod.cart.CartModule');
        $systemId = Yii::app()->request->getQuery('system');
        $paymentMethodId = Yii::app()->request->getQuery('payment_method_id');
        if (empty($systemId))
            exit;
        $manager = new PaymentSystemManager;
        $system = $manager->getSystemClass($systemId);
        echo $system->getConfigurationFormHtml($paymentMethodId);
    }
}
