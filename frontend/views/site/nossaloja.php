<?php

use app\models\Newsletter;
use common\models\CategoriaModelo;
use common\models\Fabricante;
use common\models\Filial;
use common\models\Marca;
use common\models\Modelo;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ProdutoSearch;
use common\models\ValorProdutoFilial;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use common\models\Categoria;
use frontend\widgets\FormSearch;
use yii\db\Query;
use Marketplacehub\Skyhub\ApiClient as Skyhub;

/* @var $this yii\web\View */
$this->title = 'Nossas Lojas';
$this->params['breadcrumbs'][] = $this->title;
$this->registerMetaTag(['name' => 'description', 'content' => 'O melhor preço em autopeças para carros, motos, caminhões, ônibus e tratores. Confira nossas ofertas e compre online! Entregamos em todo o Brasil!']);

?>
<style type="text/css">

    p {
        text-align: justify;

        font-size: 16px;
    }

    h2 {
        margin-bottom: 25px;
    }
    .thumbnail{
        background-color: rgba(221, 221, 221, 0.47);

    }


</style>
<div class="container col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <h2><?= Html::encode($this->title) ?></h2>

    <div class="row">

        <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
            <div class="col-md-6">
                <div class="thumbnail" >
                    <img src="/frontend/web/pecaagorasampa.jpeg" alt="" class="img-responsive">
                    <div class="caption">
                        <h4 class="pull-right"><b>Matriz:</b> São Paulo</h4>
                        <h4><a href="#"><b>Peça Agora</b></a></h4>
                        <p>

                            Email:vendassp.pecaagora@gmail.com<br>
                            TELEFONE: (11) 2193-1099<br>
                            WHATSAPP: (11) 94554-4208<br>
                            Endereço: Rua Carmópolis de Minas, 963, Vila Maria, SP - Saída Dutra KM 230<br>
                            Cep: 02116-010<br>

                        </p>
                    </div>

                    <div class="space-ten"></div>

                    <div class="space-ten"></div>

                </div>
                <br><br><br><br>
            </div>

            <div class="col-md-6">
                <div class="thumbnail" >
                    <img src="/frontend/web/pecaagorajf.jpeg" alt="" class="img-responsive">
                    <div class="caption">
                        <h4 class="pull-right"><b>Ecommerce:</b> Juiz de Fora</h4>
                        <h4><a href="#"><b>Peça Agora</b></a></h4>
                        <p>

                            Email:vendas.pecaagora@gmail.com <br>
                            TELEFONE: (32) 3015-0023<br>
                            WHATSAPP: (32) 98835-4007<br>
                            Endereço: Rua José Lourenço Kelmer, s/nº, Campus Universitário UFJF, Centro Regional de Inovação e Transferência de Tecnologia<br>
                            Cep: 36036-900

                        </p>
                    </div>

                    <div class="space-ten"></div>

                    <div class="space-ten"></div>

                </div>
                <br><br><br><br>
            </div>


        </div>


    </div>

</div>

