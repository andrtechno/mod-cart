<?php

namespace panix\mod\cart\components;

use panix\mod\shop\models\ProductVariant;
use Yii;
use yii\base\Component;
use panix\mod\shop\models\Product;
use panix\mod\shop\models\Currency;
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

    protected $_total_price = 0;
    /**
     * @var Session
     */
    private $session;
    public $data = [];

    /** @var \panix\mod\shop\models\Product */
    protected $productModel;

    public function init()
    {
        $this->session = Yii::$app->session;
        //$this->session->id = 'cart';
        $this->session->setTimeout(100);
        $this->session->cookieParams = ['lifetime' => 60];
        if (!isset($this->session['cart_data']) || !is_array($this->session['cart_data']))
            $this->session['cart_data'] = [];


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

        if (isset($currentData[$itemIndex])) {
            //echo $currentData[$itemIndex]['quantity'];
            //die();
            if ($currentData[$itemIndex]['quantity']) {
                $currentData[$itemIndex]['quantity'] += (int)$data['quantity'];
                if ($currentData[$itemIndex]['quantity'] > 999) {
                    $currentData[$itemIndex]['quantity'] = 999;
                }
            }
        } else {
            $currentData[$itemIndex] = $data;
        }

        $this->session['cart_data'] = $currentData;
    }

    /**
     * Removed item from cart
     * @param $index string generated by self::getItemIndex() method
     */
    public function remove($index)
    {
        $currentData = $this->getData();
        if (isset($currentData[$index])) {
            unset($currentData[$index]);
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

        if (empty($data))
            return [];


        foreach ($data as $index => &$item) {

            $item['variant_models'] = [];
            $item['model'] = $this->productModel::findOne($item['product_id']);
            // Load configurable product
            if ($item['configurable_id'])
                $item['configurable_model'] = $this->productModel::findOne($item['configurable_id']);


            // Process variants @todo PANIX need test

            if (!empty($item['variants']))
                $item['variant_models'] = ProductVariant::find()
                    ->joinWith(['productAttribute', 'option'])
                    ->where([ProductVariant::tableName() . '.id' => $item['variants']])
                    ->all();

            // If product was deleted during user session!.
            if (!$item['model'])
                unset($data[$index]);

        }
        unset($item);
        $this->data = $data;
        return $this->data;
    }


    /**
     * Count total price
     */
    public function getTotalPrice()
    {
        $result = 0;
        $data = $this->getDataWithModels();
        foreach ($data as $item) {
            $configurable = isset($item['configurable_model']) ? $item['configurable_model'] : 0;
            $result += $this->productModel::calculatePrices($item['model'], $item['variants'], $configurable, $item['quantity']) * $item['quantity'];
        }
        return $result;
    }

    /**
     * Count total price
     */
    public function getTotalPriceAllCurrency()
    {
        $total = [];
        // $data = $this->getDataWithModels();
        foreach ($this->data as $item) {
            $configurable = isset($item['configurable_model']) ? $item['configurable_model'] : 0;
            if ($item['currency_id']) {
                $currency = Currency::findOne($item['currency_id']);
                // print_r($currency);
                $total[$currency->iso] += ($this->productModel::calculatePrices($item['model'], $item['variants'], $configurable) * $item['quantity']);
            } else {
                $total[Yii::$app->currency->main['iso']] += $this->productModel::calculatePrices($item['model'], $item['variants'], $configurable) * $item['quantity'];
            }
        }
        return $total;
    }

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


            if (isset($currentData[$index])) {

                $currentData[$index]['quantity'] = (int)$quantity;
                $data = $currentData[$index];


                $productModel = $this->productModel::findOne($data['product_id']);

                $calcPrice = $this->productModel::calculatePrices($productModel, $data['variants'], $data['configurable_id'], $data['quantity']);
                if ($data['configurable_id']) {

                    $rowTotal = $calcPrice * $data['quantity'];
                } else {
                    //if ($productModel->hasDiscount) {
                    //$priceTotal = ;
                    //} else {
                    //     $priceTotal = $data['price'];
                    //}

                    //if ($data['quantity'] > 1 && ($pr = $productModel->getPriceByQuantity($data['quantity']))) {
                    //    $calcPrice = $pr->value;
                    //}

                    $rowTotal = $calcPrice * $data['quantity'];

                }
            }
        }

        $this->session['cart_data'] = $currentData;
        Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            'unit_price' => Yii::$app->currency->number_format(Yii::$app->currency->convert($calcPrice)),
            'rowTotal' => Yii::$app->currency->number_format($rowTotal),
            'totalPrice' => Yii::$app->currency->number_format($this->getTotalPrice()),
        ];
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
        foreach ($data as $index => $quantity) {
            if ((int)$quantity < 1)
                $quantity = 1;

            if (isset($currentData[$index]))
                $currentData[$index]['quantity'] = (int)$quantity;
        }
        $this->session['cart_data'] = $currentData;

    }

    /**
     * @return int number of items in cart
     */
    public function countItems()
    {
        $result = 0;

        foreach ($this->session['cart_data'] as $row)
            $result += $row['quantity'];

        return $result;
    }

    /**
     * Create item index base on data
     * @param $data
     * @return string
     */
    public function getItemIndex($data)
    {
        return $data['product_id'] . implode('_', $data['variants']) . $data['configurable_id'];
    }

}
