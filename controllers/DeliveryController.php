<?php

namespace panix\mod\cart\controllers;

use Yii;
use panix\mod\cart\components\delivery\BaseDeliverySystem;
use panix\mod\cart\models\Delivery;
use panix\engine\controllers\WebController;

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
        $model = Delivery::findOne($id);

        if (!$model)
            $this->error404();


        $system = $model->getDeliverySystemClass();

        if ($system instanceof BaseDeliverySystem) {
            //return $system->processRequest($model);
            return $system->renderDeliveryForm($model);

            // return $this->asJson($system->renderDeliveryForm($model));

            /*return $this->render("@cart/widgets/delivery/novaposhta/_view", [
                'cities' => ['test'],
                'address' => ['test'],
                'method' => $model
            ]);*/

        } else {
            $this->error404();
        }
    }

}
