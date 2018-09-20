<?php

namespace panix\mod\cart\widgets\cart;

use Yii;
use yii\helpers\Html;
use panix\mod\shop\models\Product;

class CartWidget extends \panix\engine\data\Widget {


    public function run() {
        $cart = Yii::$app->cart;
        $currency = Yii::$app->currency->active;
        $items = $cart->getDataWithModels();
        $total = Yii::$app->currency->number_format($cart->getTotalPrice());
        $dataRender = [
            'count' => $cart->countItems(),
            'currency' => $currency,
            'total' => $total,
            'items' => $items
        ];
        if (!Yii::$app->request->isAjax)
            echo Html::beginTag('div', array('id' => 'cart'));
        echo $this->render($this->skin, $dataRender);
        if (!Yii::$app->request->isAjax)
            echo Html::endTag('div');
    }

}
