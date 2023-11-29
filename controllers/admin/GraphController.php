<?php

namespace panix\mod\cart\controllers\admin;


use panix\engine\CMS;
use panix\mod\cart\models\Order;
use panix\mod\cart\models\OrderStatus;
use panix\mod\shop\models\Category;
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
    public $icon = 'stats';

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
            $query = Order::find(); //(new \yii\db\Query())->from(Order::tableName());
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
            $queryData = $query->asArray()->one();
            $product_count = '-'; //$query->one()->getProducts()->count();
            //}

            $total += $queryData['sum'];
            $highchartsData[] = [

                //'y' => (double)$queryData['sum'],
                'y' => (double)$queryData['sum'],
                'name' => Yii::t('app/month', date('F', strtotime("{$year}-{$index}")),['n'=>2]),
                'year' => $year,
                'month' => $index,
                'type' => $type,
                'action' => 'view-month',
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

    public function actionViewMonth()
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
                $queryData = $query->one();
            }


            $value = ($queryData['sum']) ? $queryData['sum'] : 0;
            $total += $value;
            $data[] = [
                'name' => Yii::t('cart/admin', date('l', $from_date)) . ', ' . $day,
                'y' => (double)$value,
                'action' => 'view-day',
                //'timestamp' => $from_date,
                'day' => $day,
                'year' => $year,
                'month' => $month,
                'status_id' => $statusIds,
                'value' => Yii::$app->currency->number_format($value),
                //'products' => 10
                'drilldown' => []
            ];


        }


        return $this->asJson([
            'data' => [
                'name' => Yii::$app->request->get('name'),
                'value' => 'test',
                'data' => $data
            ],
            'subtitle' => 'Итого: ' . Yii::$app->currency->number_format($total) . ' ' . Yii::$app->currency->active['symbol'],
            'title' => Yii::t('cart/admin', ($type == 'income') ? 'INCOME_FOR' : 'CIRCULATION_FOR', [
                'month' => $name,
                'year' => $year
            ])
        ]);
    }

    public function actionViewDay()
    {

        $request = Yii::$app->request;

        $year = (int)Yii::$app->request->get('year');
        $month = Yii::$app->request->get('month');
        $type = Yii::$app->request->get('type');
        $name = Yii::$app->request->get('name');
        $day = Yii::$app->request->get('day');

        $data = [];
        $total = 0;


        $range = range(0, 24, 2);

        unset($range[array_key_last($range)]);
        $timezone = Yii::$app->settings->get('app', 'timezone');
        foreach ($range as $k => $hr) {

            $queryData['sum'] = 0;
            $date_utc2 = new \DateTime();
            $date_utc2->setTimezone(new \DateTimeZone($timezone));
            $date_utc2->setDate($year, $month, $day)->setTime($hr, 0, 0, 0);


            $from_date = $date_utc2->getTimestamp(); //->format('Y-m-d H:i:s')
            $to_date = $date_utc2->modify('+2 hour')->getTimestamp() - 1;
            $name = $hr . ':00-' . $date_utc2->modify('-1 sec')->format('H:i');


            $query = Order::find();
            $query->orderBy = false;
            $query->where(['between', 'created_at', $from_date, $to_date]);
            $query->andWhere(['status_id' => $request->get('status_id')]);
            if ($type == 'circulation') {
                $query->andWhere(['>', 'total_price', 0]);
                $query->select(['SUM(total_price) as sum']);
            } else {
                $query->andWhere(['>', 'diff_price', 0]);
                $query->select(['SUM(diff_price) as sum']);
            }
            $queryData = $query->asArray()->one();


            $value = ($queryData['sum']) ? $queryData['sum'] : 0;
            $total += $value;
            $data[] = [
                'name' => $name,
                'y' => (double)$value,
                'action' => 'view-day',
                'day' => $from_date,
                'value' => Yii::$app->currency->number_format($value),
            ];


        }

        return $this->asJson([
            'data' => [
                'name' => Yii::$app->request->get('name'),
                'value' => 'test',
                'data' => $data
            ],
            'subtitle' => '' . Yii::$app->currency->number_format($total),
            'title' => Yii::t('cart/admin', 'INCOME_FOR', [
                    'month' => $month,
                    'year' => $year
                ]) . ' ' . Yii::$app->request->get('name')
        ]);
    }

    public function actionTopBrand()
    {

        $this->pageName = Yii::t('cart/admin', 'Популярные бренды');

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
                //$statusIds[] = $status['id'];
            }
        }
        $topLimit = (int)$request->get('top', 10);
        if ($topLimit > 50) {
            $topLimit = 50;
        }

        $year = (int)$request->get('year', date('Y'));
        $months = $request->get('months', [date('m')]);


        $highchartsData = [];
        $time = time();
        $total = 0;


        $topsQuery = OrderProduct::find()->alias('order_product');
        $topsQuery->addSelect(['count(*) as top', 'brand.id as brand_id', 'order.id as order_id']);
        $topsQuery->joinWith([
            'brand' => function ($query) {
                $query->alias('brand');
            },
            'order' => function ($query) use ($statusIds, $year, $months) {
                $query->alias('order')
                    ->orderBy(false);
                //->where(['between', 'order.created_at', strtotime("{$year}-{$month}-01 00:00:00"), strtotime("{$year}-{$month}-{$monthDaysCount} 23:59:59")]);
            }
        ]);
        if ($statusIds) {
            $topsQuery->joinWith([
                'order' => function ($query) use ($statusIds, $year, $months) {
                    $query->alias('order')
                        ->orderBy(false)
                        ->where(['status_id' => $statusIds]);
                    //->andWhere(['between', 'order.created_at', strtotime("{$year}-{$month}-01 00:00:00"), strtotime("{$year}-{$month}-{$monthDaysCount} 23:59:59")]);
                }
            ]);

        }

        $topsQuery->andWhere(['YEAR(FROM_UNIXTIME(order.created_at))' => $year]);
        $topsQuery->andWhere(['MONTH(FROM_UNIXTIME(order.created_at))' => $months]);

        $topsQuery->groupBy(['order_product.brand_id']);
        $topsQuery->orderBy(['top' => SORT_DESC]);
        $topsQuery->limit($topLimit);
        $topsQuery->asArray();

        $tops = $topsQuery->all();

        $i = 1;
        foreach ($tops as $top) {

            $topsQuery = OrderProduct::find();
            $topsQuery->alias('order_product');
            $topsQuery->addSelect([
                'SUM(order_product.price * order_product.in_box * order_product.quantity) as circulation',
                'SUM((order_product.price - order_product.price_purchase) * order_product.in_box * order_product.quantity) as income'
            ]);
            if (isset($top['brand']['id'])) {
                $topsQuery->where(['order_product.brand_id' => $top['brand']['id']]);
            }

            if ($statusIds) {
                $topsQuery->joinWith([
                    'order' => function ($query) use ($statusIds) {
                        $query->alias('order')->orderBy(false)->where(['status_id' => $statusIds]);
                    }
                ]);

            } else {
                $topsQuery->joinWith([
                    'order' => function ($query) {
                        $query->alias('order')->orderBy(false);
                    }
                ]);
            }
            $topsQuery->andWhere(['YEAR(FROM_UNIXTIME(order.created_at))' => $year]);
            $topsQuery->andWhere(['MONTH(FROM_UNIXTIME(order.created_at))' => $months]);
            $topsQuery->asArray();
            $result = $topsQuery->one();
            if (isset($top['brand']['id'])) {
                $circulation = $result['circulation'];
                $income = $result['income'];
            } else {
                $circulation = 0;
                $income = 0;
            }

            $highchartsData[] = [
                'y' => (double)$top['top'],
                'name' => (isset($top['brand']['id'])) ? $top['brand']['name_uk'] . ' #' . $i : '_No Brand_',
                'status_id' => $request->get('status_id'),
                'income' => Yii::$app->currency->number_format($income),
                'circulation' => Yii::$app->currency->number_format($circulation),
                'value' => Yii::$app->currency->number_format($top['top']),
                //'drilldown' => []
            ];
            $i++;
        }

        return $this->render('top-brand', [
            'highchartsData' => $highchartsData,
            'data_total' => $data_total,
            'year' => $year,
            //'month' => $month,
            'queryStatusIds' => $queryStatusIds,
            'topLimit' => $topLimit,
        ]);
    }


    public function actionTopCategory()
    {

        $this->pageName = Yii::t('cart/admin', 'Популярные категории');

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
                //$statusIds[] = $status['id'];
            }
        }
        $topLimit = (int)$request->get('top', 10);
        if ($topLimit > 50) {
            $topLimit = 50;
        }

        $year = (int)$request->get('year', date('Y'));
        $months = $request->get('months', [date('m')]);



        $highchartsData = [];
        $time = time();
        $total = 0;

        $monthDaysCount = cal_days_in_month(CAL_GREGORIAN, 12, $year);
        $topsQuery = OrderProduct::find()->alias('order_product');
        //$topsQuery->select(['*', 'count(*) as top']);
        $topsQuery->addSelect(['count(*) as top', 'originalProduct.id as product_id']);
        $topsQuery->joinWith([
            'originalProduct' => function ($query) {
                $query->alias('originalProduct');
                $query->joinWith('mainCategory as mainCategory');
                //$query->joinWith(['categories as categories']);
            },
        ]);

        if ($statusIds) {
            $topsQuery->joinWith([
                'order' => function ($query) use ($statusIds) {
                    $query->alias('order')->orderBy(false)->where(['status_id' => $statusIds]);
                }
            ]);
        }else{
            $topsQuery->joinWith([
                'order' => function ($query) use ($statusIds) {
                    $query->alias('order')->orderBy(false);
                }
            ]);
        }

        $topsQuery->andWhere(['YEAR(FROM_UNIXTIME(order.created_at))' => $year]);
        $topsQuery->andWhere(['MONTH(FROM_UNIXTIME(order.created_at))' => $months]);
        $topsQuery->groupBy('originalProduct.main_category_id');
        $topsQuery->orderBy(['top' => SORT_DESC]);
        $topsQuery->limit($topLimit);
