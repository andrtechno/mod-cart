<?php

namespace panix\mod\cart;

use panix\engine\web\AssetBundle;

class HammerAsset extends AssetBundle
{

    public $sourcePath = __DIR__ . '/assets';
    public $js = [
        'admin/js/hammer.min.js',
        'admin/js/jquery.hammer.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];

}
