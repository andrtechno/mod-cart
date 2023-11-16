<?php

namespace panix\mod\cart\components;

use panix\engine\CMS;
use panix\mod\shop\models\ProductVariant;
use Yii;
use yii\base\Component;
use panix\mod\shop\models\Product;
use panix\mod\shop\models\Currency;
use yii\helpers\Html;
use yii\web\Response;
use yii\web\Session;

class Cart extends Component
{

    /**
     * Array of products added to cart.
     * E.g:
     * array(
     *      'product_id'      => 1,
     *      'variants'        => array(ProductVariant_id),
     *      'configurable_id' => 2, // Id of configurable product or false.
     *      'quantity'        => 3,
     *      'price'           => 123 // Price of one item
     * )
     * @var array
     */
    private $_items = [];

    public $totalPrice = 0;
    /**
     * @var Session
     */
    public $session;
    public $data = [];

    /** @var \panix\mod\shop\models\Product */
    protected $productModel;

    public function init()
    {
        $this->session = Yii::$app->session;
        //$this->session->id = 'cart';
        $this->session->setTimeout(86000);
        $this->session->setCookieParams(['lifetime' => 86000]);
        if (!isset($this->session['cart_data']) || !is_array($this->session['cart_data'])) {
            $this->session['cart_data'] = [];
        }

        /** @var \panix\mod\shop\models\Product $productModel */
        $this->productModel = Yii::$app->getModule('shop')->model('Product');

    }

    /**
     * Add product to cart
     * <pre>
     *      Yii::$app->cart->add([
     *         'product_id'      => $model->id,
     *         'variants'        => $variants,// e.g: [1,2,3,...]
     *         'configurable_id' => $configurable_id,
     *         'quantity'        => (int) Yii::$app->request->post('quantity', 1),
     *         'price'           => $model->price,
     *      ]);
     * </pre>
     * @param array $data
     */
    public function add(array $data)
    {
        $itemIndex = $this->getItemIndex($data);

        $currentData = $this->getData();

        if (isset($currentData['items'][$itemIndex])) {
            //echo $currentData[$itemIndex]['quantity'];
            //die();


            //if add quantity++
            /*if ($currentData['items'][$itemIndex]['quantity']) {
                $currentData['items'][$itemIndex]['quantity'] += (int)$data['quantity'];
                if ($currentData['items'][$itemIndex]['quantity'] > 999) {
                    $currentData['items'][$itemIndex]['quantity'] = 999;
                }
            }*/
        } else {
            $currentData['items'][$itemIndex] = $data;
        }
        $this->session['cart_data'] = $currentData;
    }

    public function acceptPoint($bonus = 0)
    {
        if (Yii::$app->settings->get('user', 'bonus_enable')) {
            $data = $this->getData();

            $this->session['cart_data'] = [
                'items' => $data['items'],
                'bonus' => $bonus
            ];
        }
    }

    /**
     * Removed item from cart
     * @param $index string generated by self::getItemIndex() method
     */
    public function remove($index)
    {
        $currentData = $this->getData();
        if (isset($currentData['items'][$index])) {
            unset($currentData['items'][$index]);
            $this->session['cart_data'] = $currentData;
        }
    }

    /**
     * Clear all cart data
     */
    public function clear()
    {
        $this->session['cart_data'] = [];
    }

    /**
     * @return array current cart data
     */
    public function getData()
    {
        return $this->session['cart_data'];
    }

    /**
     * Load products added to cart
     * @return array
     */
    public function getDataWithModels()
    {
        $data = $this->getData();

        if (empty($data['items']))
            return [];

        arsort($data['items']);
        foreach ($data['items'] as $index => &$item) {

            $item['variant_models'] = [];
            $item['model'] = $this->productModel::findOne($item['product_id']);
            $model = $item['model'];
            // If product was deleted during user session!.
            if (!$model) {
                unset($data['items'][$index]);
                $this->remove($index);

                continue;
            }

            // Load configurable product
            if ($item['configurable_id'])
                $item['configurable_model'] = $this->productModel::findOne($item['configurable_id']);

            $item['attributes_data'] = json_decode($item['attributes_data']);


            $configurable = isset($item['configurable_model']) ? $item['configurable_model'] : 0;
            $this->totalPrice += $this->productModel::calculatePrices($model, $item['variants'], $configurable, $item['quantity']);


            // Process variants @todo PANIX need test
            if (!empty($item['variants']))
                $item['variant_models'] = ProductVariant::find()
                    ->joinWith(['productAttribute', 'option'])
                    ->where([ProductVariant::tableName() . '.id' => $item['variants']])
                    ->all();


        }

        unset($item);


        $this->data = $data;

        return $this->data;
    }


