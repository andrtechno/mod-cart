<?php

use yii\db\Query;
use panix\mod\cart\models\Order;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

/**
 * @var $this \yii\web\View
 */

$years = Yii::$app->db->createCommand('SELECT DISTINCT YEAR(FROM_UNIXTIME(created_at)) as year FROM {{%order}} ORDER BY created_at DESC')->queryAll();
$list = [];
foreach ($years as $year) {
    $list[$year['year']] = $year['year'];
}
$months = array();
for ($i = 0; $i < 12; $i++) {
    $timestamp = mktime(0, 0, 0, date('m') - $i, 1);
    $months[date('m', $timestamp)] = date('F', $timestamp);
}
ksort($months);
//print_r($months);die;
?>


<!-- Modal -->
<div class="modal fade" id="filterModal" tabindex="-1" role="dialog" aria-labelledby="filterModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <?= Html::beginForm(['/' . $this->context->route], 'GET'); ?>
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Filter</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <div class="mb-3 row no-gutters">
                    <label for="limit" class="col-sm-2 col-form-label pr-3">Показать</label>
                    <div class="col-sm-10">
                        <?= Html::textInput('top', (int)Yii::$app->request->get('top', 10), ['class' => 'form-control']); ?>
                    </div>
                </div>
                <div class="mb-3 row no-gutters">
                    <label for="limit"
                           class="col-sm-2 col-form-label pr-3"><?= Yii::t('app/default', 'STATUS') ?></label>
                    <div class="col-sm-10">
                        <?= Html::dropDownList('status_id', (int)Yii::$app->request->get('status_id'), ArrayHelper::map($queryStatusIds, 'id', 'name'), ['class' => 'custom-select w-100', 'prompt' => '- ' . Yii::t('app/default', 'ALL') . ' - ']); ?>
                    </div>
                </div>
                <div class="mb-3 row no-gutters">
                    <label for="limit" class="col-sm-2 col-form-label pr-3">Год</label>
                    <div class="col-sm-10">
                        <?= Html::dropDownList('year', (int)Yii::$app->request->get('year', date('Y')), $list, ['class' => 'custom-select w-auto']); ?>
                    </div>
                </div>
                <div class="row no-gutters">
                    <label for="limit" class="col-sm-2 col-form-label pr-3">Месяц</label>
                    <div class="col-sm-10">

                        <?= Html::checkboxList('months', Yii::$app->request->get('months', [date('m')]), $months, [ //ucfirst($label)
                            'item' => function ($index, $label, $name, $checked, $value) {
                                if ($checked) {
                                    $checked = 'checked="' . $checked . '"';
                                } else {
                                    $checked = '';
                                }

                                $return = '<div class="custom-control custom-checkbox">';
                                $return .= '<input type="checkbox" class="custom-control-input" name="' . $name . '" value="' . $value . '" ' . $checked . ' id="check-' . $index . '" />';
                                $return .= '<label class="custom-control-label" for="check-' . $index . '">' . Yii::t('app/month', $label, ['n' => 3]);
                                $return .= '</label></div>';
                                return $return;
                            },
                        ]); ?>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                        data-dismiss="modal"><?= Yii::t('app/default', 'CLOSE') ?></button>
                <?= Html::submitButton('Применить', ['class' => 'btn btn-primary']); ?>
            </div>
        </div>
        <?= Html::endForm(); ?>
    </div>
</div>


<div class="card">
    <div class="card-header">
        <h5><?= $this->context->pageName; ?></h5>
    </div>
    <div class="card-body">
        <button type="button" class="btn btn-primary ml-3 mt-3" data-toggle="modal" data-target="#filterModal">
            <i class="icon-filter"></i> Filter
        </button>

        <?php


        $title = $this->context->pageName;


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
                            'enabled' => true,
                            //'format' => '{point.value}'
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
                            'format' => '{point.value}',

                        ],
                    ],
                    //'marker' => [
                    //    'lineWidth' => 1
                    //]
                ],
                'tooltip' => [
                    'enabled' => true,
                    'headerFormat' => '<table border="1">',
                    'pointFormat' => '<tr><td><span style="font-size:11px">{series.name}</span></td></tr><span style="color:{point.color}">{point.sum}</span>',
                    'footerFormat' => '</table>',
                    'shared' => true,
                    'crosshairs' => true,
                    'useHTML' => true
                    //'valueSuffix' => ' кол.'
                ],

                'series' => [
                    [
                        'name' => $this->context->pageName,
                        'colorByPoint' => true,
                        'tooltip' => [
                            'pointFormat' => '<tr><td>' . Yii::t('cart/admin', 'INCOME') . ': <strong>{point.income}</strong> грн.</td></tr><tr><td>' . Yii::t('cart/admin', 'CIRCULATION') . ': <strong>{point.circulation}</strong> грн.</td></tr>'
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

