<?php
namespace panix\mod\cart\controllers;

use panix\engine\controllers\WebController;
use panix\mod\shop\models\Product;

class NotifyController extends WebController {

    public function init() {
        Yii::app()->request->enableCsrfValidation = false;
        parent::init();
    }

    public function actionIndex() {
        header('Content-Type: application/json');
        $json=array();
        $product = Product::model()->findByPk(Yii::$app->request->post('product_id'));

        if (!$product)
            throw new CHttpException(404);

        $record = new ProductNotifications();
        if (isset($_POST['ProductNotifications'])) {
            $record->attributes = array('email' => $_POST['ProductNotifications']['email']);
            $record->product_id = $product->id;
            if ($record->validate() && $record->hasEmail() === false) {
                $record->save();
                $json['message']='Мы сообщим вам когда товар появится в наличии';
                $json['status']='OK';
            }else{
                $json['message']='Ошибка';
                $json['status']='ERROR';
            }
        }
        $json['data']=$this->renderPartial('_form', array('model' => $record, 'product' => $product),true);

        echo CJSON::encode($json);
       // $this->render('_form', array('model' => $record, 'product' => $product));
    }

}