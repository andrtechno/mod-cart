<?php

namespace panix\mod\cart\controllers\admin;

use Yii;
use panix\mod\cart\models\search\PaymentSearch;
use panix\mod\cart\models\Payment;
use panix\mod\cart\components\payment\PaymentSystemManager;
use panix\engine\Html;
class PaymentController extends \panix\engine\controllers\AdminController {

    public $icon = 'icon-creditcard';

    public function actions() {
        return [
            'sortable' => [
                'class' => \panix\engine\grid\sortable\Action::class,
                'modelClass' => Payment::class,
            ],
            'delete' => [
                'class' => 'panix\engine\grid\actions\DeleteAction',
                'modelClass' => Payment::class,
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
      'model' => Delivery::model(),
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

        $searchModel = new PaymentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render('index', [
                    'dataProvider' => $dataProvider,
                    'searchModel' => $searchModel,
        ]);
    }

    public function actionUpdate($id = false) {
        if ($id === true) {
            $model = new Payment();
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
        \panix\mod\cart\assets\admin\CartAdminAsset::register($this->view);


        //$model->setScenario("admin");
        $post = Yii::$app->request->post();


        if ($model->load($post) && $model->validate()) {
            $model->save();

            /*    if ($model->payment_system) {
              $manager = new PaymentSystemManager;
              $system = $manager->getSystemClass($model->payment_system);
              $system->saveAdminSettings($model->id, $_POST);
              } */

            Yii::$app->session->setFlash('success', \Yii::t('app', 'SUCCESS_CREATE'));
            if ($model->isNewRecord) {
                return Yii::$app->getResponse()->redirect(['/admin/cart/payment']);
            } else {
                return Yii::$app->getResponse()->redirect(['/admin/cart/payment/update', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    protected function findModel($id) {
        $model = new Payment();
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

        $systemId = Yii::$app->request->get('system');
        $paymentMethodId = Yii::$app->request->get('payment_method_id');
        if (empty($systemId))
            exit;
        $manager = new PaymentSystemManager;
        $system = $manager->getSystemClass($systemId);

        echo $system->getConfigurationFormHtml($paymentMethodId);
    }

    /**
     * Дополнительное меню Контроллера.
     * @return array
     */
    public function getAddonsMenu() {
        return array(
            array(
                'label' => Yii::t('cart/admin', 'STATUSES'),
                'url' => array('/admin/cart/statuses'),
            ),
            array(
                'label' => Yii::t('cart/admin', 'STATS'),
                'url' => array('/admin/cart/statistics'),
                'icon' => Html::icon('stats'),
            ),
            array(
                'label' => Yii::t('cart/admin', 'HISTORY'),
                'url' => array('/admin/cart/history'),
                'icon' => Html::icon('history'),
            ),
            array(
                'label' => Yii::t('app', 'SETTINGS'),
                'url' => array('/admin/cart/settings'),
                'icon' => Html::icon('settings'),
            ),
        );
    }

}
