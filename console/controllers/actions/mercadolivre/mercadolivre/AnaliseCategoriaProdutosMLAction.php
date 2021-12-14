<?php

namespace console\controllers\actions\mercadolivre;

use yii\helpers\ArrayHelper;
use function GuzzleHttp\json_encode;
use function GuzzleHttp\json_decode;
use Livepixel\MercadoLivre\Meli;
use common\models\ProdutoFilial;

class AnaliseCategoriaProdutosMLAction extends Action
{
    
    public function run($cliente = 1){
       
        echo "INÍCIO\n\n";
        
        // Escreve no log
        $arquivo_log = fopen("/var/tmp/busca_categoria_produtos_".date("Y-m-d_H-i-s").".csv", "a");
        fwrite($arquivo_log, "produto_id;nome;subcategoria_peca;meli_id_peca;categoria_ml;status;link\n");
        
        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        $user = $meli->refreshAccessToken("TG-5d3ee920d98a8e0006998e2b-193724256");
        $response = ArrayHelper::getValue($user, 'body');

        if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {
            $meliAccessToken = $response->access_token;
            
            $produtos_filiais   = ProdutoFilial::find()->andWhere(['produto_id'=>[278758,278774,278825,278950,278958,278992,279077,279160,279249,279283,279325,279389,279414,284790,280915,284809,278602,280923,12211,244005,231775,226570,281289,58086,231661,231737,231743,7773,7776,7837,7775,7783,7772,7766,7828,7778,256267,234268,234269,226573,228839,226572,44736,44737,226571,7782,7832,12208,7830,7770,7769,7834,12222,280993,12711,13617,7771,56121,7774,7784,12214,7781,7777,12209,19539,7767,7838,7768,7780,7829,227425,7836,7831,12221,12206,12644,12212,12216,12218,12219,12220,12213,12205,12210,12217,7779,7835,12215,12207,273455,7833,281371,239982,233261,240025,240027,30406,228840,257810,258109,282134,259742,261739,284185,284245,284328,271911,271912,284632,273345,273396,273400,273775,274253,274300,274354,274456,274483,274591,274744,274908,274944,275049,277504,277542,277640,277636,277700,277724,277841,277983,278279,278293,278323,278417,278495,278535,278540,278569,278591,278658,278706]])->all();
            foreach($produtos_filiais as $k => $produto_filial){
                echo "\n".++$k." - Produto: ".$produto_filial->produto->id." - ".$produto_filial->produto->nome;
                fwrite($arquivo_log, $produto_filial->produto_id.';'.$produto_filial->produto->nome.";".$produto_filial->produto->subcategoria->nome.";".$produto_filial->produto->subcategoria->meli_id);
                
                if($produto_filial->meli_id == null || $produto_filial->meli_id == "")
                {
                    fwrite($arquivo_log, ";;Fora do ML;\n");
                    continue;
                }
                
                $response_item = $meli->get("/items/".$produto_filial->meli_id."?access_token=" . $meliAccessToken);
                
                if (ArrayHelper::getValue($response_item, 'httpCode') < 300)
                {
                    echo " - ".ArrayHelper::getValue($response_item, 'body.category_id')." - ".ArrayHelper::getValue($response_item, 'body.shipping.mode');
                    fwrite($arquivo_log, ";".ArrayHelper::getValue($response_item, 'body.category_id').";".ArrayHelper::getValue($response_item, 'body.shipping.mode').";".ArrayHelper::getValue($response_item, 'body.permalink')."\n");
                    //print_r($response_item); die;
                }
                else 
                {
                    fwrite($arquivo_log, ";;Fora do ML;\n");
                }
                
                
                //print_r(ArrayHelper::getValue($response_item, 'body.price'));
                
                //fwrite($arquivo_log, ArrayHelper::getValue($response_item, 'body.permalink').";".ArrayHelper::getValue($response_item, 'body.price').";".ArrayHelper::getValue($response_item, 'body.sold_quantity').";".ArrayHelper::getValue($response_item, 'body.date_created').";".$busca->seller->id.";".$busca->permalink.";".$busca->price.";".$busca->sold_quantity.";".ArrayHelper::getValue($response_data_criacao, 'body.date_created')."\n");
            }
        }
        
        fclose($arquivo_log);
        
        echo "\n\nFIM!\n\n";
    }
}




 