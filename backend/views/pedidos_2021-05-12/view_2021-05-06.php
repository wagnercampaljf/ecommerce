<?php
/* @var $this yii\web\View */
use vendor\iomageste\Moip\Moip;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use common\models\Banner;
use yii\widgets\ActiveForm;
use common\models\ProdutoFilial;
use yii\widgets\ListView;

$comprador = Yii::$app->user->getIdentity();
$this->params['active'] = 'pedidos';
$this->registerJsFile(
    Url::base() . '/js/pedidos.js'
);
$this->title = 'Pedido #' . $model->id;
?>


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
                <?php
                if ($model->statusAtual->tipoStatus->id ==5 ){
                    $state = 'active';
                    echo ' <div class="text-center bs-wizard-stepnum">' . $status::getLabel(). '</div>
                                                    <div class="progress">
                                                        <div class="progress-bar" style="background-color: rgba(177,44,27,0.95)"></div>
                                                    </div>

                                                    <p href="#" class="bs-wizard-dot mudarstatus" style="background-color: rgba(177,44,27,0.95)"></p>';
                }else{
                    echo ' <div class="text-center bs-wizard-stepnum">'. $status::getLabel().'</div>
                                                    <div class="progress">
                                                        <div class="progress-bar"></div>
                                                    </div>

                                                   ';

                }

                ?>
                <a href='<?= Url::to(['mudar-status', 'id' => $model->id, 'status' => $key]) ?>'
                   class="bs-wizard-dot mudarstatus"></a>

            </div>
        <?php } ?>
    </div>
