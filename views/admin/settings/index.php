<?php

use panix\engine\Html;
use panix\engine\bootstrap\ActiveForm;
use panix\ext\taginput\TagInput;
use panix\ext\tinymce\TinyMce;

/*
echo \panix\ext\highcharts\Highcharts::widget([
    'options' => [
        'title' => [
            'text' => 'Solar Employment Growth by Sector, 2010-2016'
        ],

        'subtitle' => [
            'text' => 'Source: thesolarfoundation.com'
        ],

        'yAxis' => [
            'title' => [
                'text' => 'Number of Employees'
            ]
        ],
        'legend' => [
            'layout' => 'vertical',
            'align' => 'right',
            'verticalAlign' => 'middle'
        ],

        'plotOptions' => [
            'series' => [
                'label' => [
                    'connectorAllowed' => false
                ],
                'pointStart' => 2010
            ]
        ],

        'series' => [
            [
                'name' => 'Installation',
                'data' => [43934, 52503, 57177, 69658, 97031, 119931, 137133, 154175]
            ], [
                'name' => 'Manufacturing',
                'data' => [24916, 24064, 29742, 29851, 32490, 30282, 38121, 40434]
            ], [
                'name' => 'Sales & Distribution',
                'data' => [11744, 17722, 16005, 19771, 20185, 24377, 32147, 39387]
            ], [
                'name' => 'Project Development',
                'data' => [null, null, 7988, 12169, 15112, 22452, 34400, 34227]
            ], [
                'name' => 'Other',
                'data' => [12908, 5948, 8105, 11248, 8989, 11816, 18274, 18111]
            ]
        ],

        'responsive' => [
            'rules' => [
                [
                    'condition' => ['maxWidth' => 500],
                    'chartOptions' => [
                        'legend' => [
                            'layout' => 'horizontal',
                            'align' => 'center',
                            'verticalAlign' => 'bottom'
                        ]
                    ]
                ]
            ]
        ]
    ]
]);
*/

$form = ActiveForm::begin();
?>
    <div class="card">
        <div class="card-header">
            <h5><?= $this->context->pageName ?></h5>
        </div>
        <div class="card-body">
            <?=
            $form->field($model, 'order_emails')
                ->widget(TagInput::class, ['placeholder' => 'E-mail'])
                ->hint('Введите E-mail и нажмите Enter');
            ?>

            <?php echo $form->field($model, 'notify_changed_status')->checkbox(); ?>
            <?php //echo $form->field($model, 'tpl_subject_admin'); ?>


            <?php /*echo $form->field($model, 'tpl_body_user')->widget(TinyMce::class, [
                'options' => ['rows' => 6],

            ]);*/
            ?>
            <?php /*echo $form->field($model, 'tpl_body_admin')->widget(TinyMce::class, [
                'options' => ['rows' => 6],

            ]);*/
            ?>
            <?php
            echo $form->field($model, 'mail_tpl_order')->widget(\panix\ext\codemirror\CodeMirrorTextArea::class, [
              //  'modes' => ['php', 'css', 'xml', 'javascript', 'smarty'],
                'mode' => ['name' => 'smarty', 'version' => 3, 'baseMode' => 'text/html'],
                'theme'=>'elegant',//'solarized', ttcn
                'options' => [
                    'rows' => 6,
                    'class' => 'form-control'
                ],

            ]);

            ?>

        </div>
        <div class="card-footer text-center">
            <?= $model->submitButton(); ?>
        </div>
    </div>
<?php ActiveForm::end(); ?>