
<?php

//index.php?r=main/ajax/widget.actionCallback
echo CHtml::ajaxLink(Yii::t('BuyOneClickWidget.default', 'BUTTON'), array('/cart/buyOneClick.action'), array(
    'type' => 'GET',
    'beforeSend' => "function(){
        $('.buyOneClick-button').attr('disabled',true);
              //$.jGrowl('Загрузка...');
              $('body').append('<div id=\"buyOneClick-dialog\"></div>');
              }
    ",
    'success' => "function( data ){
        var result = data;

        $('#buyOneClick-dialog').dialog({
            resizable: false,
            draggable:false,
            responsive: true,
            height: 'auto',
            minHeight: 95,
            title:false,
            width: 400,
            modal: true,
            close:function(){
                $('#buyOneClick-dialog').remove();

                $('a.btn-danger').removeClass(':focus');  
                $('.buyOneClick-button').attr('disabled',false);
            },
            open:function(){
                $('#buyOneClick-form').keypress(function(e) {
                    if (e.keyCode == $.ui.keyCode.ENTER) {
                          $('#buyOneClick-form').submit();
                    }
                });
                $('.ui-widget-overlay').bind('click', function() {
                    $('#buyOneClick-dialog').dialog('close');
                });
                    $('.ui-dialog :button').blur();

            },
            buttons: [
                {
                    text: '" . Yii::t('BuyOneClickWidget.default', 'BUTTON_SEND') . "',
                    'class':'btn btn-default wait',
                    click: function() {
                        buyOneClickSend();
                    }
                }
            ]
        });
        $('#buyOneClick-dialog').html(result); 

        $('.ui-dialog').position({
                  my: 'center',
                  at: 'center',
                  of: window,
                  collision: 'fit'
            });
        }",
    'data' => array(
        'id' => $this->pk,
        'quantity' => 'js:function(){return $("#quantity").val()}'
    ),
    'cache' => 'false' // если нужно можно закэшировать
        ), array(

    // 'href' => Yii::app()->createUrl('ajax/new_link222'), // подменяет ссылку на другую
    'class' => "buyOneClick-button btn btn-default" // добавляем какой-нить класс для оформления
        )
);
