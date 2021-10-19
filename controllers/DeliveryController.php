<?php

namespace panix\mod\cart\controllers;

use panix\engine\CMS;
use Yii;
use panix\mod\cart\components\delivery\BaseDeliverySystem;
use panix\mod\cart\models\Delivery;

use yii\web\Controller;
use yii\web\Response;

class DeliveryController extends Controller
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
        $accept = explode(',',Yii::$app->request->headers['accept']);


        if ($system instanceof BaseDeliverySystem) {
            if(in_array('application/json',$accept)){ //JSON
                 Yii::$app->response->format = Response::FORMAT_JSON;
                return $system->processRequest($model);
            }
            return $system->processRequestHtml($model);



        }
    }



}
