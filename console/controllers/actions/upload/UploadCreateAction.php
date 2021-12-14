<?php
/**
 * Created by PhpStorm.
 * User: Otávio
 * Date: 06/11/2017
 * Time: 17:23
 */

namespace console\controllers\actions\uploadCreate ;

use common\models\Imagens;
use common\models\Produto;
use HttpException;
use Yii;
use yii\base\Action;
use yii\helpers\FileHelper;


class UploadCreateAction extends Action
{
    public function run()
    {

        die('oi');
        $files = array();
//        $path = array();
        $images = FileHelper::findFiles('./console/imagens', ['only' => ['*.jpg', '*.png'], 'recursive' => false]);
        foreach ($images as $image) {
            $img = base64_encode(file_get_contents($image));
            $file = pathinfo($image);
            $ids[] = trim($file['filename']);
            $files[trim($file['filename'])] = $img;
//            $path[trim($file['filename'])] = $image;
            echo $image . "\n";
        }

        foreach ($files as $k => $file) {
            $imagen = Imagens::find()->byCodFabricante($ids)->one();
            if (!empty($imagen)) {


            } else {
                $model = new Imagens();
                $produto_id = Produto::findOne($k);
                echo "oi" . $produto_id;


            }
        }

        $imagens = Imagens::find()->andWhere(['is', 'imagens.imagem_sem_logo', null])->byCodFabricante($ids)->all();


        $transaction = Yii::$app->db->beginTransaction();
        try {
            foreach ($imagens as $imagen) {
                $imagen->imagem_sem_logo = $files[$imagen->produto->codigo_fabricante];
//                echo $path[$imagen->produto->codigo_fabricante]."\n";
                if (!$imagen->save()) {
                    throw new HttpException(422, implode("\n", $imagen->getFirstErros()));
                }
//                if (!unlink($path[$imagen->produto->codigo_fabricante])){
//                    echo "err\n";
//                }else{
//                    echo "ok\n";
//                }
            }

            $transaction->commit();
        } catch (\yii\base\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }

    }
}