    /**
     * Count total price
     */
    public function getTotalPrice($onlyDiscount = false)
    {
        $result = 0;
        $data = $this->getDataWithModels();
        if (isset($data['items'])) {
            foreach ($data['items'] as $item) {
                $configurable = isset($item['configurable_model']) ? $item['configurable_model'] : 0;
                if ($onlyDiscount) {
                    if (!$item['model']->hasDiscount) {
                        $result += $this->productModel::calculatePrices($item['model'], $item['variants'], $configurable, $item['quantity']) * $item['model']->in_box;
                    }
                } else {
                    $result += $this->productModel::calculatePrices($item['model'], $item['variants'], $configurable, $item['quantity']) * $item['model']->in_box;
                }
                //$result = $result * $item['model']->in_box;

            }
        }
        //if(isset($data['bonus'])){
        //     $result -= $data['bonus'];
        // }
        return $result;
    }
    /*
        public function ss($orderTotal)
        {

            //$result=[];
            // $result['success']=false;
            $config = Yii::$app->settings->get('user');
            $totalPrice = 100000;
            $points = (Yii::$app->user->identity->points * (int)$config->bonus_value);


            // $profit = round((($totalPrice-$pc)/$totalPrice)*100,2);
            $profit = (($orderTotal - $points) / $orderTotal) * 100;
            if ($profit >= (int)$config->bonus_max_use_order) {
                $points2 = Yii::$app->request->post('bonus');
                $this->acceptPoint($points2);
                return true;
            } else {
                $this->acceptPoint(0);
                return false;
            }
        }
    */
    /**
     * @param $data
     * @return array
     */
    public function ajaxRecount($data)
    {

        if (!is_array($data) || empty($data))
            return;

        $currentData = $this->getData();
        $rowTotal = 0;
        $calcPrice = 0;
        foreach ($data as $index => $quantity) {
            if ((int)$quantity < 1)
                $quantity = 1;


            if (isset($currentData['items'][$index])) {

                $currentData['items'][$index]['quantity'] = (int)$quantity;
                $data = $currentData['items'][$index];

                $response['product_id'] = $data['product_id'];
                $productModel = $this->productModel::findOne($data['product_id']);

                $calcPrice = $this->productModel::calculatePrices($productModel, $data['variants'], $data['configurable_id'], $data['quantity']);
                if ($data['configurable_id']) {

                    $rowTotal = $calcPrice;
                } else {
                    //if ($productModel->hasDiscount) {
                    //$priceTotal = ;
                    //} else {
                    //     $priceTotal = $data['price'];
                    //}

                    //if ($data['quantity'] > 1 && ($pr = $productModel->getPriceByQuantity($data['quantity']))) {
                    //    $calcPrice = $pr->value;
                    //}

                    $rowTotal = $calcPrice * $data['in_box'];

                }

                $response['rowQuantity'] = $data['quantity'];
            }

            //$total+=$rowTotal;
        }
        // $countBoxs += $quantity / $data['in_box'];
        // $this->session['cart_data'] = $currentData;


        $points2 = 0;
        if (isset(Yii::$app->request->post('OrderCreateForm')['points'])) {
            $totalSummary = $this->getTotalPrice(true);
            $total = $this->getTotalPrice();

            $points2 = (Yii::$app->request->post('OrderCreateForm')['points']) ? Yii::$app->request->post('OrderCreateForm')['points'] : 0;
            $bonusData = [];
            $config = Yii::$app->settings->get('user');
            $points = ($points2 * (int)$config->bonus_value);
            // $profit = round((($totalPrice-$pc)/$totalPrice)*100,2);
            $profit = (($totalSummary - $points) / $totalSummary) * 100;
            // echo $total;die;

            if ($points2 > 0) {
                if ($points2 <= Yii::$app->user->identity->points) {
                    if ($profit >= (int)$config->bonus_max_use_order) {
                        $bonusData['message'] = Yii::t('default', 'BONUS_ACTIVE', $points2);
                        $bonusData['success'] = true;
                        $bonusData['value'] = $points2;
                        $total -= $points2;
                    } else {
                        $points2 = 0;
                        $bonusData['message'] = Yii::t('default', 'BONUS_NOT_ENOUGH');
                        $bonusData['success'] = false;
                    }

                } else {
                    $points2 = 0;
                    $bonusData['message'] = Yii::t('default', 'BONUS_NOT_ENOUGH');
                    $bonusData['success'] = false;
                }
            } else {
                $points2 = 0;
                $bonusData['message'] = 'Вы отменили бонусы';
                $bonusData['success'] = false;

            }
            $response['bonus'] = $bonusData;

        }


        $this->session['cart_data'] = [
            'items' => (isset($currentData['items'])) ? $currentData['items'] : [],
            'bonus' => $points2
        ];


        //$this->session['cart_data'] = $currentData;

        $counter = $this->countItems();
        $response['unit_price'] = Yii::$app->currency->number_format(Yii::$app->currency->convert($calcPrice));
        $response['rowTotal'] = Yii::$app->currency->number_format($rowTotal);
        $response['total_price'] = Yii::$app->currency->number_format((isset($total)) ? $total : $this->getTotalPrice());
        $response['countItems'] = $counter['quantity'];
        $response['countBoxes'] = $counter['boxes'];
        return $response;
    }

