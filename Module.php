<?php

/**
 * @author Andrew S. <andrew.panix@gmail.com>
 * @version 0.1
 */

namespace panix\mod\cart;

use Yii;
use panix\engine\WebModule;

//use yii\base\BootstrapInterface;


class Module extends WebModule { // implements BootstrapInterface/

    public $routes = [
        'cart' => 'cart/default/index',
        'cart/view/<secret_key>' => 'cart/default/view',
        'cart/<action:[.\w]+>' => 'cart/default/<action>',
        'cart/<action:[.\w]>/*' => 'cart/default/<action>',
    ];

    public function getInfo() {
        return [
            'name' => Yii::t('cart/default', 'MODULE_NAME'),
            'author' => 'andrew.panix@gmail.com',
            'version' => '1.0',
            'icon' => 'fa-coin',
            'description' => Yii::t('cart/default', 'MODULE_DESC'),
            'url' => ['/admin/cart'],
        ];
    }

    public function getNav() {
        return [
            [
                'label' => 'Продукция',
                "url" => ['/admin/cart/products'],
                'icon' => 'fa-coin'
            ],
            [
                'label' => 'Категории',
                "url" => ['/admin/cart/categories'],
                'icon' => 'fa-folder-open'
            ],
            [
                'label' => 'Настройки',
                "url" => ['/admin/cart/settings'],
                'icon' => 'icon-settings'
            ]
        ];
    }

    protected function getDefaultModelClasses() {
        return [
            //  'Pages' => 'panix\shop\models\Pages',
            'ShopProductSearch' => 'app\system\modules\shop\models\ShopProductSearch',
            'ShopManufacturer' => 'app\system\modules\shop\models\ShopManufacturer',
            'ShopProduct' => 'app\system\modules\shop\models\ShopProduct',
            'ShopCategory' => 'panix\shop\models\ShopCategory',
        ];
    }

}
