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

$this->title = $categoriaModelo->nome;
$this->registerMetaTag(['name' => 'description', 'content' => 'O melhor preço em autopeças para carros, motos, caminhões, ônibus e tratores. Confira nossas ofertas e compre online! Entregamos em todo o Brasil!']);

$this->params['breadcrumbs'][] = [
    'label' => 'Veículos',
    'url' => ['/veiculos']
];
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
        <ul class="col-xs-12 col-sm-6 col-md-2 col-lg-2">
            <?php
            foreach ($marcas as $k => $marca) {
            $href = Url::to(['/veiculos/' . $categoriaModelo->slug]) . '/' . $marca->slug;
            ?>
            <li>
                <?= Html::a($marca->nome, $href) ?>
            </li>
            <?php
            if (($k + 1) % 5 == 0){
            ?>
        </ul>
        <ul class="col-xs-12 col-sm-6 col-md-2 col-lg-2">
            <?php
            }
            }
            ?>
        </ul>
    </div>
</div>


