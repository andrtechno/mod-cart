<?php


namespace panix\mod\cart;

use Yii;
use panix\engine\WebModule;

//use yii\base\BootstrapInterface;


class Module extends WebModule { // implements BootstrapInterface/
// public $controllerNamespace = '@cart\controllers';
    public $routes = [
        'cart' => 'cart/default/index',
        'cart/view/<secret_key>' => 'cart/default/view',
        'cart/remove/<id:(\d+)>' => 'cart/default/remove',
        'cart/<action:[.\w]+>' => 'cart/default/<action>',
        'cart/<action:[.\w]>/*' => 'cart/default/<action>',
    ];


    public function getInfo() {
        return [
            'name' => Yii::t('cart/default', 'MODULE_NAME'),
            'author' => 'andrew.panix@gmail.com',
            'version' => '1.0',
            'icon' => 'icon-cart',
            'description' => Yii::t('cart/default', 'MODULE_DESC'),
            'url' => ['/admin/cart'],
        ];
    }

    public function getNav() {
        return [
            [
                'label' => 'Заказы',
                "url" => ['/admin/cart'],
                'icon' => 'icon-cart'
            ],
            [
                'label' => Yii::t('cart/admin', 'STATUSES'),
                "url" => ['/admin/cart/statuses'],
                'icon' => 'icon-s'
            ],
            [
                'label' => Yii::t('cart/admin', 'DELIVERY'),
                "url" => ['/admin/cart/delivery'],
                'icon' => 'icon-delivery'
            ],
            [
                'label' => Yii::t('cart/admin', 'PAYMENTS'),
                "url" => ['/admin/cart/payment'],
                'icon' => 'icon-creditcard'
            ],
            [
                'label' => Yii::t('cart/admin', 'NOTIFIER'),
                "url" => ['/admin/cart/notify'],
                'icon' => 'icon-envelope'
            ],
            [
                'label' => Yii::t('app', 'SETTINGS'),
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
