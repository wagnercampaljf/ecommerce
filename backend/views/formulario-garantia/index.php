<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\FormularioCarantiaSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Formulario Garantia';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="formulario-garantia-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>


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
             'nr_nf_compra',
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

            // ['class' => 'yii\grid\ActionColumn'],

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        return Html::a(' <span class="glyphicon glyphicon-search" ></span > ',
                            Url::to(['/formulario-garantia/view', 'id' => $model->id]), [
                                'title' => Yii::t('yii', 'View'),
                            ]);

                    }
                ]
            ],
        ],
    ]); ?>

</div>
