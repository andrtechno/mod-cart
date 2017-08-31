<?php

class CartWidget extends Widget {

    public $registerFile = array(
            // 'cartWidget.css',
            //'cartWidget.js',
    );

    public function init() {
        //if (!YII_DEBUG)
        //    Yii::import('mod.cart.CartModule');
        $this->assetsPath = dirname(__FILE__) . '/assets';
        $assetsUrl = Yii::app()->getAssetManager()->publish($this->assetsPath, false, -1, YII_DEBUG);
        $modele = Yii::app()->getModule('cart');
        Yii::app()->clientScript->registerScript('cart-widget', 'cart.skin="'.$this->skin.'";',  CClientScript::POS_BEGIN);
        $modele::registerAssets();
        parent::init();
    }

    public function run() {


        $cart = Yii::app()->cart;
        $currency = Yii::app()->currency->active;
        $items = $cart->getDataWithModels();
        $total = ShopProduct::formatPrice(Yii::app()->currency->convert($cart->getTotalPrice()));

        $dataRender = array(
            'count' => $cart->countItems(),
            'currency' => $currency,
            'total' => $total,
            'items' => $items
        );
        if ($this->skin == 'bootstrap') {
            //if (!Yii::app()->request->isAjaxRequest)
            //echo Html::tag('li', array('id' => 'cart','class'=>'dropdown'));
            $this->render($this->skin, $dataRender);
            //if (!Yii::app()->request->isAjaxRequest)
            //echo Html::closeTag('li');
        } else {
            if (!Yii::app()->request->isAjaxRequest)
                echo Html::tag('div', array('id' => 'cart'));
            $this->render($this->skin, $dataRender);
            if (!Yii::app()->request->isAjaxRequest)
                echo Html::closeTag('div');
        }
    }

}
