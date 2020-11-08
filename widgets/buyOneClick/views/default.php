<?php
use panix\engine\Html;

/**
 * @var $this \yii\web\View;
 * @var $model \panix\mod\shop\models\Product;
 */

\panix\ext\fancybox\FancyboxAsset::register($this);

echo Html::a(Yii::t('cart/Order','BUYONECLICK'), ['/cart/default/buyOneClick', 'id' => $model->primaryKey], [
    'id' => 'buyOneClick-button',
    'class' => 'mt-4 btn btn-lg btn-block btn-outline-secondary',
]);

$this->registerJs("
$(document).on('click','#buyOneClick-button',function(){
    var that = $(this);
    var form = that.closest('form');
    $.fancybox.open({
        src: that.attr('href'),
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
        }
    });
	return false;
});
")
?>

