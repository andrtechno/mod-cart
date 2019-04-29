<?php


namespace panix\mod\cart;

use panix\engine\web\AssetBundle;


class OrderAsset extends AssetBundle {

    public $sourcePath = __DIR__.'/assets';
    public $js = [
         'admin/js/orders.update.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
