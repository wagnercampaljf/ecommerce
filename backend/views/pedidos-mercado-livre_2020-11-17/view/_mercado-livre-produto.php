<?php
/**
 * Created by PhpStorm.
 * User: tercio
 * Date: 13/09/17
 * Time: 18:32
 */

use yii\helpers\Html;
use yii\helpers\Url;
use common\models\PedidoMercadoLivreProduto;
use common\models\PedidoMercadoLivre;
use common\models\ProdutoFilial;
use common\models\Produto;
use yii\widgets\ActiveForm;
use yii\web\JsExpression;
use kartik\select2\Select2;
use yii\grid\GridView;

//echo "<pre>"; print_r($pedido); echo "</pre>"; die;

$this->params['active'] = 'pedidos';

$this->title = 'Pedido #' . $model['title'];

$produto_filial_pedido = ProdutoFilial::find()->andWhere(['=','id',$model->produto_filial_id])->one();
//echo "<pre>"; print_r($produto_filial_pedido); echo "</pre>"; die;

?>

<?php $form = ActiveForm::begin(['action' => Url::to(['/pedidos-mercado-livre/mercado-livre-produto-update','id'=>$model['id']])]); ?>

<div class="container">

    <article class="card card_main">
        <div class="card__body">
                <div class="card__content">
                    <div class="card__description">
                        <div class="pedido-mercado-livre-produto-form">
                        
                        	<div class="row">
                                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                    				<?= $form->field($model, 'title')->textInput(['maxlength' => true,])->label("Produto"); ?>
                    			</div>
                    			<div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    				<?= ($produto_filial_pedido) ? $form->field($produto_filial_pedido->produto,'codigo_global')->textInput(['maxlength' => true,])->label("Código Global") : "" ?>
                    			</div>
                    			<div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                    				<?= ($produto_filial_pedido) ? $form->field($produto_filial_pedido->produto,'codigo_fabricante')->textInput(['maxlength' => true,])->label("Código Fabricante") : "" ?>
                    			</div>
                            </div>
                        	<div class="row">
                                <div class="col-lg-3 col-md-12 col-sm-12 col-xs-12">
                    				<?= $form->field($model, 'produto_meli_id')->textInput(['maxlength' => true]) ?>
                    			</div>
                    			<div class="col-lg-3 col-md-12 col-sm-12 col-xs-12">
                    				<?= $form->field($model, 'categoria_meli_id')->textInput(['maxlength' => true, 'label' => 'Categoria']) ?>
                    			</div>
                                <!--<div class="col-lg-3 col-md-12 col-sm-12 col-xs-12">
                    				<?= $form->field($model, 'condition')->textInput(['maxlength' => true])->label("Condição"); ?>
                    			</div> -->
                                <!--<div class="col-lg-3 col-md-12 col-sm-12 col-xs-12">
                    				<?= $form->field($model, 'listing_type_id')->textInput(['maxlength' => true])->label("Tipos de publicação"); ?>
                    			</div>-->
                            </div>
                        	<div class="row">
								<div class="col-lg-3 col-md-12 col-sm-12 col-xs-12">
                    				<?= $form->field($model, 'quantity')->textInput()->label("Quantidade"); ?>
                    			</div>
                                <div class="col-lg-3 col-md-12 col-sm-12 col-xs-12">
                    				<?= $form->field($model, 'unit_price')->textInput()->label("Preço unitário"); ?>
                    			</div>
                    			<div class="col-lg-3 col-md-12 col-sm-12 col-xs-12">
                    				<?= $form->field($model, 'full_unit_price')->textInput()->label("Preço unitário completo"); ?>
                    			</div>
                    			<div class="col-lg-3 col-md-12 col-sm-12 col-xs-12">
                    				<?= $form->field($model, 'sale_fee')->textInput()->label("Taxa de venda"); ?>
                    			</div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
    </article>
    
    <!-- <article class="card card_main">
        <div class="card__body">
            <div class="card__content">
                <div class="card__description">
                    <div class="pedido-mercado-livre-produto-form">
                    	<div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                				<?php 
                                    /*$produto_filial = $model->produto_filial_selecionado_id ?   ProdutoFilial::findOne($model->produto_filial_selecionado_id)->filial->nome ." - ".
                                                                                                ProdutoFilial::findOne($model->produto_filial_selecionado_id)->produto->nome . "(".
                                                                                                ProdutoFilial::findOne($model->produto_filial_selecionado_id)->produto->codigo_global.")" : null;
                                        //echo "<pre>"; print_r($produto_filial); echo "</pre>"; die;
                                        echo  $form->field($model, 'produto_filial_selecionado_id')->widget(Select2::className(), [
                                            'initValueText' => $produto_filial,
                                            'pluginOptions' => [
                                                'allowClear' => true,
                                                'minimumInputLength' => 3,
                                                'ajax' => [
                                                    'url' => Url::to(['valor-produto-filial/get-produto-filial']),
                                                    'dataType' => 'json',
                                                    'data' => new JsExpression('function(params) { return {q:params.term}; }')
                                                ],
                                            ],
                                            'options' => ['placeholder' => 'Selecione um Produto']
                                        ])->label("Produto:");*/
                                ?>
                			</div>
                			<div class="col-lg-3 col-md-12 col-sm-12 col-xs-12">
                				<?= ""//$form->field($model, 'valor_cotacao')->textInput(['maxlength' => true])->label("Valor da cotação:"); ?>
                			</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </article>-->
    
    <article class="card card_main">
        <div class="card__body">
            <div class="card__content">
                <div class="card__description">
                    <div class="pedido-mercado-livre-produto-form">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                				<p>
									<?= Html::a('Cadastrar produto no pedido', ['mercado-livre-produto-produto-filial-create', 'pedido_mercado_livre_produto_id'=>$model->id], ['class' => 'btn btn-success']) ?>
								</p>
                			</div>
                        </div>
                    	<div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                				 <?= GridView::widget([
                				     'dataProvider' => $dataProviderProdutoFilial,
                				     //'filterModel' => $searchModelProdutoFilial,
                                        'columns' => [
                                            ['class' => 'yii\grid\SerialColumn'],
                                            //'id',
                                            //'pedido_mercado_livre_produto_id',
                                            //'produto_filial_id',
                                            [
                                                'label'     => 'Filial',
                                                'attribute' => 'filial_nome',
                                                'value'     => 'produtoFilial.filial.nome' ,
                                            ],
                                            [
                                                'label'     => 'Produto',
                                                'attribute' => 'produto_nome',
                                                'value'     => 'produtoFilial.produto.nome'
                                            ],
                                            'quantidade',
                                            'valor',
                                            ['class' => 'yii\grid\ActionColumn',
                                                'template'=>'{update}{delete}',
                                                'buttons'=>[
                                                    'update' => function ($url, $model) {
                                                        $url = "mercado-livre-produto-produto-filial-update?pedido_mercado_livre_produto_id=".$model->pedido_mercado_livre_produto_id."&pedido_mercado_livre_produto_produto_filial_id=".$model->id;
                                                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>  ', $url, ['title' => Yii::t('yii', 'Alterar'), ]);
                                                    },
                                                    'delete' => function ($url, $model) {
                                                        $url = "mercado-livre-produto-produto-filial-delete?pedido_mercado_livre_produto_id=".$model->pedido_mercado_livre_produto_id."&pedido_mercado_livre_produto_produto_filial_id=".$model->id;
                                                        return Html::a('  <span class="glyphicon glyphicon-trash"></span>', $url, ['title' => Yii::t('yii', 'Excluir'), 'data-confirm' => 'Confirma a exclusão deste item?']);
                                                    }
                                                ] 
                                            ],
                                    ],
                                ]); ?>
                			</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </article>


   <div class="portlet light" id="pedido">
       <div class="portlet-body">
    
    
            <?php
            
            //$pedido_mercado_livre_produtos = PedidoMercadoLivreProduto::find()->andWhere(['=','produto_mercado_livre_id',$model->id])->all();
            
            //$dataProvider = new \yii\data\ArrayDataProvider(['models' => $model['items'], 'pagination' => false]);
            //$dataProvider = new \yii\data\ArrayDataProvider(['models' => $pedido_mercado_livre_produtos, 'pagination' => false]);
            ?>
            
            <a class="btn btn-primary" href="<?= Url::to(['/pedidos-mercado-livre/mercado-livre-view',"id"=>$model['pedido_mercado_livre_id']]) ?>" role="button">Voltar</a>
            
           	<?= Html::submitButton('Salvar', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>


<style>
    /*
     * Core for cards
     */

    .cards{
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        font-family: "Roboto", sans-serif;
    }

    .card{
        box-shadow: 0 2px 2px 0 rgba(0,0,0,0.14),0 1px 5px 0 rgba(0,0,0,0.12),0 3px 1px -2px rgba(0,0,0,0.2);
        margin-bottom: 2rem;

        display: flex;
        flex-direction: column;
    }

    .card_main{
        width: 100%;
    }

    @media screen and (min-width: 801px){

        .card_main{

        .card__title{
            font-size: 180%;
        }

        .card__main-action{
            width: @card_main_action_size * 1.12;
            height: @card_main_action_size * 1.12;
        }
    }
    }

    .card_size-2xl{
        width: 66%;
    }

    @media screen and (min-width: 801px){

        .card_size-2xl{

        .card__title{
            font-size: 170%;
        }
    }
    }

    .card_size-xl{
        width: 49%;
    }

    @media screen and (min-width: 801px){

        .card_size-xl{

        .card__title{
            font-size: 160%;
        }
    }
    }

    .card_size-m{
        width: 32%;
    }

    @media screen and (min-width: 481px) and (max-width: 800px){

        .card_size-m, .card_size-2xl{
            width: 49%;
        }
    }

    @media screen and (max-width: 480px){

        .card_size-m, .card_size-xl, .card_size-2xl{
            width: 100%;
        }
    }

    .card__header{
        position: relative;
        line-height: 0;
    }

    *::-ms-backdrop,.card__header{
        display: flex;
    }

    .card__preview{
        max-width: 100%;
        height: auto;
    }

    *::-ms-backdrop,.card__preview{
        flex: 0 0 auto;
    }

    @card_main_action_size: 4em;

    .card__main-action{

        font-size: 100%;
        text-decoration: none;
        text-indent: -9999px;
        cursor: pointer;

        border: none;
        border-radius: 50%;
        padding: 0;
        box-shadow: 0 2px 2px 0 rgba(0,0,0,0.14),0 1px 5px 0 rgba(0,0,0,0.12),0 3px 1px -2px rgba(0,0,0,0.2);

        position: absolute;
        right: 5%;
        bottom: 0;
        transform: translateY(50%);

        width: @card_main_action_size;
        height: @card_main_action_size;

    &:before{

         content: "";
         display: block;

         width: 60%;
         height: 60%;

         box-sizing: border-box;

         background-position: 50% 50%;
         background-repeat: no-repeat;
         background-size: contain;

         position: absolute;
         top: 50%;
         left: 50%;
         transform: translate(-50%, -50%)
     }

    &:focus{
         outline: none;
     }
    }

    .card__body {
        display: flex;
        flex-direction: column;
        justify-content: space-around;
        flex-grow: 2;
    }

    .card__title{

        font-size: 140%;
        font-weight: 400;
        line-height: 1.5;

        margin-top: 0;
        margin-bottom: .8em;
    }

    .card__showmore{
        text-decoration: none;
    }

    .card__content{
        padding: 2.5em 4% 1.5em;
        flex-grow: 2;
    }

    .card__footer{

        padding: 1.5em 4%;
        border-top-width: 1px;
        border-top-style: solid;
        font-size: 90%;

        display: flex;
        justify-content: space-between;
    }

    .card__meta-item{
        display: inline-block;
        vertical-align: middle;
        margin-left: .8em;
    }

    .card__meta-icon{

        display: inline-block;
        vertical-align: middle;
        text-align: right;

        width: 1.5em;
        height: 1.5em;
        margin-right: .2em;

        background-position: 50% 50%;
        background-repeat: no-repeat;
        background-size: contain;
    }

    /*
     * Skin for cards
     */

    @main_color: #3F51B5;
    @light_color: #C5CAE9;
    @dark_color: #303F9F;
    @optional_color: #BDBDBD;
    @optional_color2: #448AFF;
    @color_text: #212121;

    .card{
        background-color: #fff;
        color: @color_text;
        font-size: 1.4rem;
    }

    @media screen and (min-width: 801px){

        .card_main, .card_size-2xl, .card_size-xl{
            font-size: 1.6rem;
        }
    }

    .card__main-action{
        background-color: @main_color;

    &:before{
         background-image: url("https://stas-melnikov.ru/cssgrid/bookmark.svg");
     }

    &:hover, &:focus{
                  background-color: @dark_color;
              }
    }

    .card__footer{
        border-top-color: @optional_color;
    }

    .card__showmore{

        color: @dark_color;
        transition: color .3s ease-out;

    &:hover, &:focus{
                  color: @main_color;
              }
    }

    .card__meta-comments{
        background-image: url("https://stas-melnikov.ru/cssgrid/comment.svg");
    }

    .card__meta-likes{
        background-image: url("https://stas-melnikov.ru/cssgrid/favorite.svg");
    }</style>