    /**
     * Recount quantity by index
     * @param $data array(index=>quantity)
     */
    public function recount($data)
    {
        if (!is_array($data) || empty($data))
            return;

        $currentData = $this->getData();
        foreach ($data['items'] as $index => $quantity) {
            if ((int)$quantity < 1)
                $quantity = 1;

            if (isset($currentData[$index]))
                $currentData['items'][$index]['quantity'] = (int)$quantity;
        }


        $this->session['cart_data'] = $currentData;

    }

    public function hasIndex($index)
    {
        $data = $this->getData();
        if (isset($data['items'][$index]))
            return true;

        return false;
    }

    /**
     * @return int number of items in cart
     */
    public function countItems()
    {
        $result['quantity'] = 0;
        $result['boxes'] = 0;
        if (isset($this->session['cart_data']['items'])) {
            foreach ($this->session['cart_data']['items'] as $row) {
                $result['quantity'] += $row['quantity'];
                if (Yii::$app->settings->get('cart', 'quantity_convert')) {
                    $result['boxes'] += $row['quantity'] / $row['in_box'];
                } else {
                    $result['boxes'] += $row['quantity'];
                }
            }

        }
        return $result;
    }

    /**
     * Create item index base on data
     * @param $data
     * @return string
     */
    public function getItemIndex($data)
    {
        $index = $data['product_id'];
        if ($data['configurable_id']) {
            $index .= ':' . $data['configurable_id'];
        }
        if ($data['variants']) {
            $index .= ':' . implode('_', $data['variants']);
        }

        return $index;
    }

    public function buy($value, Product $model, array $options)
    {

        $configurable_id = 0;
        if ($model->use_configurations) {
            $configurable_id = $model->primaryKey;
        }

        /*$options['data'] = [
            'product' => $model->primaryKey,
            'configurable' => $configurable_id,
            'quantity' => 1
        ];*/
        $options['data'] = [
            'product' => $model->primaryKey,
            //  'configurable' => $configurable_id,
            // 'quantity' => 1
        ];

        if (Yii::$app->cart->hasIndex($model->id)) {
            Html::addCssClass($options, 'btn-already-in-cart');
            $options['onclick'] = 'cart.popup(false)';
            return Html::button(Yii::t('cart/default', 'BUTTON_ALREADY_CART'), $options);
        } else {
            if ($model->isAvailable) {

                Html::addCssClass($options, 'btn-buy');
                //$options['data-toggle']='modal';
                //$options['data-target']='#myModal';
                if ($model->availability == $model::STATUS_PREORDER) {
                    $value = Yii::t('shop/Product', 'AVAILABILITY_2');
                }
                $options['onclick'] = 'cart.add(this)';
                return Html::button($value, $options);


            } else {
                //\panix\mod\shop\bundles\NotifyAsset::register($this);
                //return Html::button(Yii::t('shop/default', 'NOT_AVAILABLE'), ['onclick'=>'javascript:notify(' . $model->id . ');', 'class' => 'text-danger']);
            }

        }

    }
}
