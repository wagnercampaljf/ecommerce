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

class CreateSPDuplicadaCorrecaoFlexAction extends Action
{
    public function run()
    {

        $arquivo_log = fopen("/var/tmp/log_mercado_livre_create_SP3_".date("Y-m-d_H-i-s").".csv", "a");
        fwrite($arquivo_log, "produto_filial_id;permalink;status");

        echo "Criando produtos...\n\n";
        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        $filials = Filial::find()   ->andWhere(['IS NOT', 'refresh_token_meli', null])
                                    ->andWhere(['id' => [96]])
                                    ->orderBy(["id" => SORT_ASC])
                                    ->all();
        
        foreach ($filials as $filial) {
            echo "\n\n==>".$filial->id." - ".$filial->nome."<==\n\n";

            $user = $meli->refreshAccessToken($filial->refresh_token_meli);
            $response = ArrayHelper::getValue($user, 'body');
            if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 300) {
                $meliAccessToken = $response->access_token;

                $produtoFilials = $filial->getProdutoFilials()
        		    ->where("   quantidade > 0
                                and produto_filial.id in (select distinct produto_filial_id from valor_produto_filial)
                                and filial_id not in (98, 100) ")
        		    ->orderBy('id')
                    ->all();

                /* @var $produtoFilial ProdutoFilial */
                foreach ($produtoFilials as $k => $produtoFilial) {          
                    echo "\n".$k." - ".$produtoFilial->id;//." - ".$produtoFilial->produto->nome;

                    if($k < 0){
                        echo " - Pular!!";
                        continue;
                    }
                    
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
                    $nome               =   $nome_array[0]
                                            .((array_key_exists(1,$nome_array)) ? "%20".$nome_array[1] : "")
                                            .((array_key_exists(2,$nome_array)) ? "%20".$nome_array[2] : "");
                    /*foreach($nome_array as $i => $nome_explode){
                        if($i>=2) break;
                        $nome .= "%20".$nome_array[$i+1];
                    }*/
                    //echo "\n".$nome; //continue;

                    $response_categoria_recomendada = $meli->get("sites/MLB/domain_discovery/search?q=".$nome);
                    //print_r($response_categoria_recomendada);
                    if ($response_categoria_recomendada['httpCode'] >= 300) {
                        echo " - ERRO Categoria Recomendada";
                        
                        //print_r($response_categoria_recomendada);
                        
                        $categoria_meli_id = "MLB191833";
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
                        "price" => utf8_encode(round(($produtoFilial->getValorMercadoLivre()*1.05), 2)),
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
                                ],
                                [
                                    'id' => 'SKU',
                                    'name' => 'SKU',
                                    'value_id' => null,
                                    'value_name' => $produtoFilial->id.'_meli_id',
                                    'value_struct' => null,
                                    'values' => [
                                        [
                                            'id' => null,
                                            'name' => $produtoFilial->id.'_meli_id',
                                            'struct' => null
                                        ]
                                    ],
                                    'attribute_group_id' => "OTHERS",
                                    'attribute_group_name' => "Outros"
                                ]
                          ]
                    ];
                    
                    $produtos_filiais_conta_duplicada = ProdutoFilial::find()   ->andWhere(['=', 'produto_filial_origem_id', $produtoFilial->id])
                                                                                ->andWhere(["is", "meli_id_full", null])
                                                                                ->andWhere(["=", "filial_id", 98])
                                                                                ->all();
                    
                    foreach ($produtos_filiais_conta_duplicada as $i => $produto_filial_conta_duplicada) {
                        if(is_null($produto_filial_conta_duplicada->meli_id_full)){
                            $user_outro     = $meli->refreshAccessToken($produto_filial_conta_duplicada->filial->refresh_token_meli);
                            $response_outro = ArrayHelper::getValue($user_outro, 'body');
                            if (is_object($response_outro) && ArrayHelper::getValue($user_outro, 'httpCode') < 300) {
                                echo "\nLogou no ML";
                                $meliAccessToken_outro = $response_outro->access_token;
                                $response = $meli->post("items?access_token=" . $meliAccessToken_outro, $body);
                                //echo "<pre>"; print_r($response); echo "</pre>";
                                if ($response['httpCode'] >= 300) {
                                    print_r($response);
                                    fwrite($arquivo_log, ';Produto não criado no ML(Duplicado');
                                } else {
                                    $produto_filial_conta_duplicada->meli_id_full = $response['body']->id;
                                    if ($produto_filial_conta_duplicada->save()) {
                                        FunctionsML::atualizarDescricao($produto_filial_conta_duplicada->produto);
                                        
                                        //$produto_filial_conta_duplicada->produto->atualizarMLDescricao();
                                        echo "\n".ArrayHelper::getValue($response, 'body.permalink')." - ok(Conta duplicada)";
                                    } else {
                                        print_r($response);
                                        fwrite($arquivo_log, "\n".$produtoFilial->id.";;erro(Conta duplicada)");
                                    }
                                }
                                //die;
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
