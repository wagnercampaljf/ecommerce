<?php
namespace frontend\controllers;

use app\models\Newsletter;
use common\models\AnoModelo;
use common\models\Imagens;
use common\models\LoginForm;
use common\models\Lojista;
use common\models\Marca;
use common\models\Modelo;
use common\models\Produto;
use frontend\models\ContactForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use Yii;
use yii\base\InvalidParamException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\redactor\actions\ImageManagerJsonAction;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use common\models\MercadoLivreOrder;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
//                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionIndex()
    {
        Yii::$app->user->setReturnUrl(Url::to(['site/index']));

        return $this->render(
            'index'
        );
    }

    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            //return $this->redirect(Url::to(['site/index']));
	    if (empty(Yii::$app->session['carrinho'])) {
                return $this->redirect(Url::to(['site/index']));
            } else {
                return $this->redirect(['checkout/']);
		//return $this->redirect(['carrinho/update-address-confirmar?from=checkout/']);
            }
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            Yii::$app->session->set('locale', Yii::$app->user->identity->empresa->enderecoEmpresa->cidade_id);

            //return $this->goBack(Url::to(['site/index']));
	    if (empty(Yii::$app->session['carrinho'])) {
                return $this->redirect(Url::to(['site/index']));
            } else {
                return $this->redirect(['carrinho/update-address-confirmar?from=checkout/']);
            }
        } else {
            return $this->render(
                'login',
                [
                    'model' => $model,
                ]
            );
        }
    }

    public function actionCadastrar()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->redirect(Yii::$app->urlManager->baseUrl);
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack(Yii::$app->urlManager->baseUrl);
        } else {
            return $this->render(
                'login',
                [
                    'model' => $model,
                ]
            );
        }
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->redirect(Url::to(['site/index']));
    }

    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash(
                    'success',
                    'Thank you for contacting us. We will respond to you as soon as possible.'
                );
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending email.');
            }

            return $this->refresh();
        } else {
            return $this->render(
                'contact',
                [
                    'model' => $model,
                ]
            );
        }
    }

    public function actionSobre()
    {
        return $this->render('about');
    }

    public function actionNossaloja()
    {
        return $this->render('nossaloja');
    }

    public function actionTests()
    {
        ini_set('memory_limit', '1024M');
        return $this->render('tests');
    }

    public function actionPoliticas()
    {
        return $this->render('politicas');
    }

    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->signup()) {
                if (Yii::$app->getUser()->login($user)) {
                    return $this->goHome();
                }
            }
        }

        return $this->render(
            'signup',
            [
                'model' => $model,
            ]
        );
    }

    public function actionEsqueci()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->getSession()->setFlash('success', 'Por favor, cheque seu email para mais informações');

                return $this->redirect(Yii::$app->urlManager->baseUrl);
            } else {
                Yii::$app->getSession()->setFlash(
                    'error',
                    'Email não encontrado'
                );
            }
        }

        return $this->render(
            'esqueciMinhaSenha',
            [
                'model' => $model,
            ]
        );
    }

    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->getSession()->setFlash('success', 'Senha salva com sucesso.');

            return $this->redirect(Url::to(['site/index']));
        }

        return $this->render(
            'resetPassword',
            [
                'model' => $model,
            ]
        );
    }

    /**
     * Recebe via post o id da categoria via POST e retorna um json contendo os pares id:label das marcas
     * @author Vinicius Schettino 02/12/2014
     */
    public function actionMarcasXCategoria($placeholder = true)
    {
        if ($id = Yii::$app->request->post('id', null) == null) {
            return Json::encode(($placeholder ? ['' => 'Marca'] : ['']));
        }
        $marcas = Marca::find()->byCategoria(Yii::$app->request->post('id', 0))->ordemAlfabetica()->all();
        $marcas = (ArrayHelper::map($marcas, 'id', 'label'));
        !$placeholder ?: $marcas[''] = 'Marca';

        return Json::encode($marcas);
    }

    /**
     * Recebe via post o id da categoria e o id da marca via POST e retorna
     * um json contendo os pares id:label dos modelos
     * @author Vinicius Schettino 02/12/2014
     */
    public function actionModelosXCategoriaXMarca($placeholder = true)
    {
        if (($id = Yii::$app->request->post('id', null)) == null ||
            ($categoria = Yii::$app->request->post('categoria', null)) == null
        ) {
            return Json::encode(($placeholder ? ['' => 'Modelo'] : []));
        }
        $modelos = Modelo::find()->byCategoria($categoria)->byMarca($id)->ordemAlfabetica()->all();
        $modelos = (ArrayHelper::map($modelos, 'id', 'label'));
        !$placeholder ?: $modelos[''] = 'Modelo';

        return Json::encode($modelos);
    }

    /**
     * Recebe via post o id do modelo via POST e retorna um json contendo os pares id:label dos anos compatíveis
     * @author Vinicius Schettino 02/12/2014
     */
    public function actionAnosXModelo($placeholder = true)
    {
        if (($id = Yii::$app->request->post('id', null)) == null) {
            return Json::encode(($placeholder ? ['' => 'Ano'] : []));
        }
        $anos = AnoModelo::find()->byModelo($id)->ordemAlfabetica()->all();
        $anos = (ArrayHelper::map($anos, 'id', 'label'));
        !$placeholder ?: $anos[''] = 'Ano';

        return Json::encode($anos);
    }

    /**
     * Altera a localização padrão
     * @author Igor Mageste 08/10/2015
     */
    public function actionSetLocale()
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->session->set(
                'locale',
                Yii::$app->request->post(
                    'locale',
                    Yii::$app->getLocation->getCidade()
                )
            );
        }
    }

    public function actionNewsletterCreate()
    {
        $model = new Newsletter();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('success', 'Seu E-mail foi cadastrado em nossa Lista com Sucesso!');

            return $this->redirect(['index']);
        } else {
            Yii::$app->getSession()->setFlash('error', array_pop($model->getErrors('email')));

            return $this->redirect(['index'], [
                'model' => $model,
            ]);
        }
    }

    public function actionGetLink()
    {
        $produto_id = Yii::$app->request->get('produto_id');
        $ordem = Yii::$app->request->get('ordem');

        $ordem = (is_null($ordem)) ? 1 : $ordem;
        $imagem = Imagens::find()->andWhere(['produto_id' => $produto_id])->andWhere(['ordem' => $ordem])->one();

        header('Content-Type: image/jpg');

        if (!empty($imagem)){
            echo base64_decode(stream_get_contents( $imagem->imagem));
        }else{
            $src = yii::$app->urlManagerFrontEnd->baseUrl . '/frontend/web/assets/img/produtos/no-image.png';
            echo Html::img($src);
        }
    }

    public function actionGetLinkTemporario()
    {
        $produto_id = Yii::$app->request->get('produto_id');
        $ordem = Yii::$app->request->get('ordem');
        
        $imagem = Imagens::find()->andWhere(['produto_id' => $produto_id])->andWhere(['ordem' => $ordem])->one();
        
        if($imagem){
	    $caminho    = "https://www.pecaagora.com/site/get-link?produto_id=".$imagem->produto_id."&ordem=".$imagem->ordem;
            copy($caminho, '/var/www/html/frontend/web/assets/img/imagens_temporarias/'.$imagem->id.".webp" );
            
            $resultado = shell_exec('cd /var/www/html/frontend/web/assets/img/imagens_temporarias/ ; mogrify -format jpg '.$imagem->id.'.webp');
	    echo yii::$app->urlManagerFrontEnd->baseUrl . '/frontend/web/assets/img/imagens_temporarias/'.$imagem->id.".jpg"; die;
	    echo base64_decode(stream_get_contents(base64_encode(file_get_contents(yii::$app->urlManagerFrontEnd->baseUrl . '/frontend/web/assets/img/imagens_temporarias/'.$imagem->id.".jpg"))));
            //$src = yii::$app->urlManagerFrontEnd->baseUrl . '/frontend/web/assets/img/imagens_temporarias/'.$imagem->id.".jpg";
            echo Html::img($src);
        }
        else{
            $src = yii::$app->urlManagerFrontEnd->baseUrl . '/frontend/web/assets/img/produtos/no-image.png';
            echo Html::img($src);
        }
    }

    public function actionImag()
    {
        $produto_id = Yii::$app->request->get('pid');
        $ordem = Yii::$app->request->get('ordem');

        $ordem = (is_null($ordem)) ? 1 : $ordem;
        $imagem = Imagens::find()->andWhere(['produto_id' => $produto_id])->andWhere(['ordem' => $ordem])->one();

        header('Content-Type: image/jpeg');

        if (!empty($imagem)){
            echo base64_decode(stream_get_contents( $imagem->imagem));
        }else{
            $src = yii::$app->urlManagerFrontEnd->baseUrl . '/frontend/web/assets/img/produtos/no-image.png';
            echo Html::img($src);
        }
    }

    public function actionGetLinkSemLogo(){
        $produto_id = Yii::$app->request->get('produto_id');
        $ordem = Yii::$app->request->get('ordem');

        $ordem = (is_null($ordem)) ? 1 : $ordem;
        $imagem = Imagens::find()->andWhere(['produto_id' => $produto_id])->andWhere(['ordem' => $ordem])->one();

        header('Content-Type: image/jpeg');

        if (!empty($imagem->imagem_sem_logo)){
            echo base64_decode(stream_get_contents( $imagem->imagem_sem_logo));
        }else{
            $src = yii::$app->urlManagerFrontEnd->baseUrl . '/frontend/web/assets/img/produtos/no-image.png';
            echo Html::img($src);
        }
    }



    public function actionGetLinkLojista()
    {
        $id = Yii::$app->request->get('id');
        $lojista = Lojista::findOne($id);
        if (empty($lojista)) {
            return "Lojista Inválido";
        }
        header('Content-Type: image/jpeg');
        echo base64_decode(stream_get_contents($lojista->imagem));
    }

    public function actionPedidoml(){

        $arrayOrder =   Yii::$app->request->post();
        /*[   "user_id"           => 1234,
            "resource"          => "/orders/731867397",
            "topic"             => "orders",
            "received"          => "2011-10-19T16:38:34.425Z",
            "application_id"    => 14529,
            "sent"              => "2011-10-19T16:40:34.425Z",
            "attempts"          => 0
        ];*/
        echo "\n\n"; print_r($arrayOrder); echo "\n\n";

//	$arrayOrder = json_decode($arrayOrder,true);

	echo "\n\n"; print_r($arrayOrder); echo "\n\n";

	Yii::$app->response->statusCode = 200;

        //$orderML = new MercadoLivreOrder();
        //$orderML->user_id           = $arrayOrder["user_id"];
        //$orderML->resource          = $arrayOrder["resource"];
        //$orderML->topic	            = $arrayOrder["topic"];
        //$orderML->received	    = $arrayOrder["received"];
        //$orderML->application_id    = $arrayOrder["application_id"];
        //$orderML->sent 		    = $arrayOrder["sent"];
        //$orderML->attempts          = $arrayOrder["attempts"];
        //$orderML->save();
	//print_r($arrayOrder["attempts"]); echo "\n\n";

	return "blablabla";
    }

}
