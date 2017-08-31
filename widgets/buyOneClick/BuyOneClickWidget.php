<?php

/**
 * Виджет купить в один клик.
 * 
 * Пример кода для контроллера:
 * <code>
 * public function actions() {
 *      return array(
 *          'buyOneClick.' => 'mod.cart.widgets.BuyOneClickWidget'
 *      );
 * }
 * </code>
 * 
 * @author CORNER CMS development team <dev@corner-cms.com>
 * @license http://corner-cms.com/license.txt CORNER CMS License
 * @link http://corner-cms.com CORNER CMS
 * @package modules
 * @subpackage commerce.cart.widgets.buyOneClick
 * @uses CWidget
 */
class BuyOneClickWidget extends CWidget {

    protected $assetsPath;
    protected $assetsUrl;
    public $pk;

    public static function actions() {
        return array(
            'action' => 'mod.cart.widgets.buyOneClick.actions.BuyOneClickAction'
        );
    }

    public function init() {

        parent::init();
        if ($this->assetsPath === null) {
            $this->assetsPath = dirname(__FILE__) . DS . 'assets';
        }
        if ($this->assetsUrl === null) {
            $this->assetsUrl = Yii::app()->assetManager->publish($this->assetsPath, false, -1, YII_DEBUG);
        }
        $this->registerClientScript();
    }

    public function run() {
        if (Yii::app()->hasModule('cart')) {
            $this->render($this->skin);
        }
    }

    protected function registerClientScript() {
        $cs = Yii::app()->clientScript;
        if (is_dir($this->assetsPath)) {
            $cs->registerScriptFile($this->assetsUrl . '/js/buyOneClick.js', CClientScript::POS_END);
        } else {
            throw new Exception(__CLASS__ . ' - Error: Couldn\'t find assets to publish.');
        }
    }

}
