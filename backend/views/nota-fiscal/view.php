<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\NotaFiscal */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Nota Fiscals', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="nota-fiscal-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'chave_nf',
            'valor_nf',
            'data_nf',
            'id_nf',
            'id_pedido',
            'numero_nf',
            'modo_frete',
            'id_recebimento',
            'id_transportadora',
            'data_cancelamento',
            'data_emissao',
            'data_inutilizacao',
            'data_registro',
            'data_saida',
            'finalidade_emissao',
            'tipo_nf',
            'tipo_ambiente',
            'serie',
            'codigo_modelo',
            'indice_pagamento',
            'h_saida_entrada_nf',
            'h_emissao',
            'cod_int_empresa',
            'cod_empresa',
            'cod_int_cliente_fornecedor',
            'cod_cliente',
        ],
    ]) ?>

</div>