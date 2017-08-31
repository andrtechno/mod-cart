<?php

use yii\helpers\Html;
use app\system\modules\shop\models\ShopCategory;
//$countries = new ShopCategory(['seo_alias' => 'root']);
//$countries->makeRoot();


$countries = ShopCategory::findOne(['seo_alias' => 'root']);
$children = $countries->children()->all();
//print_r($children);
foreach($children as $r){
    echo $r->seo_alias;
    echo '<br>';
}
//$australia = new ShopCategory(['seo_alias' => 'sssss222']);
//$australia->appendTo($countries);

//$russia = new ShopCategory(['seo_alias' => 'Russia']);
//$russia->prependTo($countries);

?>
<div class="pages-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', ['model' => $model]) ?>

</div>
<?php
/*echo \panix\engine\widgets\nestable\Nestable::widget([
    'modelClass' => 'app\system\modules\shop\models\ShopCategory',
]);*/

use kartik\tree\TreeView;
echo TreeView::widget([
    // single query fetch to render the tree
    'query' => app\models\Product::find()->addOrderBy('root, lft'), 
    'headingOptions' => ['label' => 'Categories'],
    'fontAwesome' => true,     // optional
    'isAdmin' => true,         // optional (toggle to enable admin mode)
   // 'displayValue' => 1,        // initial display value
    'iconEditSettings'=> [
        'show' => 'list',
        'listData' => [
            'folder' => 'Folder',
            'file' => 'File',
            'mobile' => 'Phone',
            'bell' => 'Bell',
        ]
    ],
    'softDelete' => true,    // normally not needed to change
    //'cacheSettings' => ['enableCache' => true] // normally not needed to change
]);

        ?>