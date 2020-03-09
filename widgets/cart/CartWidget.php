<?php

namespace panix\mod\cart\widgets\cart;

use Yii;
use yii\helpers\Html;
use panix\mod\shop\models\Product;
use panix\engine\data\Widget;
use yii\web\View;

class CartWidget extends Widget
{


    public function run()
    {

        $this->getView()->registerJs("cart.skin = '{$this->skin}';", View::POS_END);

        $cart = Yii::$app->cart;
        $currency = Yii::$app->currency->active;
        $items = $cart->getDataWithModels();
        //$items = $cart->data;
        $total = Yii::$app->currency->number_format($cart->getTotalPrice());
        $dataRender = [
            'count' => $cart->countItems(),
            'currency' => $currency,
            'total' => $total,
            'items' => $items
        ];
        if (!Yii::$app->request->isAjax)
            echo Html::beginTag('div', ['class' => 'cart']);
        echo $this->render($this->skin, $dataRender);
        if (!Yii::$app->request->isAjax)
            echo Html::endTag('div');
    }

}
