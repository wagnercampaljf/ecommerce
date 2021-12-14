<?php

namespace console\controllers\actions\funcoesgerais;

use Yii;
use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;

class ZerarEstoqueAction extends Action
{
    public function run(){

        echo "INÍCIO da rotina de criação preço: \n\n";
        
        $LinhasArray = Array();
        //$file = fopen('/home/dev_peca_agora/teste.csv', 'r');
	$file = fopen('/home/pecaagoradev/teste.csv', 'r');
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
        }
        fclose($file);
        
        if (file_exists("/var/tmp/log.csv")){
            unlink("/var/tmp/log.csv");
        }
        
        $arquivo_log = fopen("/var/tmp/log.csv", "a");
        // Escreve no log
        fwrite($arquivo_log, "produto_id;codigo_global;produto_filial_id;valor;status_busca;status_ncm;status_valor;Cod.;Deriv.;Descricao;Modelo;Referência;NCM\n");
        
        foreach ($LinhasArray as &$linhaArray){
            $codigo_global = str_replace(' ','',$linhaArray[4]);
            
            if ($codigo_global == null or $codigo_global == ""){
                // Escreve no log
                fwrite($arquivo_log, ';;;'.$linhaArray[5].';"Sem codigo_referencia";;;'.$linhaArray[0].';'.$linhaArray[1].';'.$linhaArray[2].';'.$linhaArray[3].';'.$linhaArray[4].';'.$linhaArray[6]."\n");
            }
            else {
                $produto = Produto::find()->andWhere(['=','codigo_global',$codigo_global])->one();

                if (isset($produto)){
                    
                    echo "\nProduto encontrado para código - "; print_r($codigo_global); echo " - ID - "; print_r($produto->id);
                    
                    $produtoFilial = ProdutoFilial::find()->andWhere(['=', 'filial_id', 60])
                                                           ->andWhere(['=', 'produto_id', $produto->id])
                                                           ->one();
                    $produtoFilialId = null;
                    $status_valor = "Não alterado";
                    if (isset($produtoFilial)){
                        $produtoFilial->quantidade  = 0;
                        $produtoFilial->save();
                    }
                    
                    // Escreve no log
                    fwrite($arquivo_log, $produto->id.';'.$produto->codigo_global.';'.$produtoFilialId.';'.$linhaArray[5].';"Encontrado";;'.$status_valor.';'.$linhaArray[0].';'.$linhaArray[1].';'.$linhaArray[2].';'.$linhaArray[3].';'.$linhaArray[4].';'.$linhaArray[6]."\n");
                } else{
                    echo "\nProduto NÃO encontrado para código - "; print_r($codigo_global);
                    fwrite($arquivo_log, ';;;'.$linhaArray[5].';"Não encontrado";;;'.$linhaArray[0].';'.$linhaArray[1].';'.$linhaArray[2].';'.$linhaArray[3].';'.$linhaArray[4].';'.$linhaArray[6]."\n");
                }
            }
            
        }
        
        // Fecha o arquivo
        fclose($arquivo_log); 
        
        echo "\n\nFIM da rotina de criação preço!";
    }
}
