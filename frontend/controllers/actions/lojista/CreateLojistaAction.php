<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace frontend\controllers\actions\lojista;

use common\mail\AsyncMailer;
use common\models\Banco;
use common\models\Empresa;
use common\models\EnderecoEmpresa;
use common\models\EnderecoFilial;
use common\models\Filial;
use common\models\FilialTransportadora;
use common\models\Lojista;
use common\models\Usuario;
use frontend\controllers\MailerController;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\ServerErrorHttpException;
use yii\rest\Action;

/**
 * CreateAction implements the API endpoint for creating a new model from the given data.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class CreateLojistaAction extends Action
{
    /**
     * @var string the scenario to be assigned to the new model before it is validated and saved.
     */
    public $scenario = Model::SCENARIO_DEFAULT;
    /**
     * @var string the name of the view action. This property is need to create the URL when the model is successfully created.
     */
    public $viewAction = 'view';
    CONST RETIRADA_NA_LOJA_ID = 4;
    CONST RETIRADA_NA_LOJA_DIAS = 1;

    /**
     * Creates a new model.
     * @return \yii\db\ActiveRecordInterface the model newly created
     * @throws ServerErrorHttpException if there is any error when creating the model
     */
    public function run()
    {
        if (!Yii::$app->session['moipAccountId']) {
            $this->controller->redirect(['intro']);
        }

        $lojista = new Lojista();
        $usuario = new Usuario();
        $filial = new Filial();
        $enderecoFilial = new EnderecoFilial();
        $erro = false;
        $bancos = Banco::find()->asArray()->all();
        $bancos = ArrayHelper::map($bancos, 'id', 'nome');
        $usuario->load(Yii::$app->request->post());

        if (!empty(Yii::$app->request->post())) {
            //coloca os valores padroes
            $usuario->dt_criacao = date("Y-m-d h:i:s");
            $usuario->ativo = false;
            $usuario->nivel_acesso_id = 1;

            $usuario->cpf = preg_replace('/[^\0-9]/', '', $usuario->cpf);
            $filial->documento = preg_replace('/[^\0-9]/', '', $filial->documento);
            //carrega os valores da empresa e endereco - no caso de pessoa fisica somente o telefone
            $usuario->username = $usuario->email;
            $filial->load(Yii::$app->request->post());
            $enderecoFilial->load(Yii::$app->request->post());
            $filial->juridica = true;
            $lojista->load(Yii::$app->request->post());
            $lojista->nome = $filial->nome;
            $lojista->razao = $filial->razao;
            $lojista->dt_criacao = $usuario->dt_criacao;
            $lojista->documento = $filial->documento;
            $lojista->aprovado = false;
            $lojista->ativo = false;
            $lojista->imagem = "teste";
            $lojista->juridica = true;
            $filial->lojista_id = $lojista->id;
            //validacao dos campos
            if (!$usuario->validate()) {
                $erro = true;
            }
            if (!$filial->validate()) {
                //var_dump($filial->errors);
                //exit();
                $erro = true;
            }
            if (!$lojista->validate()) {

                $erro = true;
            }
            if (!$enderecoFilial->validate()) {
                //var_dump($enderecoFilial->errors);
                //exit();
                $erro = true;
            }
            if ($erro) {

            }
            if (!$erro) {
                $transaction = EnderecoFilial::getDb()->beginTransaction();
                try {
                    $lojista->save(false);
                    $filial->lojista_id = $lojista->id;
                    $filial->banco_id = 1;
                    $filial->save(false);
                    $usuario->filial_id = $filial->id;
                    $usuario->setPassword($usuario->password);
                    $usuario->save(false);
                    $enderecoFilial->filial_id = $filial->id;
                    $enderecoFilial->save(false);
                    $filialTransportadora = new FilialTransportadora();
                    $filialTransportadora->filial_id = $filial->id;
                    $filialTransportadora->transportadora_id = static::RETIRADA_NA_LOJA_ID;
                    $filialTransportadora->dias_postagem = static::RETIRADA_NA_LOJA_DIAS;
                    $filialTransportadora->save();
                    $transaction->commit();

                    $params = array(
                        'id' => $usuario->id,
                    );

                    //AsyncMailer::sendMail($params, AsyncMailer::CRIACAO_LOJISTA);

                    Yii::$app->asyncMailer->sendMail($params, MailerController::CRIACAO_LOJISTA);
                    Yii::$app->session->setFlash('success', 'Você foi cadastrado com sucesso.');

                    unset(Yii::$app->session['moipAccountId']);

                    return $this->controller->redirect(Yii::$app->urlManager->createUrl('lojista/web/site/login'));
                } catch (\Exception $e) {
                    $transaction->rollBack();
                    throw $e;
                }
            }
        }

        return $this->controller->render('create', [
            'lojista' => $lojista,
            'usuario' => $usuario,
            'filial' => $filial,
            'bancos' => $bancos,
            'enderecoFilial' => $enderecoFilial,
        ]);
    }
}
