<?php
//Essa action adiciona uma foto sem logo à uma imagem(objeto) jaexistente,
//    Caso o produto ja tenha uma foto sem logo ela será substituida


/**
 * Created by PhpStorm.
 * User: Otávio
 * Date: 06/11/2017
 * Time: 17:23
 */

namespace console\controllers\actions\upload;

use common\models\Imagens;
use HttpException;
use Yii;
use yii\base\Action;
use yii\helpers\FileHelper;


class UploadAction extends Action
{
    public function run()
    {
        $files = array();
        $images = FileHelper::findFiles('./console/imagens/criar/semLogo', ['only' => ['*.jpg', '*.png', '*.gif'], 'recursive' => false]);
        if (!empty($images)) {
            foreach ($images as $image) {
                $img = base64_encode(file_get_contents($image));
                $file = pathinfo($image);
                $ids[] = trim(substr($file['filename'], 2, strpos($file['filename'], '-') - 2));
//                $ids[] = trim($file['filename']);
                $files[trim(substr($file['filename'], 2, strpos($file['filename'], '-') - 2))] = $img;
//                $files[trim($file['filename'])] = $img;
                echo trim(substr($file['filename'], 2, strpos($file['filename'], '-') - 2)) . "\n";
            }
            $imagens = Imagens::find()->andWhere(['is', 'imagens.imagem_sem_logo', null])->byCodFabricante($ids)->all();
            foreach ($imagens as $imagen) {
                echo $imagen->produto->codigo_fabricante . "ok \n";
            }

            $transaction = Yii::$app->db->beginTransaction();
            try {
                foreach ($imagens as $imagen) {
                    $imagen->imagem_sem_logo = $files[$imagen->produto->codigo_fabricante];
//                echo $path[$imagen->produto->codigo_fabricante]."\n";
                    echo $imagen->produto->codigo_fabricante;
                    if (!$imagen->save()) {
                        echo " -\n";
                        throw new HttpException(422, implode("\n", $imagen->getFirstErros()));
                    }
                    echo " ok\n";
                }
                $transaction->commit();
            } catch (\yii\base\Exception $e) {
                $transaction->rollBack();
                throw $e;
            }
            echo "codigo global \n";
            $imagens2 = Imagens::find()->andWhere(['is', 'imagens.imagem_sem_logo', null])->byCodGlobal($ids)->all();
            foreach ($imagens2 as $imagen2) {
                echo $imagen2->produto->codigo_fabricante . "ok \n";
            }
            $transaction = Yii::$app->db->beginTransaction();
            try {
                foreach ($imagens2 as $imagen2) {
                    $imagen2->imagem_sem_logo = $files[$imagen2->produto->codigo_global];
                    echo $imagen2->produto->codigo_global;
                    if (!$imagen2->save()) {
                        echo " - \n";
                        throw new HttpException(422, implode("\n", $imagen2->getFirstErros()));
                    }
                    echo " ok\n";
                }
                $transaction->commit();
            } catch (\yii\base\Exception $e) {
                $transaction->rollBack();
                throw $e;
            }
        }
    }
}