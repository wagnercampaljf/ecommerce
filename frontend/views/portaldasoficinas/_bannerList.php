<?php
/**
 * Created by PhpStorm.
 * User: Otávio
 * Date: 18/03/2016
 * Time: 16:08
 */
use frontend\widgets\BannerWidget;
use yii\helpers\Html;
use yii\helpers\Url;

?>
<!-- Tab panes -->
<div class="tab-content col-xs-12 col-sm-9 col-md-9 col-lg-9 ">
    <div class="panel panel-primary ">
        <div class="panel-heading"><h2>Oficinas Mecânicas</h2></div>
        <div role="tabpanel" class="panel-body tab-pane fade in active" id="entrega">
            <?php
            if ($banners) {
                foreach ($banners as $banner) {
                    ?>
                    <div class="bannerPortal panel panel-default col-xs-12 col-sm-12 col-md-6 col-lg-6 clearfix">
                        <div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <div class="h3">
                                <?= $banner->nome ?>
                            </div>

                            <div class="h5 margin-bottom-10">
                                <i class="fa fa-map-marker"></i> <?= $banner->cidade ?>
                            </div>
                        </div>
                        <?php
                        echo Html::tag('div',
                            Html::a(Html::img('data:image/png;base64,' . stream_get_contents($banner->imagem), ['class' => 'col-xs-12 col-sm-12 col-md-12 col-lg-123']),
                                $banner->link, ['target' => '_blank']),
                            ['class' => 'imagemBanner col-xs-12 col-sm-12 col-md-12 col-lg-12']);
                        echo Html::tag('div', $banner->descricao,
                            ['class' => 'descricaoBanner col-xs-12 col-sm-12 col-md-12 col-lg-12']);
                        ?>

                    </div>


                    <?php
                }
            };
            ?>

        </div>
    </div>
</div>
