<?php

namespace console\controllers\actions\funcoesgerais;

use Yii;
use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;
use common\models\Filial;

class AtualizarPrecosDibCaixaAction extends Action
{
    public function run(){
        
        echo "INÍCIO da rotina de atualizacao do preço: \n\n";
        
        $LinhasArray = Array();
        //$file = fopen("/var/tmp/dib_estoque_15-08-19.csv", 'r');
        //$file = fopen("/var/tmp/ESTOQUE_DIB_26-08-2019_precificado.csv", 'r');
        //$file = fopen("/var/tmp/Lista_Geral_DIB_09-09-19_precificado_caixa.csv", 'r');
	$file = fopen("/var/tmp/estoque_dib_06-12-2019_precificado_correto_precificado_caixa.csv", 'r');
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
        }
        fclose($file);
        
        $produtos_dib_caixa = ProdutoFilial::find() ->joinWith('produto')
                                                    ->andWhere(['like','nome','CAIXA COM 10 '])
                                                    ->all();
        
        foreach ($produtos_dib_caixa as $i => $produtos_dib_caixa){
            echo "\n".$i." - ".$produtos_dib_caixa->produto->id." - ".$produtos_dib_caixa->produto->codigo_fabricante." - ".$produtos_dib_caixa->produto->nome;
            
            foreach ($LinhasArray as $i => &$linhaArray){
                
                if ($i <= 0){
                    continue;
                }
                
                if ($produtos_dib_caixa->produto->codigo_fabricante != "CX.D".$linhaArray[0]){
                    continue;
                }
                
                echo "\n".$i." - ".$linhaArray[0]." - ".$linhaArray[3]." - ".$linhaArray[9];
                
                echo " - encontrado";
                    
                $quantidade = $linhaArray[6];
                if($linhaArray[3] == "0,00" || $linhaArray[3] == "0.00"){
                    $quantidade = 0;
                }
                
                $produtos_dib_caixa->quantidade = $quantidade;
                if($produtos_dib_caixa->save()){
                    echo " - quantidade atualizada";
                }
                else{
                    echo " - quantidade não atualizada";
                }
                
                $preco_venda = $linhaArray[9];
                
                $valor_produto_filial = new ValorProdutoFilial;
                $valor_produto_filial->produto_filial_id    = $produtos_dib_caixa->id;
                $valor_produto_filial->valor                = $preco_venda;
                $valor_produto_filial->valor_cnpj           = $preco_venda;
                $valor_produto_filial->dt_inicio            = date("Y-m-d H:i:s");
                $valor_produto_filial->promocao             = false;
                if($valor_produto_filial->save()){
                    echo " - Preço atualizado";
                }
                else{
                    echo " - Preço não atualizado";
                }
            }
        }
        die;
        
        echo "\n\nFIM da rotina de atualizacao do preço!";
    }
}








