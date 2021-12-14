<?php

namespace console\controllers\actions\mercadolivre;


use common\models\Filial;
use common\models\MarcaProduto;
use common\models\ProdutoCondicao;
use common\models\ProdutoFilial;
use Livepixel\MercadoLivre\Meli;
use Yii;
use yii\helpers\ArrayHelper;

class CreateMGAction extends Action
{
    public function run()
    {

	//die;

	$produtosArray = Array();
	//$file = fopen("/var/tmp/morelate_subir_ml_0-2000.csv", 'r');
	//$file = fopen("/var/tmp/morelate_subir_ml_2000-4000.csv", 'r');
	//$file = fopen("/var/tmp/morelate_subir_ml_4000-6000.csv", 'r');
	//$file = fopen("/var/tmp/morelate_subir_ml_6000-8000.csv", 'r');
	//$file = fopen("/var/tmp/morelate_subir_ml_8000-1000.csv", 'r');
	//$file = fopen("/var/tmp/morelate_subir_ml_10000-12000.csv", 'r');
	//$file = fopen("/var/tmp/morelate_subir_ml_12000-14000.csv", 'r');
	//$file = fopen("/var/tmp/morelate_subir_ml_14000-16000.csv", 'r');
	//$file = fopen("/var/tmp/morelate_subir_ml_16000-18000.csv", 'r');
	//$file = fopen("/var/tmp/morelate_subir_ml_18000-20000.csv", 'r');
	//$file = fopen("/var/tmp/morelate_subir_ml_20000-22000.csv", 'r');
	//$file = fopen("/var/tmp/morelate_subir_ml_22000-24000.csv", 'r');
	//$file = fopen("/var/tmp/morelate_subir_ml_24000-29000.csv", 'r');
	$file = fopen("/var/tmp/lusar_20-05-2021_criar_precificado.csv", 'r');
	
	while (($line = fgetcsv($file,null,';')) !== false)
	{
	    $produtosArray[] = $line[0].".M";
	    //print_r($line);
	}
	fclose($file);
	
	$arquivo_log = fopen("/var/tmp/log_mercado_livre_create_".date("Y-m-d_H-i-s").".csv", "a");
        fwrite($arquivo_log, "produto_filial_id;permalink;status");

        echo "Criando produtos...\n\n";
        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        $filials = Filial::find()
            ->andWhere(['IS NOT', 'refresh_token_meli', null])
            ->andWhere(['id' => [99]])
            ->andWhere(['<>', 'id', 98])
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
        		    //->joinWith('produto')
                    //->andWhere(['is','meli_id',NULL])
        		    //->andWhere(['=','meli_id',''])
        		    ->where(" (meli_id is null or meli_id = '') and quantidade > 0 and produto_filial.id in (select distinct produto_filial_id from valor_produto_filial) ")
                    //->andWhere(['>','quantidade',0])
        		    //->andWhere(['=','produto_filial.id',240780])
        		    //->andWhere(['produto_filial.produto_id' => [13327,314111]])
        		    //->andWhere(['<>','produto_filial.id',143972])
        		    //->andWhere(['<>','produto_filial.id',138789])
        		    ->orderBy('id')
                    ->all();

                /* @var $produtoFilial ProdutoFilial */
                foreach ($produtoFilials as $k => $produtoFilial) {          
                    echo "\n".$k." - ".$produtoFilial->id;//." - ".$produtoFilial->produto->nome;
                    
        		    /*if(!array_search($produtoFilial->produto->codigo_fabricante,$produtosArray)){
        		        echo " - produto não encontrado";
        		        continue;
        		    }*/
        		    
        		    //echo " - Produto encontrado";
        		    //continue;

                    $subcategoriaMeli = $produtoFilial->produto->subcategoria->meli_id;
                    if (!isset($subcategoriaMeli)) {
                        Yii::error("Produto Filial: {$produtoFilial->produto->nome}({$produtoFilial->id}), não categoria",
                            'error_yii');
                        echo " - SEM SUBCATEGORIA";
                        continue;
                    }
        		    //$subcategoriaMeli = "MLB191833"; //Subcategoria para 70,82 - Sem ME;
        		    /*if ($subcategoriaMeli == "MLB117382" || $subcategoriaMeli == "MLB63569" || $subcategoriaMeli == "MLB188063" || $subcategoriaMeli == "MLB193148" || $subcategoriaMeli == "MLB49274"){
        			$subcategoriaMeli = "MLB191833";
        		    }*/

                    /*if (is_null($produtoFilial->valorMaisRecente)) {
                        Yii::error("Produto Filial: {$produtoFilial->produto->nome}({$produtoFilial->id}), não possui valor",
                            'error_yii');
                        echo " - PRODUTO SEM PRECO";
                        continue;
                    }*/

                    //$page = $this->controller->renderFile(__DIR__ . '/../../../../lojista/views/mercado-livre/produto.php',['produto' => $produtoFilial]);
                    $page = $this->controller->renderFile(__DIR__ . '/../../../../lojista/views/mercado-livre/produtoML.php',['produto' => $produtoFilial]);
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

        		    $page = substr($page,0,5000);

        		    $title = Yii::t('app', '{nome}', ['nome' => $produtoFilial->produto->nome ]);
       		    
        		    echo "\n==>"; print_r($title); echo "<==";
        		    
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
                    
                    switch ($produtoFilial->envio) {
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
                    }

                    /*$condicao = "new";
                    if($produtoFilial->produto->e_usado){
                            $condicao = "used";
                    }*/

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
                    $categoria_meli_id  = "MLB101764";

                    $nome = str_replace(" ","%20",$titulo_novo);

                    $response_categoria_recomendada = $meli->get("sites/MLB/domain_discovery/search?q=".$nome);

                    if ($response_categoria_recomendada['httpCode'] >= 300) {
                        echo " - ERRO Categoria Recomendada";
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
            		"description" => ["plain_text" => $page],//utf8_encode($page)],
            		//"plain_text" => $page,
            		"pictures" => $produtoFilial->produto->getUrlImagesML(),
                        /*"pictures" => [
                            ["source" => utf8_encode($produtoFilial->produto->getUrlImageML())],
                        ],*/
                        "shipping" => [
                            "mode" => $modo,
                            "local_pick_up" => true,
                            "free_shipping" => false,
                            "free_methods" => [],
                        ],
                        //"warranty" => "3 meses contra defeitos de fabricação.",
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
                    //print_r($body); //die;
                    
                    $response = $meli->post("items?access_token=" . $meliAccessToken,$body);

                    Yii::info(ArrayHelper::merge($response, ['request' => $body]), 'mercado_livre_create');
                    if ($response['httpCode'] >= 300) {
                        //Yii::error($response['body'], 'mercado_livre_create');
                        print_r($response);
                        //print_r($body);
                        fwrite($arquivo_log, "\n".$produtoFilial->id.";;erro");
                        echo "erro";
                    } else {
                        $produtoFilial->meli_id = $response['body']->id;
                        
                        $meli_salvo = ($produtoFilial->save() ? "meli_id salvo" : "meli_id não salvo");

                        echo ArrayHelper::getValue($response, 'body.permalink')." - ok";
                        fwrite($arquivo_log, "\n".$produtoFilial->id.";".ArrayHelper::getValue($response, 'body.permalink').";ok;".$meli_salvo);
                        
                    }
                    //die;
                }
            }
            //die;
        }

	fclose($arquivo_log);


    }
}
