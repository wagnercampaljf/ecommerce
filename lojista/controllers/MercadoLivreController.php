<?php

namespace lojista\controllers;

use common\models\ProdutoFilial;
use common\models\UploadForm;
use common\models\ValorProdutoFilial;
use Livepixel\MercadoLivre\Meli;
use lojista\controllers\actions\estoque;
use lojista\models\ProdutoFilialSearch;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * EstoqueController implements the CRUD actions for ProdutoFilial model.
 */
class MercadoLivreController extends Controller
{

      const APP_ID = '3029992417140266';
    //const APP_ID = '3029992417140266';
      const SECRET_KEY = '1DjEfVPwkjOmqowN1ALujTJpgQh5DGdh';
    //const SECRET_KEY = '1DjEfVPwkjOmqowN1ALujTJpgQh5DGdh';
   //const APP_ID = '7346648451576903';
    //const SECRET_KEY = 'ps9zV3qhUfFRZiwdBK0kPnmQFyf7PXWp';
    const REDIRECT_URI = 'https://www.pecaagora.com/lojista/web/mercado-livre/callback';
    const MELI_TOKEN_SESSION_KEY = 'meliAccessToken';
    const MELI_EXPIRES_SESSION_KEY = 'meliExpiresIn';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all ProdutoFilial models.
     * @return mixed
     */
    public function actionIndex()
    {
        $meliAccessToken = Yii::$app->session->get(static::MELI_TOKEN_SESSION_KEY);
        $expiresIn = Yii::$app->session->get(static::MELI_EXPIRES_SESSION_KEY);
        $filial = Yii::$app->user->identity->filial;
	//print_r($filial);
        if (isset($meliAccessToken)) {
            if ($expiresIn + time() + 1 < time()) {
                $this->refreshToken($filial->refresh_token_meli);
            }

            return $this->render('index', ['accessToken' => $meliAccessToken]);
        }

        if (isset($filial->refresh_token_meli)) {
            $this->refreshToken($filial->refresh_token_meli);
        } else {
            return $this->render('login');
        }
    }

    public function actionLogin()
    {
        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
	//echo "<pre>"; var_dump($meli); echo "</pre>";
        $redirectUrl = $meli->getAuthUrl(static::REDIRECT_URI);
	//echo "<pre>"; var_dump($redirectUrl); echo "</pre>"; die;
        $this->redirect($redirectUrl);
    }

    public function actionCallback()
    {
	//echo "<pre>"; print_r($_GET); echo "</pre>"; die;

        if (isset($_GET['code'])) {
            $meli = new Meli(static::APP_ID, static::SECRET_KEY);
	    //echo "<pre>"; var_dump($meli); echo "</pre>"; die;
            $user = $meli->authorize($_GET['code'], static::REDIRECT_URI);
            $this->setSession($user['body']->access_token, $user['body']->expires_in);
            $filial = Yii::$app->user->identity->filial;
            $filial->refresh_token_meli = $user['body']->refresh_token;
            $filial->save();
            $this->redirect(['index']);
        } else {
            echo "Token nao atualizado";
        }
    }

    public function actionMercadoview()
    {
//        $produto = ProdutoFilial::find()->where(['produto_id' => 12021])->one();
        $produto = ProdutoFilial::find()->where(['produto_id' => 2])->one();

        return $this->render('produto', [
            'produto' => $produto
        ]);
    }

    public function setSession($accessToken, $expires_in)
    {
        Yii::$app->session->set(static::MELI_TOKEN_SESSION_KEY, $accessToken);
        Yii::$app->session->set(static::MELI_EXPIRES_SESSION_KEY, $expires_in);
    }

    public function actionValidate()
    {
        ini_set('max_execution_time', 300);

        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        $meliAccessToken = Yii::$app->session->get(static::MELI_TOKEN_SESSION_KEY);

        $produtosFilial = ProdutoFilial::find()->joinWith(['produto'])
            ->innerJoin([
                'valor_produtoFilial' => ValorProdutoFilial::find()->ativo(),
            ], '"valor_produtoFilial"."produto_filial_id" = "produto_filial"."id"')
            ->andWhere(['produto_filial.filial_id' => Yii::$app->user->identity->filial_id])->produtoDisponivel()->all();

        foreach ($produtosFilial as $produtoFilial) {
            $subcategoriaMeli = $produtoFilial->produto->subcategoria->meli_id;
            if (!isset($subcategoriaMeli)) {
                continue;
            }

            $page = $this->renderPartial('produto', ['produto' => $produtoFilial]);
//            $imagem = Yii::$app->urlManager->hostInfo . Yii::$app->urlManagerFrontEnd->baseUrl . '/site/' . $produtoFilial->produto->codigo_global . '/img.jpg';
            $imagem = '169.55.78.61' . Yii::$app->urlManagerFrontEnd->baseUrl . '/site/' . $produtoFilial->produto->codigo_global . '/img.jpg';
//            $imagem = 'http://www.ikea.com/PIAimages/09379_PE085867_S5.JPG';
            $imagem = '169.55.78.61/images/foto.JPG';
            $body = [
                'title' => 'teste',
                "category_id" => $subcategoriaMeli,
                "listing_type_id" => "bronze",
                "currency_id" => "BRL",
                "price" => 9999,
                "available_quantity" => $produtoFilial->quantidade,
                "seller_custom_field" => $produtoFilial->id,
                "condition" => "new",
                "description" => $page,
                "pictures" => [
                    ["source" => $imagem],
                    ["source" => $imagem]
                ]
            ];
            var_dump($meli->post("items?access_token=" . $meliAccessToken,
                $body));
            exit();
        }
    }

    public function refreshToken($token)
    {
        $meli = new Meli(static::APP_ID, static::SECRET_KEY);

        $user = $meli->refreshAccessToken($token);
        if (isset($user['body']->access_token)) {
            $this->setSession($user['body']->access_token, $user['body']->expires_in);
            $this->redirect(['index']);
        }
    }
}
