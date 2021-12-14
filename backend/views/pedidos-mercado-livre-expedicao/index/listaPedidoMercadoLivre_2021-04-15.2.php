<?php

use common\models\PedidoMercadoLivreProduto;
use common\models\PedidoMercadoLivreProdutoProdutoFilial;
use common\models\ProdutoFilial;
use yii\helpers\Url;
use backend\models\PedidoMercadoLivreProdutoSearch;
use common\models\PedidoMercadoLivre;
use yii\widgets\ListView;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\rbac\PhpManager;
use common\models\PedidoMercadoLivreShipments;

$pedido_mercado_livre_shipments = PedidoMercadoLivreShipments::find()->andWhere(['=', 'pedido_mercado_livre_id', $model->id])->one();

?>


<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <article class="card card_main" style="background-color: #ffffff; border-radius: 10px">
                <div class="card__body">
                    <div class="card__content">
                        <p style="color: #b10c10; font-weight: bold;"></p>
                        <div class="row">
                        	<div class="col-sm-9">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="row">
        
                                            <?php
        
                                            //echo '<h2><b>'."Nº". $model->pedido_meli_id.'</b></h2>';
					    echo '<h2><b>'."Nº". $model->pedido_meli_id.'</b></h2><h3><b><span id="substatus_'.$model->id.'" style="color:#008000;">';
                                            
                                            if($pedido_mercado_livre_shipments){
                                                $substatus = "(";
                                                $substatus .= (($model->e_etiqueta_impressa==true) ? 'Etiqueta Impressa, '  : '');
                                                $substatus .= ((!is_null($pedido_mercado_livre_shipments->history_date_shipped)) ? "Produto enviado, " : "" );
                                                $substatus .= ((!is_null($pedido_mercado_livre_shipments->history_date_delivered)) ? "Produto entregue, " : "" );
                                                $substatus .= ")";
            
                                                if($substatus != "()"){
                                                    
                                                    $substatus = str_replace(", )", ")", $substatus);
                                                    
                                                    //echo '<h3><b><span style="color:#008000;">'.$substatus.'</span></b><h3>';
						    echo $substatus;
                                                }
                                            }
                                            
					    echo "</span></b><h3>";

                                            ?>
        
                                        </div>
                                        <div class="row">
                                            <?php
        
                                            if ($model->shipping_status == 'delivered')
                                                echo '<p style="color: #000000; font-weight: bold;">' ."Entregue".'</p>' ;
                                            elseif ($model->shipping_status == 'shipped')
                                                echo '<p style="color: #000000; font-weight: bold;">' ."A caminho".'</p>' ;
                                            elseif ($model->shipping_status == 'cancelled')
                                                echo '<p style="color: #ff0000; font-weight: bold;">' ."Cancelado".'</p>'
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                	<div class="col-sm-1">
                                        <div><img src="https://img.icons8.com/color/64/000000/name.png"/></div>
                                    </div>
                                    <div class="col-sm-11">
                                        <di>
                                            <p style="font-weight: bold; color: rgba(41,41,41,0.95); white-space: nowrap;overflow: hidden; text-overflow: ellipsis"><span><?= $model['buyer_first_name'] ?> <?= $model['buyer_last_name'] ?></span> </p><p style=" white-space: nowrap;overflow: hidden; text-overflow: ellipsis"><?= $model['buyer_nickname'] ?></p>
        
                                        </di>
                                    </div>
                                	
                                </div>
                                <div class="row">
                                	<div class="col-sm-1"></div>
                                    <div class="col-sm-11">
                                        <?php
                                        
                                            $pedidos_mercado_livre_produto = PedidoMercadoLivreProduto::find()->andWhere(['=', 'pedido_mercado_livre_id', $model->id])->all();
                                            
                                            foreach($pedidos_mercado_livre_produto as $k => $pedido_mercado_livre_produto){
                                                $pedidos_mercado_livre_produto_produto_filiais = PedidoMercadoLivreProdutoProdutoFilial::find()->andWhere(['=', 'pedido_mercado_livre_produto_id', $pedido_mercado_livre_produto->id])->all();
                                                
                                                foreach($pedidos_mercado_livre_produto_produto_filiais as $k => $pedido_mercado_livre_produto_produto_filial){
                                                    $produto_filial = ProdutoFilial::find()->andWhere(['=', 'id', $pedido_mercado_livre_produto_produto_filial->produto_filial_id])->one();
                                                    
                                                    if($produto_filial){
                                                        echo '<div class="row"><div class="col-sm-1" style="color: rgba(145,145,145,0.56)"></div><div class="col-sm-1">';
                                                        
                                                        if($produto_filial){
                                                            echo "<img src='".$produto_filial->produto->getUrlImageBackend()."' height='60' width = '60'>";
                                                        }
                                                        
                                                        echo "</div>";
                                                        
                                                        echo '<div class="col-sm-3"><h6>'.$produto_filial->produto->nome.'</h6></div>';
                                                        echo '<div class="col-sm-1"><h6> '.$pedido_mercado_livre_produto_produto_filial->quantidade .'u.</h6></div>';
                                                        echo '<div class="col-sm-2"><h6>PA'.$produto_filial->produto->id.'</h6></div>';
                                                        echo '<div class="col-sm-2"><h6>'.$produto_filial->produto->codigo_fabricante.'</h6></div>';
                                                        echo '<div class="col-sm-2"><h6>'.$produto_filial->produto->codigo_global.'</h6></div>';
                                                        echo '</div>';
                                                        echo '<div class="row"><b><div class="col-sm-2">Email Fornecedor: </div><div class="col-sm-10">'.$pedido_mercado_livre_produto_produto_filial->email.'</div></b></div>';
                                                        
                                                        
                                                    }
                                                }
                                            }
        
                                        ?>

                                    </div>
                                </div>
                                <div class="row">
                                	<div class="col-sm-12">
                                     
                                        <br><br>
                                        <button onClick="window.open('<?= Url::to(['/pedidos-mercado-livre-expedicao/gerar-etiqueta', 'id' => $model['id']])?>'); document.getElementById('substatus_<?=$model->id?>').innerHTML = '(Etiqueta Impressa)';" type="button" class="btn btn-primary">Imprimir etiqueta</button>
                                        <button onClick="window.open('<?= Url::to(['/pedidos-mercado-livre-expedicao/obter-nota-fiscal', 'id' => $model['id']])?>')" type="button" class="btn btn-primary">Imprimir Nota Fiscal</button>
                                    </div><br>
                                </div>
                            </div>
                            <div class="col-sm-3">
                            	<?php
                                
                                ////////////////////////////////
                                //Teste
                                ////////////////////////////////
                                
                                if($pedido_mercado_livre_shipments){

                                    //var_dump(gettype($pedido_mercado_livre_shipments->history_date_delivered));
                                    //$dataentrega  = date_create($pedido_mercado_livre_shipments->history_date_ready_to_ship);
                                    //var_dump(gettype($dataentrega)); 
                                    //var_dump($dataentrega); 
                                    //$dataentrega = date_format(date_create($pedido_mercado_livre_shipments->history_date_ready_to_ship), 'H:i:s d/m/Y'); 
                                    //var_dump(date_format(date_create($pedido_mercado_livre_shipments->history_date_ready_to_ship), 'H:i:s d/m/Y')); die;
                                    //var_dump($pedido_mercado_livre_shipments->history_date_delivered); die;
                                    
                                    echo '<div class="col-sm-12" style="background-color: rgba(74,78,90,0.2); border-radius: 10px">';
                                    echo '<table class="table">';
                                    
                                    echo '<tr><td></td></tr>';
                                    echo '<tr>';
                                    echo '<td scope="row" style="font-size: 12px; border:none !important;">'. "Data entrega".'</td>';
                                    echo '<td scope="row" style="font-size: 12px; border:none !important;">'. ((!is_null($pedido_mercado_livre_shipments->history_date_delivered)) ? date_format(date_create($pedido_mercado_livre_shipments->history_date_delivered) , 'H:i:s d/m/Y') : "" ) .'</td>';
                                    echo '</tr>';
                                    echo '<tr>';
                                    echo '<td scope="row" style="font-size: 12px; border:none !important;">'. "Data não entrega".'</td>';
                                    echo '<td scope="row" style="font-size: 12px; border:none !important;">'. ((!is_null($pedido_mercado_livre_shipments->history_date_not_delivered)) ? date_format(date_create($pedido_mercado_livre_shipments->history_date_not_delivered), 'H:i:s d/m/Y') : "" ) .'</td>';
                                    echo '</tr>';
                                    echo '<tr>';
                                    echo '<td scope="row" style="font-size: 12px; border:none !important;">'. "Data pronto para envio".'</td>';
                                    echo '<td scope="row" style="font-size: 12px; border:none !important;">'. ((!is_null($pedido_mercado_livre_shipments->history_date_ready_to_ship)) ? date_format(date_create($pedido_mercado_livre_shipments->history_date_ready_to_ship), 'H:i:s d/m/Y') : "" ) .'</td>';
                                    echo '</tr>';
                                    echo '<tr>';
                                    echo '<td scope="row" style="font-size: 12px; border:none !important;">'. "Data envio".'</td>';
                                    echo '<td scope="row" style="font-size: 12px; border:none !important;">'. ((!is_null($pedido_mercado_livre_shipments->history_date_shipped)) ? date_format(date_create($pedido_mercado_livre_shipments->history_date_shipped), 'H:i:s d/m/Y') : "" ).'</td>';
                                    echo '</tr>';
                                    echo '<tr>';
                                    echo '<td scope="row" style="font-size: 12px; border:none !important;">'. "Data retorno".'</td>';
                                    echo '<td scope="row" style="font-size: 12px; border:none !important;">'. ((!is_null($pedido_mercado_livre_shipments->history_date_returned)) ? date_format(date_create($pedido_mercado_livre_shipments->history_date_returned), 'H:i:s d/m/Y') : "" ) .'</td>';
                                    echo '</tr>';

                                    echo '</table>';
                                    echo '</div>';
                                    
                                }

                                ////////////////////////////////
                                //Teste
                                ////////////////////////////////
                                
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <?php

                        $this->registerJs(
                            '$("document").ready(function(){ 
		                $("#new_country").on("pjax:end", function() {
			            $.pjax.reload({container:"#countries"});  //Reload GridView
		                });
                            });'
                        );
                        ?>


                            <?php yii\widgets\Pjax::begin(['id' => 'new_country']) ?>
                            <?php $form = ActiveForm::begin(['options' => ['data-pjax' => true ], 'action' => Url::to(['/pedidos-mercado-livre-expedicao/update-observacao','id'=>$model['id']])] ); ?>
                                <?= $form->field($model, 'observacao')->textarea(['maxlength' => 10000,  "onfocusout" => "document.getElementById('"."botao_".$model->id."').click();"]) ?>

                            </div>
                            <div class="form-group">
                                <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Alterar') : Yii::t('app', 'Salvar'), ['class' => $model->isNewRecord ? 'btn btn-success hidden' : 'btn btn-primary hidden', "id" => "botao_".$model->id]) ?>
                            </div>

                            <?php ActiveForm::end(); ?>
                            <?php yii\widgets\Pjax::end() ?>
                        </div>



                        </div>

                    </div>
                </div>
            </article>
        </div>
    </div>
</div>






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
    }
</style>
