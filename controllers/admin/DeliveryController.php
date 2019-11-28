<?php

namespace panix\mod\cart\controllers\admin;


use panix\mod\cart\components\delivery\DeliverySystemManager;
use Yii;
use panix\mod\cart\models\search\DeliverySearch;
use panix\mod\cart\models\Delivery;
use panix\engine\controllers\AdminController;
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

    public function actionIndex()
    {
        $this->pageName = Yii::t('cart/admin', 'DELIVERY');

        $this->buttons = [
            [
                'icon' => 'add',
                'label' => Yii::t('app', 'CREATE'),
                'url' => ['create'],
                'options' => ['class' => 'btn btn-success']
            ],

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

    public function actionUpdate($id = false)
    {
        $model = Delivery::findModel($id);
        $isNew = $model->isNewRecord;
        \panix\mod\cart\CartDeliveryAsset::register($this->view);

        $this->breadcrumbs[] = [
            'label' => Yii::t('cart/admin', 'ORDERS'),
            'url' => ['/admin/cart']
        ];
        $this->breadcrumbs[] = [
            'label' => Yii::t('cart/admin', 'DELIVERY'),
            'url' => ['index']
        ];

        $this->pageName = Yii::t('app', $isNew ? 'CREATE' : 'UPDATE');

        $this->breadcrumbs[] = $this->pageName;

        $post = Yii::$app->request->post();


        if ($model->load($post)) {
            if($model->validate()){
                $model->save();

                if ($model->system) {
                    $manager = new DeliverySystemManager;
                    $system = $manager->getSystemClass($model->system);
                    $system->saveAdminSettings($model->id, $_POST);
                }

                $this->redirectPage($isNew, $post);
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
            $model = Delivery::find()->where(['id'=>$_REQUEST['id']])->all();

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

        return $this->renderPartial('@cart/widgets/delivery/' . $systemId . '/_form', ['model' => $system->getConfigurationFormHtml($delivery_id)]);
    }


}
