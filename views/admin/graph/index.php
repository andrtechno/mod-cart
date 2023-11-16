<?php

use yii\db\Query;
use panix\mod\cart\models\Order;
use yii\helpers\Html;

/**
 * @var $this \yii\web\View
 */

$years = Yii::$app->db->createCommand('SELECT DISTINCT YEAR(FROM_UNIXTIME(created_at)) as year FROM {{%order}} ORDER BY created_at DESC')->queryAll();
$list = [];
foreach ($years as $year) {
    $list[$year['year']] = $year['year'];
}
?>


<div class="card">
    <div class="card-header">
        <h5><?= $this->context->pageName; ?></h5>
    </div>
    <div class="card-body">

        <div class="m-3">
            <?= Html::beginForm(['/admin/cart/graph'], 'GET'); ?>
            <div class="row">
                <div class="col-auto mb-3 mb-md-3">
                    <?= Html::dropDownList('year', (int)Yii::$app->request->get('year', date('Y')), $list, ['class' => 'custom-select w-auto']); ?>
                </div>
                <div class="col-auto mb-3 mb-md-3">
                    <?= Html::dropDownList('status_id', (int)Yii::$app->request->get('status_id'), \yii\helpers\ArrayHelper::map($queryStatusIds, 'id', 'name'), ['class' => 'custom-select w-auto']); ?>
                </div>
                <div class="col-auto">
                    <?= Html::dropDownList('type', Yii::$app->request->get('type', 'income'), ['income' => Yii::t('cart/admin', 'INCOME'), 'circulation' => Yii::t('cart/admin', 'CIRCULATION')], ['class' => 'custom-select']); ?>
                </div>
                <div class="col-auto">
                    <?= Html::submitButton('Показать', ['class' => 'btn btn-secondary']); ?>
                </div>
            </div>
            <?= Html::endForm(); ?>
        </div>
        <?php


        $title = Yii::t('cart/admin', (Yii::$app->request->get('type', 'income') == 'income') ? 'INCOME_FOR' : 'CIRCULATION_FOR', [
            'month' => '',
            'year' => (int)Yii::$app->request->get('year', date('Y'))
        ]);
        $subTitle = 'Итого: ' . $total . ' ' . Yii::$app->currency->active['symbol'];

        echo \panix\ext\highcharts\Highcharts::widget([
            'scripts' => [
                // 'highcharts-more', // enables supplementary chart types (gauge, arearange, columnrange, etc.)
                'modules/exporting',
                'modules/drilldown',
            ],
            'options' => [
                'chart' => [
                    'type' => 'bar',
                    'height' => 800,
                    'plotBackgroundColor' => null,
                    'plotBorderWidth' => null,
                    'plotShadow' => false,
                    'backgroundColor' => 'rgba(255, 255, 255, 0)',
                    'events' => [
                        'drillup' => new \yii\web\JsExpression('
                            function (e) {
                                chart.update({
                                    subtitle:{text:"' . $subTitle . '"},
                                    title:{text:"' . $title . '"}
                                });
                            }
                        '),
                        'drilldown' => new \yii\web\JsExpression('
                            function (e) {
                                if (!e.seriesOptions) {
                                    $.ajax({
                                        type:"GET",
                                        //url:"' . \yii\helpers\Url::toRoute(['testajax']) . '",
                                        url: common.url("/admin/cart/graph/"+e.point.action),
                                        data:{
                                            action: e.point.action,
                                            name:e.point.name,
                                            year:e.point.year,
                                            month:e.point.month,
                                            day:e.point.day,
                                            type:e.point.type,
                                            status_id:e.point.status_id
                                        },
                                        dataType:"json",
                                        beforeSend:function(){
                                            chart.showLoading("Загрузка");
                                        },
                                        success:function(response){
                                            chart.hideLoading();
                                           // chart.addSingleSeriesAsDrilldown(e.point, 123);
                                            chart.addSeriesAsDrilldown(e.point, response.data);

                                            //chart.applyDrilldown();
                                            //chart.series.forEach(function(el, inx) {
                                            //    el.update({type: "bar"});
                                            //});
                                            
                                            chart.update({
                                                height: 700,
                                                subtitle:{text:response.subtitle},
                                                title:{text:response.title}
                                            })
                                        }
                                    })
                                }
                            }
                        '),
                    ],
                ],
                'title' => ['text' => $title],
                'subtitle' => [
                    'text' => $subTitle
                ],
                'xAxis' => [

                    'type' => 'category',
                    //'categories' => range(1, cal_days_in_month(CAL_GREGORIAN, $month, $year))
                    //'categories' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
                ],
                'yAxis' => [
                    'min' => 0,
                    'title' => false,
                    'labels' => [
                        'overflow' => 'justify'
                    ],

                    //    'title' => ['text' => 'Сумма']
                ],

                'legend' => [
                    'enabled' => false,
                    'layout' => 'vertical',
                    'align' => 'right',
                    'verticalAlign' => 'top',
                    'x' => -40,
                    'y' => 80,
                    'floating' => true,
                    'borderWidth' => 1,
                    'shadow' => true
                ],
                'plotOptions' => [
                    'areaspline' => [
                        'fillOpacity' => 0.5
                    ],
                    //'column' => [
                    //'pointPadding' => 0.1,
                    //'borderWidth' => 0.0
                    //],
                    'bar' => [
                        'dataLabels' => [
                            'enabled' => true
                        ]
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
                        //'borderWidth' => 1,
                        'dataLabels' => [
                            'enabled' => true,
                            'format' => '{point.value}'
                        ]
                    ],
                    //'marker' => [
                    //    'lineWidth' => 1
                    //]
                ],
                'tooltip' => [
                    'enabled' => false,
                    'headerFormat' => '<table border="1">',
                    'pointFormat' => '<tr><td><span style="font-size:11px">{series.name}</span></td></tr><span style="color:{point.color}">{point.name}</span>: <strong>{point.value} грн. Продано товаров: {point.products}</strong>',
                    'footerFormat' => '</table>',
                    'shared' => true,
                    'crosshairs' => true,
                    'useHTML' => true
                    //'valueSuffix' => ' кол.'
                ],
                'series' => [
                    [
                        'name' => Yii::t('cart/admin', (Yii::$app->request->get('type', 'income') == 'income') ? 'INCOME' : 'CIRCULATION'),
                        'colorByPoint' => true,
                        'tooltip' => [
                            'pointFormat' => '<tr><td><span style="font-weight: bold; color: {series.color}">{series.name}</span>: {point.value}<br/><div>Продано товаров: {point.products}<br/></div></td></tr>'
                        ],
                        'data' => $highchartsData
                    ],
                ],

                "drilldown" => [
                    'activeDataLabelStyle' => [
                        'color' => '#333',
                        'cursor' => 'pointer',
                        'fontWeight' => 'bold',
                        'textDecoration' => 'none',
                    ],
                    // "series" => $highchartsDrill
                ]
            ]
        ]);
        ?>
    </div>
</div>

