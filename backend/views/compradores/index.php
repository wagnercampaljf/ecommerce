<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel common\models\CompradorSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Compradores';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="comprador-index">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],

//            'id',
            'nome',
//            'empresa_id',
//            'cpf',
            [
                'attribute' => 'cpf',
                'format' => 'raw',
                'header' => 'CNPJ / CPF',
                'value' => function ($data) {
                    return $data->empresa->getDocumentoLabel();
                },
            ],
//            'username',
            // 'password',
            // 'dt_criacao',
            // 'ativo:boolean',
            // 'dt_ultima_mudanca_senha',
            'email:email',
            // 'cargo',
            // 'nivel_acesso_id',
            // 'auth_key',
            // 'password_reset_token',
            // 'token_moip',
            [
                'attribute' => 'telefone',
                'format' => 'raw',
                'header' => 'Telefone',
                'value' => function ($data) {
                    return Yii::$app->formatter->asTelefone($data->empresa->telefone);
                },
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        return Html::a(' <span class="glyphicon glyphicon-search" ></span > ',
                            Url::to(['/compradores/view', 'id' => $model->id]), [
                                'title' => Yii::t('yii', 'View'),
                            ]);

                    }
                ]
            ],
        ],
    ]); ?>

</div>
