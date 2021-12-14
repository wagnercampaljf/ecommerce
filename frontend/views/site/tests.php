<?php
/**
 * Created by PhpStorm.
 * User: Otávio
 * Date: 24/10/2017
 * Time: 18:26
 */


//C:\Users\Otávio\Desktop\imagem

use common\models\Filial;
use common\models\Imagens;
use common\models\Produto;
use yii\base\Exception;
use yii\helpers\FileHelper;
use yii\helpers\Html;
use yii\helpers\Url;


echo '<pre>';
$files = array();
//$caminhos = array();
$images = FileHelper::findFiles('../../console/imagens/criar/comLogo', ['only' => ['*.jpg', '*.png'], 'recursive' => false]);
foreach ($images as $image) {
    $img = base64_encode(file_get_contents($image));
    $file = pathinfo($image);
    $ids[] = trim($file['filename']);
    $files[trim($file['filename'])] = $img;
    $codigos[] = trim(substr($file['filename'], 0, strpos($file['filename'], '-')));
//    $caminhos[trim($file['filename'])] = $image;
//    unlink($image);
//    echo Html::img('data:image;base64,' . $img);
}
$count = 0;
foreach ($codigos as $codigo) {
    $produto_id = null;
    $produto_id = Produto::findOne($codigo);
    if (!isset($produto_id)) {
        echo $codigo.'<br>';
    }
}
die;
//conferindo se o $k vai ser o codigo fabricante

//var_dump($ids);die;
if (!empty($ids)) {
//    $imagens = Imagens::find()->byCodFabricante($ids)->all();
//    $imagens = Imagens::find()->andWhere(['is','imagens.imagem_sem_logo', null])->byCodFabricante($ids)->all();
//    var_dump($imagens);die;
    foreach ($imagens as $imagen) {
        echo $caminhos[$imagen->produto->codigo_fabricante];
        echo "<br>";
//        unlink($caminhos[$imagen->produto->codigo_fabricante]);
//        echo $file['basename'];
//        echo "<br>";
//        echo $imagen->produto_id . ', ';
    }
}
//var_dump($imagens);

//$transaction = Yii::$app->db->beginTransaction();
//try {
//    foreach ($imagens as $imagen) {
//        $imagen->imagem_sem_logo = $files[$imagen->produto->codigo_fabricante];
//        if (!$imagen->save(true,[])) {
//            throw new HttpException(422, implode("\n", $imagen->getFirstErros()));
//        }
//    }
//
//    $transaction->commit();
//} catch (Exception $e) {
//    $transaction->rollBack();
//    throw $e;
//}
//    $produtos = Produto::find()->select('imagens.id')->joinWith('imagens')->andWhere([ "codigo_fabricante"=> $ids])->column();


//$filials = Filial::find()
////    ->andWhere(['IS NOT', 'refresh_token_meli', null])
//    ->andWhere(['id' => 72])
//    ->all();
//
//foreach ($filials as $filial) {
//
//    $produtoFilials = $filial->getProdutoFilials()->hasImage()->all();
//    var_dump($produtoFilials); die("ok");
//    foreach ($produtoFilials as $produtoFilial) {
//        var_dump($produtoFilial->getSkyhubData());
//        echo "<br>";
//        die;
//    }
//}
?>

<div style="height: 500px">

</div>


