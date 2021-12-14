<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Filial */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Filials', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="filial-view">

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
            'nome',
            'razao',
            'documento',
            'juridica:boolean',
            'lojista_id',
            'banco_id',
            'numero_banco',
            'token_moip',
            'porcentagem_venda',
            'id_tipo_empresa',
            'telefone',
            'telefone_alternativo',
            'refresh_token_meli',
            'integrar_b2w:boolean',
            'envio',
            'mercado_livre_secundario:boolean',
            'mercado_livre_logo:boolean',
            'email_pedido:email',
        ],
    ]) ?>

</div>
