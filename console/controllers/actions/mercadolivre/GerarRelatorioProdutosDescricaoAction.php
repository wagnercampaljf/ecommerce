<?php

namespace console\controllers\actions\mercadolivre;

use yii\helpers\ArrayHelper;
use function GuzzleHttp\json_encode;
use function GuzzleHttp\json_decode;
use Livepixel\MercadoLivre\Meli;
use common\models\ProdutoFilial;

class GerarRelatorioProdutosDescricaoAction extends Action
{
    
    
    public function run($cliente = 1){
        
        echo "INÍCIO\n\n";
        
        // Escreve no log
        $arquivo_log = fopen("/var/tmp/produtos_descricao_ml_".date("Y-m-d_H-i-s").".csv", "a");
        fwrite($arquivo_log, "meli_id;status_antigo;status\n");
        
        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        //$user = $meli->refreshAccessToken("TG-5e2efe08144ef6000642cdb6-193724256"); //conta principal
        $user = $meli->refreshAccessToken("TG-5e7a7c2ba95cdb00064289bf-435343067"); //conta secundaria
        $response = ArrayHelper::getValue($user, 'body');
        
        if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {
            $meliAccessToken = $response->access_token;
            
            //echo "\n\n".$meliAccessToken."\n\n";die;
            
            $x = 0;
            $i = 0;
            //$response_order = $meli->get("/users/193724256/items/search?search_type=scan&limit=100&access_token=" . $meliAccessToken); //conta principal
	    $response_order = $meli->get("/users/435343067/items/search?search_type=scan&limit=100&access_token=" . $meliAccessToken); //contasecundaria

            while (ArrayHelper::getValue($response_order, 'httpCode') <> 404){
                
                if ($i >= 0){
                    foreach (ArrayHelper::getValue($response_order, 'body.results') as $meli_id){
                        //$response_item = $meli->get("/items/".$meli_id."?access_token=" . $meliAccessToken);
                        echo "\n".++$x." - MELI_ID: ".$meli_id;
                        
                        $produto_filial = ProdutoFilial::find()->andWhere(['=','meli_id',$meli_id])->one();
                        
                        if($produto_filial){
                            echo " - Produto encontrado Peça";
                        }
                        else{
                            echo " - Produto não encontrado Peça";
                            continue;
                        }
                        
                        $response_descricao = $meli->get("/items/".$meli_id."/description?access_token=" . $meliAccessToken);
                        
                        if (ArrayHelper::getValue($response_descricao, 'httpCode') < 300){
                            $texto = ArrayHelper::getValue($response_descricao, 'body.plain_text');
                            
                            if (strpos($texto, "PEÇA AGORA") || strpos($texto, "Peça Agora")){
                                echo " - Descrição desatualizada";
                                fwrite($arquivo_log, "\n".$meli_id.";Produto descrição desatualizada");
                                
                                //continue;
                                $page = $this->controller->renderFile(__DIR__ . '/../../../../lojista/views/mercado-livre/produtoML.php',['produto' => $produto_filial]);
                                $page = str_replace("'", "", $page);
                                $page = str_replace("<p>", " ", $page);
                                $page = str_replace("</p>", " ", $page);
                                $page = str_replace("<br>", "\n", $page);
                                $page = str_replace("<BR>", "\n", $page);
                                $page = str_replace("<br/>", "\n", $page);
                                $page = str_replace("<BR/>", "\n", $page);
                                $page = str_replace("<strong>", " ", $page);
                                $page = str_replace("</strong>", " ", $page);
                                $page = str_replace('<span class="redactor-invisible-space">', " ", $page);
                                $page = str_replace('</span>', " ", $page);
                                $page = str_replace('<span>', " ", $page);
                                $page = str_replace('<ul>', " ", $page);
                                $page = str_replace('</ul>', " ", $page);
                                $page = str_replace('<li>', "\n", $page);
                                $page = str_replace('</li>', " ", $page);
                                $page = str_replace('<p style="margin-left: 20px;">', " ", $page);
                                $page = str_replace('<h1>', " ", $page);
                                $page = str_replace('</h1>', " ", $page);
                                $page = str_replace('<h2>', " ", $page);
                                $page = str_replace('</h2>', " ", $page);
                                $page = str_replace('<h3>', " ", $page);
                                $page = str_replace('</h3>', " ", $page);
                                $page = str_replace('<span class="redactor-invisible-space" style="">', " ", $page);
                                $page = str_replace('>>>', "(", $page);
                                $page = str_replace('<<<', ")", $page);
                                $page = str_replace('<u>', " ", $page);
                                $page = str_replace('</u>', "\n", $page);
                                $page = str_replace('<b>', " ", $page);
                                $page = str_replace('</b>', " ", $page);
                                $page = str_replace('<o:p>', " ", $page);
                                $page = str_replace('</o:p>', " ", $page);
                                $page = str_replace('<p style="margin-left: 40px;">', " ", $page);
                                $page = str_replace('<del>', " ", $page);
                                $page = str_replace('</del>', " ", $page);
                                $page = str_replace('/', "-", $page);
                                $page = str_replace('<em>', " ", $page);
                                $page = str_replace('<-em>', " ", $page);
                                
                                $body = ["plain_text" => $page];
				//$body = ["description" => ["plain_text" => $page],];
                                $response = $meli->put("items/{$meli_id}/description?access_token=" . $meliAccessToken, $body, [] );
                                if ($response['httpCode'] >= 300) {
                                    echo " - ERROR Descrição";
                                    fwrite($arquivo_log, ";Descrição não alterado");
                                }
                                else{
                                    echo " - OK Descrição";
                                    fwrite($arquivo_log, ";Descrição alterada");
                                }
                            }
                        }
                       
                        //fwrite($arquivo_log, $meli_id.";".ArrayHelper::getValue($response_item, 'body.title').";".ArrayHelper::getValue($response_item, 'body.status').";".$status_suspenso.";".$status_completo."\n");
                    }
                }
                
                echo "\n Scroll: ".$i++;
                //$response_order = $meli->get("/users/193724256/items/search?search_type=scan&scroll_id=".ArrayHelper::getValue($response_order, 'body.scroll_id')."&limit=100&access_token=" . $meliAccessToken); //conta principal
		$response_order = $meli->get("/users/435343067/items/search?search_type=scan&scroll_id=".ArrayHelper::getValue($response_order, 'body.scroll_id')."&limit=100&access_token=" . $meliAccessToken); //conta secundaria
            }
        }
        
        fclose($arquivo_log);
        
        echo "\n\nFIM!\n\n";
    }
}