</div>
<style>
    .card {
        position: relative;
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-orient: vertical;
        -webkit-box-direction: normal;
        -ms-flex-direction: column;
        flex-direction: column;
        min-width: 0;
        word-wrap: break-word;
        background-color: #fff;
        background-clip: border-box;
        border: 1px solid rgba(0, 0, 0, 0.1);
        border-radius: 0.10rem
    }


    .track {
        position: relative;
        background-color: #ddd;
        height: 7px;
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        margin-bottom: 60px;
        margin-top: 50px
    }

    .track .step {
        -webkit-box-flex: 1;
        -ms-flex-positive: 1;
        flex-grow: 1;
        width: 25%;
        margin-top: -18px;
        text-align: center;
        position: relative
    }

    .track .step.active:before {
        background: #FF5722
    }

    .track .step::before {
        height: 7px;
        position: absolute;
        content: "";
        width: 100%;
        left: 0;
        top: 18px
    }

    .track .step.active .icon {
        background: #ee5435;
        color: #fff
    }

    .track .icon {
        display: inline-block;
        width: 40px;
        height: 40px;
        line-height: 40px;
        position: relative;
        border-radius: 100%;
        background: #ddd
    }

    .track .step.active .text {
        font-weight: 400;
        color: #000
    }

    .track .text {
        display: block;
        margin-top: 7px
    }


    ul.row,
    ul.row-sm {
        list-style: none;
        padding: 0
    }


    /*Form Wizard*/
    .bs-wizard {
        border-bottom: solid 1px #e0e0e0;
        padding: 0 0 10px 0;
    }

    .bs-wizard > .bs-wizard-step {
        padding: 0;
        position: relative;
    }

    .bs-wizard > .bs-wizard-step + .bs-wizard-step {
    }

    .bs-wizard > .bs-wizard-step .bs-wizard-stepnum {
        color: #595959;
        font-size: 10px;
        margin-bottom: 5px;
        margin-left: -100%;
    }

    .bs-wizard > .bs-wizard-step .bs-wizard-info {
        color: #999;
        font-size: 14px;
    }

    .bs-wizard > .bs-wizard-step > .bs-wizard-dot {
        position: absolute;
        width: 30px;
        height: 30px;
        display: block;
        background: #cdcdbc;
        top: 45px;
        left: 0%;
        margin-top: -15px;
        margin-left: -15px;
        border-radius: 50%;
    }

    .bs-wizard > .bs-wizard-step > .bs-wizard-dot:after {
        content: ' ';
        width: 14px;
        height: 14px;
        background: #fbbd19;
        border-radius: 50px;
        position: absolute;
        top: 8px;
        left: 8px;
    }

    .bs-wizard > .bs-wizard-step > .progress {
        position: relative;
        border-radius: 0px;
        height: 10px;
        box-shadow: none;
        margin: 20px 0;
        background: #cdcdbc
    }

    .bs-wizard > .bs-wizard-step.active > .progress > .progress-bar {
        width: 100%;
        box-shadow: none;
        background: #68b85c; /* Old browsers */
        background: -moz-linear-gradient(left, #68b85c 0%, #f0974e 84%, #f0974e 100%); /* FF3.6+ */
        background: -webkit-gradient(linear, left top, right top, color-stop(0%, #68b85c), color-stop(84%, #f0974e), color-stop(100%, #f0974e)); /* Chrome,Safari4+ */
        background: -webkit-linear-gradient(left, #68b85c 0%, #f0974e 84%, #f0974e 100%); /* Chrome10+,Safari5.1+ */
        background: -o-linear-gradient(left, #68b85c 0%, #f0974e 84%, #f0974e 100%); /* Opera 11.10+ */
        background: -ms-linear-gradient(left, #68b85c 0%, #f0974e 84%, #f0974e 100%); /* IE10+ */
        background: linear-gradient(to right, #68b85c 0%, #f0974e 84%, #f0974e 100%); /* W3C */
        filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#68b85c', endColorstr='#f0974e', GradientType=1); /* IE6-9 */


    }

    .bs-wizard > .bs-wizard-step > .progress {
        width: 90%;
        margin-left: -90%;
        z-index: 0;
    }

    .bs-wizard > .bs-wizard-step.complete > .progress > .progress-bar {
        width: 100%;
        background: #5cb85c;
    }


    .bs-wizard > .bs-wizard-step.disabled:last-child > .progress {
        width: 100%;
        margin-left: -100%;
    }

    .bs-wizard > .bs-wizard-step.complete:first-child > .progress {
        width: 0px;
    }

    .bs-wizard > .bs-wizard-step > .bs-wizard-dot {
        z-index: 50;
    }

    .bs-wizard > .bs-wizard-step.complete > .bs-wizard-dot {
        position: absolute;
        cursor: auto;
        width: 30px;
        height: 30px;
        display: block;
        background: #5cb85c;
        top: 40px;
        left: 0%;
        margin-top: -15px;
        margin-left: 0px;
        border-radius: 50%;
    }

    .bs-wizard > .bs-wizard-step.active > .bs-wizard-dot {
        position: absolute;
        width: 50px;
        height: 50px;
        display: block;
        background: #f0ad4e;
        top: 30px;
        left: 0%;
        margin-top: -15px;
        margin-left: -15px;
        border-radius: 50%;
    }

    .bs-wizard > .bs-wizard-step.active > .bs-wizard-dot:hover {
        position: absolute;
        width: 50px;
        height: 50px;
        display: block;
        background: #f0ad4e;
        top: 40px;
        left: 0%;
        margin-top: -15px;
        margin-left: -15px;
        border-radius: 40%;
    }

    .bs-wizard > .bs-wizard-step > .bs-wizard-dot:after {
        content: ' ';
        width: 14px;
        height: 14px;
        background: #fff;
        border-radius: 50px;
        position: absolute;
        top: 8px;
        left: 8px;
    }

    .bs-wizard > .bs-wizard-step.active > .bs-wizard-dot:after {
        content: ' ';
        width: 20px;
        height: 20px;
        background: #fff;
        border-radius: 50px;
        position: absolute;
        top: 15px;
        left: 15px;
    }

    /*.bs-wizard > .bs-wizard-step.active > .progress > .progress-bar {width:50%;}*/
    .bs-wizard > .bs-wizard-step.disabled > .bs-wizard-dot {
        background-color: #bcbcab;
        top: 40px;

    }

    .bs-wizard > .bs-wizard-step.disabled > .bs-wizard-dot:after {
        background: #fff;
    }

    /*.bs-wizard > .bs-wizard-step:first-child.active > .progress > .progress-bar {width:0%;}
    .bs-wizard > .bs-wizard-step:last-child.active > .progress > .progress-bar {width: 50%;}
    .bs-wizard > .bs-wizard-step:first-child  > .progress {width: 50%;}
    .bs-wizard > .bs-wizard-step:last-child  > .progress {width: 50%;}*/
    .bs-wizard > .bs-wizard-step.disabled a.bs-wizard-dot {
        pointer-events: none;
    }

    .bs-wizard > .bs-wizard-step.complete a.bs-wizard-dot {
        pointer-events: none;
    }

    /*END Form Wizard*/


</style>

<div class="container">
    <div class="row">
        <div class="col-sm-7">
            <p>Venda: <?= $model['id'] ?> | <?= Yii::$app->formatter->asDate($model->dt_referencia) ?></p><br>
            <div class="cards">
                <article class="card card_main" style="background-color: #f5f5f5">

                    <div class="card__body">
                        <div class="card__content">
                            <div class="card__description">
                                <p><span style="font-weight: bold; color: rgba(41,41,41,0.95)"><?= $model->comprador->empresa->nome; ?></span> |
                                    <?= $model->comprador->empresa->nome; ?> | <?= $model->comprador->empresa->getDocumentoLabel(); ?> | <?= $model->comprador->empresa->telefone; ?></p>
                            </div>
                        </div>

                    </div>
                </article>


                <article class="card card_main">
                    <div class="card__body">
                        <div class="card__content">
                            <div class="card__description">
                                <?= ListView::widget([
                                    'dataProvider' => $dataProvider,
                                    'itemView' => 'listapedidosprodutos',
                                    'summary'=>'',
                                ]); ?>
                                <br><br><br>

                                <p style="font-weight: bold; color: rgba(41,41,41,0.95)"> Dados do envio</p>
                                <p style="font-size: 14px">Envio: <?= isset($model['transportadora']['nome']) ? $model['transportadora']['nome'] : 'Não selecionado' ?></>
                                <p style="font-size: 14px"><?= $model['comprador']['empresa']['enderecosEmpresa'][0] ?></p>
                                <p style="font-size: 14px">Quem recebe: <?= $model->comprador->empresa->nome; ?>  </p>
                                <p style="font-size: 14px">email:  <?= $model['comprador']['email'] ?> </p>
                                <p style="font-size: 14px">Telefone:  <?= Yii::$app->formatter->asTelefone($model['comprador']['empresa']['telefone']) ?> </p>
                            </div>
                        </div>
                    </div>
                </article>

                <article class="card card_main">

                    <div class="card__body">
                        <div class="card__content">
                            <div class="card__description">
                                <p style="font-weight: bold; color: rgba(41,41,41,0.95)"> Dados para sua fatura</p>
                                <p style="font-size: 14px">Nome: <?= $model->comprador->empresa->nome; ?> - CPF/CNJP: <?= $model->comprador->empresa->getDocumentoLabel(); ?> </p>
                                <p style="font-size: 14px"><?= $model['comprador']['empresa']['enderecosEmpresa'][0] ?> </p>


                            </div>
                        </div>
                    </div>

                </article>

                <article class="card card_main">

                    <div class="card__body">
                        <div class="card__content">
                            <div class="card__description">
                                <p style="font-weight: bold; color: rgba(41,41,41,0.95)"> Dados de Pagamento</p>
                                <p style="font-size: 14px">ID Moip: <?= isset($model['transportadora']['nome']) ? $model['token_moip'] : '---' ?> </p>
                                <p style="font-size: 14px">Forma de Pagamento: <?= $model['formaPagamento']['nome'] ?> </p>


                            </div>
                        </div>
                    </div>

                </article>


            </div>
        </div>
        <div class="col-sm-5">
            <div class="cards">
                <article class="card card_main" style="background-color: #f5f5f5">
                    <div class="card__body">
                        <div class="card__content">
                            <div>
                                <?php
                                if ($model['statusAtual']['tipoStatus']['nome'] == 'Cancelado'){
                                    echo ' <p style="font-weight: bold; color: rgba(217,0,16,0.95)">CANCELADO</p>';
                                }elseif ($model['statusAtual']['tipoStatus']['nome'] == 'Concluído'){
                                    echo ' <p style="font-weight: bold; color: rgba(217,0,16,0.95)">CONCLUIDO</p>';
                                }
                                elseif ($model['statusAtual']['tipoStatus']['nome'] == 'Enviado'){
                                    echo ' <p style="font-weight: bold; color: rgba(27,109,133,0.95)">ENVIADO</p>';
                                }
                                elseif ($model['statusAtual']['tipoStatus']['nome'] == 'Pagamento Confirmado'){
                                    echo ' <p style="font-weight: bold; color: rgba(217,0,16,0.95)">PAGAMENTO CONFIRMADO</p>';
                                }

                                elseif ($model['statusAtual']['tipoStatus']['nome'] == 'Em Aberto'){
                                    echo ' <p style="font-weight: bold; color: rgba(217,0,16,0.95)">EM ABERTO</p>';
                                }


                                ?>


                                <p style="font-size: 14px">Nº <?= $model['id'] ?> | <?= Yii::$app->formatter->asDate($model->dt_referencia) ?></p><br>
                                <table class="table">

                                    <?= ListView::widget([
                                        'dataProvider' => $dataProvider,
                                        'itemView' => 'lista_pedidos_produtos_valor',
                                        'summary'=>'',
                                    ]); ?>



                                    <tr>
                                            <th scope="row" style="font-size: 14px; border:none !important;">Taxa Fixa</p></th>
                                            <td style="border:none !important;"></td>
                                            <td style="border:none !important;"></td>
                                            <td style="font-size: 14px; border:none !important;"><?= Yii::$app->formatter->asCurrency(Moip::TAXMOIP) ?></td>
                                        </tr>



                                    <tr>
                                        <th scope="row" style="font-size: 14px; border:none !important;">Juros</p></th>
                                        <td style="border:none !important;"></td>
                                        <td style="border:none !important;"></td>
                                        <td style="font-size: 14px; border:none !important;"><?= Yii::$app->formatter->asCurrency($model['valor_total'] - $model->valorTotalSemJuros - Moip::TAXMOIP); ?></td>
                                    </tr>


                                    <tr>
                                        <th scope="row" style="font-size: 14px; border:none !important;">Frete</p></th>
                                        <td style="border:none !important;"></td>
                                        <td style="border:none !important;"></td>
                                        <td style="font-size: 14px; border:none !important;"><?= Yii::$app->formatter->asCurrency($model['valor_frete']) ?></td>
                                    </tr>

                                    <tr>
                                        <th scope="row" >Total</p></th>
                                        <td ></td>
                                        <td></td>
                                        <td style="font-size: 14px;" ><?= Yii::$app->formatter->asCurrency($model['valor_total'] + $model['valor_frete']) ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                    </div>
                </article>
            </div>
        </div>
        <article class="card card_main" style="background-color: #f5f5f5">
            <div class="card__body">
                <div class="card__content">
                    <style>
                        textarea.form-control{
                            height: 250px;
                        }

                    </style>
                    EMAIL
                </div>
            </div>
        </article>
    </div>
</div>



<a class="btn btn-primary" href="<?= Url::to(['/pedidos']) ?>" role="button">Voltar</a>





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
    }
</style>




