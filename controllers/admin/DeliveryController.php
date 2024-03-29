<?php

namespace panix\mod\cart\controllers\admin;


use panix\engine\CMS;
use panix\mod\cart\components\delivery\BaseDeliverySystem;
use panix\mod\cart\components\delivery\DeliverySystemManager;
use Yii;
use panix\mod\cart\models\search\DeliverySearch;
use panix\mod\cart\models\Delivery;
use panix\engine\controllers\AdminController;
use yii\web\ForbiddenHttpException;
use yii\web\HttpException;

class DeliveryController extends AdminController
{

    public $icon = 'delivery';

    public function actions()
    {
        return [
            'sortable' => [
                'class' => 'panix\engine\grid\sortable\Action',
                'modelClass' => Delivery::class,
            ],
            'delete' => [
                'class' => 'panix\engine\actions\DeleteAction',
                'modelClass' => Delivery::class,
            ],
            'switch' => [
                'class' => 'panix\engine\actions\SwitchAction',
                'modelClass' => Delivery::class,
            ],
        ];
    }

    public function beforeAction($action)
    {
        if ($action == 'process') {
            $this->enableCsrfValidation = false;
        }

        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        $this->pageName = Yii::t('cart/admin', 'DELIVERY');
        if (Yii::$app->user->can("/{$this->module->id}/{$this->id}/*") || Yii::$app->user->can("/{$this->module->id}/{$this->id}/create")) {
            $this->buttons = [
                [
                    'icon' => 'add',
                    'label' => Yii::t('app/default', 'CREATE'),
                    'url' => ['create'],
                    'options' => ['class' => 'btn btn-success']
                ],
            ];
        }
        $this->view->params['breadcrumbs'][] = [
            'label' => Yii::t('cart/default', 'MODULE_NAME'),
            'url' => ['/admin/cart']
        ];
        $this->view->params['breadcrumbs'][] = $this->pageName;

        $searchModel = new DeliverySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    public function actionUpdate($id = false)
    {
        $model = Delivery::findModel($id);
        $isNew = $model->isNewRecord;
        \panix\mod\cart\CartDeliveryAsset::register($this->view);

        $this->view->params['breadcrumbs'][] = [
            'label' => Yii::t('cart/admin', 'ORDERS'),
            'url' => ['/admin/cart']
        ];
        $this->view->params['breadcrumbs'][] = [
            'label' => Yii::t('cart/admin', 'DELIVERY'),
            'url' => ['index']
        ];

        $this->pageName = Yii::t('app/default', $isNew ? 'CREATE' : 'UPDATE');

        $this->view->params['breadcrumbs'][] = $this->pageName;

        $post = Yii::$app->request->post();


        if ($model->load($post)) {
            if ($model->validate()) {
                $model->save();

                if ($model->system) {
                    $manager = new DeliverySystemManager;
                    $system = $manager->getSystemClass($model->system);

                    $modelD = $system->getModelConfig();
                    if ($modelD) {
                        if ($modelD->load($post)) {
                            if ($modelD->validate()) {
                                $system->setSettings($model->id, Yii::$app->request->post($system->getModelName()));
                            } else {
                                CMS::dump($modelD->getErrors());
                                die;
                                //echo 'ERRR';die;

                            }
                        }
                    }
                }
                return $this->redirectPage($isNew, $post);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Delete method
     * @param array $id
     */
    public function actionDelete($id = [])
    {
        if (Yii::$app->request->isPost) {
            $model = Delivery::find()->where(['id' => $_REQUEST['id']])->all();

            if (!empty($model)) {
                foreach ($model as $m) {
                    if ($m->countOrders() == 0)
                        $m->delete();
                    else
                        throw new HttpException(409, Yii::t('cart/admin', 'ERR_DEL_DELIVERY'));
                }
            }

            if (!Yii::$app->request->isAjax)
                return $this->redirect('index');
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
        $model = $system->getModelConfig();
        if ($model) {
            $model->attributes = (array)$system->getSettings($delivery_id);
        } else {
            return;
        }

        return $this->renderAjax('@cart/widgets/delivery/' . $systemId . '/_form', ['model' => $model]);
    }

    public function actionCreate()
    {
        return $this->actionUpdate(false);
    }


    public function actionProcess($id)
    {
        $model = Delivery::findOne($id);
        if (!$model)
            $this->error404();

        $system = $model->getDeliverySystemClass();

        if ($system instanceof BaseDeliverySystem) {
            return $system->processRequestAdmin($model);
        } else {
            throw new ForbiddenHttpException();
        }
    }
}
