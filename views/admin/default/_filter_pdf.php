<?php
use panix\engine\CMS;
use panix\engine\Html;

?>
<div class="card">
    <div class="card-header">
        <h5>Фиьтры</h5>
    </div>
    <div class="card-body">
        dsasad
    </div>
</div>
<div class="p-5">
        <div class="form-group">


            <div class="input-group">
                <div class="input-group-append"><span class="input-group-text">с</span></div>
                <?php
                /*$this->widget('app.jui.JuiDatePicker', array(
                    'value' => (isset($_GET['start'])) ? $_GET['start'] : date('Y-m-d'),
                    'options' => array(
                        'dateFormat' => 'yy-mm-dd',
                    ),
                    'htmlOptions' => array('name' => 'start')
                ));*/
                ?>
                <div class="input-group-prepend"><span class="input-group-text">по</span></div>
                <?php
               /* $this->widget('app.jui.JuiDatePicker', array(
                    'value' => (isset($_GET['end'])) ? $_GET['end'] : date('Y-m-d'),
                    'options' => array(
                        'dateFormat' => 'yy-mm-dd',
                        'maxDate' => 0,
                    ),
                    'htmlOptions' => array('name' => 'end')
                ));*/
                ?>
            </div>
        </div>


        <div class="form-group">
            <?php
           /* $this->widget('ext.bootstrap.selectinput.SelectInput', array(
                'name' => "render",
                'value' => 'delivery',
                'data' => array('delivery' => 'Распределить по доставке', 'brands' => 'Распределить по производителю'),
                'options' => array(
                    'mobile' => CMS::isMobile(),
                    'width' => CMS::isMobile() ? false : 'auto'
                ),
                'htmlOptions' => array()
            ));*/
            ?>
        </div>
        <div class="form-group">
            <?php
           /* $this->widget('ext.bootstrap.selectinput.SelectInput', array(
                'name' => "type",
                'value' => 1,
                'data' => array(1 => 'PDF', 0 => 'Html'),
                'options' => array(
                    'mobile' => CMS::isModile(),
                    'width' => CMS::isModile() ? false : 'auto'
                ),
                'htmlOptions' => array()
            ));*/
            ?>
        </div>
        <div class="form-group">
            <?= Html::checkBox('image', true, array('class' => 'form-control2')); ?>
            <?= Html::label('Картинки', 'image', array('class' => 'control-label')); ?>
        </div>

        <?= Html::submitButton('Показать', array('class' => 'btn btn-success', 'name' => '')); ?>


</div>


