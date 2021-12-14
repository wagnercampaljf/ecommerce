<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\OperacaoFinanceira;
use common\models\MovimentacaoFinanceiraTipo;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\OperacaoFinanceiraSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>
<div class="operacao-financeira-index">

    <h1>Movimentação Fincaneira</h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            [
                'label'     => 'Operação',
                'value'     => function($model) {
                $operacao = OperacaoFinanceira::find()->andWhere(["=", "id", $model->operacao_financeira_id])->one();
                return $operacao->numero;
                },
                'format'    => 'text',
                ],
            [
                "label" => "Movimentação",
                "value" => 'numero',
                'format'    => 'text',
            ],
            [
                'label'     => 'Tipo de Movimentação',
                'value'     => function($model) {
                $tipo = MovimentacaoFinanceiraTipo::find()->andWhere(["=", "id", $model->movimentacao_financeira_tipo_id])->one();
                return $tipo->descricao;
                },
                'format'    => 'text',
                ],
            'data_hora',
            [
                "label" => "Valor",
                "value" => 'valor',
                'format'    => 'currency',
            ],
            [
                "label" => "Valor Total",
                "value" => 'valor_total',
                'format'    => 'currency',
            ],
            [
                'label'     => 'CPF/CNPJ',
                'value'     => function($model) {
                $operacao = OperacaoFinanceira::find()->andWhere(["=", "id", $model->operacao_financeira_id])->one();
                return $operacao->cliente_cpf_cnpj;
                },
                'format'    => 'text',
                ],
            [
                'label'     => 'Nome',
                'value'     => function($model) {
                $operacao = OperacaoFinanceira::find()->andWhere(["=", "id", $model->operacao_financeira_id])->one();
                return $operacao->cliente_nome;
                },
                'format'    => 'text',
                ],
            //['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