//echo $topsQuery->createCommand()->rawSql;die;
        $topsQuery->asArray();
        $tops = $topsQuery->all();
        //   CMS::dump($tops);die;
        $i = 1;
        foreach ($tops as $top) {

            if (isset($top['originalProduct']['mainCategory'])) {
                $parentCategory = Category::find()->where(['id' => $top['originalProduct']['mainCategory']['id']])->one();
                if ($parentCategory) {
                    $parentCategory = $parentCategory->parent()->asArray()->one();
                }

                $topsQuery = OrderProduct::find()->alias('order_product');
                $topsQuery->joinWith([
                    'originalProduct' => function ($query) use ($top) {
                        $query->andWhere(['main_category_id' => $top['originalProduct']['mainCategory']['id']]);
                    }
                ]);
                if ($statusIds) {
                    $topsQuery->joinWith([
                        'order' => function ($query) use ($statusIds) {
                            $query->alias('order')->orderBy(false)->andWhere(['status_id' => $statusIds]);
                        }
                    ]);
                } else {
                    $topsQuery->joinWith([
                        'order' => function ($query) {
                            $query->alias('order')->orderBy(false);
                        }
                    ]);
                }

                $topsQuery->addSelect([
                    'SUM(order_product.price * order_product.in_box * order_product.quantity) as circulation',
                    'SUM((order_product.price - order_product.price_purchase) * order_product.in_box * order_product.quantity) as income'
                ]);

                //  echo $topsQuery->createCommand()->rawSql;die;
                $topsQuery->andWhere(['YEAR(FROM_UNIXTIME(order.created_at))' => $year]);
                $topsQuery->andWhere(['MONTH(FROM_UNIXTIME(order.created_at))' => $months]);
                $topsQuery->asArray();
                $result = $topsQuery->one();
//CMS::dump($result);die;
                $circulation = $result['circulation'];
                $income = $result['income'];

                $highchartsData[] = [
                    'y' => (double)$top['top'],
                    'name' => (isset($top['originalProduct']['mainCategory']['name_uk'])) ? $parentCategory['name_uk'] . ' > ' . $top['originalProduct']['mainCategory']['name_uk'] . ' #' . $i : 'Unknown',
                    //'name' => (isset($top['originalProduct']['mainCategory']['name_uk'])) ? $top['originalProduct']['mainCategory']['name_uk'] : 'Unknown',
                    'status_id' => $request->get('status_id'),
                    'income' => Yii::$app->currency->number_format($income),
                    'circulation' => Yii::$app->currency->number_format($circulation),
                    'value' => (double)$top['top'],
                    //'drilldown' => []
                ];
                $i++;
            }


        }
//CMS::dump($highchartsData);die;
        return $this->render('top-brand', [
            'highchartsData' => $highchartsData,
            'data_total' => $data_total,
            'year' => $year,
            'queryStatusIds' => $queryStatusIds,
            'topLimit' => $topLimit,
            'total' => Yii::$app->currency->number_format($total)
        ]);
    }

}
