<?php

namespace console\controllers\actions\funcoesgerais;

use Yii;
use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;

class AnaliseProdutosLNGAction extends Action
{
    public function run(){
        
        echo "INÍCIO da rotina de analise de produtos LNG: \n\n";
        
        $LinhasArray = Array();
        //$file = fopen('/var/tmp/lng_vericacao_12-09-2019.csv', 'r');
	$file = fopen('/var/tmp/lng_atualizado_28-08-19.csv', 'r');
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
        }
        fclose($file);
        
	/*if (file_exists("/var/tmp/log_lng_vericacao_12-09-2019.csv")){
            unlink("/var/tmp/log_lng_vericacao_12-09-2019.csv");
        }
        
        $arquivo_log = fopen("/var/tmp/log_lng_vericacao_12-09-2019.csv", "a");*/

	if (file_exists("/var/tmp/log_lng_atualizado_28-08-19.csv")){
            unlink("/var/tmp/log_lng_atualizado_28-08-19.csv");
        }

        $arquivo_log = fopen("/var/tmp/log_lng_atualizado_28-08-19.csv", "a");
        // Escreve no log
        fwrite($arquivo_log, "produto_filial_id;codigo_fabricante;codigo_global;filial_id;filial_nome;quantidade;dt_ultimo_valor;valor;fabricante_id;fabriante_nome;status\n");
        
        $produtos_lng = ProdutoFilial::find()   ->joinWith('produto')
                                                ->andWhere(['like','produto.codigo_fabricante','L'])
                                                ->andWhere(['<>','filial_id',43])
                                                //->andWhere(['produto_filial.id' => [137760,137784]])
                                                ->all();
        foreach($produtos_lng as $k => $produto_lng){
            if (substr($produto_lng->produto->codigo_fabricante,0,1) != "L"){
                continue;
            }
            
            echo "\n".$k." - ".$produto_lng->id." - ".$produto_lng->produto->codigo_fabricante." - ".substr($produto_lng->produto->codigo_fabricante,0,1);
            
            $status = "Produto não encontrado";
            foreach ($LinhasArray as &$linhaArray){
                $codigo_fabricante = $linhaArray[0];
                if($codigo_fabricante == $produto_lng->produto->codigo_fabricante){
                    $status = "Produto encontrado";
                    break;
                }
            }
            
            $valor_produto_filial = ValorProdutoFilial::find()  ->andWhere(['=','produto_filial_id',$produto_lng->id])
                                                                ->orderBy('dt_inicio DESC')
                                                                ->one();
            
            $valor      = null;
            $dt_inicio  = null;
            if($valor_produto_filial){
                $valor      = $valor_produto_filial->valor;
                $dt_inicio  = $valor_produto_filial->dt_inicio;
            }

	    $fabricante_nome = "";
	    if(isset($produto_lng->produto->fabricante->nome)){
		$fabricante_nome = $produto_lng->produto->fabricante->nome;
	    }
            
            fwrite($arquivo_log, $produto_lng->id.";".$produto_lng->produto->codigo_fabricante.";".$produto_lng->produto->codigo_global.";".$produto_lng->filial_id.";".$produto_lng->filial->nome.";".$produto_lng->quantidade.";".$dt_inicio.";".$valor.";".$produto_lng->produto->fabricante_id.";".$fabricante_nome.";".$status."\n");
        }
        
        // Fecha o arquivo
        fclose($arquivo_log);
                
        echo "\n\nFIM da rotina de analise de produtos LNG!";
    }
}
