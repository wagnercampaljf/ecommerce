<?php

namespace lojista\controllers;

use common\models\Cidade;
use common\models\EnderecoFilial;
use common\models\Filial;
use common\models\Lojista;
use common\models\Usuario;
use lojista\models\MudarSenhaForm;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * MinhaContaController implements the CRUD actions for Usuario model.
 */
class MinhaContaController extends Controller
{
    /**
     * Lists all Usuario models.
     * @return mixed
     */
    public function actionIndex()
    {
        /* @var $model Usuario */
        $model = Yii::$app->user->identity;
        $filial = $model->filial;
        $enderecoFilial = $filial->enderecoFilial;

        return $this->render('index', [
            'model' => $model,
            'filial' => $filial,
            'enderecoFilial' => $enderecoFilial,
        ]);
    }

    /**
     * Updates an existing Usuario model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionUpdateUsuario()
    {
        /* @var $model Usuario */
        $model = Yii::$app->user->identity;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('update-usuario', [
                'model' => $model,
            ]);
        }
    }

    /**
     * @author Igor Mageste 05/01/2016
     * @return string|Response
     */
    public function actionUpdateFilial()
    {
        /* @var $model Filial */
        $model = Yii::$app->user->identity->filial;

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            /* @var $lojista Lojista */
            $lojista = $model->lojista;
            $lojista->nome = $model->nome;
            $lojista->razao = $model->razao;
            $lojista->documento = $model->documento;

            if ($model->save() && $lojista->save()) {
                return $this->redirect(['index']);
            }
        }

        return $this->render('update-filial', [
            'model' => $model,
        ]);
    }

    /**
     * @author Igor Mageste 05/01/2016
     * @param $id integer
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdateEndereco($id)
    {
        /* @var $model null|EnderecoFilial */
        $model = EnderecoFilial::findOne($id);

        if ($model) {
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                return $this->redirect(['index']);
            } else {
                return $this->render('update-endereco', [
                    'model' => $model,
                ]);
            }
        } else {
            throw new NotFoundHttpException('Endereço não existe!');
        }
    }

    /**
     * @author Igor Mageste 05/01/2016
     * @param null|string $q
     * @param null|integer $id
     * @return array
     */
    public function actionGetCidade($q = null, $id = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        if (!is_null($q)) {
            $data = Cidade::find()->select(['cidade.id', 'cidade.nome || \' (\' || estado.sigla || \')\' AS text'])
                ->joinWith('estado')
                ->where(['like', 'lower(cidade.nome)', strtolower($q)])
                ->limit(10)->createCommand()->queryAll();
            $out['results'] = array_values($data);
        } elseif ($id > 0) {
            $out['results'] = ['id' => $id, 'text' => Cidade::findOne($id)->name];
        }

        return $out;
    }

    /**
     * Altera a senha do Comprador
     *
     * @author Igor Mageste 06/01/2016
     * @return array|string|Response
     */
    public function actionChangePassword()
    {
        $model = new MudarSenhaForm();
        $model->user_id = Yii::$app->user->id;

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post()) && $model->changePassword()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('changePassword', ['model' => $model]);
        }
    }
}
