<?php

namespace panix\mod\cart\assets;

use yii\web\AssetBundle;

class CartAsset extends AssetBundle {

    public $sourcePath = '@cart/assets';

    public $js = [
        'cart.js',
    ];

    public $depends = [
        'panix\engine\assets\NumberFormatAsset',
    ];
}
