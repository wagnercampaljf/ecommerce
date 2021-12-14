<?php

namespace console\controllers\actions\funcoesgerais;

use Yii;
use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;
use common\models\Filial;

class AtualizarPrecos4FiliaisAction extends Action
{
    public function run(){
        
        echo "INÍCIO da rotina de atualizacao do preço: \n\n";
        
        $linhasArray = Array();
        //$file = fopen("/var/tmp/lista_completa_vannucci_16-09-2019_precificado_venda.csv", 'r'); //Abre arquivo com preços para subir
        $file = fopen("/var/tmp/alteracao_preco_filiais_4_2021-05-13.csv", 'r'); //Abre arquivo com preços para subir
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $linhasArray[] = $line; //Popula um array com os dados do arquivo, para facilitar o processamento
        }
        fclose($file);

        //Verifica se já existe o arquivo de log da função, exclui caso exista
        if (file_exists("/var/tmp/log_alteracao_preco_filiais_4_2021-05-13.csv")){
            unlink("/var/tmp/log_alteracao_preco_filiais_4_2021-05-13.csv");
        }

        //Abre o arquivo de log pra ir logando a função
        $arquivo_log = fopen("/var/tmp/log_alteracao_preco_filiais_4_2021-05-13.csv", "a");
        //Escreve no log
        fwrite($arquivo_log, "produto_filial_id;filial_id;produto_id;dt_inicio;valor;valor_cnpj;valor_compra;status");
        
        foreach ($linhasArray as $i => &$linhaArray){ //Looper que vai percorrer o array com as imformações de preços a subir

            //Coloca os dados da planilha de preços no log
            fwrite($arquivo_log, "\n".'"'.$linhaArray[0].'";"'.$linhaArray[1].'";"'.$linhaArray[2].'";"'.$linhaArray[3].'";'.$linhaArray[4].'";'.$linhaArray[5]);
            
            if ($i <= 0){ //Pula a primeira linha de preços, por se tratar dos títulos da planilha
                continue;
            }
            
            echo "\n".$i." - ".$linhaArray[0]." - ".$linhaArray[1]." - ".$linhaArray[2]." - ".$linhaArray[3]." - ".$linhaArray[4]; //Exibe no console(Terminal) as informações dos preços durante o processamento
            fwrite($arquivo_log, "\n".$linhaArray[0].";".$linhaArray[1].";".$linhaArray[2].";".$linhaArray[3].";".$linhaArray[4].";".$linhaArray[5].";".$linhaArray[6]);
            
            $filiais_mesmo_valor = array();
            
            $filial_id  = $linhaArray[1];
            $produto_id = $linhaArray[2];
            
            switch ($filial_id){
                case 8:
                    $filiais_mesmo_valor = [94,95,96];
                    break;
                case 94:
                    $filiais_mesmo_valor = [8,95,96];
                    break;
                case 95:
                    $filiais_mesmo_valor = [8,94,96];
                    break;
                case 96:
                    $filiais_mesmo_valor = [8,94,95];
                    break;
            }
            
            foreach($filiais_mesmo_valor as $k => $filial_mesmo_valor_id){
                //echo "<br>Filial: ".$filial_mesmo_valor_id;
                $produto_filial_mesmo_valor = ProdutoFilial::find() ->andWhere(["=", "produto_id", $produto_id])
                                                                    ->andWhere(["=", "filial_id", $filial_mesmo_valor_id])
                                                                    ->one();
                
                if($produto_filial_mesmo_valor){
                    $valor_produto_filial_mesmo_valor                       = new ValorProdutoFilial();
                    $valor_produto_filial_mesmo_valor->produto_filial_id    = $produto_filial_mesmo_valor->id;
                    $valor_produto_filial_mesmo_valor->valor                = $linhaArray[4];
                    $valor_produto_filial_mesmo_valor->valor_cnpj           = $linhaArray[4];
                    $valor_produto_filial_mesmo_valor->dt_inicio            = $linhaArray[3];
                    $valor_produto_filial_mesmo_valor->promocao             = false;
                    $valor_produto_filial_mesmo_valor->valor_compra         = $linhaArray[6];
                    if($valor_produto_filial_mesmo_valor->save()){
                        echo " - VALOR ADICIONADO";
                        fwrite($arquivo_log, ";VALOR ADICIONADO(".$filial_mesmo_valor_id.")");
                    }
                    else{
                        echo " - VALOR NÃO ADICIONADO";
                        fwrite($arquivo_log, ";VALOR NÃO ADICIONADO(".$filial_mesmo_valor_id.")");
                    }
                }
            }
        }
        
        // Fecha o arquivo log
        fclose($arquivo_log); 
        
        echo "\n\nFIM da rotina de atualizacao do preço!";
    }
}







