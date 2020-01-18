<?php
namespace panix\mod\cart\models;

use Yii;
use panix\engine\db\ActiveRecord;
/**
 * This is the model class for table "OrderHistory".
 *
 * The followings are the available columns in table 'OrderHistory':
 * @property integer $id
 * @property integer $order_id
 * @property integer $user_id
 * @property string $username
 * @property string $handler
 * @property string $data_before
 * @property string $data_after
 * @property string $date_create
 */
class OrderHistory extends ActiveRecord {



    /**
     * @return string the associated database table name
     */
    public static function tableName() {
        return '{{%order__history}}';
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        $this->_attrLabels = array(
            'id' => 'ID',
            'order_id' => 'Order',
            'user_id' => 'User',
            'username' => 'Username',
            'handler' => 'Handler',
            'data_before' => 'Data Before',
            'data_after' => 'Data After',
        );
        return CMap::mergeArray($this->_attrLabels, parent::attributeLabels());
    }

    /**
     * @return array
     */
    public function getDataBefore() {
        if ($this->handler === 'attributes')
            return $this->prepareData($this->data_before);
        else
            return unserialize($this->data_before);
    }

    /**
     * @return array
     */
    public function getDataAfter() {
        if ($this->handler === 'attributes')
            return $this->prepareData($this->data_after);
        else
            return unserialize($this->data_after);
    }

    /**
     * @param $data
     * @return array
     */
    public function prepareData($data) {
        $order = new Order;
        $result = array();
        $data = unserialize($data);

        foreach ($data as $key => $val) {
            if ($key === 'paid') {
                if ($val)
                    $val = Yii::t('app/default', 'YES');
                else
                    $val = Yii::t('app/default', 'NO');
            }
            $result[$order->getAttributeLabel($key)] = $val;
        }

        return $result;
    }

}