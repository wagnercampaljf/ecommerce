<?php
/**
 * Created by PhpStorm.
 * User: Otávio
 * Date: 27/09/2016
 * Time: 11:46
 */

use common\models\Marca;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = "Veículos ";
$this->registerMetaTag(['name' => 'description', 'content' => 'O melhor preço em autopeças para carros, motos, caminhões, ônibus e tratores. Confira nossas ofertas e compre online! Entregamos em todo o Brasil!']);

$this->params['breadcrumbs'][] = $this->title;
$this->registerLinkTag(['rel' => 'canonical', 'href' => Url::to([Url::canonical()], 'https')]);
?>
<div class="containerHeight">
    <br>
    <div class="row">
        <div class="h2 col-xs-12 col-sm-12 col-md-8 col-lg-8"><?= Html::encode($this->title) ?></div>
    </div>
    <br>
    <div class="row categoriasFooter">
        <?php
        foreach ($categoriasModelos as $categoriaModelo) {
            $alt = $categoriaModelo->nome;
            echo "<div class='col-xs-12 col-sm-6 col-md-2 col-lg-2'>";
            echo Html::a(Html::tag(
                'img',
                '',
                [
                    'class' => 'grow',
                    'src' => 'data:image/png;base64,' . stream_get_contents($categoriaModelo->foto),
                    'title' => $categoriaModelo->nome,
                    'alt' => $categoriaModelo->nome,
                    'width' => '100%',
                ]
            ), Url::to('veiculos/' . $categoriaModelo->slug));
            echo '<br><br>';
            echo Html::a(Html::tag('h3', $categoriaModelo->nome, ['class' => 'text-center']), Url::to('veiculos/' . $categoriaModelo->slug));
            echo '</div>';
        }
        ?>
    </div>
</div>
