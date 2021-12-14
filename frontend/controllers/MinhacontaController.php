<?php

namespace frontend\controllers;

use common\mail\Autoloader;
use common\mail\Task;
use common\models\Carrinho;
use common\models\CarrinhoProdutoFilial;
use common\models\Cidade;
use common\models\Comprador;
use common\models\EnderecoEmpresa;
use common\models\Pedido;
use common\models\PedidoProdutoFilial;
use common\models\Empresa;
use frontend\models\MudarSenhaForm;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;


class MinhacontaController extends \yii\web\Controller
{

    public $layout = 'minhaContaLayout';

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


    public function actionDados()
    {
        $pagina = 'dados';
        $comprador = \Yii::$app->user->getIdentity();
        $model = Comprador::findOne($comprador->getId());

        return $this->render('dados', [
            'pagina' => $pagina,
            'model' => $model,
        ]);
    }

    public function actionIndex()
    {
        $pagina = 'home';

        return $this->render('home', [
            'pagina' => $pagina,
            'pageData' => '',
        ]);

    }

    /**
     * Renderiza pagina de pedido individual
     *
     * @param $id
     * @return string
     * @throws \yii\web\BadRequestHttpException Quando id é inválido
     * @throws \yii\web\ForbiddenHttpException  Quando id do pedido não pertence usuário logado
     * @since 0.1
     * @author Vitor Horta 03/12/2015
     */
    public function actionPedido($id)
    {
        $comprador = \Yii::$app->user->getIdentity();
        if (empty($id) || !is_numeric($id)) {
            throw new \yii\web\BadRequestHttpException('Parâmetro inválido: id');
        }

        $pedido = Pedido::find()->where(['id' => $id])->andWhere(['comprador_id' => $comprador->getId()])->one();

        if (is_null($pedido)) {
            throw new \yii\web\ForbiddenHttpException('Sem permissão');
        }

        $dataProvider = new ActiveDataProvider([
            'query' => PedidoProdutoFilial::find()->with([
                'produtoFilial',
                'produtoFilial.produto',
            ])->where(['pedido_id' => $id]),
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => ['attributes' => ['']],
        ]);


        return $this->render('pedido', [
            'dataProvider' => $dataProvider,
            'pedido' => $pedido,
        ]);
    }

    /**
     * Renderiza pagina de pedidos
     *
     * @return string
     * @since 0.1
     * @author Vitor Horta 03/12/2015
     */
    public function actionPedidos()
    {
        $comprador = \Yii::$app->user->getIdentity();

        $dataProvider = new ActiveDataProvider([
            'query' => Pedido::find()->where(['comprador_id' => $comprador->id])->with([
                'statusAtual',
                'statusAtual.tipoStatus'
            ])->orderBy('dt_referencia DESC'),
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => ['attributes' => ['']],
        ]);

        return $this->render('pedidos', ['dataProvider' => $dataProvider,]);
    }

    /**
     * Renderiza pagina de carrinhos
     *
     * @return string
     * @since 0.1
     * @author Vitor Horta 03/20/2015
     */
    public function actionCarrinhos()
    {
        $comprador = \Yii::$app->user->getIdentity();

        $dataProvider = new ActiveDataProvider([
            'query' => Carrinho::find()->where(['comprador_id' => $comprador->id])->orderBy('dt_criacao DESC'),
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => ['attributes' => ['']],
        ]);

        return $this->render('carrinhos', ['dataProvider' => $dataProvider]);
    }

    /**
     * Restaura carrinho colocando seus produtos e respectivas quantidades na session
     * Redireciona para pagina de carrinho
     *
     * @return string
     * @since 0.1
     * @author Vitor Horta 03/20/2015
     */
    public function actionRestaurarCarrinho($id)
    {
        $produtos_carrinho = CarrinhoProdutoFilial::find()->where(['carrinho_id' => $id])->all();
        $produtosCarrinhoSession = \Yii::$app->session['carrinho'];

        foreach ($produtos_carrinho as $produto_carrinho) {
            $produtosCarrinhoSession[$produto_carrinho->produto_filial_id] = $produto_carrinho->quantidade;
        }

        \Yii::$app->session['carrinho'] = $produtosCarrinhoSession;

        $this->redirect(\Yii::$app->urlManager->baseUrl . '/carrinho');
    }

