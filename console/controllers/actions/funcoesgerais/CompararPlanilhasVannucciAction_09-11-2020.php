<?php

namespace console\controllers\actions\funcoesgerais;

use common\models\Imagens;
use Yii;
use yii\base\Action;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;

class CompararPlanilhasVannucciAction extends Action
{
    public function run(){
        
        echo "INÍCIO da rotina de atualizacao do preço: \n\n";
        
        $LinhasArrayAntiga = Array();
        $file = fopen('/var/tmp/planilha-antiga.csv', 'r');
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArrayAntiga[] = $line;
        }
        fclose($file);
        if (file_exists("/var/tmp/log_comparar_planilha-antiga.csv")){
            unlink("/var/tmp/log_comparar_planilhas_planilha-antiga.csv");
        }
        $arquivo_log_antigo = fopen("/var/tmp/log_comparar_planilhas_planilha-antiga.csv", "a");
        
        $LinhasArrayNova = Array();
        $file = fopen('/var/tmp/planilha-nova.csv', 'r');
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArrayNova[] = $line;
        }
        fclose($file);
        if (file_exists("/var/tmp/log_comparar_planilhas_planilha-nova.csv")){
            unlink("/var/tmp/log_comparar_planilhas_planilha-nova.csv");
        }
        $arquivo_log_novo = fopen("/var/tmp/log_comparar_planilhas_planilha-nova.csv", "a");




        foreach ($LinhasArrayNova as $i => &$LinhaArrayNova ){
            echo "\n".$i." - ".$LinhaArrayNova[0];
            $status = "Não está presente na planilha antiga";

            $produto_filial_sorocaba = ProdutoFilial::find()
                ->andwhere(['=', 'id', $LinhaArrayNova[0]])
                ->one();
            if($produto_filial_sorocaba){

                $quantidade = 0;
                if($produto_filial_sorocaba->filial_id == 38 || $produto_filial_sorocaba->filial_id == 43 || $produto_filial_sorocaba->filial_id == 60 || $produto_filial_sorocaba->filial_id == 72 || $produto_filial_sorocaba->filial_id == 97){
                    $quantidade = $produto_filial_sorocaba->quantidade;
                }


            }



            $preco_venda = ValorProdutoFilial::find()->andWhere(['=','produto_filial_id', $produto_filial_sorocaba->id])->orderBy(['dt_inicio'=>SORT_DESC])->one();



            $status = ";Não encontrado";
            if($produto_filial_sorocaba){
                $status = ";Encontrado";
            }
            $preco_venda->valor_cnpj=$preco_venda->valor_cnpj*1.14;
            fwrite($arquivo_log_antigo, $status.";".$LinhaArrayNova[0].";".$preco_venda->valor_cnpj.";".$quantidade ."\n");
        }

            die;
        
        foreach ($LinhasArrayNova as $i => &$LinhaArrayNova ){
            echo "\n".$i." - ".$LinhaArrayNova[0];
            $status = "Não está presente na planilha antiga";
            
            foreach ($LinhasArrayAntiga as $k => &$LinhaArrayAntiga ){
                if($LinhaArrayAntiga[0] == $LinhaArrayNova[0]){
                    $status = "Está presente na planilha antiga";
                    break;
                }
                
            }
            
            fwrite($arquivo_log_novo, $status.";".$LinhaArrayNova[0].";".$LinhaArrayNova[1]."\n");
        }






        
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







