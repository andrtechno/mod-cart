<?php

use panix\engine\Html;
use panix\mod\shop\models\Product;

/**
 * @var $this \yii\web\View;
 * @var $model \panix\mod\shop\models\Product;
 */

//\panix\ext\fancybox\FancyboxAsset::register($this);

echo Html::button(Yii::t('cart/Order', 'BUYONECLICK'), [
    //'data-url'=>\yii\helpers\Url::to(['/cart/default/buyOneClick', 'id' => $model->primaryKey]),
    'id' => 'buyOneClick-button',
    'data' => [
        'target2' => '#buyOneClick-modal',
        'toggle2' => 'modal'
    ],
    'class' => 'mt-4 btn btn-block btn-outline-secondary',
]);

$this->registerJs("
/*$(document).on('click','#buyOneClick-button',function(){
    var that = $(this);
    var form = that.closest('form');
    $.fancybox.open({
        src: that.data('url'),
        type: 'ajax',
        opts: {
            touch: {
                vertical: false,
                momentum: false
            },
            ajax: {
                settings: {
                    method:'POST',
                    data: form.serialize()
                }
            }
        },

    });
	return false;
});*/


$(document).on('click','#buyOneClick-button', function(){
    //var form = $('#buyOneClick-modal').find('form');
    var form = $(this).closest('form');
    $.ajax({
        url: '" . \yii\helpers\Url::to(['/cart/default/buyOneClick', 'id' => $model->primaryKey]) . "',
        method:'POST',
        data: form.serialize(),
        success:function(result){
            $('#buyOneClick-modal').find('.buyOneClick-content').html(result);
            $('#buyOneClick-modal').modal('show');
            $('#buyOneClick-modal #order-user_phone').css({'padding-left':95});
        }
    });
    
    return false;
});
/*
$('#buyOneClick-modal').on('show.bs.modal', function (e) {
    var form = $(this).closest('form');
    var content = $(e.target).find('.buyOneClick-content');
});*/
");

?>




