<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\NotaFiscalProduto */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Nota Fiscal Produtos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="nota-fiscal-produto-view">

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
            'nota_fiscal_id',
            'valor_produto',
            'codigo_produto',
            'descricao',
            'pa_produto',
            'cod_int_item',
            'cod_int_produto',
            'cod_item',
            'cod_produto',
            'cod_fiscal_operacao_servico',
            'cod_situacao_tributaria_icms',
            'cod_ncm',
            'ean',
            'ean_tributável',
            'codigo_produto_original',
            'codigo_local_estoque',
            'cmc_total',
            'cmc_unitario',
            'aliquota_icms',
            'qtd_comercial',
            'qtd_tributavel',
            'unid_tributavel',
            'valor_desconto',
            'valor_total_frete',
            'valor_icms',
            'outras_despesas',
            'valor_unitario_tributacao',
            'descricao_original',
        ],
    ]) ?>

</div>
