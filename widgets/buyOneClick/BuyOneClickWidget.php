<?php

namespace panix\mod\cart\widgets\buyOneClick;

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
class BuyOneClickWidget extends \panix\engine\data\Widget {

    public $pk;

 

    public function init() {


        //$this->registerClientScript();
        parent::init();
    }

    public function run() {

        return $this->render($this->skin);
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
