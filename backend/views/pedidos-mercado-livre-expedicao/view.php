<?php
/* @var $this yii\web\View */
use vendor\iomageste\Moip\Moip;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use common\models\Banner;

$comprador = Yii::$app->user->getIdentity();
$this->params['active'] = 'pedidos';
$this->registerJsFile(
    Url::base() . '/js/pedidos.js'
);
$this->title = 'Pedido #' . $model->id;
?>

<div class="portlet light" id="pedido">
    <div class="portlet-body">
        <form class="form-horizontal" style="margin-bottom: 5%">
            <div class="form-group">
                <label class="col-sm-3 control-label">Número do Pedido</label>

                <div class="col-sm-9">
                    <p class="form-control-static"><?= '#' . $model['id'] ?></p>
                </div>
                <label class="col-sm-3 control-label">ID Moip</label>

                <div class="col-sm-9">
                    <p class="form-control-static"><?= isset($model['transportadora']['nome']) ? $model['token_moip'] : '---' ?></p>
                </div>
                <label class="col-sm-3 control-label">Data</label>

                <div class="col-sm-9">
                    <p class="form-control-static"><?= Yii::$app->formatter->asDate($model['dt_referencia']) ?></p>
                </div>
                <label class="col-sm-3 control-label">Status</label>

                <div class="col-sm-9">
                    <p class="form-control-static"><?= $model['statusAtual']['tipoStatus']['nome'] ?></p>
                </div>
                <label class="col-sm-3 control-label">Forma de Pagamento</label>

                <div class="col-sm-9">
                    <p class="form-control-static"><?= $model['formaPagamento']['nome'] ?></p>
                </div>
                <label class="col-sm-3 control-label">Forma de Frete</label>

                <div class="col-sm-9">
                    <p class="form-control-static"><?= isset($model['transportadora']['nome']) ? $model['transportadora']['nome'] : 'Não selecionado' ?></p>
                </div>
                <label class="col-sm-3 control-label">Endereço</label>

                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= $model['comprador']['empresa']['enderecosEmpresa'][0] ?>
                    </p>
                </div>

                <label class="col-sm-3 control-label">E-mail</label>

                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= $model['comprador']['email'] ?>
                    </p>
                </div>
                <label class="col-sm-3 control-label">Telefone</label>

                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= Yii::$app->formatter->asTelefone($model['comprador']['empresa']['telefone']) ?>
                    </p>
                </div>
            </div>
        </form>

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
                'quantidade',
                [

                    'format' => 'text',
                    'header' => 'Valor Unitário',
                    'value' => function ($data) {
                        return Yii::$app->formatter->asCurrency($data['valor']);

                    },
                ],
                [

                    'format' => 'text',
                    'header' => 'Valor Total',
                    'value' => function ($data) {
                        return Yii::$app->formatter->asCurrency($data['valor'] * $data['quantidade']);

                    },
                ],


            ],
        ]); ?>
        <?php if ($model['formaPagamento']['id'] == 1): ?>
            <span class="price">
                <p align="right" class="bg-success">
                    Taxa Fixa: <?= Yii::$app->formatter->asCurrency(Moip::TAXMOIP) ?>
                </p>
            </span>
        <?php endif; ?>
        <span class="price">
            <p align="right" class="bg-success">
                Juros: <?= Yii::$app->formatter->asCurrency($model['valor_total'] - $model->valorTotalSemJuros - Moip::TAXMOIP); ?>
            </p>
        </span>
        <span class="price">
            <p align="right" class="bg-success">
                Frete: <?= Yii::$app->formatter->asCurrency($model['valor_frete']) ?>
            </p>
        </span>
        <span class="price lead">
            <p align="right" class="bg-success">
                Total: <?= Yii::$app->formatter->asCurrency($model['valor_total'] + $model['valor_frete']) ?>
            </p>
        </span>
        <a class="btn btn-primary" href="<?= Url::to(['/pedidos']) ?>" role="button">Voltar</a>
    </div>
</div>
