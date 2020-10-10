<?php
namespace panix\mod\cart\widgets\buyOneClick;

use yii\base\Model;
use Yii;

/**
 * Форма купить в один клик.
 *
 * @author PIXELION CMS development team <dev@pixelion.com.ua>
 * @link http://pixelion.com.ua PIXELION CMS
 * @package modules
 * @subpackage commerce.cart.widgets.buyOneClick
 * @uses FormModel
 * 
 * @property string $phone Телефон
 * @property int $quantity Количество
 */
class BuyOneClickForm extends Model {

    const MODULE_ID = 'cart';

    public $phone;
    public $quantity;

    public function init() {
        parent::init();

        if (!Yii::$app->user->isGuest)
            $this->phone = Yii::$app->user->phone;
    }

    public function rules() {
        return [
            [['phone','quantity'], 'required'],
           // array('phone', 'length', 'max' => 20, 'min' => 7),
                /*  array(
                  'phone',
                  'match', 'not' => true, 'pattern' => '/^[-\s0-9-]/i',
                  'message' => Yii::t('ByOnClickWidget.default','ERR_VALID'),
                  ), */
        ];
    }

}
