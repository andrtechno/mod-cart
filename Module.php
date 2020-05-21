<?php

namespace panix\mod\cart;

use panix\mod\admin\widgets\sidebar\BackendNav;
use Yii;
use panix\engine\WebModule;
use panix\mod\cart\models\Order;
use yii\base\BootstrapInterface;

class Module extends WebModule implements BootstrapInterface
{

    public $icon = 'cart';
    public $mailPath = '@cart/mail';
    public function init()
    {
        if (!(Yii::$app instanceof yii\console\Application) && !Yii::$app->user->isGuest) {
            $count = Order::find()->where(['status_id' => 1])->count();
            $this->count['num'] = $count;
            $this->count['label'] = Yii::t('cart/default', 'WP_COUNT', ['num' => $this->count['num']]);
            $this->count['url'] = ['/admin/cart', 'OrderSearch[status_id]' => 1];
        }
        parent::init();
    }

    public function getCountByUser()
    {
        if (!Yii::$app->user->isGuest)
            return Order::find()->where([
                'status_id' => 1,
                'user_id' => Yii::$app->user->id
            ])->count();
    }

    public function bootstrap($app)
    {
        $app->urlManager->addRules(
            [
                'cart' => 'cart/default/index',
                'cart/view/<secret_key:[0-9a-z]{10}$>' => 'cart/default/view',
                'cart/remove/<id:\d+>' => 'cart/default/remove',
                'cart/promo-code' => 'cart/default/promoCode',
                // 'cart/clear' => 'cart/default/clear',
                // 'cart/payment' => 'cart/default/payment',
                // 'cart/recount' => 'cart/default/recount',

                'cart/payment/process' => 'cart/payment/process',
                'cart/delivery/process' => 'cart/delivery/process',
                'cart/orders/<page:\d+>' => 'cart/default/orders',
                'cart/orders' => 'cart/default/orders',
                'cart/<action:[0-9a-zA-Z_\-]+>' => 'cart/default/<action>',
                // 'cart/<action:[0-9a-zA-Z_\-]+>/*' => 'cart/default/<action>',

            ],
            true
        );
        if (!(Yii::$app instanceof yii\console\Application)) {
            $app->counters[$this->id] = (int)$this->count['num'];
        }
        $app->setComponents([
            'cart' => ['class' => 'panix\mod\cart\components\Cart'],
        ]);
    }

    public function getInfo()
    {
        return [
            'label' => Yii::t('cart/default', 'MODULE_NAME'),
            'author' => 'andrew.panix@gmail.com',
            'version' => '1.0',
            'icon' => $this->icon,
            'description' => Yii::t('cart/default', 'MODULE_DESC'),
            'url' => ['/admin/cart'],
        ];
    }

    public function getAdminMenu()
    {
        return [
            'cart' => [
                'label' => Yii::t('cart/admin', 'ORDERS'),
                'icon' => $this->icon,
                'badge' => $this->count['num'],
                'badgeOptions' => ['id' => 'navbar-badge-cart','class' => 'badge badge-success badge-pulse-success'],
                'items' => [
                    [
                        'label' => Yii::t('cart/admin', 'ORDERS_LIST'),
                        'url' => ['/admin/cart'],
                        'badge' => $this->count['num'],
                        'badgeOptions' => ['class' => 'badge badge-success badge-pulse'],
                        'icon' => $this->icon,
                    ],
                    [
                        'label' => Yii::t('cart/admin', 'PROMOCODE'),
                        'url' => ['/admin/cart/promo-code'],
                        'icon' => $this->icon,
                        'visible'=>false,
                    ],
                    [
                        'label' => Yii::t('cart/admin', 'STATUSES'),
                        "url" => ['/admin/cart/statuses'],
                        'icon' => 'check'
                    ],
                    [
                        'label' => Yii::t('cart/admin', 'INCOME'),
                        "url" => ['/admin/cart/graph'],
                        'icon' => 'stats'
                    ],
                    [
                        'label' => Yii::t('cart/admin', 'DELIVERY'),
                        "url" => ['/admin/cart/delivery'],
                        'icon' => 'delivery'
                    ],
                    [
                        'label' => Yii::t('cart/admin', 'PAYMENTS'),
                        "url" => ['/admin/cart/payment'],
                        'icon' => 'creditcard'
                    ],
                    [
                        'label' => Yii::t('app/default', 'SETTINGS'),
                        "url" => ['/admin/cart/settings'],
                        'icon' => 'settings'
                    ]
                ],
            ],
        ];
    }

    public function getAdminSidebar()
    {
        return (new BackendNav())->findMenu($this->id)['items'];
    }

}
