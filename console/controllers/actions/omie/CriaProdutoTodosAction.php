<?php

namespace console\controllers\actions\omie;

use common\models\Produto;
use common\models\ValorProdutoFilial;
use console\controllers\actions\omie\Omie;
use Yii;
use yii\base\Action;
use yii\helpers\ArrayHelper;


class CriaProdutoTodosAction extends Action
{
    public function run()
    {

        echo "Criando produtos...\n\n";
        $criar_omie = new Omie(1, 1);

        echo "\n entrou \n";

        $produtos = Produto::find()->andWhere(['=', 'fabricante_id', 130])
				   //->andWhere(['>', 'id', 344891])
				   ->orderBy('id')
				   ->all();

        $arquivo_log = fopen("/var/tmp/log_omie_cria_produto_todos_".date('Y-m-d_H-i').".csv", "a");
        fwrite($arquivo_log, "produto_id;http_code;status_omie\n");

        foreach ($produtos as $k => $produto) {

	    if($k <= 46763){ continue; }

            if (substr($produto->codigo_global,0,3) != 'CX.'){

                $minValue       = ValorProdutoFilial::find()->ativo()->menorValorProduto($produto->id)->one();
                $valor_produto  = ($minValue==NULL) ? "1" : str_replace(".",",",$minValue->getValorFinal());


                //$descricao = str_replace('"',"''",substr("(".$produto->codigo_global.") ". $produto->nome,0,100));

                $descricao = substr($produto->codigo_global." ".$produto->nome,0,120);
                $descricao = str_replace(" ","%20",$descricao);

                echo "\n".$k." - ".$produto->id." - ".$descricao;
                //echo "Inserindo produtos...SP\n\n";
                $body = [
                    "call" => "IncluirProduto",
                    "app_key" => '468080198586',
                    "app_secret" => '7b3fb2b3bae35eca3b051b825b6d9f43',
                    "param" => [
                            "codigo_produto_integracao" => "PA".$produto->id,
                            "codigo"                    => "PA".$produto->id,
                            "descricao"                 => $descricao,//substr($produto->nome." (".$produto->codigo_global.")",0,100),
                            "ncm"                       => ($produto->codigo_montadora=="" ? "0000.00.00" : substr($produto->codigo_montadora,0,4).".".substr($produto->codigo_montadora,4,2).".".substr($produto->codigo_montadora,6,2)),
                            "unidade"                   => "PC",
			                 "ean"			            => $produto->codigo_barras,
                            "valor_unitario"            => round($valor_produto,2),
                            "tipoItem"                  => "99",
                            "peso_liq"                  => 0.001,
                            "peso_bruto"                => 0.001,
                            "altura"                    => round($produto->altura,2),
                            "largura"                   => round($produto->largura,2),
                            "profundidade"              => round($produto->profundidade,2),
                            "marca"                     => ($produto->fabricante_id==null) ? "Peça Agora" : $produto->fabricante->nome,
                            "recomendacoes_fiscais"     =>  [
	                        "origem_mercadoria"         => 0 ,
	                        "cupom_fiscal"              => "S"
                        ]
                    ]
                ];
                //print_r($body);
                $response = $criar_omie->cria_produto("api/v1/geral/produtos/?JSON=",$body);
                print_r($response); echo "<br><br><br>"; //die;

                if (ArrayHelper::getValue($response, 'httpCode') == 200){
		    echo " - ok";
                    fwrite($arquivo_log, $produto->id.";".ArrayHelper::getValue($response, 'httpCode').";Ok\n");
                }else{
		    echo " - erro";
                    fwrite($arquivo_log, $produto->id.";".ArrayHelper::getValue($response, 'httpCode').";".ArrayHelper::getValue($response, 'body.faultstring')."\n");
                }
            }
	    //die;
        }

        // Fecha o arquivo
        fclose($arquivo_log);
    }
}
