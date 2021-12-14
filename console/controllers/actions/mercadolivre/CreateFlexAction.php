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

class CreateFlexAction extends Action
{
    public function run()
    {

        $arquivo_log = fopen("/var/tmp/log_mercado_livre_create_flex_".date("Y-m-d_H-i-s").".csv", "a");
        fwrite($arquivo_log, "produto_filial_id;permalink;status");

        echo "Criando produtos...\n\n";
        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        $filials = Filial::find()   ->andWhere(['IS NOT', 'refresh_token_meli', null])
                                    ->andWhere(['id' => [96]])
                                    //->andWhere(['<>', 'id', 98])
                                    ->orderBy(["id" => SORT_ASC])
                                    ->all();
        
        $filial_conta_duplicada = Filial::find()->andWhere(['=','id',98])->one();
        $user_conta_duplicada = $meli->refreshAccessToken($filial_conta_duplicada->refresh_token_meli);
        $response_conta_duplicada = ArrayHelper::getValue($user_conta_duplicada, 'body');
        $meliAccessToken_conta_duplicada = $response_conta_duplicada->access_token;        

        foreach ($filials as $filial) {
            echo "\n\n==>".$filial->nome."<==\n\n";
            $user = $meli->refreshAccessToken($filial->refresh_token_meli);
            $response = ArrayHelper::getValue($user, 'body');
            if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {
                $meliAccessToken = $response->access_token;

                $produtoFilials = $filial->getProdutoFilials()
        		    ->where("   (meli_id_flex is null or meli_id_flex = '') 
                                and quantidade > 0 
                                and produto_filial.id in (select distinct produto_filial_id from valor_produto_filial) ")
        		    ->orderBy('id')
                    ->all();

                /* @var $produtoFilial ProdutoFilial */
                foreach ($produtoFilials as $k => $produtoFilial) {          
                    echo "\n".$k." - ".$produtoFilial->id;//." - ".$produtoFilial->produto->nome;
                    
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
                    
                    /*switch ($produtoFilial->envio) {
                        case 1:
                            $modo = "me2";
                            break;
                        case 2:
                            $modo = "not_specified";
                            break;
                        case 3:
                            $modo = "custom";
                            break;
                        default:
                            $modo = "me2";
                            break;
                    }*/
                    $modo = "me2";
                    

                    //Atualização da Condição
                    $produto_condicao = ProdutoCondicao::find()->andWhere(['=', 'id', $produtoFilial->produto->produto_condicao_id])->one();

                    $condicao = "new";
                    $condicao_id = "2230284";
                    $condicao_name = "Novo";
                    if($produto_condicao){
                        switch ($produto_condicao->meli_id){
                            case "new":
                                $condicao = "new";
                                $condicao_id = "2230284";
                                $condicao_name = "Novo";
                                break;
                            case "used":
                                $condicao = "used";
                                $condicao_id = "2230581";
                                $condicao_name = "Usado";
                                break;
                            case "recondicionado":
                                $condicao = "new";
                                $condicao_id = "2230582";
                                $condicao_name = "Recondicionado";
                                break;
                            default:
                                $condicao = "new";
                                $condicao_id = "2230284";
                                $condicao_name = "Novo";
                        }
                    };

                    $marca_produto = MarcaProduto::find()->andWhere(['=','id',$produtoFilial->produto->marca_produto_id])->one();
                    $marca = "OPT";
                    if($marca_produto){
                        $marca = $marca_produto->nome."/CONSULTAR";
                    }

                    //Obter dados da categoria recomendada                    
                    $categoria_meli_id  = "";
                    
                    $nome_array         = explode(" ", $titulo_novo);
                    $nome               = $nome_array[0]."%20".((array_key_exists(1,$nome_array)) ? $nome_array[1] : "");
                    /*foreach($nome_array as $i => $nome_explode){
                        if($i>=2) break;
                        $nome .= "%20".$nome_array[$i+1];
                    }*/
                    echo "\n".$nome; //continue;

                    $response_categoria_recomendada = $meli->get("sites/MLB/domain_discovery/search?q=".$nome);
                    //print_r($response_categoria_recomendada);
                    if ($response_categoria_recomendada['httpCode'] >= 300) {
                        echo " - ERRO Categoria Recomendada";
                        
                        $categoria_meli_id = $produtoFilial->produto->subcategoria->meli_id;
                    }
                    else {
                        echo " - OK Categoria Recomendada";
                        $categoria_meli_id      = ArrayHelper::getValue($response_categoria_recomendada, 'body.0.category_id');
                        $categoria_meli_nome    = ArrayHelper::getValue($response_categoria_recomendada, 'body.0.category_name');
                        echo " - ".$categoria_meli_id.' - '.$categoria_meli_nome;
                    }
                                       
                    //continue;
                    
                    $body = [
                        //"title" => (strlen($title) <= 60) ? $title : substr($title, 0,60),
                        "title" => mb_substr($titulo_novo,0,60),
                        "category_id" => utf8_encode($categoria_meli_id),
                        "listing_type_id" => "bronze",
                        "currency_id" => "BRL",
                        "price" => utf8_encode(round($produtoFilial->getValorMercadoLivre(), 2)),
                        "available_quantity" => utf8_encode($produtoFilial->quantidade),
                        "seller_custom_field" =>utf8_encode($produtoFilial->id),
                        "condition" => $condicao,
            			"pictures" => $produtoFilial->produto->getUrlImagesML(),
                        "shipping" => [
                            "mode" => $modo,
                            "local_pick_up" => true,
                            "free_shipping" => false,
                            "free_methods" => [],
                            "tags" => ["self_service_in"],
                        ],
                        "sale_terms" => [
                                             [       "id" => "WARRANTY_TYPE",
                                                     "value_id" => "2230280"
                                             ],
                                             [       "id" => "WARRANTY_TIME",
                                                     "value_name" => "3 meses"
                                             ]
                                        ],
                        'attributes' =>[
                                [
                                'id'                    => 'PART_NUMBER',
                                'name'                  => 'Número de peça',
                                'value_id'              => null,
                                'value_name'            => $produtoFilial->produto->codigo_global,
                                'value_struct'          => null,
                                'values'                => [[
                                        'id'    => null,
                                        'name'  => $produtoFilial->produto->codigo_global,
                                        'struct'=> null,
                                ]],
                                'attribute_group_id'    => "OTHERS",
                                'attribute_group_name'  => "Outros"
                                ],
                                [
                                    "id"=> "BRAND",
                                    "name"=> "Marca",
                                    "value_id"=> null,
                                    "value_name"=> $marca,
                                    "value_struct"=> null,
                                    "attribute_group_id"=> "OTHERS",
                                    "attribute_group_name"=> "Outros"
                                ],
                                [
                                    'id'                    => 'ITEM_CONDITION',
                                    'name'                  => 'Condição do item',
                                    'value_id'              => $condicao_id,
                                    'value_name'            => $condicao_name,
                                    'value_struct'          => null,
                                    'values'                => [[
                                        'id'    => $condicao_id,
                                        'name'  => $condicao_name,
                                        'struct'=> null,
                                    ]],
                                    'attribute_group_id'    => "OTHERS",
                                    'attribute_group_name'  => "Outros"
                                ]
                          ]

                    ];
                    
                    $body_principal            = $body;
                    $body_principal["official_store_id"] = 3627;

                    $response = $meli->post("items?access_token=" . $meliAccessToken,$body_principal);

                    Yii::info(ArrayHelper::merge($response, ['request' => $body]), 'mercado_livre_create');
                    if ($response['httpCode'] >= 300) {
                        print_r($body_principal);
                        print_r($response);
                        //print_r($body);
                        fwrite($arquivo_log, "\n".$produtoFilial->id.";;erro");
                        echo "erro";
                        //die;
                    } else {
                        $produtoFilial->meli_id_flex = $response['body']->id;
                        
                        $meli_salvo = ($produtoFilial->save() ? "meli_id salvo" : "meli_id não salvo");
                        
                        FunctionsML::atualizarDescricao($produtoFilial->produto);

                        echo "\n".ArrayHelper::getValue($response, 'body.permalink')." - ok";
                        fwrite($arquivo_log, "\n".$produtoFilial->id.";".ArrayHelper::getValue($response, 'body.permalink').";ok;".$meli_salvo);
                        
                        $produtos_filiais_conta_duplicada = ProdutoFilial::find()->andWhere(['=', 'produto_filial_origem_id', $produtoFilial->id])->all();
                        
                        foreach ($produtos_filiais_conta_duplicada as $i => $produto_filial_conta_duplicada) {
                            $user_outro     = $meli->refreshAccessToken($produto_filial_conta_duplicada->filial->refresh_token_meli);
                            $response_outro = ArrayHelper::getValue($user_outro, 'body');
                            
                            if (is_object($response_outro) && ArrayHelper::getValue($user_outro, 'httpCode') < 400) {
                                $meliAccessToken_outro = $response_outro->access_token;
                                
                                $response = $meli->post("items?access_token=" . $meliAccessToken_outro, $body);
                                //echo "<pre>"; print_r($response); echo "</pre>";
                                if ($response['httpCode'] >= 300) {
                                    fwrite($arquivo_log, ';Produto não criado no ML(Duplicado');
                                } else {
                                    $produto_filial_conta_duplicada->meli_id_flex = $response['body']->id;
                                    if ($produto_filial_conta_duplicada->save()) {
                                        FunctionsML::atualizarDescricao($produto_filial_conta_duplicada->produto);
                                        
                                        //$produto_filial_conta_duplicada->produto->atualizarMLDescricao();
                                        echo "\n".ArrayHelper::getValue($response, 'body.permalink')." - ok(Conta duplicada)";
                                    } else {
                                        print_r($response);
                                        fwrite($arquivo_log, "\n".$produtoFilial->id.";;erro(Conta duplicada)"); 
                                    }
                                }
                            }
                        }
                    }
                    //die;
                }
            }
        }

        fclose($arquivo_log);

    }
}
