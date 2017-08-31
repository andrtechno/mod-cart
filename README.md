mod-cart
===========
Module for CORNER CMS

[![Latest Stable Version](https://poser.pugx.org/panix/mod-cart/v/stable)](https://packagist.org/packages/panix/mod-cart) [![Total Downloads](https://poser.pugx.org/panix/mod-cart/downloads)](https://packagist.org/packages/panix/mod-cart) [![Monthly Downloads](https://poser.pugx.org/panix/mod-cart/d/monthly)](https://packagist.org/packages/panix/mod-cart) [![Daily Downloads](https://poser.pugx.org/panix/mod-cart/d/daily)](https://packagist.org/packages/panix/mod-cart) [![Latest Unstable Version](https://poser.pugx.org/panix/mod-cart/v/unstable)](https://packagist.org/packages/panix/mod-cart) [![License](https://poser.pugx.org/panix/mod-cart/license)](https://packagist.org/packages/panix/mod-cart)


Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist panix/mod-cart "*"
```

or add

```
"panix/mod-cart": "*"
```

to the require section of your `composer.json` file.

Add to web config.
```
'modules' => [
    'cart' => ['class' => 'panix\mod\cart\Module'],
],
```