<?php

namespace console\controllers\actions\funcoesgerais;

use common\models\Imagens;
use Yii;
use yii\base\Action;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;

class BonfanteProdutosNCMAction extends Action
{
    public function run(){
        
        echo "INÍCIO da rotina de atualizacao do preço: \n\n";
        
        $LinhasArrayAntiga = Array();
        $file = fopen('/var/tmp/produtos_bonfante2.csv', 'r');
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArrayAntiga[] = $line;
        }
        fclose($file);
        if (file_exists("/var/tmp/log_produtos_bonfante2.csv")){
            unlink("/var/tmp/log_produtos_bonfante2.csv");
        }
        $arquivo_log_antigo = fopen("/var/tmp/log_produtos_bonfante2.csv", "a");

        $LinhasArrayNova = Array();
        $file = fopen('/var/tmp/produtos_bonfante2.csv', 'r');
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArrayNova[] = $line;
        }
        fclose($file);
        if (file_exists("/var/tmp/log_produtos_bonfante2.csv")){
            unlink("/var/tmp/log_produtos_bonfante2.csv");
        }
        $arquivo_log_novo = fopen("/var/tmp/log_produtos_bonfante2.csv", "a");



        foreach ($LinhasArrayNova as $i => &$LinhaArrayNova ){
            echo "\n".$i." - ".$LinhaArrayNova[5];
            $status = "Não está presente na planilha antiga";

            $produto_filial = ProdutoFilial::find()->joinWith('produto')
                ->andWhere(['=', 'filial_id', 86])
                ->andwhere(['=', 'codigo_montadora', $LinhaArrayNova[5]])
                ->one();

            $status = ";Não encontrado";
            if($produto_filial){
                $status = ";Encontrado";
            }
            /* else{
                $produto = new Produto;
                $produto->nome                   = $LinhaArrayNova[1];
                $produto->codigo_global          = $LinhaArrayNova[0];
                $produto->codigo_similar         = $LinhaArrayNova[10];
                $produto->peso                   = $peso;
                $produto->altura                 = $altura;
                $produto->largura                = $largura;
                $produto->profundidade           = $profundidade;
                $produto->aplicacao              = $LinhaArrayNova[2];
                $produto->codigo_montadora       = $LinhaArrayNova[9];


                if($produto->save()){
                    echo " - Produto criado";
                    $status .= " - Produto criado";

                    $produto_filial_novo                = new ProdutoFilial;
                    $produto_filial_novo->filial_id     = 59;
                    $produto_filial_novo->produto_id    = $produto->id;
                    if($produto_filial_novo->save()){
                        echo " - Estoque criado";
                        $status .= " - Estoque criado";
                        // CRIAR VALOR

                        /* $valor_produto_filial = new ValorProdutoFilial;
                          $valor_produto_filial->valor =
                          $valor_produto_filial->valor                = $preco_venda;
                          $valor_produto_filial->valor_cnpj           = $preco_venda;
                          $valor_produto_filial->dt_inicio            = date("Y-m-d H:i:s");
                          $valor_produto_filial->promocao             = false;
                          $valor_produto_filial->valor_compra         = $preco_compra;
                          //...

                          if($valor_produto_filial->save()){
                              echo " - Valor criado";
                              $status .= " - Valor criado";
                          }
                          else{
                              echo " - Valor não criado";
                              $status .= " - Valor não criado";
                          }
                    }
                    else{
                        echo " - Estoque não criado";
                        $status .= " - Estoque não criado";
                    }
                }
                else{
                    echo " - Produto não criado";
                    $status .= " - Produto não criado";
                }

            }*/

            fwrite($arquivo_log_novo, $LinhaArrayNova[0].";".$LinhaArrayNova[1].";".$LinhaArrayNova[2].";".$LinhaArrayNova[3].";".$LinhaArrayNova[4].";".$LinhaArrayNova[5].";".$LinhaArrayNova[6].$status."\n");
        }

        die;






        
        foreach ($LinhasArrayAntiga as $i => &$LinhaArrayAntiga ){
            echo "\n".$i." - ".$LinhaArrayNova[0];
            $status = "Não está presente na planilha nova";
            
            foreach ($LinhasArrayNova as $k => &$LinhaArrayNova ){
                if($LinhaArrayAntiga[0] == $LinhaArrayNova[0]){
                    $status = "Está presente na planilha antiga";
                    break;
                }
            }
            
            fwrite($arquivo_log_antigo, $status.";".$LinhaArrayAntiga[0].";".$LinhaArrayAntiga[1]."\n");
        }
        
        fclose($arquivo_log_novo);
        fclose($arquivo_log_antigo);
        
        echo "\n\nFIM da rotina de atualizacao do preço!";
    }
}







