<?php

namespace console\controllers\actions\omie;

use common\models\Filial;
use common\models\ProdutoFilial;
use Livepixel\MercadoLivre\Meli;
use Yii;
use yii\helpers\ArrayHelper;
use console\models\SkyhubClient;
use yii\helpers\Json;
use common\models\ValorProdutoFilial;

class CriaProdutoAction extends Action
{ 
    public function run($global_id)
    {
       
        echo "Criando produtos...\n\n";
        $meli = new Omie(static::APP_ID, static::SECRET_KEY);
        $filial = Filial::find()->andWhere(['=', 'id', 43])->one();

        echo "\n entrou \n";
        
        $produtoFilial = ProdutoFilial::find()->andWhere(['=','id',$global_id])->one();

        $minValue       = ValorProdutoFilial::find()->ativo()->menorValorProduto($produtoFilial->produto->id)->one();
        $valor_produto  = ($minValue==NULL) ? "1" : str_replace(".",",",$minValue->getValorFinal());
        $body = [
            "call" => "IncluirProduto",
            "app_key" => static::APP_KEY_OMIE_SP,
            "app_secret" => static::APP_SECRET_OMIE_SP,
            "param" => [
                    "codigo_produto_integracao" => "",
                    "codigo"                    => $produtoFilial->produto->codigo_global,
                    "descricao"                 => $produtoFilial->produto->nome.' '.$produtoFilial->produto->codigo_global,
                    "ncm"                       => ($produtoFilial->produto->codigo_montadora=="" ? "00000000" : $produtoFilial->produto->codigo_montadora),
                    "valor_unitario"            => $valor_produto,
                    "unidade"                   => "PC",
                    "tipoItem"                  => "99",
                    "peso_liq"                  => $produtoFilial->produto->peso,
                    "peso_bruto"                => $produtoFilial->produto->peso,
                    "altura"                    => $produtoFilial->produto->altura,
                    "largura"                   => $produtoFilial->produto->largura,
                    "profundidade"              => $produtoFilial->produto->profundidade,
                    "marca"                     => $produtoFilial->produto->fabricante->nome,
                    "recomendacoes_fiscais"     =>  [ "origem_mercadoria" => 0 ]
            ]                
        ];
        $response = $meli->cria_produto("api/v1/geral/produtos/?JSON=",$body);
        
        $body = [
            "call" => "IncluirProduto",
            "app_key" => static::APP_KEY_OMIE_MG,
            "app_secret" => static::APP_SECRET_OMIE_MG,
            "param" => [
                "codigo_produto_integracao" => "",
                "codigo"                    => $produtoFilial->produto->codigo_global,
                "descricao"                 => $produtoFilial->produto->nome.' '.$produtoFilial->produto->codigo_global,
                "ncm"                       => ($produtoFilial->produto->codigo_montadora=="" ? "00000000" : $produtoFilial->produto->codigo_montadora),
                "valor_unitario"            => $valor_produto,
                "unidade"                   => "PC",
                "tipoItem"                  => "99",
                "peso_liq"                  => $produtoFilial->produto->peso,
                "peso_bruto"                => $produtoFilial->produto->peso,
                "altura"                    => $produtoFilial->produto->altura,
                "largura"                   => $produtoFilial->produto->largura,
                "profundidade"              => $produtoFilial->produto->profundidade,
                "marca"                     => $produtoFilial->produto->fabricante->nome,
                "recomendacoes_fiscais"     =>  [ "origem_mercadoria" => 0 ]
            ]
        ];
        $response = $meli->cria_produto("api/v1/geral/produtos/?JSON=",$body);
        //echo "123::";var_dump($response);
    }
}



