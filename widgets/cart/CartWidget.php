<?php

namespace panix\mod\cart\widgets\cart;


use Yii;
use yii\helpers\Html;
use yii\web\View;
use panix\mod\shop\models\Product;
use panix\engine\data\Widget;
use panix\engine\CMS;

class CartWidget extends Widget
{

    /**
     * @inheritdoc
     */
    public function run()
    {

        $this->getView()->registerJs("cart.skin = '{$this->skin}';", View::POS_END);

        /** @var \panix\mod\cart\components\Cart $cart */
        $cart = Yii::$app->cart;
        $currency = Yii::$app->currency->active;
        $items = $cart->getDataWithModels();
        $dataRender = [
            'count' => $cart->countItems(),
            'currency' => $currency,
            'total' => $cart->getTotalPrice(),
            'items' => isset($items['items']) ? $items['items'] : []
        ];
        if (!Yii::$app->request->isAjax)
            echo Html::beginTag('div', ['class' => 'cart']);
        echo $this->render($this->skin, $dataRender);
        if (!Yii::$app->request->isAjax)
            echo Html::endTag('div');
    }

}
