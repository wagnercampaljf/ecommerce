<?php

use yii\helpers\Url;
use backend\models\PedidoMercadoLivreProdutoSearch;
use yii\widgets\ListView;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use console\controllers\actions\omie\Omie;

//echo "(".$model->ordenacao.")";




// Estoque
$omie = new Omie(1, 1);

$body = [
    "call" => "MovimentoEstoque",
    "app_key" => '468080198586',
    "app_secret" => '7b3fb2b3bae35eca3b051b825b6d9f43',
    "param" => [
        "cod_int"       => "PA".$model->id,
        "datainicial"   => "01/01/2010",
        "dataFinal"     => "31/12/2020",
    ]
];
$response_omie = $omie->consulta("/api/v1/estoque/consulta/?JSON=",$body);
//echo "<pre>"; print_r($response_omie); echo "</pre>";

$saldo = 0;

$movimentacoes = ArrayHelper::getValue($response_omie, 'body.movProduto');

if($movimentacoes){
    foreach(ArrayHelper::getValue($movimentacoes[count($movimentacoes)-1], 'movPeriodo') as $movimentacao){
        $pos = strpos(ArrayHelper::getValue($movimentacao, 'tipo'), "Atual");
        if (!($pos === false)) {
            
            $salto_atual = ArrayHelper::getValue($movimentacao, 'qtde');
            $saldo = $salto_atual;
            echo "\nSaldo atual: ".$salto_atual;
        }
    }
}

?>

<div class="col-sm-12">
    <article class="card card_main" style="background-color: #ffffff; border-radius: 10px">
        <div class="card__body">
            <div class="card__content">
                <p style="color: #b10c10; font-weight: bold;"></p>
                <div class="row">
                    <div class="col-sm-5">
                        <?= "<img style='width:100%; height:100%;' src='".$model->getUrlImageBackend()."'>"?>
                    </div>

                    <div class="col-sm-7">
                    	<div class="row">
                    		<div class="col-sm-4">
                    			<b>ID</b>
                    		</div>
                    		<div class="col-sm-8">
                    			<?= $model->id?> (PA<?= $model->id?>)
                    		</div>
                    	</div>
                    	<div class="row">
                    		<div class="col-sm-4">
                    			<b>NOME</b>
                    		</div>
                    		<div class="col-sm-8">
                    			<?= $model->nome?>
                    		</div>
                    	</div>
                    	<div class="row">
                    		<div class="col-sm-4">
                    			<b>CODIGO GLOBAL</b>
                    		</div>
                    		<div class="col-sm-8">
                    			<?= $model->codigo_global?>
                    		</div>
                    	</div>
                    	<div class="row">
                    		<div class="col-sm-4">
                    			<b>CODIGO FABRICANTE</b>
                    		</div>
                    		<div class="col-sm-8">
                    			<?= $model->codigo_fabricante?>
                    		</div>
                    	</div>
                    	<div class="row">
                    		<div class="col-sm-4">
                    			<b>NCM</b>
                    		</div>
                    		<div class="col-sm-8">
                    			<?= $model->codigo_montadora?>
                    		</div>
                    	</div>
                    	<div class="row">
                    		<div class="col-sm-4">
                    			<b>EAN/GTIN</b>
                    		</div>
                    		<div class="col-sm-8">
                    			<?= $model->codigo_barras?>
                    		</div>
                    	</div>

                    	<div class="row">
                    		<div class="col-sm-4">
                    			<b>PESO</b> (kg)
                    		</div>
                    		<div class="col-sm-8">
                                <?= $model->peso . ' kg ' ?><?php echo $model->e_medidas_conferidas =='crm'?'<i class="fa fa-check" style="display: contents "!important></i>':'';?>
                    		</div>
                    	</div>

                        <div class="row">
                            <div class="col-sm-4">
                                <b>ALTURA</b> (cm)
                            </div>
                            <div class="col-sm-8">
                                <?= $model->altura . ' cm' ?><?php echo $model->e_medidas_conferidas =='crm'?' <i class="fa fa-check" style="display: contents "!important></i>':'';?>

                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <b>LARGURA</b> (cm)
                            </div>
                            <div class="col-sm-8">

                                <?= $model->largura . ' cm' ?><?php echo $model->e_medidas_conferidas =='crm'?' <i class="fa fa-check" style="display: contents "!important></i>':'';?>
                            </div>
                        </div>

                    	<div class="row">
                    		<div class="col-sm-4">
                    			<b>PROFUNDIDADE</b> (cm)
                    		</div>
                    		<div class="col-sm-8">


                                <?= $model->profundidade . ' cm' ?><?php echo $model->e_medidas_conferidas =='crm'?' <i class="fa fa-check" style="display: contents "!important></i>':'';?>
                    		</div>
                    	</div>
                    	<div class="row">
                    		<div class="col-sm-4">
                    			<b>APLICAÇÃO</b>
                    		</div>
                    		<div class="col-sm-8">
                    			<?= $model->aplicacao?>
                    		</div>
                    	</div>
                        <div class="row">
                            <div class="col-sm-4">
                                <b>LOCALIZAÇÃO</b>
                            </div>
                            <div class="col-sm-8">
                                <?= $model->localizacao?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <b>ESTOQUE</b>
                            </div>
                            <div class="col-sm-8">
                                <?= $saldo ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="card__content" style="background-color:rgba(0,0,0,.03); border-radius: 0px 0px 10px 10px; padding: 10px 55px">
            <div class="col-sm-11"></div>
            <div class="col-sm-1">
				<a href=<?= Url::to(['/produto/update', 'id' => $model->id])?>
					<button type="button" class="btn btn-primary">Alterar Dados</button>
            	</a>
            </div>
        </div>
    </article>
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