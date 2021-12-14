<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\FormularioGarantia */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Formulario Garantias', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="formulario-garantia-view">

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
            'email:email',
            'data_compra',
            'razao_social',
            'nr_nf_compra',
            'codigo_peça_seis_digitos',
            'modelo_do_veiculo',
            'ano',
            'chassi',
            'numero_de_serie_do_motor',
            'data_aplicação',
            'km_montagem',
            'km_defeito',
            'contato',
            'telefone',
            'descrição_do_defeito_apresentado',
        ],
    ]) ?>

</div>
