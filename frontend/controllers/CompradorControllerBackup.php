<?php

namespace frontend\controllers;

use common\mail\AsyncMailer;
use common\models\Empresa;
use common\models\Grupo;
use common\models\EnderecoEmpresa;
use Yii;
use common\models\Comprador;
use yii\base\ErrorException;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use frontend\controllers\MailerController;

/**
 * CompradorController implements the CRUD actions for Comprador model.
 */
class CompradorController extends Controller
{
    public $defaultAction = 'create';

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }


    /**
     * Displays a single Comprador model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Comprador model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($tipoEmpresa = 'juridica')
    {
        $comprador = new Comprador();
        $empresa = new Empresa(['scenario' => $tipoEmpresa] );
        $grupo = new Grupo();
        $EnderecoEmpresa = new EnderecoEmpresa();
        $erro = false;

        if (!empty(Yii::$app->request->post())) {

            $comprador->cpf = preg_replace('/[^\0-9]/', '', $comprador->cpf);
            $empresa->documento = preg_replace('/[^\0-9]/', '', $empresa->documento);

            $comprador->username = $comprador->email;
            //coloca os valores padroes
            $comprador->dt_criacao = date("Y-m-d h:i:s");
            $comprador->ativo = true;
            $comprador->nivel_acesso_id = 1;
            $empresa->juridica = true;

            //carrega os valores do comprador
            $comprador->load(Yii::$app->request->post());

            //carrega os valores da empresa e endereco - no caso de pessoa fisica somente o telefone
            $empresa->load(Yii::$app->request->post());
            $EnderecoEmpresa->load(Yii::$app->request->post());

            //Preenche os valores da empresa para pessoa fisica
            if ($tipoEmpresa == 'fisica') {

                $empresa->nome = $comprador->nome;
                $empresa->razao = $comprador->nome;
                $empresa->documento = $comprador->cpf;
                $empresa->email = $comprador->email;
                $empresa->juridica = false;
                $empresa->id_tipo_empresa = 1;
            }


            //monta o grupo da empresa
            $grupo->nome = $empresa->nome;
            $grupo->razao = $empresa->razao;

            //validacao dos campos
            if (!$comprador->validate()) {
                $erro = true;
            }
            if (!$empresa->validate()) {
                $erro = true;
            }
            if (!$grupo->validate()) {
                $erro = true;
            }
            if (!$EnderecoEmpresa->validate()) {
                $erro = true;
            }
//            echo "<pre>";
//            var_dump($empresa);
//            echo "</pre>";
//            die("oi");
            if (!$erro) {

                $transaction = EnderecoEmpresa::getDb()->beginTransaction();
                try {
                    $grupo->save(false);
                    //coloca o id do grupo na empresa
                    $empresa->grupo_id = $grupo->id;
                    $empresa->save(false);
                    //coloca id_empresa do comprador
                    $comprador->empresa_id = $empresa->id;
                    $comprador->setPassword($comprador->password);
                    $comprador->save(false);
                    $EnderecoEmpresa->empresa_id = $empresa->id;
                    $EnderecoEmpresa->save(false);
                    $transaction->commit();

                    $params = array(
                        'id' => $comprador->id,
                    );
                    //AsyncMailer::sendMail($params, AsyncMailer::CRIACAO_COMPRADOR);
                    Yii::$app->asyncMailer->sendMail($params, MailerController::CRIACAO_COMPRADOR);

                    \Yii::$app->session->setFlash('success', 'Você foi cadastrado com sucesso.');

                    return $this->redirect(Yii::$app->urlManager->createUrl('site/login'));
                } catch (\Exception $e) {
                    $transaction->rollBack();
                    throw $e;
                }
            }
        }

        return $this->render('create', [
            'comprador' => $comprador,
            'empresa' => $empresa,
            'grupo' => $grupo,
            'EnderecoEmpresa' => $EnderecoEmpresa,
            'tipoEmpresa' => $tipoEmpresa
        ]);

    }

    /**
     * Finds the Comprador model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Comprador the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected
    function findModel(
        $id
    ) {
        if (($comprador = Comprador::findOne($id)) !== null) {
            return $comprador;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionGetEndereco($cep)
    {
        $cep = str_replace('-', '', $cep);
        $cep = substr($cep, 0, 8);
        try {
            $end = file_get_contents('https://viacep.com.br/ws/' . $cep . '/json/');
        } catch (ErrorException $e) {
            return Json::encode(['error' => true]);

        }
        $endereco = Json::decode($end);
        if (!empty($endereco)) {
            return Json::encode($endereco);
        }

        return $endereco;
    }

}
