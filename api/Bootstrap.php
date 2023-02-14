<?php

namespace panix\mod\cart\api;

use Yii;
use yii\base\BootstrapInterface;
use yii\rest\UrlRule;
use panix\mod\shop\models\Category;

/**
 * Class Module
 * @package panix\mod\shop\api
 */
class Bootstrap implements BootstrapInterface
{

    public function bootstrap($app)
    {
        $rules[] = [
            'class' => UrlRule::class,
            'controller' => 'cart/index',
            'pluralize' => false,
            'extraPatterns' => [
                'GET /' => 'index',
                'GET,HEAD /<id>' => 'index',
            ],
            'tokens' => ['{id}' => '<id:\\w+>']
        ];



        $app->urlManager->addRules(
            $rules,
            false
        );

    }


}
