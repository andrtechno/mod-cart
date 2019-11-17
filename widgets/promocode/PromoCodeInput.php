<?php

namespace panix\mod\cart\widgets\promocode;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use panix\engine\data\Widget;
use yii\widgets\InputWidget;

class PromoCodeInput extends InputWidget
{

    public $result;
    public $form;
    public function init()
    {
        parent::init();
        //$this->view->registerJs("var intlTelInput{$hash} = $('#$id').intlTelInput($jsOptions);");
        $id = ArrayHelper::getValue($this->options, 'id');
        if (!isset($this->options['class']))
            $this->options['class'] = 'form-control';


            $this->view->registerJs("
            $('#$id').on('change', function() {
                var value = $(this).val();
                console.log('dsadas');
                $.ajax({
                    type:'POST',
                    dataType:'json',
                    url:common.url('/cart/promo-code'),
                    data:{code:value},
                    success:function(data){
                    var resultSelector = $('#promocode-result'); //#promocode-result, #promoCodeResult
                        if(data.success){
                            resultSelector.removeClass('text-danger').addClass('text-success');
                        }else{
                            resultSelector.removeClass('text-success').addClass('text-danger');
                        }
                        resultSelector.html(data.message);
                    }
                });
            });
            
            $('#submit-promocode').on('click', function() {
                var value = $('#$id').val();
                $.ajax({
                    type:'POST',
                    dataType:'json',
                    url:common.url('/cart/promo-code'),
                    data:{code:value,accept:1},
                    success:function(data){
                        if(data.success){
                            $('#promocode-result').html(data.message);
                        }
                    }
                });
            });
        ");





    }

    /**
     * @return string
     */
    public function run()
    {
        if ($this->hasModel()) {
            return Html::activeInput('text', $this->model, $this->attribute, $this->options);
        }
        return Html::input('text', $this->name, $this->value, $this->options);
    }
}
