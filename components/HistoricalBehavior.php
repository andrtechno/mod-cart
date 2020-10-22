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

    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_FIND => 'afterFind',
            ActiveRecord::EVENT_AFTER_DELETE => 'afterDelete',
            ActiveRecord::EVENT_AFTER_INSERT => 'afterSave',
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterSave',
            self::EVENT_PRODUCT_ADDED => [$this, 'onProductAdded'],
            self::EVENT_PRODUCT_QUANTITY_CHANGED => [$this, 'onProductQuantityChanged'],
            self::EVENT_PRODUCT_DELETED => [$this, 'onProductDeleted'],
        ];
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
                'image' => $event->ordered_product->originalProduct->getMainImage()->url,
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
                'image' => ($event->ordered_product->originalProduct)?$event->ordered_product->originalProduct->getMainImage('50x50')->url:'no image',
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
                'image' => ($event->ordered_product->originalProduct)?$event->ordered_product->originalProduct->getMainImage('50x50')->url:'no image',
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

        $changed = [];
        $old_data = [];
        $new_data = [];

        foreach ($this->getTrackAttributes() as $attr) {
            if ($old->{$attr} != $new->{$attr}) {
                $changed[] = $attr;
                $old_data[$attr] = $old->{$attr};
                $new_data[$attr] = $new->{$attr};
            }
        }

        if (!empty($changed)) {
            $this->log([
                'handler' => self::ATTRIBUTES_HANDLER,
                'data_before' => $this->prepareAttributes($old_data),
                'data_after' => $this->prepareAttributes($new_data),
            ]);
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
        $result = [];

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
        } elseif ('payment_id' === $key) {
            $model = Payment::findOne($id);
            if ($model)
                $val = $model->name;
        } elseif ('status_id' === $key) {
            $model = OrderStatus::findOne($id);
            if ($model)
                $val = Html::tag('span', $model->name, ['class' => 'badge', 'style' => 'background:' . $model->color]);;
        }

        return $val;
    }

    /**
     * @return array
     */
    public function getTrackAttributes()
    {
        return [
            'delivery_id',
            'payment_id',
            'status_id',
            'paid',
            'user_name',
            'user_email',
            'delivery_address',
            'user_phone',
            'user_comment',
            'admin_comment',
            'discount',
        ];
    }

}