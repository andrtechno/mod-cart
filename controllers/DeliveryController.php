<?php

namespace panix\mod\cart\controllers;

use panix\engine\CMS;
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

       // if(is_int($id)){
            $model = Delivery::findOne($id);
       // }else{
       //     $model = Delivery::findOne(['system'=>$id]);
       // }
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

        }
    }


    public function actionProcess2($id)
    {

        //if(is_int($id)){
            $model = Delivery::findOne($id);
        //}else{
        //    $model = Delivery::findOne(['system'=>$id]);
        //}
        if (!$model)
            $this->error404();

        $system = $model->getDeliverySystemClass();

        if ($system instanceof BaseDeliverySystem) {
            //return $system->processRequest($model);


            return $this->asJson($system->processRequest($model));
           // return $system->renderDeliveryForm($model);

            // return $this->asJson($system->renderDeliveryForm($model));

            /*return $this->render("@cart/widgets/delivery/novaposhta/_view", [
                'cities' => ['test'],
                'address' => ['test'],
                'method' => $model
            ]);*/

        }
    }


    public function actionTest(){

    }

}
