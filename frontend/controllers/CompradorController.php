<?php

namespace frontend\controllers;

use common\mail\AsyncMailer;
use common\models\Empresa;
use common\models\Grupo;
use common\models\EnderecoEmpresa;
use Yii;
use common\models\Comprador;
use common\models\LoginForm;
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
//echo (1);
            $comprador->cpf = preg_replace('/[^\0-9]/', '', $comprador->cpf);
            $empresa->documento = preg_replace('/[^\0-9]/', '', $empresa->documento);
//echo "("..")";
            $comprador->username = $comprador->email;
            //coloca os valores padroes
            $comprador->dt_criacao = date("Y-m-d h:i:s");
            $comprador->ativo = true;
            $comprador->nivel_acesso_id = 1;
            $empresa->juridica = true;

            //carrega os valores do comprador
            $comprador->load(Yii::$app->request->post());

            $password = $comprador->password;

            //carrega os valores da empresa e endereco - no caso de pessoa fisica somente o telefone
            $empresa->load(Yii::$app->request->post());
            $EnderecoEmpresa->load(Yii::$app->request->post());

            //Preenche os valores da empresa para pessoa fisica
            if ($tipoEmpresa == 'fisica') {

                $empresa->nome = $comprador->nome;
                $empresa->razao = $comprador->nome;
                $empresa->documento = $comprador->cpf;
                $empresa->juridica = false;
                $empresa->id_tipo_empresa = 1;
            }
            //Seta email da empresa
            $empresa->email = $comprador->email;

            //monta o grupo da empresa
            $grupo->nome = $empresa->nome;
            $grupo->razao = $empresa->razao;


            //validacao dos campos
            if (!$comprador->validate()) {
                $erro = true;
                var_dump('1');
            }
            if (!$empresa->validate()) {
                $erro = true;
                var_dump('2');
            }
//            if (!$grupo->validate()) {
//                $erro = true;
//                var_dump('3');
//            }

            // REMOVIDO PARA ALTERA????O DOS CADASTROS DE USU??RIO
//            if (!$EnderecoEmpresa->validate()) {
//                $erro = true;
//                var_dump('4');
//            }

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

//                    $params = array(
//                        'id' => $comprador->id,
//                    );
                    //AsyncMailer::sendMail($params, AsyncMailer::CRIACAO_COMPRADOR);
//                    Yii::$app->asyncMailer->sendMail($params, MailerController::CRIACAO_COMPRADOR);


                    // ACTION DE ENVIAR E-MAIL TRANSFERIDA DE frontend/controllers/MailerController
                    \Yii::$app->mailer->compose('criacao_comprador', ['nome' => $comprador->nome, 'email' => $comprador->email])
                        ->setFrom(Yii::$app->params['supportEmail'])
                        ->setTo($comprador->email)
                        ->setSubject('Cadastro Confirmado')
                        ->send();

                    \Yii::$app->session->setFlash('success', 'Voc?? foi cadastrado com sucesso.');

//                  ## Tenta Login ap??s cadastro

                    $model = new LoginForm();

                    $model->password = $password;
                    $model->username = $comprador->email;

                    if ($model->login()) {
                        Yii::$app->session->set('locale', Yii::$app->user->identity->empresa->enderecoEmpresa->cidade_id);

                        //return $this->redirect(Yii::$app->urlManager->createUrl('site/index'));
			if (empty(Yii::$app->session['carrinho'])) {
                            return $this->redirect(Yii::$app->urlManager->createUrl('site/index'));
                        } else {
                            //return $this->redirect(['minhaconta/update-address?from=checkout']);//$this->redirect(['checkout/']);
			    return $this->redirect(['carrinho/update-address-confirmar?from=checkout/']);
                        }
                    }

//                  ## Se o login falha, redireciona para a pagina de login

                    return $this->redirect(Yii::$app->urlManager->createUrl('site/login'));

                } catch (\Exception $e) {
                    // Adiciona feedback caso o cadastro falhe
                    \Yii::$app->session->setFlash('warning', 'Falha ao realizar o cadastro. Tente novamente.');
                    $transaction->rollBack();
                    throw $e;
                }
            } else {
//                var_dump($erro);
                // Adiciona feedback caso o cadastro falhe
                \Yii::$app->session->setFlash('warning', 'Falha ao realizar o cadastro. Tente novamente.');
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
