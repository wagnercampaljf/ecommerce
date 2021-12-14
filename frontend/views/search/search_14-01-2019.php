<?php
//Helpers
use frontend\widgets\ProductList;
use frontend\widgets\TagsSearch;
use common\models\Caracteristica;
use common\models\Categoria;
use common\models\Cidade;
use common\models\Estado;
use common\models\Fabricante;
use common\models\Filial;
use common\models\Marca;
use common\models\Modelo;
use common\models\Subcategoria;
use common\models\ValorProdutoFilial;
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\helpers\ArrayHelper;

//Widgets
//Models
//Grid

/* @var $this yii\web\View */
/* @var $dataProvider \yii\data\ActiveDataProvider */
/* @var $searchModel \common\models\ProdutoSearch */

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

$buscatitulo = Yii::$app->request->get('nome');
$buscatitulo = ucwords($buscatitulo);
$this->title = (!empty($title) || !empty($buscatitulo)) ? 'Ofertas de ' . $title . ' ' . $buscatitulo : '';
$this->params['breadcrumbs'][] = "Buscar " . $this->title;

if (!empty(Yii::$app->request->get('nome'))) {
    $this->registerLinkTag(['href' => Url::to(['/search'], "https") . '?nome=' . str_replace(' ', '+', Yii::$app->request->get('nome')), 'rel' => 'canonical']);
} else if (!empty(Yii::$app->request->get('categoria_id')) && !empty(Yii::$app->request->get('subcategoria_id'))) {
    $categoria = Categoria::findOne(Yii::$app->request->get('categoria_id'));
    $subcategoria = Subcategoria::findOne(Yii::$app->request->get('subcategoria_id'));
    if (!empty($categoria) && !empty($subcategoria)) {
        $this->registerLinkTag(['href' => Url::to(['/auto'], "https") . '/' . $categoria->slug . '/' . $subcategoria->slug, 'rel' => 'canonical']);
    } else {
        $this->registerLinkTag(['href' => Url::to(['/search'], 'https'), 'rel' => 'canonical']);
    }
} else if (!empty(Yii::$app->request->get('categoria_id'))) {
    $categoria = Categoria::findOne(Yii::$app->request->get('categoria_id'));
    if (!empty($categoria)) {
        $this->registerLinkTag(['href' => Url::to(['/auto'], "https") . '/' . $categoria->slug, 'rel' => 'canonical']);
    } else {
        $this->registerLinkTag(['href' => Url::to(['/search'], 'https'), 'rel' => 'canonical']);
    }
} else if (!empty(Yii::$app->request->get('marca_id'))) {
    $marca = Marca::findOne(Yii::$app->request->get('marca_id'));
    if (!empty($marca)) {
        $this->registerLinkTag(['href' => Url::to(['/search'], "https") . '?marca_id=' . $marca->id, 'rel' => 'canonical']);
    } else {
        $this->registerLinkTag(['href' => Url::to(['/search'], 'https'), 'rel' => 'canonical']);
    }
} else {
    $this->registerLinkTag(['href' => Url::to(['/search'], 'https'), 'rel' => 'canonical']);
}


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
if (!empty($this->title)) {
    $this->registerMetaTag(['name' => 'description', 'content' => 'Encontre os melhores preços de ' . $title . ' ' . $buscatitulo . ' e aproveite nossas ofertas. Entregamos em todo o Brasil.']);
} else {
    $this->registerMetaTag(['name' => 'description', 'content' => 'O melhor preço em autopeças para carros, motos, caminhões, ônibus e tratores. Confira nossas ofertas e compre online! Entregamos em todo o Brasil!']);
}

echo "<script> fbq('track', 'Search');</script>";

?>

<div class="site-search">
    <!--    <div class="row clearfix">-->
    <!--        <div class="col-md-12">-->
    <!--            --><?php
    //            Pjax::begin(['id' => 'banners', 'timeout' => 10000]);
    //            echo \frontend\widgets\BannerWidget::widget([
    //                'options' => [
    //                    'class' => 'col-md-8'
    //                ],
    //                'posicao_banner' => 'Banner_Busca_1',
    //                'cidade' => Yii::$app->session->get('location',
    //                    Yii::$app->getLocation->getCidade(Yii::$app->request->userIP)),
    //                'subcategoria' => Yii::$app->request->get('subcategoria_id', 0)
    //            ]);
    //            echo \frontend\widgets\BannerWidget::widget([
    //                'options' => [
    //                    'class' => 'col-md-4'
    //                ],
    //                'posicao_banner' => 'Banner_Busca_2',
    //                'cidade' => Yii::$app->session->get('location',
    //                    Yii::$app->getLocation->getCidade(Yii::$app->request->userIP)),
    //                'subcategoria' => Yii::$app->request->get('subcategoria_id', 0)
    //            ]);
    //
    //            Pjax::end();
    //            ?>
    <!--        </div>-->
    <!--    </div>-->
    <h1 class="span-logo"><?= $h1 = empty($buscatitulo) ? "Peça Agora" : $buscatitulo; ?></h1>
    <h2 class="span-logo">O shopping online de autopeças seu carro, moto e caminhao.</h2>
    <div class="site-search-body container row">

        <?php
        Pjax::begin(['id' => 'filters', 'timeout' => 10000]);
        //        echo FormSearch::widget([
        //            'view' => 'formSearch',
        //            'container' => 'container-fluid',
        //            'formGroupClass' => 'form-group col-md-2 col-sm-2'
        //        ]);
        echo $this->render('_filters', ['searchModel' => $searchModel]);
        Pjax::end();
        Pjax::begin(['id' => 'grid-busca', 'timeout' => 10000]);
        ?>
        <div class="search-results col-xs-12 col-sm-8 col-md-9 col-lg-9">
            <div class="">
                <div class="loading hide">
                    <i class="fa fa-spinner fa-spin fa-4x"></i>
                </div>
                <div class="">
                    <?= ProductList::widget([
                        'dataProvider' => $dataProvider,
                        'emptyText' =>
                            '<h3>Sua busca por <b>' . $this->title . '</b> não trouxe resultados.</h3>
                            <h4><i class="fa fa-pencil-square-o"></i><a href="' . \yii\helpers\Url::to(['/orcamento']) . '"> Solicite um Orçamento! </h4></a>  ou <br><br>
                            <i class="fa fa-lightbulb-o fa-2x"></i> Dicas:<br>
                            <ul>
                                <li> Tente palavras menos específicas.</li>
                                <li> Tente palavras-chave diferentes.</li>
                                <li> Digite no mí­nimo 4 caracteres.</li>
                            </ul>
                            <br>
                            Estamos atualizando nossos estoques, caso você não encontre a peça do seu veículo. <br>
                            Entre em contato para uma cotação: <br><br>
                            <ul style="list-style-type: none">
                                <li><i class="fa fa-phone-square"></i> Televendas:(32)3015-0023 </li>
                                <li><i class="fa fa-whatsapp"></i> Whatsapp:(32)988354007 </li>
                                <li><i class="fa fa-envelope"></i> sac@pecaagora.com</li>
                            </ul>
                            '
                    ])
                    ?>
                </div>
            </div>
        </div>
        <?php Pjax::end(); ?>
    </div>
</div>
<span class="btn-link"></span>
