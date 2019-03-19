<?php

namespace panix\mod\cart;

use Yii;
use panix\engine\WebModule;
use panix\mod\cart\models\Order;
use yii\base\BootstrapInterface;

class Module extends WebModule implements BootstrapInterface
{

    public $icon = 'cart';

    public function init()
    {



        if (!(Yii::$app instanceof yii\console\Application)) {
            $count = Order::find()->where(['status_id' => 1])->count();
            $this->count['num'] = $count;
            $this->count['label'] = Yii::t('cart/default', 'WP_COUNT', ['num' => $this->count['num']]);
            $this->count['url'] = ['/admin/cart', 'OrderSearch[status_id]' => 1];
        }
        parent::init();
    }

    public function bootstrap($app)
    {
        $app->urlManager->addRules(
            [
                'cart' => 'cart/default/index',
                'cart/view/<secret_key>' => 'cart/default/view',
                'cart/remove/<id:(\d+)>' => 'cart/default/remove',
                'cart/clear' => 'cart/default/clear',
                'cart/payment' => 'cart/default/payment',
                'cart/recount' => 'cart/default/recount',
                'cart/<action:[.\w]+>' => 'cart/default/<action>',
                'cart/<action:[.\w]>/*' => 'cart/default/<action>',
            ],
            true
        );

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
            'url' => ['/cart'],
        ];
    }

    public function getAdminMenu()
    {
        return [
            'cart' => [
                'label' => Yii::t('cart/default', 'MODULE_NAME'),
                'icon' => $this->icon,
                'items' => [
                    [
                        'label' => Yii::t('cart/default', 'MODULE_NAME'),
                        'url' => ['/cart'],
                        'icon' => $this->icon,
                    ],
                    [
                        'label' => Yii::t('cart/admin', 'STATUSES'),
                        "url" => ['/cart/statuses'],
                        'icon' => 'stats'
                    ],
                    [
                        'label' => Yii::t('cart/admin', 'DELIVERY'),
                        "url" => ['/cart/delivery'],
                        'icon' => 'delivery'
                    ],
                    [
                        'label' => Yii::t('cart/admin', 'PAYMENTS'),
                        "url" => ['/cart/payment'],
                        'icon' => 'creditcard'
                    ],
                    [
                        'label' => Yii::t('cart/admin', 'NOTIFIER'),
                        "url" => ['/cart/notify'],
                        'icon' => 'envelope'
                    ],
                    [
                        'label' => Yii::t('app', 'SETTINGS'),
                        "url" => ['/cart/settings'],
                        'icon' => 'settings'
                    ]
                ],
            ],
        ];
    }

    public function getAdminSidebar()
    {
        $mod = new \panix\engine\bootstrap\Nav;
        $items = $mod->findMenu($this->id);
        return $items['items'];
    }

}
