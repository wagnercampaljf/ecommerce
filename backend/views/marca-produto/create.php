<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\MarcaProduto */

$this->title = 'Criar Marca';
$this->params['breadcrumbs'][] = ['label' => 'Marca Produtos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="marca-produto-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
