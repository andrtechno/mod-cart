<?php

namespace panix\mod\cart\components;

use panix\engine\CMS;
use panix\engine\Html;
use panix\mod\cart\components\delivery\DeliverySystemManager;
use panix\mod\cart\components\events\EventProduct;
use panix\mod\cart\models\Payment;
use panix\mod\novaposhta\models\Area;
use panix\mod\novaposhta\models\Cities;
use panix\mod\novaposhta\models\Warehouses;
use Yii;
use panix\mod\cart\models\Delivery;
use panix\mod\cart\models\OrderStatus;
use panix\mod\cart\models\Order;
use panix\mod\cart\models\OrderHistory;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

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
    const EVENT_PRODUCT_UPDATE_QUANTITY = 'onProductUpdateQuantity';
    const EVENT_PRODUCT_DELETED = 'onProductDeleted';

    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_FIND => 'afterFind',
            ActiveRecord::EVENT_AFTER_DELETE => 'afterDelete',
            ActiveRecord::EVENT_AFTER_INSERT => 'afterSave',
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterSave',
            self::EVENT_PRODUCT_ADDED => [$this, 'onProductAdded'],
            self::EVENT_PRODUCT_UPDATE_QUANTITY => [$this, 'onProductUpdateQuantity'],
            self::EVENT_PRODUCT_DELETED => [$this, 'onProductDeleted'],
        ];
    }


    /**
     * @param $event EventProduct
     */
    public function onProductAdded($event)
    {
        $original = $event->ordered_product->originalProduct ? $event->ordered_product->originalProduct : null;
        $this->log([
            'handler' => self::PRODUCT_HANDLER,
            'data_before' => serialize([
                'deleted' => false,
                'name' => $event->ordered_product->getRenderFullName(),
                'price' => ($event->ordered_product->currency_id) ? $event->ordered_product->price / $event->ordered_product->currency_rate  : $event->ordered_product->price,
                'currency' => ($event->ordered_product->currency_id) ? Yii::$app->currency->getById($event->ordered_product->currency_id)->iso : Yii::$app->currency->main['iso'],
                'image' => ($original) ? $original->getMainImage('small')->url : 'no image',
                'quantity' => $event->ordered_product->quantity
            ]),
            'data_after' => '',
        ]);
    }

    /**
     * @param  $event EventProduct
     */
    public function onProductDeleted($event)
    {
        $original = $event->ordered_product->originalProduct ? $event->ordered_product->originalProduct : null;
        $this->log([
            'handler' => self::PRODUCT_HANDLER,
            'data_before' => serialize([
                'deleted' => true,
                'name' => $event->ordered_product->getRenderFullName(),
                'price' => ($event->ordered_product->currency_id) ? $event->ordered_product->price / $event->ordered_product->currency_rate  : $event->ordered_product->price,
                'currency' => ($event->ordered_product->currency_id) ? Yii::$app->currency->getById($event->ordered_product->currency_id)->iso : Yii::$app->currency->main['iso'],
                'image' => ($original) ? $original->getMainImage('small')->url : 'no image',
                'quantity' => $event->ordered_product->quantity
            ]),
            'data_after' => '',
        ]);
    }

    /**
     * @param $event EventProduct
     */
    public function onProductUpdateQuantity($event)
    {
        $original = $event->ordered_product->originalProduct ? $event->ordered_product->originalProduct : null;
        $this->log([
            'handler' => self::PRODUCT_HANDLER,
            'data_before' => serialize([
                'changed' => true,
                'name' => Html::a($event->ordered_product->name, $original->getUrl()),
                'image' => ($original) ? $original->getMainImage('small')->url : 'no image',
                'quantity' => $event->ordered_product->quantity
            ]),
            'data_after' => serialize([
                'quantity' => $event->quantity
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
                'data_before' => $this->prepareAttributes($old_data, $old),
                'data_after' => $this->prepareAttributes($new_data, $new),
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
    public function prepareAttributes(array $attrs, $modelOrder)
    {
        $result = [];

        foreach ($attrs as $key => $val)
            $result[$key] = $this->idToText($key, $val, $modelOrder);

        return serialize($result);
    }

    /**
     * @param $key
     * @param $id
     * @return string
     */
    public function idToText($key, $id, $modelOrder)
    {
        $val = $id;

        if ('delivery_id' === $key) {
            $model = Delivery::findOne($id);
            if ($model) {
                $val = $model->name;
                if ($model->system) {

                 // $data = Json::decode($modelOrder->delivery_data);
                    //$manager = new DeliverySystemManager();
                   // $system = $manager->getSystemClass($model->system);

                    $html = '<br/>';
                    $html .= $modelOrder->getDeliveryHtml();
                    $val .= $html;
                }
            }
        } elseif ('payment_id' === $key) {
            $model = Payment::findOne($id);
            if ($model)
                $val = $model->name;
        } elseif ('status_id' === $key) {
            $model = OrderStatus::findOne($id);
            if ($model)
                $val = Html::tag('span', $model->name, ['class' => 'badge', 'style' => 'background:' . $model->color]);
        }

        return $val;
    }


    public function idToText_old($key, $id, $modelOrder)
    {
        $val = $id;

        if ('delivery_id' === $key) {
            $model = Delivery::findOne($id);
            if ($model) {
                $val = $model->name;
                if ($model->system) {

                    $data = Json::decode($modelOrder->delivery_data);
                    $manager = new DeliverySystemManager();
                    $system = $manager->getSystemClass($model->system);

                    $html = '<br/>';
                    if ($model->system == 'novaposhta') {

                        if (isset($data['type']) && $data['type'] == 'warehouse') {
                            if (isset($data['area'])) {
                                $areas = Yii::$app->novaposhta->getAreas();
                                $area = ArrayHelper::map($areas['data'], 'Ref', function ($model) {
                                    return (Yii::$app->language == 'ru') ? $model['DescriptionRu'] : $model['Description'];
                                });
                                if ($area) {
                                    $html .= $area[$data['area']] . ', ';
                                }
                            }
                            if (isset($data['city'])) {
                                $city = Cities::findOne($data['city']);
                                if ($city) {
                                    $html .= Yii::t('cart/Delivery', 'CITY') . ' ' . $city->getDescription() . '';
                                }
                            }
                            if (isset($data['warehouse'])) {
                                $result = Yii::$app->novaposhta->getWarehouses($data['city'], 0, 9999);
                                if ($result) {
                                    $warehouses = ArrayHelper::map($result['data'], 'Ref', function ($data) {
                                        return $data['Description'];
                                    });
                                    if (isset($warehouses[$data['warehouse']])) {
                                        $html .= '<br/>' . $warehouses[$data['warehouse']];
                                    }
                                }
                            }

                        } else {
                            $html .= $data['address'];
                        }

                    } elseif ($model->system == 'meest') {
                        $api = new \panix\mod\cart\widgets\delivery\meest\api\MeestApi();

                        if ($data['type'] == 'warehouse') {
                            if (isset($data['warehouse'])) {
                                $ware = $api->getBranchesById($data['warehouse']);
                            }
                        }else{
                            $ware = '';
                        }

                        if (isset($data['area'])) {
                            if(isset($ware[0])){
                                $value = $ware[0]['region']['ua'];
                            }else{
                                $regions = $api->getGeoRegions();
                                $region = ArrayHelper::map($regions, 'region_id', function ($model) {
                                    return $model['ua'];
                                });
                                $value = (isset($region[$data['area']])) ? $region[$data['area']]: 'unknown';
                            }

                            $html .= $value.', ';
                        }
                        if (isset($data['city'])) {
                            $html .= $data['city'].'<br/>';
                        }
                        if ($data['type'] == 'warehouse') {
                            if (isset($data['warehouse'])) {
                                $warehouse = ArrayHelper::map($ware, 'br_id', function ($model) {
                                    $value = '№' . $model['num_showcase'] . ' ' . $model['type_public']['ua'] . ' ' . $model['street']['ua'] . ' ' . $model['street_number'];
                                    if ($model['limits']['parcel_max_kg']) {
                                        $value .= ' (до ' . floor($model['limits']['parcel_max_kg']) . 'кг)';
                                    }
                                    return $value;
                                });
                                $html .= $warehouse[$data['warehouse']];
                            }
                        } else {
                            $html .= $data['address'];
                        }
                    } elseif ($model->system == 'pickup') {
                        $settings = $system->getSettings($model->id);
                        $list = $system->getList($settings);
                        if(isset($list[$data['address']])){
                            $html .= $system->getList($settings)[$data['address']];
                        }

                    } else { //address
                        $html .= $data['address'];
                    }
                    $val .= $html;
                }
            }
        } elseif ('payment_id' === $key) {
            $model = Payment::findOne($id);
            if ($model)
                $val = $model->name;
        } elseif ('status_id' === $key) {
            $model = OrderStatus::findOne($id);
            if ($model)
                $val = Html::tag('span', $model->name, ['class' => 'badge', 'style' => 'background:' . $model->color]);
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
            'user_lastname',
            'user_phone',
            'user_comment',
            'admin_comment',
            'discount',
        ];
    }

}
