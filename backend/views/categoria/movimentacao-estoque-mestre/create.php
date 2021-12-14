<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\MovimentacaoEstoqueMestre */

$this->title = 'Criar Movimentacao de Estoque Mestre';
$this->params['breadcrumbs'][] = ['label' => 'Movimentacao Estoque  Mestres', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="movimentacao-estoque-mestre-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
