<?php

namespace frontend\controllers;

use common\models\Comprador;
use common\models\EnderecoEmpresa;
use common\models\Filial;
use common\models\FormPagamento;
use common\models\Pedido;
use common\models\PedidoProdutoFilial;
use common\models\ProdutoFilial;
use common\models\StatusPedido;
use Faker\Provider\cs_CZ\DateTime;
use frontend\controllers\actions\common\FreteAction;
use Inacho\CreditCard;
use vendor\iomageste\Moip\Moip;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\filters\AccessControl;
use yii\helpers\Url;

class CheckoutController extends \yii\web\Controller
{
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

    public function actions()
    {
        return [
            'get-frete' => [
                'class' => 'frontend\controllers\actions\common\FreteAction',
            ],
            'pagar' => [
                'class' => 'frontend\controllers\actions\checkout\PagarAction',
            ]
        ];
    }

    public function actionIndex()
    {
        if (empty(Yii::$app->session['carrinho'])) {
            return $this->redirect(['carrinho/']);
	    //return $this->redirect(['index/']);
        }

        $filiais = array();
        foreach (Yii::$app->session['carrinho'] as $produto_filial_id => $qtd) {
            $produtoFilial = ProdutoFilial::find()->byIds($produto_filial_id)->one();
            $filiais[$produtoFilial->filial_id][] = $produtoFilial;
        }

        $pedido = new Pedido();
        $formPagamento = new FormPagamento();
        $error = [];

        if (Yii::$app->request->post('FormPagamento') && Yii::$app->request->post('Pedido')) {
            $formPagamento->load(Yii::$app->request->post());
            $valid = $formPagamento->validate();

            foreach (Yii::$app->request->post('Pedido') as $filial_id => $dados) {
                $pedido->scenario = 'checkout';
                $pedido->attributes = $dados;
                if (!$pedido->validate(array_keys($dados))) {
                    $error = array_merge($error, $pedido->getErrors());
                    $valid = false;
                }
            }
            if ($valid) {
                return $this->redirect(array_merge(['pagar'], Yii::$app->request->post()));
            } else {
                $pedido->addErrors($error);
            }
        }

        $address = EnderecoEmpresa::find()->byComprador(Yii::$app->user->id)->one();

        return $this->render(
            'index',
            [
                'pedido' => $pedido,
                'formPagamento' => $formPagamento,
                'filiais' => $filiais,
                'address' => $address
            ]
        );
    }

    public function actionGetParcelas($valor)
    {
        if (Yii::$app->request->isAjax) {
            $options = '';
            foreach (CreditCard::$parcelamentoMOIP as $nrParcelas => $juros) {
                $valor_prazo = 0;
                foreach (Yii::$app->request->get('valores') as $v) {
                    $valor_prazo += $v;
                }
                $taxMoip = Moip::TAXMOIP + ($valor_prazo * CreditCard::taxaMoip($nrParcelas));
                $valor_prazo += ($valor_prazo - $taxMoip) * CreditCard::jurosMoip($nrParcelas);
                $valor_parcela = $valor_prazo / $nrParcelas;
                $label = $nrParcelas . "x de " . Yii::$app->formatter->asCurrency($valor_parcela) . " Total: " . Yii::$app->formatter->asCurrency($valor_prazo) . " ($juros%)";
                $options .= Html::tag('option', $label, ['value' => $nrParcelas]);
            }
            echo $options;
        }
    }

    public function actionConcluido()
    {
        if ($ids = Yii::$app->request->get('pedidos')) {
	    //echo "Processando pagamento ...."; die;
            $pedidos = Pedido::findAll($ids);

            return $this->render('concluido', ['pedidos' => $pedidos]);
        } else {
	    //echo "Processando Pagamento ..."; die;
            return $this->redirect(['carrinho/']);
        }
    }

    public function actionConcluidofake()
    {
        $pedidos = Pedido::find()->one();

        return $this->render('concluido', ['pedidos' => $pedidos]);
    }
}
