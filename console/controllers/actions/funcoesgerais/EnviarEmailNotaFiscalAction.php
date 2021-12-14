<?php

namespace console\controllers\actions\funcoesgerais;

use backend\models\NotaFiscal;
use backend\models\NotaFiscalProduto;
use common\models\Produto;
use Yii;

class EnviarEmailNotaFiscalAction extends Action
{
    public function run($chave)
    {
        $modelNotaFiscal = NotaFiscal::findOne(['chave_nf' => $chave]);
        $modelNotaFiscalProduto = NotaFiscalProduto::findAll(['nota_fiscal_id' => $modelNotaFiscal->id]);
        $produtos = '';

        foreach ($modelNotaFiscalProduto as $produto) {

            $markup = Yii::$app->db->createCommand("select margem from markup_detalhe md 
                inner join markup_mestre mm on md.markup_mestre_id = mm.id 
                where ($produto->valor_real_produto ::float between valor_minimo and valor_maximo) and mm.e_markup_padrao = true
                order by mm.id desc 
                limit 1")->queryScalar();

            $nome = '';
            $codigo_global = '';
            if (isset($produto->pa_produto)) {
                $pa = substr($produto->pa_produto, 2);
                $dados_produto = Produto::findOne($pa);
                $codigo_global = $dados_produto->codigo_global;
                $nome = $dados_produto->nome;
            } else {
                $nome = $produto->descricao;
            }

            $produtos .= "\n
                Cód. Forn.: " . $produto->codigo_produto_original . "
                Cód. Global: " . $codigo_global . "
                Descrição: " . $nome . "
                Quantidade: " . $produto->qtd_comercial . "
                Valor: " . $produto->valor_real_produto . "
                PA: " . $produto->pa_produto . "
                Markup: " . $markup;
        }

        $emails = 'compraestoque.pecaagora@gmail.com,compras.pecaagora@gmail.com';

        $emails_destinatarios = explode(",", $emails);
        var_dump(\Yii::$app->mailer->compose()
            ->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->name])
            ->setTo($emails_destinatarios)
            ->setSubject("[Entrada de NF] nº $modelNotaFiscal->numero_nf - $modelNotaFiscal->fornecedor")
            ->setTextBody($produtos)
            ->send());
    }
}
