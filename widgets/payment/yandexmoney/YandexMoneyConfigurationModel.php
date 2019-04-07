<?php
namespace panix\mod\cart\widgets\payment\yandexmoney;
class YandexMoneyConfigurationModel extends \yii\base\Model {

    /**
     * @var integer YandexMoney account number
     */
    public $uid;

    /**
     * @var string YandexMoney secret word
     */
    public $password;

    /**
     * @return array
     */
    public function rules() {
        return array(
            array('uid, password', 'type', 'type' => 'string')
        );
    }

    /**
     * @return array
     */
    public function attributeNames() {
        return array(
            'uid' => Yii::t('CartModule.payments', 'YAMONEY_UID'),
            'password' => Yii::t('CartModule.payments', 'YAMONEY_SECRET'),
        );
    }

    /**
     * @return array
     */
    public function getForm() {
        $id = Yii::$app->request->getQuery('payment_method_id');

        return array(
            'type' => 'form',
            'elements' => array(
                'uid' => array(
                    'label' => Yii::t('CartModule.payments', 'YAMONEY_UID'),
                    'type' => 'text'
                ),
                'password' => array(
                    'label' => Yii::t('CartModule.payments', 'YAMONEY_SECRET'),
                    'type' => 'text',
                ),
                '<div class="row">
					<label>Адрес, на который получать уведомления</label>
					<input type="text" value="' . $this->getCallbackUrl($id) . '" disabled>
					<div class="hint">
						Сохраните этот адрес на странице <a href="https://sp-money.yandex.ru/myservices/online.xml" target="_blank">HTTP-уведомления</a>
					</div>
				</div>'
                ));
    }

    /**
     * Builds full url to accept callback requests.
     *
     * @param $id
     * @return string
     */
    public function getCallbackUrl($id) {
        if ($id === 'undefined')
            return Yii::t('CartModule.payments', 'SUCCESS_TEXT');

        return Yii::$app->createAbsoluteUrl('/orders/payment/process', array('payment_id' => $id));
    }

}
