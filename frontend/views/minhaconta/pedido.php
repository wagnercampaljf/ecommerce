<?php
/* @var $this yii\web\View */
/* @var $dataProvider \yii\data\ActiveDataProvider */
/* @var $pedido \common\models\Pedido */

use common\models\FormaPagamento;
use vendor\iomageste\Moip\Moip;
use yii\grid\GridView;
use yii\helpers\Html;

$comprador = Yii::$app->user->getIdentity();
$this->title = Yii::t('app', 'Pedido #{id}', ['id' => $pedido->id]);
$this->params['active'] = 'pedidos';
$this->params['breadcrumbs'][] = ['label' => 'Minha Conta', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Pedidos', 'url' => ['pedidos']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tab-pane active col-md-9 col-sm-12" id="pedido">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <?= Html::encode($this->title) ?>
        </div>
        <div class="panel-body">
            <div class="form-horizontal " style="margin-bottom: 5%">
                <div class="form-group">
                    <div class="col-sm-12 row">
                        <label class="col-sm-3 control-label">ID Pedido</label>

                        <div class="col-sm-9">
                            <p class="form-control-static"><?= '#' . $pedido['id'] ?></p>
                        </div>
                    </div>
                    <div class="col-sm-12 row">
                        <label class="col-sm-3 control-label">Número do pedido</label>

                        <div class="col-sm-9">
                            <p class="form-control-static"><?= isset($pedido['token_moip']) ? $pedido['token_moip'] : '---' ?></p>
                        </div>
                    </div>
                    <div class="col-sm-12 row">
                        <label class="col-sm-3 control-label">Data</label>

                        <div class="col-sm-9">
                            <p class="form-control-static"><?= isset($pedido['dt_referencia']) ? Yii::$app->formatter->asDate($pedido['dt_referencia']) : '---' ?></p>
                        </div>
                    </div>
                    <div class="col-sm-12 row">
                        <label class="col-sm-3 control-label">Status</label>

                        <div class="col-sm-9">
                            <p class="form-control-static"><?= isset($pedido['statusAtual']['tipoStatus']['nome']) ? $pedido['statusAtual']['tipoStatus']['nome'] : '---' ?></p>
                        </div>
                    </div>
                    <div class="col-sm-12 row">
                        <label class="col-sm-3 control-label">Forma de Pagamento</label>

                        <div class="col-sm-9">
                            <p class="form-control-static"><?= isset($pedido['formaPagamento']['nome']) ? $pedido['formaPagamento']['nome'] : '---' ?></p>
                        </div>
                    </div>
                    <div class="col-sm-12 row">
                        <label class="col-sm-3 control-label">Forma de Frete</label>

                        <div class="col-sm-9">
                            <p class="form-control-static"><?= isset($pedido['transportadora']['nome']) ? $pedido['transportadora']['nome'] : 'Não selecionado' ?></p>
                        </div>
                    </div>
                    <?php if ($pedido['transportadora_id'] == 4): ?>
                        <div class="col-sm-12 row">
                            <label class="col-sm-3 control-label">Endereço para retirada</label>

                            <div class="col-sm-9">
                                <p class="form-control-static">
                                    <?= isset($pedido['filial']['enderecoFilial']) ? $pedido['filial']['enderecoFilial'] : '---' ?>
                                </p>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="col-sm-12 row">
                        <label class="col-sm-3 control-label">Endereço</label>

                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= isset($pedido['comprador']['empresa']['enderecoEmpresa']) ? $pedido['comprador']['empresa']['enderecoEmpresa'] : '---' ?>
                            </p>
                        </div>
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
            ]);
            if ($pedido['forma_pagamento_id'] == FormaPagamento::BOLETO && !is_null($pedido['token_payment'])):
                $moip = new Moip(); ?>
                <span class="price">
                    <p align="right" class="bg-success">
                        <?= Html::a(
                            '<i class="fa fa-barcode"></i> Boleto',
                            $moip->payments()->get($pedido['token_payment'])->getLinks()->checkout->payBoleto->redirectHref."/print",
                            ['class' => 'btn btn-lg btn-info', 'target' => '_blank']
                        ) ?>
                    </p>
                </span>
            <?php endif;
            if ($pedido['token_moip']): ?>
                <span class="price">
                    <p align="right" class="bg-success">
                        Taxa Fixa: <?= Yii::$app->formatter->asCurrency(Moip::TAXMOIP) ?>
                    </p>
                </span>
            <?php endif; ?>
            <span class="price">
                <p align="right" class="bg-success">
                    Juros: <?php echo Yii::$app->formatter->asCurrency($pedido['valor_total'] - $pedido->valorTotalSemJuros - Moip::TAXMOIP); ?>
                </p>
            </span>
            <span class="price">
                <p align="right" class="bg-success">
                    Frete: <?= Yii::$app->formatter->asCurrency($pedido['valor_frete']) ?>
                </p>
            </span>
            <span class="price lead">
                <p align="right" class="bg-success">
                    Total: <?= Yii::$app->formatter->asCurrency($pedido['valor_total'] + $pedido['valor_frete']) ?>
                </p>
            </span>
            <!--<a class="pull-right" href="https://www.pecaagora.com/portal" target="_blank">
                <span class="btn btn-primary">
                    <h4>Precisa de uma Oficina?</h4>Encontre Aqui!
                </span>
            </a>-->
            <?= Html::a('Voltar', ['pedidos'], ['class' => 'btn btn-primary']) ?>
        </div>
    </div>
</div>