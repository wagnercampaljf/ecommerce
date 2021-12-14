<?php
namespace common\models;

use Yii;
use yii\base\Model;
use common\models\EnderecoEmpresa;
use common\mail\AsyncMailer;
use frontend\controllers\MailerController;

/**
 * Login form
 */
class LoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = true;

    private $_user = false;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }

    /**
     * @inheritdoc
     * @author Vinicius Schettino 02/12/2014
     */
    public function attributeLabels()
    {
        return [
            'username' => 'Email',
            'password' => 'Senha',
            'rememberMe' => 'Mantenha-me conectado',
        ];
    }



    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        //print_r($attribute);die;
        if (!$this->hasErrors()) {
            if (!$this->getUser()->validatePassword($this->password)) {
                $this->addError($attribute, 'E-mail ou senha incorretos.');
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return boolean whether the user is logged in successfully
     */
    public function login()
    {
        $user = $this->getUser();
        if ($user == null) {
            if (\Yii::$app->request->post()['LoginForm']['facebookLogin']) {
                return $this->createUserFromFacebookLogin();

            } else {
                $this->addError("username", 'Usuário não cadastrado.');
                \Yii::$app->session->setFlash('warning', 'Usuário inexistente. Favor realizar o cadastro.');
                //\Yii::$app->response->redirect(\Yii::$app->urlManager->baseUrl . '/comprador/create?tipoEmpresa=fisica');
            }
       /* } else if (\Yii::$app->request->post()['LoginForm']['facebookLogin'] && ($user->empresa->id_tipo_empresa == 1)) {
            return Yii::$app->user->login($user, $this->rememberMe ? 3600 * 24 * 30 : 0);*/
        } else if ($this->validate()) {
            return Yii::$app->user->login($user, $this->rememberMe ? 3600 * 24 * 30 : 0);
        }
        return false;
    }

    public function createUserFromFacebookLogin(){

        $comprador = new Comprador();
        $empresa = new Empresa(['scenario' => 'fisica'] );
        $grupo = new Grupo();
        $EnderecoEmpresa = new EnderecoEmpresa();
        $erro = false;

        if (!empty(Yii::$app->request->post())) {


            //coloca os valores padroes
            $comprador->dt_criacao = date("Y-m-d h:i:s");
            $comprador->ativo = true;
            $comprador->nivel_acesso_id = 1;

            //coloca os valores da request
            $comprador->cpf = preg_replace('/[^\0-9]/', '', $comprador->cpf);
            $comprador->email = \Yii::$app->request->post()['LoginForm']['username'];
            $comprador->nome = \Yii::$app->request->post()['LoginForm']['name'];
            $comprador->password = \Yii::$app->request->post()['LoginForm']['password'];
//            $comprador->repeat_password =  \Yii::$app->request->post()['LoginForm']['password'];


            //carrega valores da empresa
            $empresa->nome = $comprador->nome;
            $empresa->razao = $comprador->nome;
            $empresa->documento = $comprador->cpf;
            $empresa->juridica = false;
            $empresa->id_tipo_empresa = 1;

            $password = $comprador->password;

            //carrega os valores da empresa e endereco - no caso de pessoa fisica somente o telefone
            $empresa->load(Yii::$app->request->post());
            $EnderecoEmpresa->load(Yii::$app->request->post());

            //Seta email da empresa
            $empresa->email = $comprador->email;

            //monta o grupo da empresa
            $grupo->nome = $empresa->nome;
            $grupo->razao = $empresa->razao;

//            var_dump($comprador);


            //validacao dos campos
            if (!$comprador->validate()) {
                $erro = true;
//                var_dump($comprador->errors);
            }
            if (!$empresa->validate()) {
                $erro = true;
//                var_dump($empresa->errors);
            }

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
                    AsyncMailer::sendMail($params, AsyncMailer::CRIACAO_COMPRADOR);
                    Yii::$app->asyncMailer->sendMail($params, MailerController::CRIACAO_COMPRADOR);


                    // ACTION DE ENVIAR E-MAIL TRANSFERIDA DE frontend/controllers/MailerController
                    \Yii::$app->mailer->compose('criacao_comprador', ['nome' => $comprador->nome, 'email' => $comprador->email])
                        ->setFrom(Yii::$app->params['supportEmail'])
                        ->setTo($comprador->email)
                        ->setSubject('Cadastro Confirmado')
                        ->send();

                    \Yii::$app->session->setFlash('success', 'Você foi cadastrado com sucesso.');

//                  ## Tenta Login após cadastro

                    $model = new LoginForm();

                    $model->password = $password;
                    $model->username = $comprador->email;

                    if ($model->login()) {
                        Yii::$app->session->set('locale', Yii::$app->user->identity->empresa->enderecoEmpresa->cidade_id);

                        //return $this->redirect(Yii::$app->urlManager->createUrl('site/index'));
                        if (empty(Yii::$app->session['carrinho'])) {
                            return \Yii::$app->response->redirect(Yii::$app->urlManager->createUrl('site/index'));
                        } else {
                            return \Yii::$app->response->redirect(Yii::$app->urlManager->createUrl('site/index'));
                        }
                    }

//                  ## Se o login falha, redireciona para a pagina de login

                    return \Yii::$app->response->redirect(Yii::$app->urlManager->createUrl('site/login'));

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
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = Comprador::findByUsername($this->username);
        }

        return $this->_user;
    }
}
