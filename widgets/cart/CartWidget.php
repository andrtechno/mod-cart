<?php

namespace panix\mod\cart\widgets\cart;


use Yii;
use yii\bootstrap4\Modal;
use yii\helpers\Html;
use yii\web\View;
use panix\mod\shop\models\Product;
use panix\engine\data\Widget;
use panix\engine\CMS;
use yii\helpers\ArrayHelper;

class CartWidget extends Widget
{
    public $closeButton = [];
    public $options = [];
    public $dialogOptions = [];
    public $total;
    public $items;
    public $count;
    public $title;
    public $skin;

    public static function modal($config = [])
    {
        $config['class'] = get_called_class();
        $widget = Yii::createObject($config);
        return $widget->renderModal($config);
    }


    public static function button($config = [])
    {
        $config['class'] = get_called_class();
        $widget = Yii::createObject($config);
        return $widget->renderButton($config);

    }

    public function init()
    {
        /** @var \panix\mod\cart\components\Cart $cart */
        $cart = Yii::$app->cart;
        $items = $cart->getDataWithModels();
        $this->items = isset($items['items']) ? $items['items'] : [];
        $this->count = $cart->countItems();
        $this->total = $cart->getTotalPrice();
        //$cart = Yii::$app->getModule('cart')->cart;
        //$this->items = $cart['items'];
        //$this->count = $cart['count'];
        //$this->total = $cart['total'];
        $this->title = 'Ваша корзина';
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function run()
    {

        $this->getView()->registerJs("cart.skin = '{$this->skin}';", View::POS_END);

        /** @var \panix\mod\cart\components\Cart $cart */
        // $cart = Yii::$app->cart;
        $currency = Yii::$app->currency->active;
        // $items = $cart->getDataWithModels();
        $dataRender = [
            'count' => $this->count,
            'currency' => $currency,
            'total' => $this->total,
            'items' => $this->items
        ];
        // if (!Yii::$app->request->isAjax)
        //     echo Html::beginTag('div', ['class' => 'cart']);
        echo $this->render('popup', $dataRender);
        //  if (!Yii::$app->request->isAjax)
        //  echo Html::endTag('div');
    }

    protected function renderModal($config = [])
    {

        $this->skin = Yii::$app->getModule('cart')->modalView;
        if (isset($config['skin'])) {
            $this->skin = $config['skin'];
        }

        $currency = Yii::$app->currency->active;
        $dataRender = [
            'count' => $this->count,
            'currency' => $currency,
            'total' => $this->total,
            'items' => $this->items,
            'isPopup' => true
        ];
        $this->view->registerJs("
            $('#cart-modal').on('shown.bs.modal', function (e) {
                if ($(window).width() <= 992) {
                    var footerHeight = $('.modal-footer', this).outerHeight();
                    var headerHeight = $('.modal-header', this).outerHeight();
                    $(this).find('.cart-items').css({'max-height': $(window).height() - footerHeight - headerHeight});
                }
            });
            $(window).resize(function () {
                if ($(this).width() <= 992) {
                    var footerHeight = $('.modal-footer', '.modal').outerHeight();
                    var headerHeight = $('.modal-header', '.modal').outerHeight();
                    var mh = $(this).height() - footerHeight - headerHeight;
                } else {
                    var mh = 'inherit';
                }
                $('.modal').find('.cart-items').css({'max-height': mh});
            });
        ");
        return $this->render($this->skin, $dataRender);
        /*return strtr($this->templateBs3, [
            '{title}' => $this->renderTitle(),
            '{close}' => $this->renderCloseButton(),
            '{body}' => $this->render($this->skin, $dataRender)
        ]);*/
        // return $this->render('popup', $dataRender);
    }


    protected function renderButton($config = [])
    {
        $this->skin = 'button';
        if (isset($config['skin'])) {
            $this->skin = $config['skin'];
        }

        $currency = Yii::$app->currency->active;
        $dataRender = [
            'count' => $this->count,
            'currency' => $currency,
            'total' => $this->total,
            'items' => $this->items
        ];

        return $this->render($this->skin, $dataRender);
    }

    /**
     * Renders the closing tag of the modal body.
     * @return string the rendering result
     */
    protected function renderBodyEnd()
    {
        return Html::endTag('div');
    }

    protected function renderHeader()
    {
        $button = $this->renderCloseButton();
        if ($this->title !== null) {
            Html::addCssClass($this->titleOptions, ['widget' => 'modal-title']);
            $header = Html::tag('h5', $this->title, $this->titleOptions);
        } else {
            $header = '';
        }

        if ($button !== null) {
            $header .= "\n" . $button;
        } elseif ($header === '') {
            return '';
        }
        Html::addCssClass($this->headerOptions, ['widget' => 'modal-header']);
        return Html::tag('div', "\n" . $header . "\n", $this->headerOptions);
    }

    /**
     * Renders the opening tag of the modal body.
     * @return string the rendering result
     */
    protected function renderBodyBegin()
    {
        Html::addCssClass($this->bodyOptions, ['widget' => 'modal-body']);
        return Html::beginTag('div', $this->bodyOptions);
    }

    /**
     * Renders the close button.
     * @return string the rendering result
     */
    protected function renderCloseButton()
    {
        if (($closeButton = $this->closeButton) !== false) {
            $tag = ArrayHelper::remove($closeButton, 'tag', 'button');
            $label = ArrayHelper::remove($closeButton, 'label', Html::tag('span', '&times;', [
                'aria-hidden' => 'true'
            ]));

            return Html::tag($tag, Html::tag('span', '&times;', [
                'aria-hidden' => 'true'
            ]), ['class' => 'close', 'data-dismiss' => 'modal', 'aria-label' => 'Close'

            ]);
        } else {
            return null;
        }
    }


    protected function renderTitle()
    {
        if ($this->title || !empty($this->title)) {
            return Html::tag('h4', $this->title, ['class' => 'modal-title', 'id' => 'cart-modalLabel']);
        } else {
            return null;
        }
    }
}
