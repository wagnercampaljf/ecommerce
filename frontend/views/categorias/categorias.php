<?php
/**
 * Created by PhpStorm.
 * User: Otávio
 * Date: 16/05/2016
 * Time: 12:24
 */
use frontend\widgets\ProductList;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;


$this->title = $categoria->nome . " ";
$this->params['breadcrumbs'][] = [
    'label' => 'Categorias',
    'url' => ['/auto']
];
$this->params['breadcrumbs'][] = $this->title;
$this->registerJs("
$('#LeiaMais').click(function(){
    $('html, body').animate({
        scrollTop: $( $(this).attr('href') ).offset().top
    }, 1300);
    return false;
});
");
$this->registerMetaTag(['name' => 'description', 'content' => 'Encontre as melhores ofertas de ' . $categoria->nome . ' no Peça Agora. Entregamos em todo o Brasil.']);
$this->registerLinkTag(['rel' => 'canonical', 'href' => Url::to([Url::canonical()], 'https')]);
?>

<div class="containerHeight">
    <div class="row">
        <div class="h2 col-xs-12 col-sm-12 col-md-8 col-lg-8"><?= Html::encode($this->title) ?></div>
        <br><br>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 introDescricao"><?= $categoria->descricao ?>
        </div>

    </div>
    <?php if (!empty($categoria->descricao)) { ?>
        <div class="row">
            <a id="LeiaMais" href=".descricaoCompleta" rel="nofollow">Leia Mais</a>
        </div>
    <?php } ?>
    <hr>
    <div class="clearfix col-xs-12 col-sm-4 col-md-3 col-lg-3">
        <a class="title-categoria-label" data-toggle="collapse" rel="nofollow" href=".categorias">
            <div class="title-categoria clearfix ">
                CATEGORIAS
                <div class="categorias collapse in pull-right" style="transition: none;margin-right: 4px;">
                    <i class="fa fa-chevron-right"></i>
                </div>
                <div class="categorias collapse pull-right" style="transition: none;">
                    <i class="fa fa-chevron-down"></i>
                </div>
            </div>
        </a>
        <div class="categorias collapse clearfix">
            <ul class="categorias-portal nav nav-tabs nav-stacked clearfix " role="tablist">
                <?php
                foreach ($subCategorias as $subCategoria) {
                    $href = Url::to($categoria->slug);
//                    if (!empty($subCategoria->produtos)) {
                    echo "<li>
                            <a id='id_'". $subCategoria->nome."' class='list-group-item' href='".$href . '/' . $subCategoria->slug."'>".$subCategoria->nome."</a>
</li>";

//                    echo Html::tag('li', Html::a($subCategoria->nome, $href . '/' . $subCategoria->slug, ['class' => 'list-group-item ', 'id' => 'id_' . $subCategoria->nome]));
//                    }
                }
                ?>
            </ul>
        </div>
    </div>
    <div class="col-xs-12 col-sm-8 col-md-9 col-lg-9">
        <div class="panel panel-primary">
            <div class="title-text panel-heading">PRODUTOS</div>
            <div class="panel-body">
                <?php
                echo ProductList::widget([
                    'dataProvider' => $dataProvider,
                    'emptyText' =>
                        '<div class="alert alert-success"><b>Essa categoria não têm produtos!</b></div>'
                ])
                ?>
            </div>
        </div>
    </div>
    <?php if (!empty($categoria->descricao)) { ?>
        <div class="col-xs-12 col-sm-8 col-md-9 col-md-offset-3 col-lg-9 col-lg-offset-3">
            <div class="panel panel-default">
                <div class="title-text panel-heading descricaoCompleta">Descrição de <?= Html::encode($this->title) ?></div>
                <div class="panel-body">
                    <?= $categoria->descricao ?>
                </div>
            </div>
        </div>
    <?php } ?>
</div>
