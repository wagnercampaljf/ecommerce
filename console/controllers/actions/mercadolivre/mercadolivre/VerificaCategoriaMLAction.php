<?php

namespace console\controllers\actions\mercadolivre;

use yii\helpers\ArrayHelper;
use function GuzzleHttp\json_encode;
use function GuzzleHttp\json_decode;
use Livepixel\MercadoLivre\Meli;
use common\models\ProdutoFilial;

class VerificaCategoriaMLAction extends Action
{
    public function run($cliente = 1){
       
        echo "INÍCIO\n\n";
        
        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        $user = $meli->refreshAccessToken('TG-5c4f26ef9b69e60006493768-193724256');
        $response = ArrayHelper::getValue($user, 'body');

        if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {
            $meliAccessToken = $response->access_token;
            
            $LinhasArray = Array();
            $file = fopen('/var/tmp/produtos_novos_br_28-05-2019_mariana.csv', 'r');
            while (($line = fgetcsv($file,null,';')) !== false)
            {
                $LinhasArray[] = $line;
            }
            fclose($file);
            
            foreach ($LinhasArray as $k => &$linhaArray ){
                
                if ($k <= 2){
                    continue;
                }
                
                $produto_filial = ProdutoFilial::find()->andWhere(['=','produto_id',$linhaArray[0]])
                                                ->andWhere(['=','filial_id',72])
                                                ->one();
                                    
                echo "\n".$k.";".$linhaArray[0];
                if (isset($produto_filial)){
                    $response_order = $meli->get("/items/".$produto_filial->meli_id);
                    
                    $response_categoria = $meli->get("/categories/".ArrayHelper::getValue($response_order, 'body.category_id'));

                    echo ";".ArrayHelper::getValue($response_order, 'body.category_id').";".ArrayHelper::getValue($response_categoria, 'body.name').";".ArrayHelper::getValue($response_order, 'body.permalink');
                }
            }
        }
        
        echo "\n\nFIM!\n\n";
    }
}