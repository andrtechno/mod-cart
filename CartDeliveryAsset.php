<?php


namespace panix\mod\cart;

use panix\engine\web\AssetBundle;


class CartDeliveryAsset extends AssetBundle {

    public $sourcePath = __DIR__.'/assets';

    public $js = [
         'admin/js/delivery.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
