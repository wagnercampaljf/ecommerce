<?php

namespace console\controllers\actions\mercadolivre;

use yii\helpers\ArrayHelper;
use function GuzzleHttp\json_decode;
use Livepixel\MercadoLivre\Meli;
use common\models\ProdutoFilial;
use common\models\Filial;

class AtualizarPrecoGeralAction extends Action
{
    
    public function run($cliente = 1){
        
        echo "INÍCIO\n\n";
        
        $filiais = Filial::find()   ->andWhere(["is not", "refresh_token_meli", null])
                                    ->andWhere(["<>", "id", 98])
                                    ->orderBy("id")
                                    ->all();
        
        foreach ($filiais as $k => $filial){
            
            echo "\n\nFILIAL: ".$filial->id." \n\n";
             
            if($filial->id < 43){
                echo " - Pular FILIAL";
                continue;
            }
            
            $produtos_filiais = ProdutoFilial::find()   //->andWhere(["is not", "meli_id", null])
                                                        //->andWhere(["=", "filial_id", $filial->id])
                                                        ->where(" filial_id = ".$filial->id." and meli_id is not null and id in (select distinct produto_filial_id from valor_produto_filial) ")
                                                        ->orderBy("id")
                                                        ->all();
            
            foreach ($produtos_filiais as $i => $produto_filial){
            
                echo "\n".$i." - PRODUTO FILIAL: ".$produto_filial->id." - ".$produto_filial->filial_id." \n";
            
                if($filial->id == 43 && $produto_filial->id <= 447480){
                    echo " - Pular ESTOQUE";
                    continue;
                }
                
                print_r($produto_filial->atualizarMLPreco());
                
            }
            
        }
        
        echo "\n\nFIM!\n\n";
    }
}
