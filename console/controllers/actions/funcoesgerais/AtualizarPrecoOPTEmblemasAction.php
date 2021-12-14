<?php

namespace console\controllers\actions\funcoesgerais;

use Yii;
use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;

class AtualizarPrecoOPTEmblemasAction extends Action
{
    public function run(){

        echo "INÍCIO da rotina de atualizacao do preços OPT Emblemas: \n\n";
        
        $arquivo_log = fopen("/var/tmp/log_alterar_preco_OPT_Emblemas_Nao_2012.csv", "a");
        // Escreve no log
        fwrite($arquivo_log, "produto_id;nome;filial_id;status\n");
        
        $produtos = Produto::find() ->andWhere(['=', 'marca_produto_id', 1170])
                                    ->andWhere(["not like", "nome", "2012 EM DIANTE"])
                                    ->all();
        
        foreach ($produtos as $k => $produto){
            echo "\n".$k." - ".$produto->nome." - ".$produto->codigo_global; 
            
            $produtos_filials = ProdutoFilial::find()   ->andWhere(["=", "produto_id", $produto->id])
                                                        ->andWhere(["<>", "filial_id", 98])
                                                        ->andWhere(["<>", "filial_id", 100])
                                                        ->all();
            
            foreach($produtos_filials as $i => $produto_filial){
                echo "\n        ".$i." - ".$produto_filial->id." - ".$produto_filial->filial_id;
                fwrite($arquivo_log,"\n".$produto->id.";".$produto->nome.";".$produto_filial->filial_id);
                
                $valor_produto_filial_verif = ValorProdutoFilial::find()->andWhere(["=", "produto_filial_id", $produto_filial->id])
                                                                        ->orderBy(["dt_inicio"=>SORT_DESC])
                                                                        ->one();

                if($valor_produto_filial_verif){
                    if($valor_produto_filial_verif->valor < 21 ){
                        echo " -  Valor menor que 21 ";
                        continue;
                    }
                }                                                        
                
                $valor_produto_filial                       = new ValorProdutoFilial();
                $valor_produto_filial->valor                = 75;
                $valor_produto_filial->valor_cnpj           = 75;
                $valor_produto_filial->dt_inicio            = date("Y-m-d H:i:s");
                $valor_produto_filial->produto_filial_id    = $produto_filial->id;
                if($valor_produto_filial->save()){
                    fwrite($arquivo_log, ";Salvo com sucesso!");
                }
                else{
                    fwrite($arquivo_log, ";Não salvo!");
                }
            }
            
            //$produto->save();
        }
        
        fclose($arquivo_log);
        
        echo "\n\nFIM da rotina de atualizacao do nome!";
    }
}