<?php


namespace panix\mod\cart\assets\admin;

use yii\web\AssetBundle;


class CartAdminAsset extends AssetBundle {

    public $sourcePath = '@vendor/panix/mod-cart/assets/admin';

    public $js = [
         'js/payment.js',
    ];

}
