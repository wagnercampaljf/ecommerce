<?php

namespace console\controllers\actions\omie;

use common\models\Produto;
use common\models\ValorProdutoFilial;
use console\controllers\actions\omie\Omie;
use Yii;
use yii\base\Action;
use yii\helpers\ArrayHelper;


class CriaProdutosContaDuplicadaAction extends Action
{
    public function run()
    {
       
        echo "Criando produtos...\n\n";
        $criar_omie = new Omie(1, 1);

        echo "\n entrou \n";
        
        $produtos = Produto::find() //->andWhere(['=', 'fabricante_id', 123])
                                    //->andWhere(['=', 'codigo_global', '446786'])
                                    //->andWhere(['id' => [246620,246803]])
                                    ->addOrderBy('id')
                                    ->all();
        
        if (file_exists("/var/tmp/log_omie_cria_produtos_dib_todos.csv")){
            unlink("/var/tmp/log_omie_cria_produtos_dib_todos.csv");
        }
        
        $arquivo_nome = "/var/tmp/log_omie_cria_produtos_dib_todos_".date("Y-m-d_H-i-s").".csv";
        $arquivo_log = fopen($arquivo_nome, "a");
        //Escreve no log
        fwrite($arquivo_log, "produto_id;http_code;status_omie\n");
 
        foreach ($produtos as $k => $produto) {
         
            echo "\n".$k." - ".$produto->id." - ".$produto->nome;

	    if($k<=146256){
		echo " - pular";
	    	continue;
	    }
            
            if (substr($produto->codigo_global,0,3) != 'CX.'){
                
                $minValue       = ValorProdutoFilial::find()->ativo()->menorValorProduto($produto->id)->one();
                $valor_produto  = ($minValue==NULL) ? "1" : str_replace(".",",",$minValue->getValorFinal());
                
                $descricao = str_replace('"',"''",substr("".$produto->codigo_global." ".$produto->nome,0,100));
                $body = [
                    "call" => "IncluirProduto",
                    "app_key" => '1017311982687',
                    "app_secret" => '78ba33370fac6178da52d42240591291',
                    "param" => [
                            "codigo_produto_integracao" => "PA".$produto->id,
                            "codigo"                    => "PA".$produto->id,
                            "descricao"                 => str_replace(" ","%20",$descricao),
                            "ncm"                       => ($produto->codigo_montadora=="" ? "0000.00.00" : substr($produto->codigo_montadora,0,4).".".substr($produto->codigo_montadora,4,2).".".substr($produto->codigo_montadora,6,2)),
                            "valor_unitario"            => round($valor_produto,2),
                            "unidade"                   => "PC",
                            "tipoItem"                  => "99",
                            "peso_liq"                  => round($produto->peso,2),
                            "peso_bruto"                => round($produto->peso,2),
                            "altura"                    => round($produto->altura,2),
                            "largura"                   => round($produto->largura,2),
                            "profundidade"              => round($produto->profundidade,2),
                            "marca"                     => ($produto->fabricante_id==null) ? "Peça Agora" : $produto->fabricante->nome,
                            "recomendacoes_fiscais"     =>  [ "origem_mercadoria" => 0 ]
                        ]
                    ];
                //print_r($body);
                $response = $criar_omie->cria_produto("api/v1/geral/produtos/?JSON=",$body);
                print_r($response); 
                
                if (ArrayHelper::getValue($response, 'httpCode') == 200){
                    echo " - OK";
                    fwrite($arquivo_log, $produto->id.";".ArrayHelper::getValue($response, 'httpCode').";Ok\n");
                }else{
                    echo " - Erro";
                    fwrite($arquivo_log, $produto->id.";".ArrayHelper::getValue($response, 'httpCode').";Error\n");
                }           
            }

            /*if($k >= 20){
                die;
            }*/
        }
        
        // Fecha o arquivo
        fclose($arquivo_log);
    }
}



