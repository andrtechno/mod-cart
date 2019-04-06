<?php

namespace panix\mod\cart\controllers\admin;

use Yii;
use panix\engine\controllers\AdminController;
use panix\mod\cart\models\forms\SettingsForm;

class SettingsController extends AdminController
{

    public $icon = 'settings';

    public function actionIndex()
    {
        $this->pageName = Yii::t('app', 'SETTINGS');
        $this->breadcrumbs[] =
            [
                'label' => $this->module->info['label'],
                'url' => $this->module->info['url'],

            ];


        $this->breadcrumbs[] = $this->pageName;

        $model = new SettingsForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->save();
        }
        return $this->render('index', [
            'model' => $model
        ]);
    }

}
