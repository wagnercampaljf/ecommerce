<?php
/* @var $this yii\web\View */
/* @var $model \lojista\models\Pedido */
/* @var $pedidoStatus \common\models\PedidoStatus */
/* @var $dataProvider \yii\data\ActiveDataProvider */

use vendor\iomageste\Moip\Moip;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;


$comprador = Yii::$app->user->getIdentity();
$this->params['active'] = 'pedidos';
$this->registerJsFile(Url::base() . '/js/pedidos.js', ['depends' => \lojista\assets\AppAsset::className()]);
$this->registerCssFile("https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css");
$this->registerJsFile("https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js");

?>
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 container-fluid">
    <div class="tab-pane active col-md-9 col-sm-12" id="pedido">
        <form class="form-horizontal" style="margin-bottom: 5%">
            <?php
            if (Yii::$app->session->hasFlash('error')) {
                echo Html::tag('div', Yii::$app->session->getFlash('error'), ['class' => 'alert alert-error']);
            }
            ?>
            <h1>Pedido</h1>

            <label class="col-sm-3 control-label">Número do Pedido</label>
            <div class="col-sm-9">
                <p class="form-control-static">
                    <?= '#' . $model['id'] ?>
                </p>
            </div>

            <label class="col-sm-3 control-label">ID Moip</label>
            <div class="col-sm-9">
                <p class="form-control-static">
                    <?= isset($model['transportadora']['nome']) ? $model['token_moip'] : '---' ?>
                </p>
            </div>

            <label class="col-sm-3 control-label">Data</label>
            <div class="col-sm-9">
                <p class="form-control-static">
                    <?= Yii::$app->formatter->asDate($model['dt_referencia']) ?>
                </p>
            </div>

            <label class="col-sm-3 control-label">Status</label>
            <div class="col-sm-9">
                <p class="form-control-static">
                    <?= $model['statusAtual']['tipoStatus']['nome'] ?>
                </p>
            </div>

            <label class="col-sm-3 control-label">Forma de Pagamento</label>
            <div class="col-sm-9">
                <p class="form-control-static">
                    <?= $model['formaPagamento']['nome'] ?>
                </p>
            </div>

            <label class="col-sm-3 control-label">Forma de Frete</label>
            <div class="col-sm-9">
                <p class="form-control-static">
                    <?= isset($model['transportadora']['nome']) ? $model['transportadora']['nome'] : 'Não selecionado' ?>
                </p>
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
        </form>
    </div>
    <div class="clearfix"></div>

    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 row">
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
    </div>
    <div class="clearfix"></div>

    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 row">
        <?php if ($model['formaPagamento']['id'] == 1) { ?>
            <p align="right" class="bg-success">
            <span class="price">
                Taxa Fixa: <?= Yii::$app->formatter->asCurrency(Moip::TAXMOIP) ?>
            </span>
            </p>
        <?php } ?>

        <p align="right" class="bg-success">
        <span class="price">
            Juros: <?php echo Yii::$app->formatter->asCurrency($model['valor_total'] - $model->valorTotalSemJuros - Moip::TAXMOIP); ?>
        </span>
        </p>
        <p align="right" class="bg-success">
        <span class="price">
            Frete: <?= Yii::$app->formatter->asCurrency($model['valor_frete']); ?>
        </span>
        </p>
        <p align="right" class="bg-success">
        <span class="price lead">
            Total: <?= Yii::$app->formatter->asCurrency($model['valor_total'] + $model['valor_frete']); ?>
        </span>
        </p>
    </div>
    <div class="clearfix"></div>

    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 row">
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-left row">
            <?= Html::a(Yii::t('app', 'Voltar'), ['/pedidos'], ['class' => 'btn btn-primary', 'role' => 'button']); ?>
        </div>
        <div class="col-lg-offset-6 col-md-offset-6 col-sm-offset-6 col-xs-offset-6 text-right">
            <?php
            if ($model->statusAtual->tipoStatus->id == 2) {
                if (empty($model->etiqueta)) {
                    echo Html::a(Yii::t('app', 'Solicitar Etiqueta'),
                        ['solicitar-etiqueta', 'pedido_id' => $model['id']],
                        ['class' => 'btn btn-success text-right']);
                }
            }
            if (!empty($model->etiqueta) && ($model->statusAtual->tipoStatus->id >= 2 || $model->statusAtual->tipoStatus->id == 4)) {
                if (empty($model->plp_id)) {
                    echo Html::a(Yii::t('app', 'Imprimir Etiqueta'),
                        ['imprimir-etiqueta', 'pedido_id' => $model['id']],
                        ['class' => 'btn btn-success margin-right-10', 'target' => '_blank']);
                    echo Html::a(Yii::t('app', 'Fechar Etiqueta'), ['fechar-etiqueta', 'pedido_id' => $model['id']],
                        ['class' => 'btn btn-success', 'target' => '_blank']);
                } else {
                    echo Html::a(Yii::t('app', 'Rastrear Objeto {etiqueta}', ['etiqueta' => $model->etiqueta]),
                        '#eventos', ['class' => 'btn btn-success rastro']);
                }
            }

            ?>
        </div>
        <div class="clearfix"></div>
        <hr/>

        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 hide" id="eventos">
            <?php
            $rastro = $model->rastrearObjeto();
            if ($rastro) {
                $eventos = $rastro->getEventos();
                $etiqueta = $rastro->getEtiqueta();
                foreach ($eventos as $evento) {
                    $data = Html::tag('strong', $evento->getDataHora()->format('d/m/Y H:i'));
                    $conteudo = [
                        Html::tag('strong', $evento->getDataHora()->format('d/m/Y H:i')),
                        $evento->getDescricao(),
                        $evento->getCidade() . ' - ' . $evento->getUf(),
                        Yii::t('app', 'Agência: {agencia}', ['agencia' => $evento->getLocal()]),
                    ];

                    echo Html::tag('address', implode('<br>', $conteudo));
                }
            } else {
                echo Html::tag('p', 'Sem eventos', ['class' => 'lead']);
            }
            ?>
        </div>

    </div>
    <div class="clearfix"></div>

    <div class="container-fluid text-center">
        <div class="bs-wizard" style="border-bottom:0;">
            <?php
            $state = '';
            foreach (\common\models\Pedido::$statusClasses as $key => $status) {
                if ($status::isCompleted($model->statusAtual->tipoStatus->id)) {
                    $state = 'complete';
                } else {
                    if ($status::isNext($model->statusAtual->tipoStatus->id)) {
                        $state = 'active';
                    } else {
                        $state = 'disabled';
                    }
                }
                ?>

                <div class="col-xs-2 bs-wizard-step <?= $state ?>">
                    <div class="text-center bs-wizard-stepnum"><?= $status::getLabel() ?></div>
                    <div class="progress">
                        <div class="progress-bar"></div>
                    </div>

                    <a href='<?= Url::to(['mudar-status', 'id' => $model->id, 'status' => $key]) ?>'
                       class="bs-wizard-dot mudarstatus"></a>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
