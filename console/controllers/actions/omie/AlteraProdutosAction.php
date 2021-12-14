<?php

namespace console\controllers\actions\omie;

use yii\helpers\ArrayHelper;
use common\models\ValorProdutoFilial;
use common\models\Produto;

class AlteraProdutosAction extends Action
{
    public function run()//$global_id)
    {
       
        echo "Criando produtos... São Paulo\n\n";
        $meli = new Omie(static::APP_ID, static::SECRET_KEY);

        //$filial = Filial::find()->andWhere(['=', 'id', 72])->one();
        //$produtoFilials = $filial->getProdutoFilials()->andWhere(['=','id',$global_id])->all();
        $produtos = Produto::find() //->where(" CHAR_LENGTH(codigo_global||' '||nome) > 120 ")
				    ->where(["=","codigo_global","6938175715"])
				    ->orderBy("id")
                                    ->all();
        
        foreach ($produtos as $k => $produto) {
            
	    echo "\n".$k." - ".$produto->id." - ".$produto->codigo_global." - ".$produto->nome;

	    /*if($k < 84201){
	    	continue;
	    }*/

            $body = [
                "call" => "AlterarProduto",
                "app_key" => static::APP_KEY_OMIE_SP,
                "app_secret" => static::APP_SECRET_OMIE_SP,
                "param" => [
                        "codigo_produto_integracao" => $produto->codigo_global,
                        "codigo"               	    => "PA".$produto->id,
                        "descricao"                 => substr($produto->codigo_global." ".$produto->nome,0,120),
                        "ncm"                       => ($produto->codigo_montadora=="" ? "00000000" : $produto->codigo_montadora),
                        "ean"                       => $produto->codigo_barras,
                        //"valor_unitario"            => $menor_preco,
                        //"unidade"                   => "PC",
                        //"tipoItem"                  => "99",
                        //"peso_liq"                  => $produto->peso,
                        //"peso_bruto"                => $produto->peso,
                        //"altura"                    => $produto->altura,
                        //"largura"                   => $produto->largura,
                        //"profundidade"              => $produto->profundidade,
                        //"marca"                     => $produto->fabricante->nome,
                        //"recomendacoes_fiscais"     =>  [ "origem_mercadoria" => 0 ]
                ]                
            ];
	    print_r($body);
            $response = $meli->altera_produto("api/v1/geral/produtos/?JSON=",$body);
            if(ArrayHelper::getValue($response, 'httpCode') >= 300){
                echo " - Erro (SP)";
		print_r($response);
            }
            else{
                echo " - OK (SP)";
            }
            
            $body = [
                "call" => "AlterarProduto",
                "app_key" => static::APP_KEY_OMIE_MG,
                "app_secret" => static::APP_SECRET_OMIE_MG,
                "param" => [
                    "codigo_produto_integracao" => $produto->codigo_global,
                    "codigo"                    => "PA".$produto->id,//$produto->codigo_global,
		    "descricao"                 => substr($produto->codigo_global." ".$produto->nome,0,120),
                    "ncm"                       => ($produto->codigo_montadora=="" ? "00000000" : $produto->codigo_montadora),
                    "ean"                       => $produto->codigo_barras,
                    //"valor_unitario"            => $menor_preco,
                    //"unidade"                   => "PC",
                    //"tipoItem"                  => "99",
                    //"peso_liq"                  => $produto->peso,
                    //"peso_bruto"                => $produto->peso,
                    //"altura"                    => $produto->altura,
                    //"largura"                   => $produto->largura,
                    //"profundidade"              => $produto->profundidade,
                    //"marca"                     => $produto->fabricante->nome,
                    //"recomendacoes_fiscais"     =>  [ "origem_mercadoria" => 0 ]
                ]
            ];
            $response = $meli->altera_produto("api/v1/geral/produtos/?JSON=",$body);
            if(ArrayHelper::getValue($response, 'httpCode') >= 300){
                echo " - Erro (MG)";
            }
            else{
                echo " - OK (MG)";
            }
            //die;
        }
    }
}




