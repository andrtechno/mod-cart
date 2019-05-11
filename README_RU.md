Модуль корзина
===========
[Description of English](README.md)

Модуль для PIXELION CMS

[![Latest Stable Version](https://poser.pugx.org/panix/mod-cart/v/stable)](https://packagist.org/packages/panix/mod-cart)
[![Total Downloads](https://poser.pugx.org/panix/mod-cart/downloads)](https://packagist.org/packages/panix/mod-cart)
[![Monthly Downloads](https://poser.pugx.org/panix/mod-cart/d/monthly)](https://packagist.org/packages/panix/mod-cart)
[![Daily Downloads](https://poser.pugx.org/panix/mod-cart/d/daily)](https://packagist.org/packages/panix/mod-cart)
[![Latest Unstable Version](https://poser.pugx.org/panix/mod-cart/v/unstable)](https://packagist.org/packages/panix/mod-cart)
[![License](https://poser.pugx.org/panix/mod-cart/license)](https://packagist.org/packages/panix/mod-cart)


Установка
------------

Предпочтительный способ установить это через расширение [composer](http://getcomposer.org/download/).

Запустите

```
php composer require --prefer-dist panix/mod-cart "*"
```

или добавте

```
"panix/mod-cart": "*"
```

в раздел require ваш `composer.json` файл.

Добавить в файл конфига.
```
'modules' => [
    'cart' => ['class' => 'panix\mod\cart\Module'],
]
```

#### Миграция
```
php yii migrate --migrationPath=vendor/panix/mod-cart/migrations
```
