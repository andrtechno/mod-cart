<?php
use yii\helpers\Html;
\yii\bootstrap4\BootstrapAsset::register($this);
\yii\bootstrap4\BootstrapPluginAsset::register($this);
\panix\engine\assets\CommonAsset::register($this);
\panix\mod\cart\CartAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html dir="ltr" lang="ru">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <?= Html::csrfMetaTags() ?>
    <?php $this->head() ?>

</head>
<body class="common-home">
<?php $this->beginBody() ?>
<div class="container">
    <?php
    echo $content;
    ?>

</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
