<?php

namespace console\controllers\actions\funcoesgerais;

use Yii;
use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;

class ImportacaoPrecosAction extends Action
{
    public function run(){

        echo "INÍCIO da rotina de criação preço: \n\n";
        
        $LinhasArray = Array();
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
                    
                    echo "Produto encontrado para código - "; print_r($codigo_global); echo " - ID - "; print_r($produto->id); echo "\n";
       
                    echo "NCN: ";print_r($produto->codigo_montadora); echo " - Novo NCN: "; print_r($linhaArray[6]); echo "\n";
                    $status_ncm = "Não alterado";
                    if ($produto->save()){
                        $status_ncm = "Alterado";
                    }
                    
                    $produtoFilial = ProdutoFilial::find()->andWhere(['=', 'filial_id', 60])
                                                           ->andWhere(['=', 'produto_id', $produto->id])
                                                           ->one();
                    $produtoFilialId = null;
                    $status_valor = "Não alterado";
                    if (isset($produtoFilial)){
                        $valorProdutoFilial                     = New ValorProdutoFilial;
                        $valorProdutoFilial->produto_filial_id  = $produtoFilial->id;
                        $valorProdutoFilial->valor              = $linhaArray[5];
                        $valorProdutoFilial->valor_cnpj         = $linhaArray[5];
                        $valorProdutoFilial->dt_inicio          = date("Y-m-d h:i:s");
                        $valorProdutoFilial->promocao           = false;
                        $valorProdutoFilial->save();
                        $produtoFilialId = $valorProdutoFilial->id;
                        $status_valor = "Alterado, valor_produto_filial criado";
                    } else {
                        $produtoFilialNovo              = new ProdutoFilial;
                        $produtoFilialNovo->produto_id  = $produto->id;
                        $produtoFilialNovo->filial_id   = 60;
                        $produtoFilialNovo->quantidade  = 99999;
                        $produtoFilialNovo->envio       = 1;
                        if ($produtoFilialNovo->save()){
                            $produtoFilialId = $produtoFilialNovo->id;
                            
                            $valorProdutoFilial                     = New ValorProdutoFilial;
                            $valorProdutoFilial->produto_filial_id  = $produtoFilialNovo->id;
                            $valorProdutoFilial->valor              = $linhaArray[5];
                            $valorProdutoFilial->valor_cnpj         = $linhaArray[5];
                            $valorProdutoFilial->dt_inicio          = date("Y-m-d h:i:s");
                            $valorProdutoFilial->promocao           = false;
                            $valorProdutoFilial->save();
                            
                            $status_valor = "Alterado, produto_filial criado";
                        }
                    }
                    
                    // Escreve no log
                    fwrite($arquivo_log, $produto->id.';'.$produto->codigo_global.';'.$produtoFilialId.';'.$linhaArray[5].';"Encontrado";'.$status_ncm.';'.$status_valor.';'.$linhaArray[0].';'.$linhaArray[1].';'.$linhaArray[2].';'.$linhaArray[3].';'.$linhaArray[4].';'.$linhaArray[6]."\n");
                }
                else{
                    fwrite($arquivo_log, ';;;'.$linhaArray[5].';"Não encontrado";;;'.$linhaArray[0].';'.$linhaArray[1].';'.$linhaArray[2].';'.$linhaArray[3].';'.$linhaArray[4].';'.$linhaArray[6]."\n");
                }
            }
            
        }
        
        // Fecha o arquivo
        fclose($arquivo_log); 
        
        //print_r($LinhasArray);
        
        echo "\n\nFIM da rotina de criação preço!";
    }
}
