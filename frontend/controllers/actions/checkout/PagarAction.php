<?php

namespace frontend\controllers\actions\checkout;

use common\models\FormPagamento;
use common\models\Pedido;
use common\models\PedidoProdutoFilial;
use common\models\ProdutoFilial;
use common\models\Transportadora;
use common\models\ValorProdutoMenorMaior;
use Inacho\CreditCard;
use vendor\iomageste\Moip\Moip;
use Yii;
use yii\base\Action;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\HttpException;

class PagarAction extends Action
{
    private $formas_pagamentos = [
        'moip_creditCard' => 1,
        'moip_boleto' => 2
    ];

    public function run()
    {
        $fretes = Yii::$app->request->get('radio_frete');

        $formPagamento = new FormPagamento();
        $formPagamento->setAttributes(Yii::$app->request->get('FormPagamento'));
        $transaction = Pedido::getDb()->beginTransaction();
        try {
            $pedidos = [];

            $this->createPedidos(Yii::$app->request->get('Pedido'), $formPagamento, $fretes, $pedidos);
            
            $pagamento = Yii::createObject([
                'class' => 'vendor\iomageste\forma_pagamento\Conector',
                'conector' => $formPagamento->method,
                'comprador' => Yii::$app->user->getIdentity(),
                'pedidos' => $pedidos,
                'formPagamento' => $formPagamento
            ])->getFormaPagamento();
            
            //echo "Processando Pagamento ..."; die;
            $transaction->commit();

            unset(Yii::$app->session['carrinho']);
            $this->controller->redirect([
                'concluido',
                'pedidos' => array_keys($pedidos),
                'method' => $formPagamento->method,
                'token' => $pagamento->multipagamento->getId()
            ]);
        } catch (HttpException $r) {
            $transaction->rollBack();
            throw $r;
        }
    }

    private function atualizarEstoque($produto_filial_id, $qtd)
    {
        if ($produtoFilial = ProdutoFilial::findOne($produto_filial_id)) {
            $produtoFilial->quantidade -= $qtd;
            $produtoFilial->save(false);
        }
    }

    private function createPedidos($dados, $formPagamento, $fretes, &$pedidos)
    {
        foreach ($dados as $filial_id => $attributes) {
            $pedido = new Pedido();

            $pedido->attributes = $attributes;
            $pedido->comprador_id = Yii::$app->user->id;
            $pedido->filial_id = $filial_id;
            $pedido->forma_pagamento_id = ArrayHelper::getValue(
                $this->formas_pagamentos,
                $formPagamento->method
            );
            $pedido->valor_frete = $fretes[$filial_id];
            $pedido->email_enderecos = "entregasp.pecaagora@gmail.com, compras.pecaagora@gmail.com";
            $pedido->email_assunto = "{de} Pedido Site {num_pedido} ({codigo_fabricante} * {quantidade})";

            $pedido->email_texto = "DESTACAR O ST RECOLHIDO ANTERIORMENTE EM INFORMAÇÕES ADICIONAIS E TAMBÉM NO XML DA NOTA, CASO CONTRÁRIO A MESMA SERÁ RECUSADA.

                    Cód.: {codigo}
                    Descrição: {descricao}
                    Quantidade: {quantidade}
                    Valor: R$ {valor}
                    Observação: {observacao}
                    NCM: {ncm}
                    PA{pa}
                    
                    Envio: Carmópolis de Minas, 963, Vila Maria.
                    
                    Atenciosamente,
                    Peça Agora
                    Site: https://www.pecaagora.com/
                    E-mail: compras.pecaagora@gmail.com Setor de Compras:(32)3015-0023 Whatsapp:(32)988354007
                    Skype: pecaagora";

            if ($pedido->save(false)) {

                $this->createPedidoProdutoFilials($attributes['pedidoProdutoFilials'], $formPagamento, $pedido);
                $pedidos[$pedido->id] = $pedido;
            }
        }
    }

    private function createPedidoProdutoFilials($dados, $formPagamento, $pedido)
    {
        foreach ($dados as $produto_filial_id => $attributes) {
            $pedido_produtoFilial = new PedidoProdutoFilial();
            $pedido_produtoFilial->attributes = $attributes;

            $produto = ProdutoFilial::findOne($produto_filial_id);
            $menor_valor = ValorProdutoMenorMaior::find()->Where(['produto_id' => $produto->produto_id])->one();
            $pedido_produtoFilial->valor = $menor_valor->menor_valor;

            $pedido_produtoFilial->produto_filial_id = $produto_filial_id;
            $pedido_produtoFilial->link('pedido', $pedido);
            $this->atualizarEstoque($produto_filial_id, $attributes['quantidade']);
        }
    }
}
