<?php
/**
 * Created by PhpStorm.
 * User: Otávio
 * Date: 17/05/2016
 * Time: 14:48
 */

use common\models\Categoria;
use common\models\ProdutoFilial;
use yii\bootstrap\Html;
use yii\helpers\Url;

$this->title = "Categorias ";
$this->params['breadcrumbs'][] = $this->title;
$this->registerLinkTag(['rel' => 'canonical', 'href' => Url::to([Url::canonical()], 'https')]);
$this->registerMetaTag(['name' => 'description', 'content' => 'O melhor preço em autopeças para carros, motos, caminhões, ônibus e tratores. Confira nossas ofertas e compre online! Entregamos em todo o Brasil!']);

?>


<div class="containerHeight">
    <div class="row">
        <div class="h2 col-xs-12 col-sm-12 col-md-8 col-lg-8"><?= Html::encode($this->title) ?></div>
    </div>
    <br>
    <div class="row categoriasFooter">
        <?php
        $contador = 1;
        $categorias = Categoria::find()->orderBy('nome')->all();
        foreach ($categorias as $categoria) {
            $subcategorias = $categoria->subcategoriasAtivas;
            if (!empty($subcategorias)) {
                ?>
                <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3 text-left divCategoriaFooter">
                    <?php

                    echo Html::a(Html::tag("span", Html::tag('b', $categoria->nome), ['class' => 'categoriaFooter']), Url::base() . '/auto/' . $categoria->slug);
                    foreach ($subcategorias as $subcategoria) {
                        echo "<div class='col-xs-12 col-sm-12 col-md-12 col-lg-12 text-left'>";
                        echo Html::a(Html::tag("span", $subcategoria->nome, ['class' => 'subcategoriaFooter']), Url::base() . '/auto/' . $categoria->slug . "/" . $subcategoria->slug);
                        echo "</div>";
                    }
                    ?>
                </div>
                <?php
                if ($contador % 4 == 0) {
                    echo '</div>
              <div class="row categoriasFooter">';

                }
                $contador++;
            }
        }
        ?>
    </div>
</div>
