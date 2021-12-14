<?php
//Essa action cria fotos com logo para os produtos que ainda não tem nenhuma foto,
//caso o produto já tenha uma imagem ela será substituida

/**
 * Created by PhpStorm.
 * User: Otávio
 * Date: 06/11/2017
 * Time: 17:23
 */

namespace console\controllers\actions\upload;

use common\models\Imagens;
use common\models\Produto;
use HttpException;
use Yii;
use yii\base\Action;
use yii\helpers\FileHelper;


class CreateAction extends Action
{
    public function run()
    {
        $naoEncontrado = array();
        $files = array();
        $images = FileHelper::findFiles('./console/imagens/criar/comLogo', ['only' => ['*.jpg', '*.png', '*.gif'], 'recursive' => false]);
        if (!empty($images)) {
            foreach ($images as $k => $image) {
                $img = base64_encode(file_get_contents($image));
                $file = pathinfo($image);
                $ids[] = trim(substr($file['filename'], 0, strpos($file['filename'], '-')));
                // $ids[] = trim($file['filename']);
                $files[trim(substr($file['filename'], 0, strpos($file['filename'], '-')))] = $img;
                // $files[trim($file['filename'])] = $img;
                // echo trim(substr($file['filename'], 0, strpos($file['filename'], '-')));
            }
            foreach ($files as $k => $file) {
                $imagen = Imagens::find()->byCodFabricante($k)->one();
                if (!empty($imagen)) {
                    // echo "-\n";
//                    $transaction = Yii::$app->db->beginTransaction();
//                    try {
//                        $imagen->imagem = $files[$k];
//                        if (!$imagen->save()) {
//                            throw new HttpException(422, implode("\n", $imagen->getFirstErros()));
//                        }
//                        $transaction->commit();
//                        echo "-" . $imagen->produto_id . "\n";
//                    } catch (\yii\base\Exception $e) {
//                        $transaction->rollBack();
//                        throw $e;
//                    }
                } else {
                    $produto_id = Produto::findOne(['codigo_fabricante' => $k]);
                    if (!empty($produto_id)) {
                        // echo '+\n';
//                        $model = new Imagens();
//                        $transaction = Yii::$app->db->beginTransaction();
//                        try {
//                            $model->produto_id = $produto_id->id;
//                            $model->imagem = $files[$k];
//                            $model->ordem = 1;
//                            if (!$model->save()) {
//                                throw new HttpException(422, implode("\n", $model->getFirstErros()));
//                            }
//                            $transaction->commit();
//                            echo "+" . $produto_id->id . "\n";
//                        } catch (\yii\base\Exception $e) {
//                            $transaction->rollBack();
//                            throw $e;
//                        }
                    } else {
                        // echo $k . " ";
                        $imagen2 = Imagens::find()->byCodGlobal($k)->one();
                        if (!empty($imagen2)) {
                            // echo "ja tem\n";
        //                    $transaction = Yii::$app->db->beginTransaction();
        //                    try {
        //                        $imagen->imagem = $files[$k];
        //                        if (!$imagen->save()) {
        //                            throw new HttpException(422, implode("\n", $imagen->getFirstErros()));
        //                        }
        //                        $transaction->commit();
        //                        echo "-" . $imagen->produto_id . "\n";
        //                    } catch (\yii\base\Exception $e) {
        //                        $transaction->rollBack();
        //                        throw $e;
        //                    }
                        } else {
                            $produto_id = Produto::findOne(['codigo_global' => $k]);
                            if (!empty($produto_id)) {
                                // echo "nao tem\n";
                                // $model = new Imagens();
                                // $transaction = Yii::$app->db->beginTransaction();
                                // try {
                                //     $model->produto_id = $produto_id->id;
                                //     $model->imagem = $files[$k];
                                //     $model->ordem = 1;
                                //     if (!$model->save()) {
                                //         throw new HttpException(422, implode("\n", $model->getFirstErros()));
                                //     }
                                //     $transaction->commit();
                                //     echo "+" . $produto_id->id . "\n";
                                // } catch (\yii\base\Exception $e) {
                                //     $transaction->rollBack();
                                //     throw $e;
                                // }
                            } else {
                                echo $k ."\n";
                            }
                        }
                    }
                }

            }
        }
    }
}
