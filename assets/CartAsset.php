<?php

namespace panix\mod\cart\assets;

use yii\web\AssetBundle;

class CartAsset extends AssetBundle {

    public $sourcePath = '@cart/assets';
    public $jsOptions = array(
        'position' => \yii\web\View::POS_BEGIN
    );
    public $js = [
        'cart.js',
        'number_format.js',
    ];

}
