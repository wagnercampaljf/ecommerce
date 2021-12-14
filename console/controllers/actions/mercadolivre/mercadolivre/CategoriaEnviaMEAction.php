<?php

namespace console\controllers\actions\mercadolivre;

use yii\helpers\ArrayHelper;
use function GuzzleHttp\json_encode;
use function GuzzleHttp\json_decode;
use Livepixel\MercadoLivre\Meli;
use common\models\ProdutoFilial;
use common\models\Subcategoria;
use common\models\Imagens;

class CategoriaEnviaMEAction extends Action
{
    
    public function run($cliente = 1){
       
        echo "INÍCIO\n\n";
        
        // Escreve no log
        $arquivo_log = fopen("/var/tmp/subcategoria_ME.csv", "a");
        fwrite($arquivo_log, "id;nome;meli_cat_nome;meli_id;modo_envio\n");
        
        $meli = new Meli(static::APP_ID, static::SECRET_KEY);

        $subcategorias = Subcategoria::find()->all();
        
        foreach ($subcategorias as $k => $subcategoria){
            echo "\n".$k." - ".$subcategoria->meli_id;
            $response_categoria = $meli->get("/categories/".$subcategoria->meli_id);
            
            $modo_envio_me = "";
            
            if (ArrayHelper::getValue($response_categoria, 'httpCode') < 400) {
                foreach(ArrayHelper::getValue($response_categoria, 'body.settings.shipping_modes') as $modo_envio){
                    echo " - ".$modo_envio;
                    if($modo_envio == "me2"){
                        $modo_envio_me = "me2";
                        break;
                    }
                }
            }
            
            fwrite($arquivo_log, $subcategoria->id.";".$subcategoria->nome.";".$subcategoria->meli_cat_nome.";".$subcategoria->meli_id.";".$modo_envio_me."\n");
        }
        
        fclose($arquivo_log);
        
        echo "\n\nFIM!\n\n";
    }
}
 