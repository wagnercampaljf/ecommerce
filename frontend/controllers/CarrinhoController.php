<?php

namespace frontend\controllers;

use common\models\Carrinho;
use common\models\CarrinhoProdutoFilial;
use Yii;
use yii\filters\AccessControl;
use common\models\EnderecoEmpresa;
use common\models\Comprador;
use yii\helpers\ArrayHelper;
use common\models\Empresa;
use common\models\ProdutoFilial;

class CarrinhoController extends \yii\web\Controller
{
    public function actions()
    {
    }

    public function actionIndex()
    {

        return $this->render('index');
    }

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['add-carrinho'],
                'rules' => [
                    [
                        'actions' => ['add-carrinho'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }
    /**
     * Salva fretes do carrinho na session
     * Ajax dara continuidade para salvar o carrinho no banco de dados
     *
     * @param null $fretes
     * @param $nomeCarrinho
     * @return string
     *
     * @since 0.1
     * @author Vitor Horta 17/03/2015
     */


    public function actionAdicionarProdutoCarrinho($id, $qte = 1, $replace = false)
    {
        $carrinho = Yii::$app->session["carrinho"];
        $pf = ProdutoFilial::find()->with(['filial', 'produto', 'valorProdutoFilials'])->andWhere(['id' => $id])->one();
        if (!is_null($pf)) {
            $qte_total = $qte;
            (isset($carrinho[$pf->id]) && !$replace) ? $qte_total += $qte : $qte_total = $qte;
            if ($pf->quantidade < $qte) {
                $qte_total = $pf->quantidade;

                return 0;
            }
            $carrinho[$pf->id] = $qte_total;
            Yii::$app->session["carrinho"] = $carrinho;

            return $this->redirect(\Yii::$app->urlManager->baseUrl . '/carrinho');
        } else {
            return 0;
        }
    }

    public function actionSalvarCarrinho($fretes = null, $nomeCarrinho)
    {
        $fretes = json_decode($fretes);
        $f = Yii::$app->session["frete"];

        if (empty($nomeCarrinho)) {
            $nomeCarrinho = Carrinho::nomeCarrinho();
        }

        foreach ($fretes as $idFilial => $frete) {
            if (!is_null($frete)) {
                $f[$idFilial] = $frete;
            }
        }
        Yii::$app->session["frete"] = $f;

        return $nomeCarrinho;
    }

    /**
     * Salva carrinho e produtos associados no banco de dados
     *
     * @param $nomeCarrinho
     * @since 0.1
     * @author Vitor Horta 17/03/2015
     */

    public function actionAddCarrinho($nomeCarrinho)
    {
        $comprador = \Yii::$app->user->getIdentity();
        $produtosCarrinho = \Yii::$app->session['carrinho'];

        $carrinho = new Carrinho();
        $carrinho->comprador_id = $comprador->getId();
        $erro = false;
        $carrinho->chave = $nomeCarrinho;
        $carrinho->dt_criacao = date("Y-m-d h:i:s");
        if ($carrinho->validate()) {
            $transaction = CarrinhoProdutoFilial::getDb()->beginTransaction();
            try {
                $carrinho->save(false);
                foreach ($produtosCarrinho as $idProdutoFilial => $quantidade) {
                    $carrinho_produto_filial = new CarrinhoProdutoFilial();
                    $carrinho_produto_filial->produto_filial_id = $idProdutoFilial;
                    $carrinho_produto_filial->data_inclusao = date("Y-m-d h:i:s");
                    $carrinho_produto_filial->quantidade = $quantidade;
                    $carrinho_produto_filial->link('carrinho', $carrinho);
                }
                $transaction->commit();
            } catch (\Exception $e) {
                $transaction->rollBack();
                throw $e;
            }
        }

        $this->redirect(\Yii::$app->urlManager->baseUrl . '/minhaconta/carrinhos');
    }

    /**
     * Remove produto do carrinho
     *
     * @param $id
     * @return string
     * @author Vitor Horta 26/03/2015
     * @since 0.1
     */
    public function actionRemoverProduto($id)
    {
        $carrinho = \Yii::$app->session['carrinho'];
        unset($carrinho[$id]);

        \Yii::$app->session['carrinho'] = $carrinho;

        return json_encode(
            [
                'carrinho_count' => count(\Yii::$app->session["carrinho"]),
            ]
        );
    }

    public function actionUpdateAddressConfirmar()
    {
        $address = EnderecoEmpresa::find()->byComprador(Yii::$app->user->id)->one();

        $comprador = Yii::$app->user->identity;
        if (!isset($comprador)) {
            return $this->redirect(\Yii::$app->urlManager->baseUrl . '/site/login?from=checkout');
        }

        $compradorController = Comprador::findOne($comprador->getId());

        $empresa_id = ArrayHelper::getValue($comprador, 'empresa_id');

        $empresa = Empresa::findOne(['id' => $empresa_id]);

        // Caso CPF não esteja cadastrado
        if (isset(Yii::$app->request->post()['Comprador']['cpf'])) {

            $compradorController->cpf = Yii::$app->request->post()['Comprador']['cpf'];
            if ($compradorController->validate()) {
                $compradorController->save();

                if ($address->load(Yii::$app->request->post())) {
                    $address->save();

                    $empresa->telefone  = ArrayHelper::getValue(Yii::$app->request->post(), 'Empresa.telefone');
                    $empresa->documento = Yii::$app->request->post()['Comprador']['cpf'];
                    $empresa->save();

                    if (isset(Yii::$app->request->get()['from']) && Yii::$app->request->get()['from'] == 'checkout') {
                        return $this->redirect(\Yii::$app->urlManager->baseUrl . '/checkout');
                    }
                }
            } else {
                \Yii::$app->session->setFlash('warning', 'CPF já cadastrado.');
                $address->load(Yii::$app->request->post());
                return $this->render('addressConfirmar', ['model' => $address, 'comprador' => $comprador, 'empresa' => $empresa]);
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

        return $this->render('addressConfirmar', ['model' => $address, 'comprador' => $compradorController, 'empresa' => $empresa]);
    }
}
