<?php


namespace panix\mod\cart\assets\admin;

use panix\engine\web\AssetBundle;


class OrderAsset extends AssetBundle {

    public $sourcePath = __DIR__;
    public $js = [
         'js/orders.update.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
