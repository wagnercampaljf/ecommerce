<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\FilialSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Filials';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="filial-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Criar Filial', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'nome',
            'razao',
            'documento',
            'juridica:boolean',
            // 'lojista_id',
            // 'banco_id',
            // 'numero_banco',
            // 'token_moip',
            // 'porcentagem_venda',
            // 'id_tipo_empresa',
            // 'telefone',
            // 'telefone_alternativo',
            // 'refresh_token_meli',
            // 'integrar_b2w:boolean',
            // 'envio',
            // 'mercado_livre_secundario:boolean',
            // 'mercado_livre_logo:boolean',
            // 'email_pedido:email',

            [
		'class' => 'yii\grid\ActionColumn',
		'template'=>'{update}',
	    ],
        ],
    ]); ?>

</div>
