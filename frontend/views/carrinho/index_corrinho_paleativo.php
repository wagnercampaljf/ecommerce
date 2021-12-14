<?php
use yii\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\MaskedInput;

$this->title = 'Meu Carrinho (' . count(Yii::$app->session['carrinho']) . ')';
$this->registerMetaTag(['name' => 'robots', 'content' => 'noindex']);
$this->params['breadcrumbs'][] = [
    'label' => $this->title,
];

$this->registerJsFile(
    Url::to(['frontend/web/js/carrinho.js']),
    ['depends' => [frontend\assets\AppAsset::className()]]
);
?>
<div class="carrinho-index">
    <div class="carrinho-height">
        <?php
        if (empty(Yii::$app->session['carrinho'])) {
            Yii::$app->session->setFlash('info', 'Você ainda não tem nenhum produto no seu carrinho de compras.');
            echo Html::a('<i style="color: #fff" class="fa fa-arrow-left"></i> Continuar Comprando', ['/search'], ['class' => 'btn btn-primary']);
            return;
        }
        $carrinhoKeys = array_keys(Yii::$app->session['carrinho']);
        $valorTotal = 0;

        $dataProvider = new ActiveDataProvider(
            [
                'query' => \common\models\ProdutoFilial::find()->byIds($carrinhoKeys),
                'pagination' => [
                    'pageSize' => 20,
                ],
            ]
        );
        \yii\widgets\Pjax::begin((['id' => 'idpjax']));
        echo '<div class="table-responsive">'.
            GridView::widget(
            [
                'dataProvider' => $dataProvider,
                'columns' => [
                    [
                        'class' => 'yii\grid\SerialColumn',
                    ],
                    [
                        'attribute' => 'Produto',
                        'class' => 'yii\grid\DataColumn',
                        'format' => 'raw',
                        'headerOptions' => ['width' => '25%'],
                        'value' => function ($data) {
                            return Html::a($data->produto->nome, $data->produto->url);
                        },
                    ],
                    [
                        'attribute' => 'filial.nome',
                        'format' => 'text',
                        'header' => 'Vendedor',
                        'headerOptions' => ['width' => '20%'],
                    ],
                    [
                        'header' => 'Valor Unitário',
                        'class' => 'yii\grid\DataColumn',
                        'headerOptions' => ['width' => '10%'],
                        'value' => function ($data) {
                            $juridica = Yii::$app->params['isJuridica']();

                            return $data->getValorProdutoFilials()->ativo()->one()->getLabel($juridica);

                        },
                    ],
                    /*[
                        'header' => 'Variação',
                        'format' => 'raw',
                        'headerOptions' => ['width' => '20%'],
                        'class' => 'yii\grid\DataColumn',
                        'value' => function ($data) {
                            $juridica = Yii::$app->params['isJuridica']();
                            $maxValue = \common\models\ValorProdutoFilial::find()->ativo()->maiorValorProduto(
                                $data->produto->id,
                                $juridica
                            )->one();
                            $minValue = \common\models\ValorProdutoFilial::find()->ativo()->menorValorProduto(
                                $data->produto->id,
                                $juridica
                            )->one();

                            return 'De <b>' . $minValue->getLabel($juridica) . '</b> até <b>' . $maxValue->getLabel(
                                $juridica
                            ) . '</b>';
                        },
                    ],*/
                    [
                        'header' => 'Quantidade',
                        'class' => 'yii\grid\DataColumn',
                        'headerOptions' => ['width' => '15%'],
                        'format' => 'raw',
                        'value' => function ($data) {
                            return '<form class="form-inline">
                                  <div class="form-group ">
                                    <div class="input-group">
                                      <div class="input-group-addon hidden-print"><i style="cursor:pointer;" onclick="return soma(\'#quantidade-field-' . $data->id . '\', -1, 1)" class="fa fa-minus"></i></div>
                                      <input type="text" data-id="' . $data->id . '" class="form-control quantidade-field" id="quantidade-field-' . $data->id . '" value="' . Yii::$app->session['carrinho'][$data->id] . '">
                                      <div class="input-group-addon hidden-print"><i style="cursor:pointer;" onclick="return soma(\'#quantidade-field-' . $data->id . '\', 1, 1)" class="fa fa-plus"></i></div>
                                    </div>
                                  </div>
                                </form>';
                        },
                    ],
                    [
                        'header' => 'Total',
                        'class' => 'yii\grid\DataColumn',
                        'headerOptions' => ['width' => '10%'],
                        'format' => 'raw',
                        'value' => function ($data) use (&$valorTotal) {
                            $juridica = Yii::$app->params['isJuridica']();
                            $valor = $data->getValorProdutoFilials()->ativo()->one()->getValorFinal(
                                    $juridica
                                ) * Yii::$app->session['carrinho'][$data->id];
                            $valorTotal += $valor;

                            return '<span id="total_produto_' . $data->id . '">' . Yii::$app->formatter->asCurrency($valor) . '</span>';

                        },

                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{delete}',
                        'buttons' => [
                            'delete' => function ($url, $model) {
                                return Html::a('<span class="glyphicon glyphicon-trash" onclick="removerProduto(' . $model->id . ')" style="padding-right: 5px; cursor:pointer"></span>',
                                    null, [
                                        'title' => Yii::t('yii', 'Delete'),
                                    ]);
                            }
                        ]
                    ],
                ],
            ]
        )."</div>";
        ?>
        <?php \yii\widgets\Pjax::end(); ?>
        

        
        <!--<div class="container-frete" style="float:left">
            <div class="form-inline" action="salvar-carrinho">
                <div class="form-group">
                    <label class="sr-only" for="exampleInputEmail3">Digite seu Cep:</label>
                    <?= ""/*MaskedInput::widget([
                        'name' => 'seu-cep',
                        'mask' => '99999-999',
                        'value' => Yii::$app->params['getCepComprador'](true),
                        'options' => [
                            'id' => 'seu_cep',
                            'placeholder' => 'Digite seu Cep:',
                            'class' => 'form-control seu-cep',
                        ]
                        ])*/
                    ?>
                </div>
                <button id="calcula-frete" class="btn btn-primary"><i class="fa no-color fa-truck"></i>
                    Calcular Frete
                </button>
            </div>
            <br/>

            <div id="resultado-frete" class="resultado-frete">

            </div>


        </div>-->
        
    	<div class="container hidden-xs">
    		<div class="row">
            	<div class=" row col-md-3 col-lg-2 col-sm-3 col-xs-12">
            		<div class="pull-left">
                        <a href="<?= yii::$app->urlManager->baseUrl . '/search' ?>" class="btn btn-primary"><i style="color: #f2f2f2;" class="fa fa-arrow-left "></i>
                            Continuar Comprando</a>
                    </div>
            	</div>
            	<div class="row col-md-3 col-lg-5 col-sm-3 col-xs-12">
            		
            	</div>
            	<div class=" row col-md-3 col-lg-2 col-sm-3 col-xs-12">
            		<div class="hidden" style="float:right;">
                        <div class="popover-markup">
                            <div class="head hide">
                                Nome do Carrinho:
                                <button type="button" class="btn btn-link cancel close" data-dismiss="modal" aria-label="Close"><span
                                        aria-hidden="true">×</span></button>
                            </div>
            
                            <div class="content hide">
            
                                <input
                                    type="text"
                                    class="form-control"
                                    id="nome-carrinho"
                                    value="<?= \common\models\Carrinho::nomeCarrinho() ?>">
                                <button id="salvar-carrinho" onclick="salvarCarrinho();" class="btn btn-default top5">
                                    Salvar
                                </button>
                            </div>
                        </div>
                        <button data-toggle="modal" style="margin-right: 5px"
                                class="btn btn-primary trigger"><i class="fa no-color fa-save"></i>
                            Salvar Carrinho
                        </button>
           			 </div>
            	</div>
            	<div class="row col-md-3 col-lg-3 col-sm-3 col-xs-12">
            		<div class="hidden-lg hidden-md hidden-sm"><br></div>
            		<div style="float:right;">
            			<a type="submit" href="<?= Url::to("carrinho/update-address-confirmar?from=checkout") ?>" class="btn-lg btn-danger hidden-print">
                    		<i class="fa no-color fa-check-circle gro"></i> Finalizar Compra
                    	</a>
                    </div>
            	</div> 
        	</div>
        </div>
        
        <div class="container hidden-lg hidden-md hidden-sm">
    		<div class="row">
            	<div class=" row col-xs-12">
            		<div class="pull-center">
                        <a href="<?= yii::$app->urlManager->baseUrl . '/search' ?>" class="btn btn-primary"><i style="color: #f2f2f2;" class="fa fa-arrow-left "></i>
                            Continuar Comprando</a>
                    </div>
            	</div>
            	<div class="row col-xs-12">
            		<div class="hidden-lg hidden-md hidden-sm"><br></div>
            		<div style="float:center;">
            			<a type="submit" href="<?= Url::to("carrinho/update-address-confirmar?from=checkout") ?>" class="btn-lg btn-danger hidden-print">
                    		<i class="fa no-color fa-check-circle gro"></i> Finalizar Compra
                    	</a>
                    </div>
            	</div> 
        	</div>
        </div>
	</div>
</div>



                      
                                    
