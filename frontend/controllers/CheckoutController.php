<?php

namespace frontend\controllers;

use common\models\Cidade;
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
use common\models\Empresa;





class CheckoutController extends \yii\web\Controller
{
    //public $layout = 'main_checkout';

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

        $address    = EnderecoEmpresa::find()->byComprador(Yii::$app->user->id)->one();

        $this->layout = 'main_checkout';
        $empresa    = Empresa::find()->andWhere(['=', 'id', $address->empresa_id])->one();
        $comprador  = Comprador::find()->andWhere(['=', 'id', Yii::$app->user->id])->one();
        //echo "<pre>"; print_r($comprador); echo "</pre>"; die;

        return $this->render(
            'index',
            [
                'address' => $address,
                'pedido' => $pedido,
                'empresa' => $empresa,
                'model' => $address,
                'formPagamento' => $formPagamento,
                'filiais' => $filiais,



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

            $this->layout = 'main';

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



    public function actionUpdateEndereco($endereco_empresa_id, $telefone = null, $cep = null, $logradouro = null, $numero = null, $complemento = null, $cidade_id = null, $bairro = null){

            $endereco_empresa = EnderecoEmpresa::find()->andWhere(['=','id',$endereco_empresa_id])->one();

            $retorno = [];

            if($cep == "" || $cep == null){
                $retorno = [
                    'mensagem'  =>"CEP não preenchido.",
                    'erro'      => true,
                ];
                return Json::encode($retorno);
            }
            if($logradouro == "" || $logradouro == null){
                $retorno = [
                    'mensagem'  =>"Logradouro não preenchido.",
                    'erro'      => true,
                ];
                return Json::encode($retorno);
            }
            if($cidade_id == 0 || $cidade_id == null){
                $retorno = [
                    'mensagem'  =>"Cidade não preenchida.",
                    'erro'      => true,
                ];
                return Json::encode($retorno);
            }
            if($bairro == "" || $bairro == null){
                $retorno = [
                    'mensagem'  =>"Bairro não preenchido.",
                    'erro'      => true,
                ];
                return Json::encode($retorno);
            }
            if($numero == "" || $numero == null){
                $retorno = [
                    'mensagem'  =>"Número não preenchido.",
                    'erro'      => true,
                ];
                return Json::encode($retorno);
            }


            if($endereco_empresa){
                $endereco_empresa->cep          = $cep;
                $endereco_empresa->logradouro   = $logradouro;
                $endereco_empresa->numero       = $numero;
                $endereco_empresa->complemento  = $complemento;
                $endereco_empresa->cidade_id    = $cidade_id;
                $endereco_empresa->bairro       = $bairro;
                //echo "<pre>"; print_r($endereco_empresa); echo "</pre>";
                if($endereco_empresa->save()){
                    $retorno = [
                            'mensagem'  =>"Endereço alterado.",
                            'erro'      => false,
                        ];
                }
                else{
                    $retorno = [
                        'mensagem'  =>"Endereço não alterado.",
                        'erro'      => true,
                    ];
                }
            }
            else{
                $retorno = [
                    'mensagem'  =>"Endereço não encontrado.",
                    'erro'      => true,
                ];
            }

        return Json::encode($retorno);
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












   /* public function actionUpdateAddres()
    {
        $address = EnderecoEmpresa::find()->byComprador(Yii::$app->user->id)->one();

        $comprador = Yii::$app->user->identity;

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

            }
        }
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
    }*/







}
