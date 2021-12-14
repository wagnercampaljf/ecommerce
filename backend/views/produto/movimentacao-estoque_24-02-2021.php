<?php

use yii\helpers\Html;
use yii\helpers\Url;
use kartik\grid\GridView;
use common\models\ProdutoFilial;
use common\models\ValorProdutoMenorMaior;
use console\controllers\actions\omie\Omie;
use kartik\grid\GridPerfectScrollbarAsset;
use Mpdf\Tag\Dd;
use yii\helpers\ArrayHelper;
use yii\widgets\ListView;

$this->title = 'Pesquisa de Movimentações';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="produto-index">

    <div class="clearfix col-md-auto cab_fixo  " id="sumir" style="padding: 1px;  background: linear-gradient(#f7f7f7, #f7f7f7); height: 56px">
        <div class="container">
            <form id="main-search" class="form form-inline sombra" role="form" action="<?= Url::to(['produto/movimentacao-estoque']) ?>">
                <div class="input-group col-lg-14 col-md-14 col-sm-14 col-xs-14" style="padding-left: 0px !important;padding-right: 0px !important;">
                    <input type="text" name="termo" id="main-search-product" class="form-control form-control-search input-lg data-hj-whitelist" placeholder="Código PA ..." autofocus="true">
                    <span class="input-group-btn">
                        <button type="submit" class="btn btn-default btn-lg control" style="background-color: #007576; width: 50px; height: 45px" id="main-search-btn"><i class="fa fa-search" style="color: white" value="pesqusiar"></i></button>
                    </span>
                </div>
            </form>
        </div>
    </div>
    
    <?= ListView::widget([
            'dataProvider' => $dataProvider,
            'itemView' => 'listaMovimentacaoes',
    ])?>
    
</div>
