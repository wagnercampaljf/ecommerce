<?php
/**
 * Created by PhpStorm.
 * User: Otávio
 * Date: 10/05/2016
 * Time: 14:21
 */

use app\models\Newsletter;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ProdutoSearch;
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

$query = new Query;
$row = $query
    ->addSelect("produto.id")
    ->addSelect("produto.slug")
    ->addSelect("produto.nome")
    ->from("produto")
    ->innerJoin("produto_filial", "produto.id = produto_filial.produto_id")
    ->innerJoin("filial", "produto_filial.filial_id = filial.id")
    ->innerJoin("lojista", "filial.lojista_id = lojista.id")
    ->where("produto_filial.quantidade > 0")
    ->andWhere(['lojista.ativo' => true])
    ->addGroupBy("produto.id")
    ->all();

foreach ($row as $v) {
    $barra = " ";
    $barra = ($v['slug'] == '') ? "" : "/";
    echo Html::a($v['nome'], "https://www.pecaagora.com/p/" . $v['id'] . $barra . $v['slug']);
}


?>