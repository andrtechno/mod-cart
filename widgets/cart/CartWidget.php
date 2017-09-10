<?php

namespace panix\mod\cart\widgets\cart;

use Yii;
use yii\helpers\Html;
use panix\mod\shop\models\ShopProduct;

class CartWidget extends \panix\engine\data\Widget {

    public function init() {

        parent::init();
    }

    public function run() {


        $cart = Yii::$app->cart;
        $currency = Yii::$app->currency->active;
        $items = $cart->getDataWithModels();
        $total = ShopProduct::formatPrice(Yii::$app->currency->convert($cart->getTotalPrice()));

        $dataRender = [
            'count' => $cart->countItems(),
            'currency' => $currency,
            'total' => $total,
            'items' => $items
        ];

        if (!Yii::$app->request->isAjax)
            echo Html::beginTag('div', array('id' => 'cart'));
        echo $this->render('default', $dataRender);
        if (!Yii::$app->request->isAjax)
            echo Html::endTag('div');
    }

}