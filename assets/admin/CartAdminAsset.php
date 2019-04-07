<?php


namespace panix\mod\cart\assets\admin;

use panix\engine\web\AssetBundle;


class CartAdminAsset extends AssetBundle {

    public $sourcePath = __DIR__;

    public $js = [
         'js/payment.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
