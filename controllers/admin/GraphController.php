<?php

namespace panix\mod\cart\controllers\admin;


use panix\mod\cart\models\Order;
use Yii;
use yii\db\Query;
use yii\web\NotFoundHttpException;
use panix\engine\controllers\AdminController;
use panix\engine\pdf\Pdf;
use panix\mod\shop\models\Product;
use panix\mod\cart\models\OrderProduct;
use panix\mod\shop\models\search\ProductSearch;
use panix\mod\cart\models\search\OrderSearch;
use yii\web\Response;

class GraphController extends AdminController
{


    public function actionIndex()
    {
        $this->pageName = Yii::t('cart/admin', 'ORDERS');
        $this->buttons = [
            [
                'label' => Yii::t('cart/default', 'CREATE_ORDER'),
                'url' => ['/admin/cart/default/create'],
                'options' => ['class' => 'btn btn-success']
            ]
        ];

        $this->breadcrumbs[] = $this->pageName;


        $data = [];
        $data_total = [];
        $request = Yii::$app->request;

        $year = (int)$request->get('year', date('Y'));
        $month = (int)$request->get('month', date('n'));

        $start_month = ($month) ? $month : '01';
        $end_month = ($month) ? $month : '12';

        $orders = Order::find()
            ->where(['between', 'updated_at', "{$year}-01-01 00:00:00", "{$year}-12-01 23:59:59"])
            ->all();

        //$query = new Query();
        //$query->select('updated_at, total_price')
        // $query->select(['order.updated_at', 'order.total_price', 'COUNT(products) AS productsCount'])
        // ->from(['order'=>Order::tableName()])
        // ->leftJoin(['products'=>OrderProduct::tableName()], 'order.id = products.order_id')
        // ->where(['between', 'updated_at', "{$year}-01-01 00:00:00", "{$year}-12-01 23:59:59"]);
//echo $query->createCommand()->rawSql;die;
        //$orders = $query->all();


        $data = [];
        $currentMonths = [];
        $percent = [];
        $highchartsDrill = [];
        $highchartsData = [];
        foreach ($orders as $order) {
            $index = date('n', strtotime($order->updated_at));

            $currentMonths[$index][] = $order;
            $percent[$index] = 0;
            $data[$index] = [];
            $data[$index]['total_price'] = 0;
            $data[$index]['product_count'] = 0;
            $data[$index]['order_count'] = count($currentMonths[$index]);
            foreach ($currentMonths[$index] as $o) {

                $percent[$index] += $o->productsCount;
                $data[$index]['total_price'] += $o->total_price;
                $data[$index]['product_count'] = $percent[$index];
            }

        }


        $counter = array_sum($percent);
        for ($i = 0; $i < 12; $i++) {
            $index = $i + 1;
            $monthDaysCount = cal_days_in_month(CAL_GREGORIAN, $index, 2019);
            $highchartsDrill[$i] = [];
            $highchartsDrill[$i]['id'] = "Month_{$index}";
            $highchartsDrill[$i]['name'] = date('F', strtotime("{$year}-{$index}"));


            foreach (range(1, $monthDaysCount) as $day) {
                $highchartsDrill[$i]['data'][] = [date('l', strtotime("{$year}-{$index}-{$day}")) . ' ' . $day . '', 4.4 + $index, '444'];
                /* $highchartsDrill[$i]['data'][] = array(
                     //'x' => $i,
                     'y' => '213',
                     'name' => $index,
                     'value' => 12321132,
                     'color' => $this->getSeasonColor($index),
                 );*/
            }


            $total_price = (isset($data[$index]['total_price'])) ? $data[$index]['total_price'] : 0;
            $product_count = (isset($data[$index]['product_count'])) ? $data[$index]['product_count'] : 0;
            $order_count = (isset($data[$index]['order_count'])) ? $data[$index]['order_count'] : 0;
            $val = (isset($percent[$index])) ? ($percent[$index] * 100) / $counter : 0;

            $highchartsData[] = [
                //'x' => $i,
                'y' => $val,
                'name' => date('F', strtotime("{$year}-{$index}")),
                'products' => $product_count,
                'value' => $order_count,
                'total_price' => Yii::$app->currency->number_format($total_price) . ' ' . Yii::$app->currency->active->symbol,
                // 'color' => $this->getSeasonColor($index),
                "drilldown" => "Month_{$index}"
            ];
        }


        return $this->render('index', [
            'highchartsData' => $highchartsData,
            'highchartsDrill' => $highchartsDrill,
            'data_total' => $data_total,
            'year' => $year,
            'month' => $month
        ]);
    }


    public function actionTest(){
        return $this->render('test', [

        ]);

    }


}
