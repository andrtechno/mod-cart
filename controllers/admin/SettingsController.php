<?php

namespace panix\mod\cart\controllers\admin;

use panix\engine\CMS;
use panix\mod\cart\models\Order;
use Yii;
use panix\engine\controllers\AdminController;
use panix\mod\cart\models\forms\SettingsForm;

class SettingsController extends AdminController
{

    public $icon = 'settings';

    public function actions()
    {
        return [
            'preview-mail' => [
                'class' => 'panix\engine\actions\PreviewMailAction',
                'data' => ['model' => Order::find()->limit(1)->one(), 'is_admin' => true]
            ],
            'preview-pdf' => [
                'class' => 'panix\engine\actions\PreviewMailAction',
                'data' => ['model' => Order::find()->limit(1)->one(), 'is_admin' => true]
            ],
        ];
    }

    public function actionIndex()
    {
        $this->pageName = Yii::t('app/default', 'SETTINGS');
        $this->view->params['breadcrumbs'][] =
            [
                'label' => $this->module->info['label'],
                'url' => $this->module->info['url'],

            ];


        $this->view->params['breadcrumbs'][] = $this->pageName;

        $model = new SettingsForm();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                $model->save();
                Yii::$app->session->setFlash("success", Yii::t('app/default', 'SUCCESS_UPDATE'));
                return $this->refresh();
            }
        }

        return $this->render('index', [
            'model' => $model
        ]);
    }

}
