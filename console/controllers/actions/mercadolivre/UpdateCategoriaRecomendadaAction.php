<?php

namespace console\controllers\actions\mercadolivre;

use common\models\Filial;
use common\models\MarcaProduto;
use common\models\ProdutoCondicao;
use common\models\ProdutoFilial;
use Livepixel\MercadoLivre\Meli;
use Yii;
use yii\helpers\ArrayHelper;
use backend\functions\FunctionsML;

class UpdateCategoriaRecomendadaAction extends Action
{
    public function run()
    {

        $arquivo_log = fopen("/var/tmp/log_atualizacao_categoria_recomendada".date("Y-m-d_H-i-s").".csv", "a");
        fwrite($arquivo_log, "produto_filial_id;permalink;status");

        echo "Alterando produtos...\n\n";
        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        $filials = Filial::find()   ->andWhere(['IS NOT', 'refresh_token_meli', null])
                                 
                                    ->orderBy(["id" => SORT_ASC])
                                    ->all();
        
        foreach ($filials as $filial) {
            echo "\n\n==>".$filial->id." - ".$filial->nome."<==\n\n";

            $user = $meli->refreshAccessToken($filial->refresh_token_meli);
            $response = ArrayHelper::getValue($user, 'body');
            print_r($response);
            die;
            if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 300) {
                $meliAccessToken = $response->access_token;

                $produtoFilials = $filial->getProdutoFilials()
                            		    ->where("   quantidade > 0
                                                    and produto_filial.id in (select distinct produto_filial_id from valor_produto_filial)
                                                    and filial_id not in (98, 100)
                                                   ")
                            		    ->orderBy('id')
                                        ->all();

                
                foreach ($produtoFilials as $k => $produtoFilial) {          
                    echo "\n".$k." - ".$produtoFilial->id;

                    
                    $title = Yii::t('app', '{nome}', ['nome' => $produtoFilial->produto->nome ]);
       		    
        		    $nome = $produtoFilial->produto->nome;
        		    
        		    if(preg_match_all("/[0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9]/", $nome, $out, PREG_PATTERN_ORDER)){
                        $title = preg_replace('/[0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9]/', '@11@', $nome);
                    }
                    elseif(preg_match_all("/[0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9]/", $nome, $out, PREG_PATTERN_ORDER)){
                        $title = preg_replace('/[0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9]/', '@10@', $nome);
                    }
                    elseif(preg_match_all("/[0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9]/", $nome, $out, PREG_PATTERN_ORDER)){
                        $title = preg_replace('/[0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9]/', '@9@', $nome);
                    }
                    elseif(preg_match_all("/[0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9]/", $nome, $out, PREG_PATTERN_ORDER)){
                        $title = preg_replace('/[0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9]/', '@8@', $nome);
                    }
                    
                    $titulo_novo = mb_convert_encoding($title, 'UTF-8', 'UTF-8');
                    
                    $modo = "me2";
                  
                    $categoria_meli_id  = "";
                    $nome_array         = explode(" ", $titulo_novo);
                    $nome               =   $nome_array[0]
                                            .((array_key_exists(1,$nome_array)) ? "%20".$nome_array[1] : "")
                                            .((array_key_exists(2,$nome_array)) ? "%20".$nome_array[2] : "");
                    
                    $categoria_meli_id = "MLB191833";                    
                                            
                    $response_categoria_recomendada = $meli->get("sites/MLB/domain_discovery/search?q=".$nome);
                   
                    if ($response_categoria_recomendada['httpCode'] >= 300) {
                        echo " - ERRO Categoria Recomendada";
                    }
                    else {
                        foreach($response_categoria_recomendada["body"] as $j => $categoria_recomendada){
                          
                            $pos = strpos($categoria_recomendada->domain_id, "AUTOMOT");
                            
                            if ($pos === false) {
                                echo "  - Categoria NÃO AUTO";
                            } else {
                                echo " - OK Categoria Recomendada - Categoria AUTO";
                                $categoria_meli_id      = ArrayHelper::getValue($response_categoria_recomendada, 'body.0.category_id');
                                $categoria_meli_nome    = ArrayHelper::getValue($response_categoria_recomendada, 'body.0.category_name');
                                echo " - ".$categoria_meli_id.' - '.$categoria_meli_nome;
                                
                                break;
                            }
                            
                        }
                    }                   
                    
                    $body = [
                        
                        "category_id" => utf8_encode($categoria_meli_id)                        
                    ];
                    
                    $body_principal            = $body;
                    $body_principal['official_store_id'] = 3627;
                     
                    if(is_null($produtoFilial->meli_id)){
                        $response = $meli->post("items?access_token=" . $meliAccessToken,$body_principal);
                        if ($response['httpCode'] >= 300) {
                            print_r($response);
                            fwrite($arquivo_log, ';Produto não criado no ML(Principal');
                        } else {
                            $produtoFilial->meli_id = $response['body']->id;
                            if ($produtoFilial->save()) {
                                FunctionsML::atualizarDescricao($produtoFilial->produto);                                
                                
                                echo "\n".ArrayHelper::getValue($response, 'body.permalink')." - Ok(Principal)";
                            } else {
                                print_r($response);
                                fwrite($arquivo_log, "\n".$produtoFilial->id.";;erro(Principal)");
                            }
                        }
                    }
                    else{
                        echo " - Produto já no ML";
                    }

                    

                    $produtos_filiais_conta_duplicada = ProdutoFilial::find()   ->andWhere(['=', 'produto_filial_origem_id', $produtoFilial->id])                                                                               
                                                                                ->all();
                    
                    foreach ($produtos_filiais_conta_duplicada as $i => $produto_filial_conta_duplicada) {
                        if(is_null($produto_filial_conta_duplicada->meli_id)){
                            $user_outro     = $meli->refreshAccessToken($produto_filial_conta_duplicada->filial->refresh_token_meli);
                            $response_outro = ArrayHelper::getValue($user_outro, 'body');
                            if (is_object($response_outro) && ArrayHelper::getValue($user_outro, 'httpCode') < 300) {
                                echo "\nLogou no ML";
                                $meliAccessToken_outro = $response_outro->access_token;
                                $response = $meli->post("items?access_token=" . $meliAccessToken_outro, $body);
                                
                                if ($response['httpCode'] >= 300) {
                                    print_r($response);
                                    fwrite($arquivo_log, ';Produto não criado no ML(Duplicado');
                                } else {
                                    $produto_filial_conta_duplicada->meli_id = $response['body']->id;
                                    if ($produto_filial_conta_duplicada->save()) {
                                        FunctionsML::atualizarDescricao($produto_filial_conta_duplicada->produto);
                                        
                                        
                                        echo "\n".ArrayHelper::getValue($response, 'body.permalink')." - ok(Conta duplicada)";
                                    } else {
                                        print_r($response);
                                        fwrite($arquivo_log, "\n".$produtoFilial->id.";;erro(Conta duplicada)");
                                    }
                                }
                                
                            }
                            else{
                                echo "\nNão logou no ML";
                            }
                        }
                        else{
                            echo " - Produto já no ML (Duplicado)";
                        }
                    }
                }
            }
        }

        fclose($arquivo_log);

    }
}
