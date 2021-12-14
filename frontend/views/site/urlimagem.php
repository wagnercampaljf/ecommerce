<?php
/**
 * Created by PhpStorm.
 * User: Otávio
 * Date: 01/02/2016
 * Time: 15:00
 */
use app\models\Newsletter;
use common\models\Produto;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use common\models\Categoria;
use frontend\widgets\FormSearch;



$arrayid = ['L43466','L43423','L46231','L46228','L46230','L60722','L60229','L43334','L13100','L43179','L13185','L43324','L13169','L43338','L43043','L43059','L43273','L43504','L43188','L42340','L42342','L42343','L43014','L43547','L42244','L43237','L43073','L43260','L13163','L43538','L29085','L43049','L43477','L43443'];
//$arrayid = [6428,6435,6449,6451,6452,6829,7278,7766,7820,7826,7835,7858,7957,7979,7981,7982,8341,8366,8371,8379,8385,8471,8480,8526,8546,8558,8598,8606,8609,8684,8710,8725,8748,8755];
$coutarray = count($arrayid);
$produtos = Produto::find()->andWhere(['codigo_global' => $arrayid])->orderBy('codigo_global')->all();

for ($i = 0; $i < $coutarray; $i++) {
    $produto = ArrayHelper::getValue($produtos, $i);
    echo $produto->getCodigo().'<br>'. $produto->getImagePath().'</br></br>' ;
}
?>