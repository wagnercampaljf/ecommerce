<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ValorProdutoFilial */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Valor Produto Filials', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="valor-produto-filial-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'valor',
            'dt_inicio',
            'produto_filial_id',
            'dt_fim',
            'promocao:boolean',
            'valor_cnpj',
            'produtoFilial.filial.id',
        ],
    ]) ?>

</div>
