<?php

namespace panix\mod\cart\controllers\admin;

use panix\mod\cart\components\delivery\DeliverySystemManager;
use Yii;
use panix\mod\cart\models\search\DeliverySearch;
use panix\mod\cart\models\Delivery;
use panix\engine\controllers\AdminController;

class DeliveryController extends AdminController {

    public $icon = 'delivery';

    public function actions() {
        return [
            'sortable' => [
                'class' => \panix\engine\grid\sortable\Action::class,
                'modelClass' => Delivery::class,
            ],
            'delete' => [
                'class' => 'panix\engine\grid\actions\DeleteAction',
                'modelClass' => Delivery::class,
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
        $this->pageName = Yii::t('cart/admin', 'DELIVERY');
        $this->buttons = [
            [
                'icon' => 'add',
                'label' => Yii::t('cart/admin', 'CREATE_DELIVERY'),
                'url' => ['create'],
                'options' => ['class' => 'btn btn-success']
            ]
        ];
        $this->breadcrumbs[] = [
            'label' => Yii::t('cart/default', 'MODULE_NAME'),
            'url' => ['/admin/cart']
        ];
        $this->breadcrumbs[] = $this->pageName;

        $searchModel = new DeliverySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render('index', [
                    'dataProvider' => $dataProvider,
                    'searchModel' => $searchModel,
        ]);
    }

    public function actionUpdate($id = false) {

            $model = Delivery::findModel($id);

        \panix\mod\cart\CartDeliveryAsset::register($this->view);

        $this->pageName = Yii::t('cart/default', 'MODULE_NAME');
        $this->buttons = [
            [
                'icon' => 'add',
                'label' => Yii::t('cart/admin', 'CREATE_DELIVERY'),
                'url' => ['create'],
                'options' => ['class' => 'btn btn-success']
            ],

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

        $post = Yii::$app->request->post();

        $isNew = $model->isNewRecord;
        if ($model->load($post) && $model->validate()) {
            $model->save();

            if ($model->system) {
                $manager = new DeliverySystemManager;
                $system = $manager->getSystemClass($model->system);
                $system->saveAdminSettings($model->id, $_POST);
            }

            $this->redirectPage($isNew, $post);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Delete method
     * @param array $id
     */
    public function actionDelete($id = array()) {
        if (Yii::$app->request->isPostRequest) {
            $model = Delivery::find()->findAllByPk($_REQUEST['id']);

            if (!empty($model)) {
                foreach ($model as $m) {
                    if ($m->countOrders() == 0)
                        $m->delete();
                    else
                        throw new CHttpException(409, Yii::t('CartModule.admin', 'ERR_DEL_DELIVERY'));
                }
            }

            if (!Yii::$app->request->isAjaxRequest)
                $this->redirect('index');
        }
    }


    /**
     * Renders payment system configuration form
     */
    public function actionRenderConfigurationForm()
    {

        $systemId = Yii::$app->request->get('system');
        $delivery_id = Yii::$app->request->get('delivery_id');
        if (empty($systemId))
            exit;
        $manager = new DeliverySystemManager();
        $system = $manager->getSystemClass($systemId);

        return $this->renderPartial('@cart/widgets/delivery/' . $systemId . '/_form', ['model' => $system->getConfigurationFormHtml($delivery_id)]);
    }


}
