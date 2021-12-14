<?php

namespace console\controllers\actions\funcoesgerais;

use Yii;
use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;

class AnaliseProdutosVannucciCompararPlanilhasAction extends Action
{
    public function run(){

        echo "INÍCIO da rotina de analise de produtos LNG: \n\n";

        $LinhasArrayAntiga = Array();
        $file = fopen('/var/tmp/vannucci_antiga_2018.csv', 'r');
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArrayAntiga[] = $line;
        }
        fclose($file);

        $LinhasArrayNova = Array();
        $file = fopen('/var/tmp/vannucci_nova_2019.csv', 'r');
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArrayNova[] = $line;
        }
        fclose($file);

        if (file_exists("/var/tmp/log_vannucci_comparacao_planilhas.csv")){
            unlink("/var/tmp/log_vannucci_comparacao_planilhas.csv");
        }

        $arquivo_log = fopen("/var/tmp/log_vannucci_comparacao_planilhas.csv", "a");
        // Escreve no log
        fwrite($arquivo_log, "planilha;codigo_fabricante_antigo;codigo_global_antigo;valor_antigo;codigo_fabricante_novo;codigo_global_novo;valor_novo;produto_filial_id;codigo_global_pecaagora;valor_peca_agora;status_antiga;status_nova;status_pecaagora;codigo_encontrado\n");

        foreach ($LinhasArrayAntiga as $k => &$linhaArrayAntiga){

            echo "\n".$k." - ".$linhaArrayAntiga[0];

            $status = ";Antiga";
            fwrite($arquivo_log, "Antiga;".$linhaArrayAntiga[0].";".$linhaArrayAntiga[2].";".$linhaArrayAntiga[5]);

            $codigo_fabricante_nova     = ";;;";
            $encontrado_nova_fabricante = false;
            $encontrado_nova_global     = false;
            foreach ($LinhasArrayNova as &$linhaArrayNova){
                if($linhaArrayAntiga[0] == $linhaArrayNova[0]){
                    $encontrado_nova_fabricante = true;
                    break;
                }
            }

            if($encontrado_nova_fabricante)
            {
                $codigo_fabricante_nova = ";".$linhaArrayNova[0].";".$linhaArrayNova[2].";".$linhaArrayNova[5];
                $status .= ";Nova Fabricante";
            }
            else
            {
                foreach ($LinhasArrayNova as &$linhaArrayNova){
                    if($linhaArrayAntiga[2] == $linhaArrayNova[2]){
                        $encontrado_nova_global = true;
                        break;
                    }
                }
                if($encontrado_nova_global)
                {
                    $codigo_fabricante_nova = ";".$linhaArrayNova[0].";".$linhaArrayNova[2].";".$linhaArrayNova[5];
                    $status .= ";Nova Global";
                }
                else
                {
                    $status .= ";";
                }
            }

            $produto_filial_fabricante = ProdutoFilial::find() ->joinWith('produto')
                                                    ->andWhere(['=','produto.codigo_fabricante',$linhaArrayAntiga[0]])
                                                    ->andWhere(['=','produto.fabricante_id',91])
                                                    ->one();

            $produto_filial_global = ProdutoFilial::find() ->joinWith('produto')
                                                    ->andWhere(['=',"replace(replace(replace(replace(replace(replace(produto.codigo_global,';',''),'/',''),' ',''),'_',''),'.',''),',','')",$linhaArrayAntiga[0]])
                                                    ->andWhere(['=','produto.fabricante_id',91])
                                                    ->one();

            $codigo_fabricante_pecaagora    = ";;;";
            $codigo_encontrado              ="";
            if($produto_filial_fabricante)
            {
                $codigo_fabricante_pecaagora = ";".$produto_filial_fabricante->id.";".$produto_filial_fabricante->produto->codigo_global.";";
                $status .= ";Peça Agora";

                $valor_produto_filial = ValorProdutoFilial::find()  ->andWhere(['=','produto_filial_id',$produto_filial_fabricante->id])
                                                                    ->orderBy('id DESC')
                                                                    ->one();
                if($valor_produto_filial){
                    $codigo_fabricante_pecaagora .= $valor_produto_filial->valor;
                }

                $codigo_encontrado = "codigo_fabricante";
            }
            else
            {
                if($produto_filial_global)
                {
                    $codigo_fabricante_pecaagora = ";".$produto_filial_global->id.";".$produto_filial_global->produto->codigo_global.";";
                    $status .= ";Peça Agora";

                    $valor_produto_filial = ValorProdutoFilial::find()  ->andWhere(['=','produto_filial_id',$produto_filial_global->id])
                    ->orderBy('id DESC')
                    ->one();
                    if($valor_produto_filial){
                        $codigo_fabricante_pecaagora .= $valor_produto_filial->valor;
                    }

                    $codigo_encontrado = "codigo_global";
                }
                else
                {
                    $status .= ";";
                }
            }

            fwrite($arquivo_log, $codigo_fabricante_nova.$codigo_fabricante_pecaagora.$status.";".$codigo_encontrado."\n");
        }


        /*foreach ($LinhasArrayNova as $k => &$linhaArrayNova){

            echo "\n".$k." - ".$linhaArrayNova[0];

            $codigo_fabricante_nova = ";".$linhaArrayNova[0].";".$linhaArrayNova[2].";".$linhaArrayNova[5];

            $esta_planilha_antiga = false;
            foreach ($LinhasArrayAntiga as &$linhaArrayAntiga){
                if($linhaArrayAntiga[0] == $linhaArrayNova[0]){
                    $esta_planilha_antiga = true;
                    break;
                }
            }

            if($esta_planilha_antiga){
                continue;
            }

            $status = ";;Nova";
            fwrite($arquivo_log, "Nova;;");

            $produto_filial = ProdutoFilial::find() ->joinWith('produto')
                                                    ->andWhere(['=','produto.codigo_fabricante',$linhaArrayNova[0]])
                                                    ->andWhere(['=','produto.fabricante_id',91])
                                                    ->one();
            $codigo_fabricante_pecaagora = ";;";
            if($produto_filial){
                $codigo_fabricante_pecaagora = ";".$produto_filial->id.";";
                $status .= "; Peça Agora";

                $valor_produto_filial = ValorProdutoFilial::find()  ->andWhere(['=','produto_filial_id',$produto_filial->id])
                ->orderBy('id DESC')
                ->one();
                if($valor_produto_filial){
                    $codigo_fabricante_pecaagora .= $valor_produto_filial->valor;
                }
            }

            fwrite($arquivo_log, $codigo_fabricante_nova.$codigo_fabricante_pecaagora.$status."\n");
        }*/

        //Fecha o arquivo
        fclose($arquivo_log);

        echo "\n\nFIM da rotina de analise de produtos LNG!";
    }
}
