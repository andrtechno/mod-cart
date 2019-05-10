<?php

use yii\db\Query;
use panix\mod\cart\models\Order;

?>


<?php

$query = new Query;
// compose the query
$query->select('total_price')
    ->from(Order::tableName())
    ->limit(10);
// build and execute the query
$rows = $query->all();


print_r($rows);
//$command = $query->createCommand();
//$rows = $command->queryAll();


$typeMonth = 2;
$monthArray = array(
    1 => Yii::t('app/month', 'January', $typeMonth),
    2 => Yii::t('app/month', 'February', $typeMonth),
    3 => Yii::t('app/month', 'March', $typeMonth),
    4 => Yii::t('app/month', 'April', $typeMonth),
    5 => Yii::t('app/month', 'May', $typeMonth),
    6 => Yii::t('app/month', 'June', $typeMonth),
    7 => Yii::t('app/month', 'July', $typeMonth),
    8 => Yii::t('app/month', 'August', $typeMonth),
    9 => Yii::t('app/month', 'September', $typeMonth),
    10 => Yii::t('app/month', 'October', $typeMonth),
    11 => Yii::t('app/month', 'November', $typeMonth),
    12 => Yii::t('app/month', 'December', $typeMonth)
);

$cnt = array_sum(array(5, 8));

echo number_format((5 * 100) / $cnt, 2, '.', ',');
echo '<br>';
echo number_format((8 * 100) / $cnt, 2, '.', ',');


?>
<div class="card">
    <div class="card-header">
        <h5>das</h5>
    </div>
    <div class="card-body">


        <?php

        // echo \yii\helpers\VarDumper::dump($highchartsData,10,true);
        // echo \yii\helpers\VarDumper::dump($highchartsDrill,10,true);

        //  die;
        echo \panix\ext\highcharts\Highcharts::widget([
            'scripts' => [
                // 'highcharts-more', // enables supplementary chart types (gauge, arearange, columnrange, etc.)
                'modules/exporting',
                //'modules/drilldown',
            ],
            'options' => [
                'chart' => [
                    'type' => 'column',
                    'plotBackgroundColor' => null,
                    'plotBorderWidth' => null,
                    'plotShadow' => false,
                    'backgroundColor' => 'rgba(255, 255, 255, 0)'
                ],
                'title' => ['text' => $this->context->pageName],
                'xAxis' => [
                    'type' => 'category',
                    //'categories' => range(1, cal_days_in_month(CAL_GREGORIAN, $month, $year))
                    //'categories' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
                ],
                'yAxis' => [
                    'title' => ['text' => 'Сумма']
                ],

                'legend' => [
                    'enabled' => false
                ],
                'plotOptions' => [
                    'areaspline' => [
                        'fillOpacity' => 0.5
                    ],
                    'area' => [
                        'pointStart' => 1940,
                        'marker' => [
                            'enabled' => false,
                            'symbol' => 'circle',
                            'radius' => 2,
                            'states' => [
                                'hover' => [
                                    'enabled' => true
                                ]
                            ]
                        ]
                    ],
                    'series' => [
                        'borderWidth' => 0,
                        'dataLabels' => [
                            'enabled' => true,
                            'format' => '{point.y:.1f}%'
                        ]
                    ]
                ],
                // 'tooltip' => array(
                //     'shared' => true,
                //     'valueSuffix' => ' кол.'
                // ),
                'tooltip' => [
                    'headerFormat' => '<span style="font-size:11px">{series.name}</span><br>',
                    'pointFormat' => '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.2f}%</b> of total<br/>'
                ],
                'series' => [
                    // array('name' => 'Сумма заказов', 'data' => $data_total),
                    //array('name' => 'Заказы', 'data' => $data),
                    [
                        'name' => 'Заказов',
                        'colorByPoint' => true,
                        'tooltip' => [
                            'pointFormat' => '<span style="font-weight: bold; color: {series.color}">{series.name}</span>: {point.value}<br/><b>Продано товаров: {point.products}<br/>{point.total_price}</b>' // {point.y:.1f}
                        ],
                        'data' => $highchartsData
                    ],

                    /* array(
                         'name' => 'Дохол',
                         'tooltip' => array(
                             'pointFormat' => '<span style="font-weight: bold; color: {series.color}">{series.name}</span>: {point.value}<br/><b>Продано товаров: {point.products}<br/>{point.y:.1f} mm</b>'
                         ),
                         'data' => $highchartsData
                     ),*/
                ],

                "drilldown" => [
                    'activeDataLabelStyle' => [
                        'color' => '#ea5510',//'#343a40',
                        'cursor' => 'pointer',
                        'fontWeight' => 'bold',
                        'textDecoration' => 'none',
                    ],
                    "series" => $highchartsDrill
                ]
            ]
        ]);
        ?>
    </div>
</div>

