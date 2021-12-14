<?php

namespace console\controllers\actions\funcoesgerais;

use common\models\MarkupDetalhe;
use frontend\models\MarkupMestre;
use Yii;
use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;

class GeralCalcularPrecoVendaAction extends Action
{
    public function run(){

        echo "INÍCIO da rotina de criação preço: \n\n";
        
        $faixas = array();


        $markup_mestre      = MarkupMestre::find()->andWhere(['=','e_markup_padrao', true])->orderBy(["id" => SORT_DESC])->one();

        $markups_detalhe = MarkupDetalhe::find()->andWhere(['=','markup_mestre_id',$markup_mestre->id])->all();

        $faixas = [];

        foreach ($markups_detalhe as $markup_detalhe){
            $faixas [] = [$markup_detalhe->valor_minimo, $markup_detalhe->valor_maximo, $markup_detalhe->margem, $markup_detalhe->e_margem_absoluta];

        }


        $LinhasArray = Array();



       // $arquivo_origem = '/var/tmp/br_preco_estoque_11-05-2021';


        $arquivo_origem = '/var/tmp/produtos_marts_parts_subir';

        $file = fopen($arquivo_origem.".csv", 'r');
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
        }
        fclose($file);
        
        $destino = $arquivo_origem."_precificado.csv";
        if (file_exists($destino)){
            unlink($destino);
        }
        
        $arquivo_destino = fopen($destino, "a");

        foreach ($LinhasArray as $i => &$linhaArray){
            
            echo "\n".$i." - ".$linhaArray[1]. " - ".$linhaArray[7];
            
            $novo_codigo_fabricante = $linhaArray[0].".M";
            
            if ($i <= 1){

                fwrite($arquivo_destino, $linhaArray[0].";".$linhaArray[1].";".$linhaArray[2].";".$linhaArray[3].";".$linhaArray[4].";".$linhaArray[5].";".$linhaArray[6].";".$linhaArray[7].";".$linhaArray[8].";".$linhaArray[9].";Preço Venda;novo_codigo_fabricante\n");

                continue;
            }
            

            $preco_compra   = (float) $linhaArray[6];



            foreach ($faixas as $k => $faixa) {
                if($preco_compra >= $faixa[0] && $preco_compra <= $faixa[1]){
                    $preco_venda = round(($preco_compra * $faixa[2]),2);
                    if ($faixa[3]){
                        $preco_venda = $faixa[2];
                    }

                    echo " - ".$preco_venda." - ".$novo_codigo_fabricante;
                    fwrite($arquivo_destino, $linhaArray[0].";".$linhaArray[1].";".$linhaArray[2].";".$linhaArray[3].";".$linhaArray[4].";".$linhaArray[5].";".$linhaArray[6].";".$linhaArray[7].";".$linhaArray[8].";".$linhaArray[9].";".$preco_venda.";".$novo_codigo_fabricante."\n");

                    break;
                }
            }
        }
        
        // Fecha o arquivo
        fclose($arquivo_destino);
        
        echo "\n\nFIM da rotina de criação preço!";
    }
    

}



