<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\FormularioCarantiaSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Formulario Garantias';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="formulario-garantia-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Formulario Garantia', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'nome',
            'email:email',
            'data_compra',
            'razao_social',
            // 'nr_nf_compra',
            // 'codigo_peca_seis_digitos',
            // 'modelo_do_veiculo',
            // 'ano',
            // 'chassi',
            // 'numero_de_serie_do_motor',
            // 'data_aplicacao',
            // 'km_montagem',
            // 'km_defeito',
            // 'contato',
            // 'telefone',
            // 'descricao_do_defeito_apresentado',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
