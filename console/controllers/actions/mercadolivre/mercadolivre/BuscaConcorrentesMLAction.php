<?php

namespace console\controllers\actions\mercadolivre;

use yii\helpers\ArrayHelper;
use function GuzzleHttp\json_encode;
use function GuzzleHttp\json_decode;
use Livepixel\MercadoLivre\Meli;
use common\models\ProdutoFilial;
use common\models\Imagens;

class BuscaConcorrentesMLAction extends Action
{

    
    public function run($cliente = 1){
       
        echo "INÍCIO\n\n";
        
        // Escreve no log
        $arquivo_log = fopen("/var/tmp/busca_produtos_clonados_".date("Y-m-d_H-i-s").".csv", "a");
        fwrite($arquivo_log, "produto_filial_id;meli_id;Peça Agora;Preço Peça Agora;Empresa Cópia;URL Cópia;Preço Cópia\n");
        
        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        $user = $meli->refreshAccessToken("TG-5d3ee920d98a8e0006998e2b-193724256");
        $response = ArrayHelper::getValue($user, 'body');

        if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {
            $meliAccessToken = $response->access_token;
            
            echo "\n\n".$meliAccessToken."\n\n";
            
            $produtos_filiais = ProdutoFilial::find()   ->andWhere(['is not','meli_id',null])
                                                        ->andWhere(['<>','filial_id',43])
                                                        ->andWhere(['<>','filial_id',98])
                                                        ->andWhere(['<>','filial_id',77])
                                                        ->andWhere(['<>','meli_id',''])
                                                        ->andWhere(['>','quantidade',0])
                                                        ->addOrderBy('id')
                                                        ->all();
            foreach($produtos_filiais as $k => $produto_filial){
                echo "\n".$k." - ".$produto_filial->meli_id." - ".$produto_filial->produto->codigo_global;
                
                /*if($produto_filial->meli_id == "MLB864728556"){
                    break;
                }*/
                
                //if ($k < 3012){//3497){
                //    continue;
                //}
                if ($k < 40000){//3497){
                    continue;
                }
                
                $permalink_peca = "";
                $preco_peca = 0;
                $response_permalink = $meli->get('/items/'.$produto_filial->meli_id);
                if(ArrayHelper::getValue($response_permalink, 'httpCode') > 300){
                    continue;
                }
                else{
                    if(ArrayHelper::getValue($response_permalink, 'body.status') == "active"){
                        $permalink_peca = ArrayHelper::getValue($response_permalink, 'body.permalink');
                        $preco_peca     = ArrayHelper::getValue($response_permalink, 'body.price');
                    }
                    else{
                        continue;
                    }
                }
                
                $codigo_global = str_replace("_","",str_replace(".","",str_replace(",","",str_replace("CX.","",$produto_filial->produto->codigo_global))));
                echo " - ".$codigo_global;
                
                $response_busca = $meli->get('/sites/MLB/search?q="'.$codigo_global.'"&search_type=scan');
                
                if(ArrayHelper::getValue($response_busca, 'body.paging.total') > 0){
                    foreach (ArrayHelper::getValue($response_busca, 'body.results') as $busca){
                        //print_r($busca); die;
                        $response_descricao = $meli->get("/items/".$busca->id."/description");
                        $texto = "";
                        if(ArrayHelper::getValue($response_descricao, 'httpCode') < 300){
                            $texto  = ArrayHelper::getValue($response_descricao, 'body.plain_text');
                        }
                        $titulo = ArrayHelper::getValue($response_busca, 'title');
                        if (strpos($texto, $codigo_global) || strpos($titulo, $codigo_global)){
                            echo "\n    Mesmo Produto";
                            
                            fwrite($arquivo_log, $produto_filial->id.";".$produto_filial->meli_id.";".$permalink_peca.";".$preco_peca.";".ArrayHelper::getValue($busca, 'seller.id').";".ArrayHelper::getValue($busca, 'permalink').";".ArrayHelper::getValue($busca, 'price')."\n");
                        }
                        else{
                            echo "\n    Produto Diferente";
                        }
                    }
                    //die;
                }
                else{
                    echo " - Nada encontrado";
                }
            }
            
            
            //die;
            
            /*$x = 0;
            $i = 0;
            $response_order = $meli->get("/users/193724256/items/search?search_type=scan&limit=100&access_token=" . $meliAccessToken);
            
            while (ArrayHelper::getValue($response_order, 'httpCode') <> 404){
                
                if ($i >= 0){
                    foreach (ArrayHelper::getValue($response_order, 'body.results') as $meli_id){
                        
                        $response_item = $meli->get("/items/".$meli_id."?access_token=" . $meliAccessToken);

                        echo "\n".++$x." - MELI_ID: ".$meli_id;//." - "." - ".ArrayHelper::getValue($response_item, 'body.permalink')."\n";
                        
                        $response_busca = $meli->get('/sites/MLB/search?q="'.str_replace(" ", "%20", ArrayHelper::getValue($response_item, 'body.title')).'"&search_type=scan');

                        $produto_foi_encontrado = false;
                        foreach (ArrayHelper::getValue($response_busca, 'body.results') as $busca){
                            
                            if ($busca->seller->id == 30314367 && $busca->title == ArrayHelper::getValue($response_item, 'body.title')){ //GALVAOL1046
                            //if ($busca->seller->id <> 30314367 && $busca->seller->id <> 193724256 && $busca->seller->id <> 390464083 && $busca->title == ArrayHelper::getValue($response_item, 'body.title')){ //GALVAOL1046
                                $response_descricao = $meli->get("/items/".$busca->id."/description");
                                
                                if (ArrayHelper::getValue($response_descricao, 'httpCode') < 300){
                                    $texto = ArrayHelper::getValue($response_descricao, 'body.plain_text');
                                                                        
                                    if (strpos($texto, "PEÇA AGORA") || strpos($texto, "Peça Agora")){
                                        $response_data_criacao = $meli->get("/items/".$busca->id);

                                        fwrite($arquivo_log, ArrayHelper::getValue($response_item, 'body.permalink').";".ArrayHelper::getValue($response_item, 'body.price').";".ArrayHelper::getValue($response_item, 'body.sold_quantity').";".ArrayHelper::getValue($response_item, 'body.date_created').";".$busca->seller->id.";".$busca->permalink.";".$busca->price.";".$busca->sold_quantity.";".ArrayHelper::getValue($response_data_criacao, 'body.date_created'));
                                        
                                        $produto_filial = ProdutoFilial::find()->andWhere(['=','meli_id',$meli_id])->one();
                                        if ($produto_filial){
                                            $imagem = Imagens::find()->andWhere(['=','produto_id',$produto_filial->produto_id])->one();
                                            if ($imagem){
                                                fwrite($arquivo_log, ";https://www.pecaagora.com/site/get-link?produto_id=".$produto_filial->produto_id."&ordem=".$imagem->ordem."\n");
                                            }
                                            else{
                                                fwrite($arquivo_log, ";\n");
                                            }
                                        }
                                        else{
                                            fwrite($arquivo_log, ";\n");
                                        }
                                        
                                        $produto_foi_encontrado = true;
                                    }
                                }
                            }
                        }
                        if (!$produto_foi_encontrado) {
                            echo " - Não encontradas  cópias do produto";
                        }
                        else {
                            echo " - Produto encontrado";
                        }
                    }
                }
                
                echo "\n Scroll: ".$i++;
                $response_order = $meli->get("/users/193724256/items/search?search_type=scan&scroll_id=".ArrayHelper::getValue($response_order, 'body.scroll_id')."&limit=100&access_token=" . $meliAccessToken);
            }*/
        }
        
        fclose($arquivo_log);
        
        echo "\n\nFIM!\n\n";
    }
}
 