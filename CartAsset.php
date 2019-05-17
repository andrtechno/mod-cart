<?php

namespace panix\mod\cart;

use panix\engine\web\AssetBundle;

class CartAsset extends AssetBundle
{

    public $sourcePath = __DIR__ . '/assets';

    public $js = [
        'cart.js',
    ];

    public $depends = [
        'panix\engine\assets\NumberFormatAsset',
    ];
}
