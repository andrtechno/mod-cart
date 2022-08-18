<?php

namespace panix\mod\cart\widgets\cart2;


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
    public $bs=4;
    public $templateBs3 = '<div class="modal fade" id="cart-modal" tabindex="-1" role="dialog" aria-labelledby="cart-modalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                {close}
                {title}
            </div>
            <div class="modal-body">{body}</div>
        </div>
    </div>
</div>';
    public $templateBs4 = '';
    public $title;
    public static function modal($config = [])
    {
        $config['class'] = get_called_class();
        $widget = Yii::createObject($config);
        return $widget->renderModal($config);

        // CMS::dump($widget);die;
    }


    public static function button($config = [])
    {
        $config['class'] = get_called_class();
        $widget = Yii::createObject($config);
        return $widget->renderButton($config);
        // CMS::dump($widget);die;
    }

    public function init()
    {
        $cart = Yii::$app->getModule('cart')->cart;
        $this->items = $cart['items'];
        $this->count = $cart['count'];
        $this->total = $cart['total'];
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

        /*$view = Yii::$app->getModule('cart')->modalView;
        if(isset($config['view'])){
            $view=$config['view'];
        }*/
        $currency = Yii::$app->currency->active;
        $dataRender = [
            'count' => $this->count,
            'currency' => $currency,
            'total' => $this->total,
            'items' => $this->items,
            'isPopup'=>true
        ];

        return strtr($this->templateBs3, [
            '{title}' => $this->renderTitle(),
            '{close}' => $this->renderCloseButton(),
            '{body}' => $this->render(Yii::$app->getModule('cart')->modalView, $dataRender)
        ]);
        // return $this->render('popup', $dataRender);
    }


    protected function renderButton($config = [])
    {
        $view = 'button';
        if (isset($config['view'])) {
            $view = $config['view'];
        }
        $currency = Yii::$app->currency->active;
        $dataRender = [
            'count' => $this->count,
            'currency' => $currency,
            'total' => $this->total,
            'items' => $this->items
        ];
        return $this->render($view, $dataRender);
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
            ]), ['class'=>'close','data-dismiss'=>'modal', 'aria-label'=>'Close'

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