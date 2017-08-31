<?php

/**
 * Форма купить в один клик.
 * 
 * @author CORNER CMS development team <dev@corner-cms.com>
 * @license http://corner-cms.com/license.txt CORNER CMS License
 * @link http://corner-cms.com CORNER CMS
 * @package modules
 * @subpackage commerce.cart.widgets.buyOneClick
 * @uses FormModel
 * 
 * @property string $phone Телефон
 * @property int $quantity Количество
 */
class BuyOneClickForm extends FormModel {

    const MODULE_ID = 'cart';

    public $phone;
    public $quantity;

    public function init() {
        parent::init();

        if (!Yii::app()->user->isGuest)
            $this->phone = Yii::app()->user->phone;
    }

    public function rules() {
        return array(
            array('phone, quantity', 'required'),
            array('phone', 'length', 'max' => 20, 'min' => 7),
                /*  array(
                  'phone',
                  'match', 'not' => true, 'pattern' => '/^[-\s0-9-]/i',
                  'message' => Yii::t('ByOnClickWidget.default','ERR_VALID'),
                  ), */
        );
    }

}
