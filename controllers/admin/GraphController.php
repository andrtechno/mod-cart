<?php

namespace panix\mod\cart\controllers\admin;


use panix\engine\CMS;
use panix\mod\cart\models\Order;
use panix\mod\cart\models\OrderStatus;
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
        $this->pageName = Yii::t('cart/admin', 'INCOME');

        $this->view->params['breadcrumbs'] = [
            [
                'label' => Yii::t('cart/admin', 'ORDERS'),
                'url' => ['/cart/admin/default/index']
            ],
            $this->pageName
        ];
        $data = [];
        $data_total = [];
        $request = Yii::$app->request;
        $queryStatus = (new \yii\db\Query())->from(OrderStatus::tableName())
            //->where(['use_in_stats' => 1])
            ->select(['id', 'name']);
        $statusIds = [];
        $queryStatusIds = $queryStatus->all();

        if ($request->get('status_id')) {
            $statusIds[] = (int)$request->get('status_id');
        } else {
            foreach ($queryStatusIds as $status) {
                $statusIds[] = $status['id'];
            }
        }


        $year = (int)$request->get('year', date('Y'));
        $month = (int)$request->get('month', date('n'));
        $type = $request->get('type', 'income');

        $start_month = ($month) ? $month : '01';
        $end_month = ($month) ? $month : '12';


        $highchartsData = [];
        $time = time();
        $total = 0;
        for ($i = 0; $i < 12; $i++) {
            $index = $i + 1;
            $monthDaysCount = cal_days_in_month(CAL_GREGORIAN, $index, date('Y'));
            $product_count = (isset($data[$index]['product_count'])) ? $data[$index]['product_count'] : 0;
            $queryData['sum'] = 0;
            //if (strtotime("{$year}-{$index}-{$monthDaysCount} 23:59:59") > $time) {
            $query = (new \yii\db\Query())->from(Order::tableName());
            $query->where(['between', 'created_at', strtotime("{$year}-{$index}-01 00:00:00"), strtotime("{$year}-{$index}-{$monthDaysCount} 23:59:59")]);
            $query->andWhere(['status_id' => $statusIds]);
            if ($type == 'circulation') {
                //$query->andWhere(['not', ['diff_price' => null]]);
                //$query->andWhere(['>', 'diff_price', 0]);
                $query->andWhere(['>', 'total_price', 0]);
                $query->select(['SUM(total_price) as sum']);
            } else {

                //$query->andWhere(['not', ['diff_price' => null]]);
                $query->andWhere(['>', 'diff_price', 0]);
                $query->select(['SUM(diff_price) as sum']);
            }
            //echo $query->createCommand()->rawSql;die;
            $queryData = $query->one();

            //}

            $total += $queryData['sum'];
            $highchartsData[] = [

                //'y' => (double)$queryData['sum'],
                'y' => (double)$queryData['sum'],
                'name' => Yii::t('cart/admin', date('F', strtotime("{$year}-{$index}"))),
                'year' => $year,
                'month' => $index,
                'type' => $type,
                'status_id' => $request->get('status_id'),
                'products' => $product_count,
                'value' => Yii::$app->currency->number_format($queryData['sum']),
                // 'color' => $this->getSeasonColor($index),
                //"drilldown" => "Month_{$index}"
                'drilldown' => []
            ];

        }


        return $this->render('index', [
            'highchartsData' => $highchartsData,
            'data_total' => $data_total,
            'year' => $year,
            'month' => $month,
            'queryStatusIds' => $queryStatusIds,
            'total' => Yii::$app->currency->number_format($total)
        ]);
    }


    public function actionTest()
    {
        $data = [];
        for ($x = 0; $x < 10000; $x++) {
            $data[] = [
                1,
                1,
                1,
                rand(1500, 5000),
                rand(2000, 6000),
                //rand(1,2),
                strtotime(rand(1, 28) . "-" . rand(1, 12) . "-" . date('Y') . " 12:59:59")
            ];
        }


        Order::getDb()->createCommand()->batchInsert(Order::tableName(), [
            'delivery_id',
            'payment_id',
            'status_id',
            'total_price',
            'total_price_purchase',
            // 'status_id',
            'created_at'
        ], $data)->execute();

    }

    public function actionTestajax()
    {
        $request = Yii::$app->request;
        $queryStatus = (new \yii\db\Query())->from(OrderStatus::tableName())
            //->where(['use_in_stats' => 1])
            ->select(['id']);
        $queryStatusIds = $queryStatus->all();
        $statusIds = [];
        if ($request->get('status_id')) {
            $statusIds[] = (int)$request->get('status_id');
        } else {
            foreach ($queryStatusIds as $status) {
                $statusIds[] = $status['id'];
            }
        }


        $year = (int)Yii::$app->request->get('year');
        $month = Yii::$app->request->get('month');
        $type = Yii::$app->request->get('type');
        $name = Yii::$app->request->get('name');
        $monthDaysCount = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        $data = [];
        $total = 0;
        $time = time();

        $timezone = Yii::$app->settings->get('app', 'timezone');
        foreach (range(1, $monthDaysCount) as $k => $day) {
            $queryData['sum'] = 0;
            $date_utc2 = new \DateTime();
            $date_utc2->setTimezone(new \DateTimeZone($timezone));
            $date_utc2->setDate($year, $month, $day)->setTime(0, 0, 0, 0);

            $from_date = $date_utc2->getTimestamp();
            $to_date = $date_utc2->modify('+1 day')->getTimestamp() - 1;


            if ($to_date < $time) {
                $query = (new \yii\db\Query())->from(Order::tableName());
                $query->where(['between', 'created_at', $from_date, $to_date]);
                $query->andWhere(['status_id' => $statusIds]);
                if ($type == 'circulation') {
                    $query->andWhere(['>', 'total_price', 0]);
                    $query->select(['SUM(total_price) as sum']);
                } else {
                    //$query->andWhere(['not', ['diff_price' => null]]);
                    $query->andWhere(['>', 'diff_price', 0]);
                    $query->select(['SUM(diff_price) as sum']);
                }
                //echo $query->createCommand()->rawSql;
                $queryData = $query->one();
            }


            $value = ($queryData['sum']) ? $queryData['sum'] : 0;
            $total += $value;
            $data[] = [
                'name' => Yii::t('cart/admin', date('l', $from_date)) . ', ' . $day,
                'y' => (double)$value,
                'value' => Yii::$app->currency->number_format($value),
                //'products' => 10
            ];


        }
        // die;

        $response = [
            'name' => Yii::$app->request->get('name'),
            'value' => 'test',
            'data' => $data
        ];


        return $this->asJson([
            'data' => $response,
            'subtitle' => 'Итого: ' . Yii::$app->currency->number_format($total) . ' ' . Yii::$app->currency->active['symbol'],
            'title' => Yii::t('cart/admin', ($type == 'income') ? 'INCOME_FOR' : 'CIRCULATION_FOR', [
                'month' => $name,
                'year' => $year
            ])
        ]);
    }

}
