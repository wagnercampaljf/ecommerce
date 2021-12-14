<?php
/**
 * Created by PhpStorm.
 * User: Otávio
 * Date: 04/01/2018
 * Time: 20:54
 */

namespace console\controllers\actions\upload;

use common\models\Imagens;
use common\models\Produto;
use HttpException;
use Yii;
use yii\base\Action;
use yii\helpers\FileHelper;

class AdicionarSemLogoAction extends Action
{
    public function run()
    {
        $files = array();
        $images = FileHelper::findFiles('./console/imagens/adicionar/semLogo', ['only' => ['*.jpg', '*.png', '*.gif'], 'recursive' => false]);
        if (!empty($images)) {
            foreach ($images as $image) {
                $img = base64_encode(file_get_contents($image));
                $file = pathinfo($image);
                $ids[] = trim($file['filename']);
//            $files[trim(substr($file['filename'], 0, strpos($file['filename'], '-')))] = $img;
                $files[trim($file['filename'])] = $img;
                echo '.';
            }

            foreach ($files as $k => $file) {
                echo $k . "\n";
                $imagens = Imagens::find()->byCodFabricanteOrdem($k)->all();
                $ordem = sizeof($imagens) + 1;
                $imagem = Imagens::findOne(['produto_id' => $imagens[0]['produto_id'], 'ordem' => $ordem]);
                if (isset($imagem)) {
                    $transaction = Yii::$app->db->beginTransaction();
                    try {
                        $imagem->imagem_sem_logo = $files[$k];
                        if (!$imagem->save()) {
                            throw new HttpException(422, implode("\n", $imagem->getFirstErros()));
                        }
                        $transaction->commit();
                        echo "-" . $imagem->produto_id . "\n";
                    } catch (\yii\base\Exception $e) {
                        $transaction->rollBack();
                        throw $e;
                    }
                } else {
                    echo "Nenhuma Imagem Sem segunda foto";
                }

            }
        }
    }
}