    /**
     * Renderiza página de carirnho
     *
     *
     * @param $id
     * @return string
     * @throws \yii\web\BadRequestHttpException Erro caso id seja inválido
     * @throws \yii\web\ForbiddenHttpException  Erro caso id não pertença ao usuário logado
     * @since .1
     * @author Vitor Horta 03/20/2015
     */
    public function actionCarrinho($id)
    {
        if (empty($id) || !is_numeric($id)) {
            throw new \yii\web\BadRequestHttpException('Parâmetro inválido: id');
        }

        $comprador = \Yii::$app->user->getIdentity();

        $carrinho = Carrinho::find()->where(['comprador_id' => $comprador->id])->andWhere(['id' => $id])->one();

        if (is_null($carrinho)) {
            throw new \yii\web\ForbiddenHttpException('Sem permissão');
        }

        $dataProvider = new ActiveDataProvider([
            'query' => CarrinhoProdutoFilial::find()->where(['carrinho_id' => $id])->with([
                'produtoFilial',
                'produtoFilial.produto',
                'produtoFilial.filial',
                'produtoFilial.valorProdutoFilials'
            ]),
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => ['attributes' => ['']],
        ]);


        return $this->render('carrinho', ['dataProvider' => $dataProvider, 'carrinho' => $carrinho]);
    }

    /**
     * Deleta carrinho
     *
     * @param $id
     * @return Response
     * @throws \Exception
     * @throws \yii\web\ForbiddenHttpException Erro caso id nao pertença ao usuário logado
     */
    public function actionDelete($id)
    {
        $comprador = \Yii::$app->user->getIdentity();

        $carrinho = Carrinho::find()->where(['id' => $id])->andWhere(['comprador_id' => $comprador->id])->one();

        if (is_null($carrinho)) {
            throw new \yii\web\ForbiddenHttpException('Sem permissão');
        }

        $carrinho->delete();

        return $this->redirect(['carrinhos']);
    }

    /**
     * Altera os dados do Comprador
     *
     * @author Igor Mageste
     * @since 11/12/2015
     * @return string
     */
    public function actionUpdate()
    {
        /* @var $model Comprador */
        $model = Yii::$app->user->identity;
        $model->scenario = 'update';

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['dados']);
        } else {
            return $this->render('update', ['model' => $model]);
        }
    }

    /**
     * Altera a senha do Comprador
     *
     * @author Igor Mageste
     * @since 11/12/2015
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
            return $this->redirect(['dados']);
        } else {
            return $this->render('changePassword', ['model' => $model]);
        }
    }

    /**
     * @author Igor Mageste
     * @since 11/12/2015
     * @return string
     */
    public function actionUpdateAddress()
    {
        $address = EnderecoEmpresa::find()->byComprador(Yii::$app->user->id)->one();

        $comprador = Yii::$app->user->identity;

        $compradorController = Comprador::findOne($comprador->getId());

        $empresa_id = ArrayHelper::getValue($comprador, 'empresa_id');

        $empresa = Empresa::findOne(['id' => $empresa_id]);


        // Caso CPF não esteja cadastrado
        if (isset(Yii::$app->request->post()['Comprador']['cpf'])) {
            $compradorController->cpf = Yii::$app->request->post()['Comprador']['cpf'];
            if ($compradorController->validate() ) {
                $compradorController->save();

                if ($address->load(Yii::$app->request->post())) {
                    $address->save();

		    $empresa->telefone = ArrayHelper::getValue(Yii::$app->request->post(), 'Empresa.telefone');
		    $empresa->documento = Yii::$app->request->post()['Comprador']['cpf'];
		    $empresa->save();

                    if (isset(Yii::$app->request->get()['from']) && Yii::$app->request->get()['from'] == 'checkout') {
                        return $this->redirect(\Yii::$app->urlManager->baseUrl . '/checkout');
                    }
                }

            } else {
                \Yii::$app->session->setFlash('warning', 'CPF já cadastrado.');
                $address->load(Yii::$app->request->post());
                return $this->render('address', ['model' => $address, 'comprador' => $comprador, 'empresa' => $empresa]);
            }
        } else {
            if ($address->load(Yii::$app->request->post())) {

                $address->save();

		$empresa->telefone = ArrayHelper::getValue(Yii::$app->request->post(), 'Empresa.telefone');
                $empresa->save();

                if (isset(Yii::$app->request->get()['from']) && Yii::$app->request->get()['from'] == 'checkout') {
                    return $this->redirect(\Yii::$app->urlManager->baseUrl . '/checkout');
                }
            }
        }


        return $this->render('address', ['model' => $address, 'comprador' => $compradorController, 'empresa' => $empresa]);
    }

    public function actionGetCidade($q = null, $id = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        if (!is_null($q)) {
            $query = Cidade::find()->select([
                'cidade.id',
                '"cidade"."nome" || \' - \' ||"estado"."sigla" as text'
            ])->joinWith(['estado'])->byNome($q)->limit(10)->createCommand()->queryAll();

            $out['results'] = array_values($query);
        } elseif ($id > 0) {
            $out['results'] = ['id' => $id, 'text' => Cidade::findOne($id)->nome];
        }

        return $out;
    }
}
