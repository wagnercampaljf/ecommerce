<?php

namespace frontend\controllers;

use common\models\Carrinho;
use common\models\CarrinhoProdutoFilial;
use common\models\Filial;
use Yii;
use yii\helpers\Json;
use yii\filters\AccessControl;
use yii\helpers\Url;
use common\models\EnderecoEmpresa;
use common\models\Comprador;
use yii\helpers\ArrayHelper;
use common\models\Empresa;

class CarrinhoController extends \yii\web\Controller
{
    public function actions()
    {
        //return ['get-frete' => ['class' => 'frontend\controllers\actions\common\FreteAction',]];
    }

    public function actionIndex()
    {
        //print_r(Yii::$app->session['carrinho']);die;
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
    /*
        public function actionGetFrete($cep, $returnType = 'html')
        {
            if (empty($cep)) {
                return;
            }
            if (empty(Yii::$app->session['carrinho'])) {
                Yii::$app->session->setFlash('info', 'Você ainda não tem nenhum produto no seu carrinho de compras.');

                return;
            }
            if (Yii::$app->params['getCepComprador']) {
                Yii::$app->session['cep'] = str_replace('-', '', $cep);
            }
            $carrinhoKeys = array_keys(Yii::$app->session['carrinho']);
            $produtos = \common\models\ProdutoFilial::findAll($carrinhoKeys);
            $fretes = [];
            $calculadoras = [];
            $juridica = Yii::$app->params['isJuridica']();
            foreach ($produtos as $produto) {
                $qt = Yii::$app->session['carrinho'][$produto->id];
                $fretes[$produto->filial->id]["peso"] = (isset($fretes[$produto->filial->id]["peso"])) ? $fretes[$produto->filial->id]["peso"] + ($produto->produto->peso * $qt) : $produto->produto->peso * $qt;
                $fretes[$produto->filial->id]["altura"] = (isset($fretes[$produto->filial->id]["altura"])) ? $fretes[$produto->filial->id]["altura"] + ($produto->produto->altura * $qt) : $produto->produto->altura * $qt;
                $fretes[$produto->filial->id]["largura"] = (isset($fretes[$produto->filial->id]["largura"])) ? $fretes[$produto->filial->id]["largura"] + ($produto->produto->largura * $qt) : $produto->produto->largura * $qt;
                $fretes[$produto->filial->id]["profundidade"] = (isset($fretes[$produto->filial->id]["profundidade"])) ? $fretes[$produto->filial->id]["profundidade"] + ($produto->produto->profundidade * $qt) : $produto->produto->profundidade * $qt;
                $fretes[$produto->filial->id]["valorTotal"] = (isset($fretes[$produto->filial->id]["valorTotal"])) ? $fretes[$produto->filial->id]["valorTotal"] + ($produto->valorAtual->getValorFinal($juridica) * $qt) : ($produto->valorAtual->getValorFinal($juridica) * $qt);
                $fretes[$produto->filial->id]["loja"] = $produto->filial->nome;
            }
            foreach ($fretes as $key => $frete) {
                $filial = Filial::find()->innerJoinWith([
                    'enderecoFilial',
                    'enderecoFilial.cidade',
                    'enderecoFilial.cidade.estado'
                ])->andWhere(['filial.id' => $key])->one();
                $calculadora = Yii::createObject(
                    [
                        'class' => '\frete\CalculadoraFrete',
                        'valorDeclarado' => $frete['valorTotal'],
                        'cepOrigem' => $filial->enderecoFilial->cep,
                        'filial' => $filial,
                        'cepDestino' => $cep,
                        'peso' => $frete["peso"],
                        'altura' => $frete["altura"],
                        'largura' => $frete["largura"],
                        'profundidade' => $frete["profundidade"],
                    ]
                );
                $fretes[$key]["valores"] = $calculadora->getFretes();
                $calculadoras[$frete["loja"]] = $calculadora->getFretes();
            }
            if ($returnType == 'json') {
                return Json::encode($calculadoras);
            }
            if ($returnType == 'html') {
                return $this->renderAjax('_resultadoFrete', ['fretes' => $calculadoras]);
            }
        }
    */
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
        if(!isset($comprador)){
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
