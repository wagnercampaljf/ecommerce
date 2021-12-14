<?php
/**
 * Created by PhpStorm.
 * User: Otávio
 * Date: 04/01/2018
 * Time: 20:38
 */

namespace console\controllers\actions\upload;

use common\models\Imagens;
use common\models\Produto;
use HttpException;
use Yii;
use yii\base\Action;
use yii\helpers\FileHelper;

class AdicionarAction extends Action
{
    public function run()
    {
        $files = array();
        $images = FileHelper::findFiles('./console/imagens/adicionar/comLogo', ['only' => ['*.jpg', '*.png', '*.gif'], 'recursive' => false]);
        if (!empty($images)) {
            foreach ($images as $image) {
                $img = base64_encode(file_get_contents($image));
                $file = pathinfo($image);
                $ids[] = trim(substr($file['filename'], 0, strpos($file['filename'], '(')-1));
//                $ids[] = trim($file['filename']);
                $files[trim(substr($file['filename'], 0, strpos($file['filename'], '(')-1))] = $img;
//                $files[trim($file['filename'])] = $img;
                echo '.';
            }

            foreach ($files as $k => $file) {
                echo $k . "\n";
                $imagens = Imagens::find()->byCodFabricante($k)->all();
                $ordem = sizeof($imagens) + 1;

                $produto_id = Produto::findOne(['codigo_fabricante' => $k]);
                if (!empty($produto_id)) {
                    $model = new Imagens();
                    $transaction = Yii::$app->db->beginTransaction();
                    try {
                        $model->produto_id = $produto_id->id;
                        $model->imagem = $files[$k];
                        $model->ordem = $ordem;
                        if (!$model->save()) {
                            throw new HttpException(422, implode("\n", $model->getFirstErros()));
                        }
                        $transaction->commit();
                        echo "+" . $produto_id->id . "\n";
                    } catch (\yii\base\Exception $e) {
                        $transaction->rollBack();
                        throw $e;
                    }
                } else {
                    echo "Produto não encontrado!\n";
                }
            }
        }
    }
}