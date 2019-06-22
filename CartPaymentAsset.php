<?php


namespace panix\mod\cart;

use panix\engine\web\AssetBundle;


class CartPaymentAsset extends AssetBundle {

    public $sourcePath = __DIR__.'/assets';

    public $js = [
         'admin/js/payment.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
