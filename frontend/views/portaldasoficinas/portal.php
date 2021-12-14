<?php

use common\models\Banner;
use common\models\CategoriaBanner;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Portal das Oficinas');
$this->params['breadcrumbs'][] = $this->title;
$categoriaSlug = Yii::$app->request->get('categoria');
$categoriaNome = ucwords($categoriaSlug);
$this->title = empty($categoriaSlug) ? "Portal das Oficinas" : $categoriaNome . " | Portal das Oficinas";
$this->registerMetaTag(['name' => 'description', 'content' => 'O melhor preço em autopeças para carros, motos, caminhões, ônibus e tratores. Confira nossas ofertas e compre online! Entregamos em todo o Brasil!']);

?>
<div class="site-portal">
    <div class="row" style="margin-bottom: 25px">
        <div class="h1 col-xs-12 col-sm-12 col-md-8 col-lg-8">Portal das Oficinas</div>
        <div class="h3 col-xs-12 col-sm-12 col-md-8 col-lg-8">O Portal das Oficinas é uma área destinada as oficinas
            parceiras do nosso site divulgarem seus Serviços
            Automotivos
        </div>
        <div class="col-xs-8 col-sm-8 col-md-4 col-lg-4 ">
            <a href="<?= Url::to("land/portal") ?>" class="btn btn-lg btn-primary">
                Anuncie seus Serviços Aqui no Portal!
            </a>
        </div>
    </div>
    <?php
    Pjax::begin();
    ?>

    <div class="categoria-portal" role="tabpanel">
        <div class="row col-xs-12 col-sm-3 col-md-3 col-lg-3 clearfix">
            <!--            <a class="title-categoria-label " data-toggle="collapse" href=".categorias-portal">-->
            <!--                <div class="title-categoria clearfix">Categorias-->
            <!--                    <div class="filtros collapse in" style="transition: none;margin-right: 4px;"><i-->
            <!--                            class="fa fa-chevron-right" style="color: #007576"></i></div>-->
            <!--                    <div class="filtros collapse" style="transition: none;"><i class="fa fa-chevron-down"-->
            <!--                                                                               style="color: #007576"></i></div>-->
            <!--                </div>-->
            <!--            </a>-->
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
            <div class="collapse categorias clearfix">
                <!-- Nav tabs -->
                <ul class="categorias-portal nav nav-tabs nav-stacked clearfix " role="tablist">
                    <?php
                    $categorias = CategoriaBanner::find()->orderBy('nome')->all();
                    foreach ($categorias as $categoria) {
                        $categoriaSlug = ($categoriaSlug == "") ? $categoria->slug : $categoriaSlug;
                        ?>
                        <li role="presentation">
                            <?php
                            $class = ($categoriaSlug == $categoria->slug) ? 'active' : '';
                            $href = Url::to(['/portaldasoficinas?categoria=']) . $categoria->slug;
                            echo Html::a($categoria->nome, $href, ['class' => 'list-group-item ' . $class, 'id' => 'id_' . $categoria->nome]);
                            ?>
                        </li>
                        <?php
                    }

                    ?>

                </ul>
            </div>
        </div>
        <?php
        echo $this->render('_bannerList', ['banners' => Banner::find()->joinWith('categoriaBanners')->andWhere(['slug' => $categoriaSlug])->all()]);
        ?>
    </div>
    <?php
    Pjax::end();
    ?>

</div>
