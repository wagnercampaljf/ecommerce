<?php

namespace console\controllers\actions\mercadolivre;


use common\models\Filial;
use common\models\ProdutoFilial;
use Livepixel\MercadoLivre\Meli;
use Yii;
use yii\helpers\ArrayHelper;
use common\models\PedidoMercadoLivre;
use common\models\PedidoMercadoLivreShipmentsItem;
use common\models\PedidoMercadoLivreShipments;

class PausarAnunciosFullAction extends Action
{
    public function run($filial_id = 0)
    {

        $meli = new Meli(static::APP_ID, static::SECRET_KEY);

        //Código de criação da tabela de preços baseadas no ME - Antigo
        echo "Inicio pausar anuncios FULL";
        
        $filial             = Filial::find()->andWhere(['=','id',98])->one();
        $user               = $meli->refreshAccessToken($filial->refresh_token_meli);
        $response           = ArrayHelper::getValue($user, 'body');
        $meliAccessToken    = $response->access_token;
        
        $produtos_filiais   = ProdutoFilial::find() ->andWhere(["=", "filial_id", $filial->id])
                                                    ->andWhere(["is not", "meli_id_full", null])
                                                    ->orderBy(["id" => SORT_ASC])
                                                    ->all();
        
        foreach($produtos_filiais as $k => $produto_filial){
            echo "\n".$k." - ".$produto_filial->id." - ".$produto_filial->meli_id_full;
            
            if ($k < 0){
                echo " - pular";
                continue;
            }
            
            //$body = ["status" =>  "paused"];
            $body = ["status" =>  "closed"];
            
            $response = $meli->put("items/{$produto_filial->meli_id_full}?access_token=" . $meliAccessToken, $body, []);
            //print_r($response);
            while($response['httpCode'] == 429) {
                echo " - ERRO";
                $response = $meli->put("items/{$produto_filial->meli_id_full}?access_token=" . $meliAccessToken, $body, []);
                //print_r($response);
            } 

            echo " - OK - ".$response['body']->status;
            
        }

	    //print_r($response); 
	    //die;
	   
        //TESTE

        //TESTE

        
        echo "Fim pausar anuncios FULL";
    }
    
    
    function atualizarProdutoML($arquivo_log, $meli, $token, $meli_id, $preco, $quantidade, $modo = "me2", $meli_origem, $produto_filial_e_preco_alterado){//, $variacoes){
        
        
        
    }
}
