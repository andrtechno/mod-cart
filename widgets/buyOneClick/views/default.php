<?php
use panix\engine\Html;

?>
<?php
echo \panix\ext\fancybox\Fancybox::widget([
    'target' => 'a[data-fancybox]',
    'options' => [
        'onInit' => new \yii\web\JsExpression('function(){
            console.log("init buy one click");
        }'),
        'touch' => [
            'vertical' => false, // Allow to drag content vertically
            'momentum' => false // Continue movement after releasing mouse/touch when panning
        ],
    ]
]);

echo Html::a('asdasd', 'javascript:;', [
    'data-fancybox' => true,
    'data-type' => 'ajax',
    'data-src' => \yii\helpers\Url::to(['/cart/default/buyOneClick', 'id' => $this->context->pk, 'quantity' => 1]),

])
?>

