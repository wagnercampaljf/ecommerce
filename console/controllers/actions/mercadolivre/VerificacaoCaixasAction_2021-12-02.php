<?php

namespace console\controllers\actions\mercadolivre;

use yii\helpers\ArrayHelper;
use function GuzzleHttp\json_encode;
use function GuzzleHttp\json_decode;
use Livepixel\MercadoLivre\Meli;
use common\models\Filial;
use common\models\ProdutoFilial;
use common\models\Imagens;

class VerificacaoCaixasAction extends Action
{

    
    public function run($cliente = 1){
       
        echo "INÍCIO\n\n";
        
        // Escreve no log
        $arquivo_log = fopen("/var/tmp/verificacao_caixas_".date("Y-m-d_H-i-s").".csv", "a");
        fwrite($arquivo_log, "filial_id;filial_nome;produto_filial_id;produto_id;produto_nome;codigo_global;codigo_fabricante;quantidade;status;link_meli");
        
        $meli = new Meli(static::APP_ID, static::SECRET_KEY);

        $filiais = Filial::find()->andWhere(['is not','refresh_token_meli',null])->orderBy("id")->all();
        
        foreach ($filiais as $k => $filial){
            echo "\n".$k." - ".$filial->nome;
            
            $user = $meli->refreshAccessToken($filial->refresh_token_meli);
            $response = ArrayHelper::getValue($user, 'body');
            
            if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {
                $meliAccessToken = $response->access_token;
                
                $produtos_filiais = ProdutoFilial::find()->joinWith("produto")
                                                         ->andWhere(['like','codigo_global','CX.'])
                                                         ->andWhere(['=','filial_id', $filial->id])
                                                         ->andWhere(['is not','meli_id',null])
                                                         ->orderBy("id")
                                                         ->all();
                
                foreach ($produtos_filiais as $i => $produto_filial){
                    
                    echo "\n".$produto_filial->id." - ".$produto_filial->produto_id." - ".$produto_filial->produto->codigo_global." - ".$produto_filial->produto->codigo_fabricante." - ".$produto_filial->quantidade;
                    
                    $response_item = $meli->get("/items/".$produto_filial->meli_id."?access_token=" . $meliAccessToken);
                    
                    if($response_item["httpCode"] < 300){
                        fwrite($arquivo_log, "\n".$filial->id.";".$filial->nome.";".$produto_filial->id.";".$produto_filial->produto_id.";".$produto_filial->produto->nome.";".$produto_filial->produto->codigo_global.";".$produto_filial->produto->codigo_fabricante.";".$produto_filial->quantidade.";".$response_item["body"]->status.";".$response_item["body"]->permalink);
                        echo " - OK";
                    }
                    else{
                        echo " - ERRO";
                    }
                }
            }
        }
        
        fclose($arquivo_log);
        
        echo "\n\nFIM!\n\n";
    }
}
 