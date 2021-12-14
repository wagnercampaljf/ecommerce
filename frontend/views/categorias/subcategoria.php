<?php
/**
 * Created by PhpStorm.
 * User: Otávio
 * Date: 19/05/2016
 * Time: 18:15
 */


use common\models\Caracteristica;
use common\models\Categoria;
use common\models\Cidade;
use common\models\Estado;
use common\models\Fabricante;
use common\models\Filial;
use common\models\Marca;
use common\models\Modelo;
use common\models\Subcategoria;
use frontend\widgets\ProductList;
use frontend\widgets\TagsSearch;
use kartik\select2\Select2;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;


$title = TagsSearch::renderTitle(
    ArrayHelper::merge(['_pjax' => '#filters'], Yii::$app->request->get()),
    [
        'categoria_id' => Categoria::className(),
        'subcategoria_id' => Subcategoria::className(),
        'filial_id' => Filial::className(),
        'estado_id' => Estado::className(),
        'cidade_id' => Cidade::className(),
        'marca_id' => Marca::className(),
        'modelo_id' => Modelo::className(),
        'caracteristica_id' => Caracteristica::className(),
    ]
);
$this->title = $subcategoria->nome . " " . $title;
$this->params['breadcrumbs'][] = [
    'label' => 'Categorias',
    'url' => ['/auto']
];
$this->params['breadcrumbs'][] = [
    'label' => $subcategoria->categoria->nome,
    'url' => ['auto/' . $subcategoria->categoria->slug]
];
$this->params['breadcrumbs'][] = $this->title;
$this->registerLinkTag(['rel' => 'canonical', 'href' => Url::to(['auto/' . $categoria->slug . '/' . $subcategoria->slug], 'https')]);
$this->registerMetaTag(['name' => 'description', 'content' => 'Encontre as melhores ofertas de ' . $subcategoria->nome . ' no Peça Agora. Entregamos em todo o Brasil.']);
$this->registerJs('
    $("#filters").on("pjax:send", function() {
        $(".loading").removeClass("hide");
    })
    $("#filters").on("pjax:complete", function() {
        $(".loading").addClass("hide");
    })
    $("#filters").on("pjax:end", function() {
        $.pjax.reload({container:"#grid-busca", timeout: 10000});  //Reload GridView
    });
    $("#grid-busca").on("pjax:end", function(){
        $.pjax.reload({container:"#banners", timeout: 10000});  //Reload GridView
    });
');
$this->registerJs("
$('#LeiaMais').click(function(){
    $('html, body').animate({
        scrollTop: $( $(this).attr('href') ).offset().top
    }, 1300);
    return false;
});
");
$this->registerCss("
.loading {
   width: 100%;
   height: 100%;
   padding-top: 150px;
   position: absolute;
   opacity: 0.7;
   background-color: #fff;
   z-index: 99;
   text-align: center;
}
");


?>
<div class="containerHeight">
    <div class="row">
        <div class="h1 col-xs-12 col-sm-12 col-md-8 col-lg-8"><?= Html::encode($this->title) ?></div>
        <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8 introDescricao"><?= $subcategoria->descricao ?></div>
    </div>
    <?php if (!empty($categoria->descricao)) { ?>
        <div class="row">
            <a id="LeiaMais" href=".descricaoCompleta" rel="nofollow">Leia Mais</a>
        </div>
    <?php } ?>
    <hr>
    <div class=" clearfix">
        <?php
        Pjax::begin(['id' => 'filters', 'timeout' => 10000]);
        echo $this->render('../categorias/_filters', ['searchModel' => $searchModel, 'subCategoria' => $subcategoria]);
        Pjax::end();
        Pjax::begin(['id' => 'grid-busca', 'timeout' => 10000]);
        ?>
        <div class="col-xs-12 col-sm-8 col-md-9 col-lg-9">
            <div class="panel panel-primary">
                <div class="loading hide">
                    <i class="fa fa-spinner fa-spin fa-4x"></i>
                </div>
                <div class="title-text panel-heading">PRODUTOS</div>
                <div class="panel-body">
                    <?= ProductList::widget([
                        'dataProvider' => $dataProvider,
                        'emptyText' =>
                            '<div class="alert alert-success"><b>Essa categoria não têm produtos!</b></div>'
                    ])
                    ?>
                </div>
            </div>
        </div>
        <?php Pjax::end(); ?>
        <?php if (!empty($subcategoria->descricao)) { ?>
            <div class="col-xs-12 col-sm-8 col-md-9 col-md-offset-3 col-lg-9 col-lg-offset-3">
                <div class="panel panel-default">
                    <div class="title-text panel-heading descricaoCompleta">Descrição de <?= Html::encode($this->title) ?></div>
                    <div class="panel-body">
                        <?= $subcategoria->descricao ?>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>
