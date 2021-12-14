<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\common\models\MarkupDetalhe */

$this->title = 'Create Markup Detalhe';
$this->params['breadcrumbs'][] = ['label' => 'Markup Detalhes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="markup-detalhe-create">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php

    ?>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>