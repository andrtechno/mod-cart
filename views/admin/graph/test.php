<?php

use yii\db\Query;
use panix\mod\cart\models\Order;

?>
<div class="card">
    <div class="card-header">
        <h5>das</h5>
    </div>
    <div class="card-body">


        <?php

        echo \yii\helpers\VarDumper::dump($highchartsData,10,true);die;
        echo \panix\ext\highcharts\Highcharts::widget([
            'scripts' => [
                'highcharts-more', // enables supplementary chart types (gauge, arearange, columnrange, etc.)
                'modules/exporting', // adds Exporting button/menu to chart
                'modules/drilldown', // adds Exporting button/menu to chart
            ],
            'options' => array(
                'chart' => array(
                    'type' => 'column',
                    'plotBackgroundColor' => null,
                    'plotBorderWidth' => null,
                    'plotShadow' => false,
                    'backgroundColor' => 'rgba(255, 255, 255, 0)'
                ),
                'title' => array('text' => $this->context->pageName),
                'xAxis' => array(
                    'type' => 'category'
                    //'categories' => range(1, cal_days_in_month(CAL_GREGORIAN, $month, $year))
                    //  'categories' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
                ),
                'yAxis' => array(
                    'title' => array('text' => 'Сумма')
                ),

                'legend' => array(
                    'enabled' => false
                ),
                'plotOptions' => array(
                    'areaspline' => array(
                        'fillOpacity' => 0.5
                    ),
                    'area' => array(
                        'pointStart' => 1940,
                        'marker' => array(
                            'enabled' => false,
                            'symbol' => 'circle',
                            'radius' => 2,
                            'states' => array(
                                'hover' => array(
                                    'enabled' => true
                                )
                            )
                        )
                    ),
                    'series' => array(
                        'borderWidth' => 0,
                        'dataLabels' => array(
                            'enabled' => true,
                            'format' => '{point.y:.1f}%'
                        )
                    )
                ),
                // 'tooltip' => array(
                //     'shared' => true,
                //     'valueSuffix' => ' кол.'
                // ),
                'tooltip' => array(
                    'headerFormat' => '<span style="font-size:11px">{series.name}</span><br>',
                    'pointFormat' => '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.2f}%</b> of total<br/>'
                ),
                'series' => array(
                    // array('name' => 'Сумма заказов', 'data' => $data_total),
                    //array('name' => 'Заказы', 'data' => $data),
                    array(
                        'name' => 'Заказов',
                        // 'colorByPoint'=> true,
                        'tooltip' => array(
                            'pointFormat' => '<span style="font-weight: bold; color: {series.color}">{series.name}</span>: {point.value}<br/><b>Продано товаров: {point.products}<br/>{point.total_price}</b>' // {point.y:.1f}
                        ),
                        'data' => $highchartsData
                    ),

                    /* array(
                         'name' => 'Дохол',
                         'tooltip' => array(
                             'pointFormat' => '<span style="font-weight: bold; color: {series.color}">{series.name}</span>: {point.value}<br/><b>Продано товаров: {point.products}<br/>{point.y:.1f} mm</b>'
                         ),
                         'data' => $highchartsData
                     ),*/
                ),

                "drilldown" => array(
                    'activeDataLabelStyle' => array(
                        'color' => '#ea5510',//'#343a40',
                        'cursor' => 'pointer',
                        'fontWeight' => 'bold',
                        'textDecoration' => 'none',
                    ),
                    "series" => $highchartsDrill
                )
                /*"drilldown" => array(
                    "series" => array(
                        array(
                            "name" => "Chrome",
                            "id" => "Month_2",
                            "data" => array(
                                [
                                    "v65.0",
                                    0.1
                                ],
                                [
                                    "v64.0",
                                    1.3
                                ],
                            )
                        ),
                    )
                )*/
            )
        ]);
        ?>
    </div>
</div>
