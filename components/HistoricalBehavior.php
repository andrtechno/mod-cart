<?php

namespace panix\mod\cart\components;

use panix\engine\Html;
use panix\mod\cart\models\Payment;
use Yii;
use panix\mod\cart\models\Delivery;
use panix\mod\cart\models\OrderStatus;
use panix\mod\cart\models\Order;
use panix\mod\cart\models\OrderHistory;
use yii\base\Behavior;
use yii\db\ActiveRecord;

/**
 * Logs order changes
 *
 * Class HistoricalBehavior
 */
class HistoricalBehavior extends Behavior
{

    /**
     * @var Order before save
     */
    private $_old_order;

    const ATTRIBUTES_HANDLER = 'attributes';
    const PRODUCT_HANDLER = 'product';

    const EVENT_PRODUCT_ADDED = 'onProductAdded';
    const EVENT_PRODUCT_QUANTITY_CHANGED = 'onProductQuantityChanged';
    const EVENT_PRODUCT_DELETED = 'onProductDeleted';
    const EVENT_ORDER_STATUS_CHANGED = 'onOrderStatusChanged';

    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_FIND => 'afterFind',
            ActiveRecord::EVENT_AFTER_DELETE => 'afterDelete',
            ActiveRecord::EVENT_AFTER_INSERT => 'afterSave',
            self::EVENT_PRODUCT_ADDED => [$this, 'onProductAdded'],
            self::EVENT_PRODUCT_QUANTITY_CHANGED => [$this, 'onProductQuantityChanged'],
            self::EVENT_PRODUCT_DELETED => [$this, 'onProductDeleted'],
            self::EVENT_ORDER_STATUS_CHANGED => [$this, 'onOrderStatusChanged'],
        ];
    }

    public function onOrderStatusChanged($event)
    {

        $changedList = array_diff_assoc($event->sender['attributes'], $event->sender['oldAttributes']);

        $oldList = [];
        $newList = [];
        if ($changedList) {
            foreach ($changedList as $key => $value) {
                $value = $event->sender['oldAttributes'][$key];
                if ($key == 'status_id') {
                    $modelStatus = OrderStatus::findOne($value);
                    $value = Html::tag('span', $modelStatus->name, ['class' => 'badge', 'style' => 'background:' . $modelStatus->color]);
                } elseif ($key == 'delivery_id') {
                    $model = Delivery::findOne($value);
                    $value =  $model->name;
                } elseif ($key == 'payment_id') {
                    $model = Payment::findOne($value);
                    $value =  $model->name;
                }

                $oldList[$key] = $value;
            }

            foreach ($changedList as $key => $val) {
                $value = $val;
                if ($key == 'status_id') {
                    $value = $this->owner->getGridStatus();
                } elseif ($key == 'delivery_id') {
                    $value = $this->owner->deliveryMethod->name;
                } elseif ($key == 'payment_id') {
                    $value = $this->owner->paymentMethod->name;
                }
                $newList[$key] = $value;
            }


            $this->log([
                'handler' => self::ATTRIBUTES_HANDLER,
                'data_before' => serialize($oldList),
                'data_after' => serialize($newList),
            ]);
        }
    }

    /**
     * @param $event
     */
    public function onProductAdded($event)
    {

        $this->log([
            'handler' => self::PRODUCT_HANDLER,
            'data_before' => serialize([
                'deleted' => false,
                'name' => $event->ordered_product->getRenderFullName(),
                'price' => $event->ordered_product->price,
                'quantity' => $event->ordered_product->quantity
            ]),
            'data_after' => '',
        ]);
    }

    /**
     * @param $event
     */
    public function onProductDeleted($event)
    {
        $this->log([
            'handler' => self::PRODUCT_HANDLER,
            'data_before' => serialize([
                'deleted' => true,
                'name' => $event->ordered_product->getRenderFullName(),
                'price' => $event->ordered_product->price,
                'quantity' => $event->ordered_product->quantity
            ]),
            'data_after' => '',
        ]);
    }

    /**
     * @param $event
     */
    public function onProductQuantityChanged($event)
    {
        $this->log([
            'handler' => self::PRODUCT_HANDLER,
            'data_before' => serialize([
                'changed' => true,
                'name' => $event->ordered_product->name,
                'quantity' => $event->ordered_product->quantity
            ]),
            'data_after' => serialize([
                'quantity' => $event->params['new_quantity']
            ]),
        ]);
    }

    public function afterFind()
    {
        $this->_old_order = clone $this->owner;
    }


    public function afterSave()
    {
        $this->saveHistory($this->_old_order, $this->owner);
        $this->_old_order = clone $this->owner;
    }


    public function afterDelete($event)
    {
        OrderHistory::deleteAll('order_id=:id', [':id' => $event->sender->id]);
    }

    /**
     * @param $old
     * @param Order $new
     */
    protected function saveHistory($old, Order $new)
    {
        if (!$old || $old->isNewRecord)
            return;

        $changed = array();
        $old_data = array();
        $new_data = array();

        foreach ($this->getTrackAttributes() as $attr) {
            if ($old->{$attr} != $new->{$attr}) {
                $changed[] = $attr;
                $old_data[$attr] = $old->{$attr};
                $new_data[$attr] = $new->{$attr};
            }
        }

        if (!empty($changed)) {
            $this->log(array(
                'handler' => self::ATTRIBUTES_HANDLER,
                'data_before' => $this->prepareAttributes($old_data),
                'data_after' => $this->prepareAttributes($new_data),
            ));
        }
    }

    /**
     * @param array $data
     */
    public function log(array $data)
    {
        $record = new OrderHistory;
        $record->handler = $data['handler'];
        $record->data_before = $data['data_before'];
        $record->data_after = $data['data_after'];
        $record->order_id = $this->owner->id;
        $record->date_create = date('Y-m-d H:i:s');

        if (!Yii::$app->user->isGuest) {
            $record->user_id = Yii::$app->user->id;
            $record->username = Yii::$app->user->username;
        }

        $record->save();
    }

    /**
     * Saves object name to ID.
     * E.g status id 5 will be saved as "Delivered"
     *
     * @param array $attrs
     * @return string
     */
    public function prepareAttributes(array $attrs)
    {
        $result = array();

        foreach ($attrs as $key => $val)
            $result[$key] = $this->idToText($key, $val);

        return serialize($result);
    }

    /**
     * @param $key
     * @param $id
     * @return string
     */
    public function idToText($key, $id)
    {
        $val = $id;

        if ('delivery_id' === $key) {
            $model = Delivery::findOne($id);
            if ($model)
                $val = $model->name;
        } elseif ('status_id' === $key) {
            $model = OrderStatus::findOne($id);
            if ($model)
                $val = $model->name;
        }

        return $val;
    }

    /**
     * @return array
     */
    public function getTrackAttributes()
    {
        return array(
            'delivery_id',
            'status_id',
            'paid',
            'user_name',
            'user_email',
            'user_address',
            'user_phone',
            'user_comment',
            'admin_comment',
            'admin_comment',
            'discount',
        );
    }

}