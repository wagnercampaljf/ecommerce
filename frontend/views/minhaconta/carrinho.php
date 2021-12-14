<?php
/* @var $this yii\web\View */
/* @var $dataProvider \yii\data\ActiveDataProvider */
/* @var $carrinho \common\models\Carrinho */

use yii\grid\GridView;
use yii\helpers\Html;

//$comprador = Yii::$app->user->getIdentity();
$this->title = Yii::t('app', '{nome} #{id}', [
    'id' => $carrinho->id,
    'nome' => $carrinho->chave
]);
$this->params['active'] = 'carrinhos';
$this->params['breadcrumbs'][] = ['label' => 'Minha Conta', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Meus Carrinhos', 'url' => ['carrinhos']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tab-pane active col-md-9 col-sm-12" id="carrinho">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <?= Html::encode($this->title) ?>
        </div>
        <div class="panel-body">
            <div class="form-horizontal " style="margin-bottom: 5%">
                <div class="form-group">
                    <label class="col-sm-3 control-label">ID Carrinho</label>

                    <div class="col-sm-9">
                        <p class="form-control-static"><?= '#' . $carrinho['id'] ?></p>
                    </div>
                    <label class="col-sm-3 control-label">Nome Carrinho</label>

                    <div class="col-sm-9">
                        <p class="form-control-static"><?= $carrinho['chave'] ?></p>
                    </div>
                    <label class="col-sm-3 control-label">Data de Criação</label>

                    <div class="col-sm-9">
                        <p class="form-control-static"><?= Yii::$app->formatter->asDate($carrinho['dt_criacao']) ?></p>
                    </div>

                </div>
            </div>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],

                    [
                        'attribute' => 'Produto',
                        'format' => 'raw',
                        'header' => 'Produto',
                        'value' => function ($data) {
                            return Html::a($data->produtoFilial->produto->nome, $data->produtoFilial->produto->getUrl(),
                                ['target' => '_blank']);

                        },
                    ],
                    'produtoFilial.filial.nome',
                    'quantidade',
                    [
                        'attribute' => 'Valor',
                        'format' => 'text',
                        'header' => 'Valor',
                        'value' => function ($data) {
                            $valor = $data->produtoFilial->getValorProdutoFilials()->ativo()->one()->getValorFinal(Yii::$app->params['isJuridica']());

                            return \Yii::$app->formatter->asCurrency($valor);
                        },
                    ],


                ],
            ]); ?>
            <span class="price lead">
            <p align="right" class="bg-success">
                Total: <?= Yii::$app->formatter->asCurrency($carrinho->getValorTotal()) ?>
            </p>
        </span>
            <?= Html::a('Voltar', ['carrinhos'], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Restaurar Carrinho', ['restaurar-carrinho', 'id' => $carrinho->id],
                ['class' => 'btn btn-primary']) ?>
        </div>
    </div>
</div>