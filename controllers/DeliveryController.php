<?php

namespace panix\mod\cart\controllers;

use panix\engine\CMS;
use Yii;
use panix\mod\cart\components\delivery\BaseDeliverySystem;
use panix\mod\cart\models\Delivery;
use panix\engine\controllers\WebController;
use yii\web\ForbiddenHttpException;

class DeliveryController extends WebController
{
    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function actionProcess($id)
    {

        // if(is_int($id)){
        $model = Delivery::findOne($id);
        // }else{
        //     $model = Delivery::findOne(['system'=>$id]);
        // }
        if (!$model)
            $this->error404();

        $system = $model->getDeliverySystemClass();

        if ($system instanceof BaseDeliverySystem) {
             return $system->processRequest($model);
        }else{
            throw new ForbiddenHttpException();
        }
    }

}
