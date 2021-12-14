<?php

namespace console\controllers\actions\mercadolivre;

use yii\helpers\ArrayHelper;
use Livepixel\MercadoLivre\Meli;
use common\models\ProdutoFilial;
use common\models\Filial;

class AnaliseAnunciosSemVinculoContaDuplicadaAction extends Action
{
     
    public function run(){
       
        echo "INÍCIO\n\n";
        
        $filial = Filial::find()->andWhere(["=", "id", 98])->one();
        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        $user = $meli->refreshAccessToken($filial->refresh_token_meli);
        $response = ArrayHelper::getValue($user, 'body');
        
        //Verifica se já existe o arquivo de log da função, exclui caso exista
        $arquivo_log = fopen("/var/tmp/log_produtos_sem_vinculo_mercado_livre_sp3_".date("Y-M-d H:i:s").".csv", "a");
        fwrite($arquivo_log, "meli_id;produto_filial_id;status;permalink");

        if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {
            $meliAccessToken = $response->access_token;

            $y = 0;
            $i = 0;
            
            $data_atual = date('Y-m-d');

            $e_processar = false; 
            
            $response_itens = $meli->get("/users/435343067/items/search?search_type=scan&status=active&limit=100&orders=price_asc&access_token=" . $meliAccessToken);

            //print_r($response_itens);die;
            
            while (ArrayHelper::getValue($response_itens, 'httpCode') <> 404){
                foreach (ArrayHelper::getValue($response_itens, 'body.results') as $meli_id){

                    echo "\n".$y++." - ".$meli_id; 
                    
                    if($meli_id == "MLB2020240745"){//
                        $e_processar = true;
                    }
                    
                    if(!$e_processar){
                        echo " - Pular";
                        continue;
                    }

                    $produto_filial_principal   = ProdutoFilial::find()->andWhere(['=','meli_id',$meli_id])->one();
                    $produto_filial_sem_juros   = ProdutoFilial::find()->andWhere(['=','meli_id_sem_juros',$meli_id])->one();
                    $produto_filial_full        = ProdutoFilial::find()->andWhere(['=','meli_id_full',$meli_id])->one();
                    $produto_filial_flex        = ProdutoFilial::find()->andWhere(['=','meli_id_flex',$meli_id])->one();
                    if (!($produto_filial_principal || $produto_filial_sem_juros || $produto_filial_full || $produto_filial_flex)){
                        echo " - ANÚNCIO SEM VÍNCULO";
                        fwrite($arquivo_log, "\n".$meli_id);
                        
                        $response_item = $meli->get('https://api.mercadolibre.com/items/'.$meli_id.'?access_token='.$meliAccessToken);
                        echo " - ".$response_item['body']->status./*" - ".$response_item['body']->permalink.*/" - ".$response_item['body']->available_quantity." - ".$response_item['body']->sold_quantity;
                        
                        $codigo_global  = "";
                        $atributos      = $response_item['body']->attributes;
                        foreach($atributos as $atributo){
                            if($atributo->id == "PART_NUMBER"){
                                $codigo_global = $atributo->value_name;
                                break;
                            }
                        }
                        echo " - ".$codigo_global;
                        
                        if($codigo_global != ""){
                            $produtos_filial_codigo_global = ProdutoFilial::find()  ->join("LEFT JOIN", "produto", "produto.id = produto_filial.produto_id")
                                                                                    ->join("LEFT JOIN", "filial", "filial.id = produto_filial.filial_id")
                                                                                    ->andWhere(["=", "codigo_global", $codigo_global])
                                                                                    ->andWhere(["is not", "refresh_token_meli", null])
                                                                                    ->andWhere(["is not", "meli_id", null])
                                                                                    ->andWhere(["<>", "filial_id", 94])
                                                                                    ->andWhere(["<>", "filial_id", 95])
                                                                                    ->andWhere(["<>", "filial_id", 98])
                                                                                    ->andWhere(["<>", "filial_id", 99])
                                                                                    ->andWhere(["<>", "filial_id", 100])
                                                                                    ->all();
                            
                            $e_produto_encontrado = false;                                                                                    
                                                                                    
                            foreach($produtos_filial_codigo_global as $k => $produto_filial_codigo_global){
                                
                                $produto_filial_duplicada = ProdutoFilial::find()   ->andWhere(["=", "produto_filial_origem_id", $produto_filial_codigo_global->id])
                                                                                    ->andWhere(["=", "filial_id", 98])
                                                                                    ->one();
                                if($produto_filial_duplicada){
                                    echo "\n        ".$produto_filial_duplicada->id."(SP3)";
                                    echo "\n        MELI_ID: ".$produto_filial_duplicada->meli_id;
                                    echo "\n        MELI_ID_SEM_JUROS: ".$produto_filial_duplicada->meli_id_sem_juros;
                                    echo "\n        MELI_ID_FULL: ".$produto_filial_duplicada->meli_id_full;
                                    echo "\n        MELI_ID_FLEX: ".$produto_filial_duplicada->meli_id_flex;
                                    
                                    if(is_null($produto_filial_duplicada->meli_id)){
                                        $produto_filial_alterar             = ProdutoFilial::find()->andWhere(["=", "id", $produto_filial_duplicada->id])->one();
                                        $produto_filial_alterar->meli_id    = $meli_id;
                                        if($produto_filial_alterar->save()){
                                            echo " - Produto vinculado (MELI_ID)";
                                            $e_produto_encontrado = true;
                                            fwrite($arquivo_log, "\n".$meli_id.";".$produto_filial_alterar->id.";Produto vinculado (MELI_ID);".$response_item['body']->permalink);
                                            break;
                                        }
                                        else{
                                            echo " - Produto não vinculado (MELI_ID)";
                                        }
                                    }
                                    if(is_null($produto_filial_duplicada->meli_id_sem_juros)){
                                        $produto_filial_alterar                     = ProdutoFilial::find()->andWhere(["=", "id", $produto_filial_duplicada->id])->one();
                                        $produto_filial_alterar->meli_id_sem_juros  = $meli_id;
                                        if($produto_filial_alterar->save()){
                                            echo " - Produto vinculado (MELI_ID_SEM_JUROS)";
                                            $e_produto_encontrado = true;
                                            fwrite($arquivo_log, "\n".$meli_id.";".$produto_filial_alterar->id.";Produto vinculado (MELI_ID);".$response_item['body']->permalink);
                                            break;
                                        }
                                        else{
                                            echo " - Produto não vinculado (MELI_ID_SEM_JUROS)";
                                        }
                                    }
                                    else{
                                        echo " - Produto duplicado não encontrado";
                                    }
                                }
                                else{
                                    echo " - Não encontrado SP3";
                                }
                            }
                            
                            if(!$e_produto_encontrado){
                                $body = ["available_quantity" => 0];
                                $response = $meli->put("items/".$meli_id."?access_token=" . $meliAccessToken, $body, []);
                                if($response['httpCode'] < 300){
                                    fwrite($arquivo_log, "\n".$meli_id.";;Produto pausado;".$response_item['body']->permalink);
                                    echo " - produto pausado";
                                }
                                else{
                                    fwrite($arquivo_log, "\n".$meli_id.";;Produto não pausado;".$response_item['body']->permalink);
                                    echo " - produto pausado";
                                }
                            }
                        }
                        else{
                            echo " - sem CODIGO_GLOBAL";
                            $body = ["available_quantity" => 0];
                            $response = $meli->put("items/".$meli_id."?access_token=" . $meliAccessToken, $body, []);
                            if($response['httpCode'] < 300){
                                fwrite($arquivo_log, "\n".$meli_id.";;Produto pausado;".$response_item['body']->permalink);
                                echo " - produto pausado";
                            }
                            else{
                                fwrite($arquivo_log, "\n".$meli_id.";;Produto não pausado;".$response_item['body']->permalink);
                                echo " - produto pausado";
                            }
                        }                        
                    }
                    else{
                        echo " - Produto com vínculo";
                    }
                }

                echo "\n Scroll: ".$i++;
                $response_itens = $meli->get("/users/435343067/items/search?search_type=scan&scroll_id=".ArrayHelper::getValue($response_itens, 'body.scroll_id')."&limit=100&access_token=" . $meliAccessToken);
            }
        }
        
        fclose($arquivo_log);

        echo "\n\nFIM!\n\n";
    }
}
