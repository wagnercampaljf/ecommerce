<?php

namespace backend\controllers;

use Yii;
use yii\web\Response;
use common\models\Fornecedor;

class FornecedorController extends \yii\web\Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionGetFornecedor($q, $id = null)
    {
        if (!Yii::$app->request->isAjax) {
            exit;
        }
        if ($id > 0) {
            return ['results' => ['id' => $id, 'text' => Fornecedor::findOne($id)->nome]];
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = ['results' => []];
        if (!is_null($q)) {
            $results = Fornecedor::find()
                ->select(['fornecedor.id', 'fornecedor.nome as text'])
                ->where([
                    'like',
                    'lower(fornecedor.nome)',
                    strtolower($q)
                ])
                ->orWhere([
                    'lower(fornecedor.id::VARCHAR)' =>  strtolower($q)
                ])
                ->andWhere(['<>', 'id', 98])
                ->limit(10)
                ->createCommand()
                ->queryAll();
            $out['results'] = array_values($results);
        }

        return $out;
    }

    public function actionGetEmailFornecedor($id)
    {
        $fornecedor = Fornecedor::find()->andWhere(['=', 'id', $id])->one();
        if ($fornecedor) {
            return '{"email":"' . $fornecedor->email . '"}';
        }

        return '{"email":""}';
    }
}
