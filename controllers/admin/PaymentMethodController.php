<?php
namespace panix\mod\cart\controllers\admin;



class PaymentMethodController extends \panix\engine\controllers\AdminController {

    public function actions() {
        return array(
            'order' => array(
                'class' => 'ext.adminList.actions.SortingAction',
            ),
            'switch' => array(
                'class' => 'ext.adminList.actions.SwitchAction',
            ),
            'delete' => array(
                'class' => 'ext.adminList.actions.DeleteAction',
            ),
            'sortable' => array(
                'class' => 'ext.sortable.SortableAction',
                'model' => ShopPaymentMethod::model(),
            )
        );
    }

    public function allowedActions() {
        return 'renderConfigurationForm';
    }

    public function actionIndex() {
        $model = new ShopPaymentMethod('search');
        $this->icon = $this->module->adminMenu['orders']['items'][5]['icon'];
        if (!empty($_GET['ShopDeliveryMethod']))
            $model->attributes = $_GET['ShopPaymentMethod'];

        $dataProvider = $model->search();
        $this->pageName = Yii::t('CartModule.admin', 'PAYMENTS');

        $this->breadcrumbs = array(
            Yii::t('CartModule.default', 'MODULE_NAME') => array('/admin/cart'),
            $this->pageName
        );

        $this->topButtons = array(array('label' => Yii::t('CartModule.admin', 'Создать способ оплаты'), 'url' => $this->createUrl('create'), 'htmlOptions' => array('class' => 'btn btn-success')));

        $this->render('index', array(
            'model' => $model,
            'dataProvider' => $dataProvider,
        ));
    }

    /**
     * Update payment method
     * @param bool $new
     * @throws CHttpException
     */
    public function actionUpdate($new = false) {


        if ($new === true) {
            $model = new ShopPaymentMethod;
            $model->unsetAttributes();
        } else
            $model = ShopPaymentMethod::model()->language(Yii::app()->language->active)->findByPk($_GET['id']);

        if (!$model)
            throw new CHttpException(404, Yii::t('CartModule.admin', 'Способ оплаты не найден.'));

        $this->icon = $this->module->adminMenu['orders']['items'][5]['icon'];
        Yii::app()->clientScript->registerScriptFile($this->module->assetsUrl . '/admin/payment.js');

        $this->pageName = ($model->isNewRecord) ? $model::t('IS_CREATE', 0) : $model::t('IS_CREATE', 1);

        $this->breadcrumbs = array(
            Yii::t('CartModule.default', 'MODULE_NAME') => array('/admin/shop'),
            Yii::t('CartModule.admin', 'PAYMENTS') => $this->createUrl('index'),
            $this->pageName
        );



        // $form = new CMSForm($model->config, $model);

        if (Yii::app()->request->isPostRequest) {
            $model->attributes = $_POST['ShopPaymentMethod'];

            if ($model->validate()) {
                $model->save();

                if ($model->payment_system) {
                    $manager = new PaymentSystemManager;
                    $system = $manager->getSystemClass($model->payment_system);
                    $system->saveAdminSettings($model->id, $_POST);
                }
                $this->redirect('index');
            }
        }

        $this->render('update', array(
            'model' => $model,
        ));
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
