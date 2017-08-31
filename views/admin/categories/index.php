<?php


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