
<?php
/**
 * Created by PhpStorm.
 * User: igorm
 * Date: 29/06/2016
 * Time: 16:49
 */
namespace console\controllers\actions\mercadolivre;


use common\models\Filial;
use common\models\MarcaProduto;
use common\models\ProdutoFilial;
use Livepixel\MercadoLivre\Meli;
use Yii;
use yii\helpers\ArrayHelper;

class CreateAction extends Action
{
    public function run()
    {

	die;

	/*$produtosArray = Array();
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
	$file = fopen("/var/tmp/morelate_subir_ml_pellegrino_01.csv", 'r');
	
	while (($line = fgetcsv($file,null,';')) !== false)
	{
	    $produtosArray[] = $line[0].".M";
	    //print_r($line);
	}
	fclose($file);*/
	
	$arquivo_log = fopen("/var/tmp/log_mercado_livre_create_".date("Y-m-d_H-i-s").".csv", "a");
        fwrite($arquivo_log, "produto_filial_id;permalink;status");

        echo "Criando produtos...\n\n";
        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        $filials = Filial::find()
            ->andWhere(['IS NOT', 'refresh_token_meli', null])
            ->andWhere(['id' => [43]])
            ->andWhere(['<>', 'id', 98])
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
        		    /*->joinWith('produto')
        		    ->andWhere(['not like','upper(produto.nome)','SEMI NOV'])
        		    ->andWhere(['not like','upper(produto.nome)','SEMI-NOV'])
                    ->andWhere(['not like','upper(produto.nome)','SEMINOV'])
                    ->andWhere(['not like','upper(produto.nome)','REMAN'])
                    ->andWhere(['not like','upper(produto.nome)','RECOND'])
                    ->andWhere(['not like','upper(produto.nome)','REFORMAD'])
		            ->andWhere(['not like','upper(produto.aplicacao)','SEMI NOV'])
                    ->andWhere(['not like','upper(produto.aplicacao)','SEMI-NOV'])
                    ->andWhere(['not like','upper(produto.aplicacao)','SEMINOV'])
                    ->andWhere(['not like','upper(produto.aplicacao)','REMAN'])
                    ->andWhere(['not like','upper(produto.aplicacao)','RECOND'])
                    ->andWhere(['not like','upper(produto.aplicacao)','REFORMAD'])
                    ->andWhere(['not like','upper(produto.aplicacao_complementar)','SEMI NOV'])
                    ->andWhere(['not like','upper(produto.aplicacao_complementar)','SEMI-NOV'])
                    ->andWhere(['not like','upper(produto.aplicacao_complementar)','SEMINOV'])
                    ->andWhere(['not like','upper(produto.aplicacao_complementar)','REMAN'])
                    ->andWhere(['not like','upper(produto.aplicacao_complementar)','RECOND'])
                    ->andWhere(['not like','upper(produto.aplicacao_complementar)','REFORMAD'])
		            ->andWhere(['not like','upper(produto.descricao)','SEMI NOV'])
                    ->andWhere(['not like','upper(produto.descricao)','SEMI-NOV'])
                    ->andWhere(['not like','upper(produto.descricao)','SEMINOV'])
                    ->andWhere(['not like','upper(produto.descricao)','REMAN'])
                    ->andWhere(['not like','upper(produto.descricao)','RECOND'])
		            ->andWhere(['not like','upper(produto.descricao)','REFORMAD'])*/
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
        		    /*echo "===>";
        		    print_r($page);
        		    echo "<===";
        		    continue;*/
        		    //var_dump($page);
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

                    //echo "\n==>"; print_r($title); echo "<=="; continue;
                    /*echo "\n";print_r(utf8_encode($title));
                    echo "\n";print_r((strlen($title) <= 60) ? $title : substr($title, 0, 60));
                    echo "\n";print_r(substr($title, 59, 1));*/
                    //die;

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
                    }

       		    /*$condicao = "new";
        	    if($produtoFilial->produto->e_usado){
                        $condicao = "used";
                    }*/
                    
                    $marca_produto = MarcaProduto::find()->andWhere(['=','id',$produtoFilial->produto->marca_produto_id])->one();
                    $marca = "OPT";
                    if($marca_produto){
                        $marca = $marca_produto->nome."/CONSULTAR";
                    }
                    
                    $body = [
                        //"title" => (strlen($title) <= 60) ? $title : substr($title, 0,60),
                        "title" => mb_substr($titulo_novo,0,60),
                        "category_id" => utf8_encode($subcategoriaMeli),
                        "listing_type_id" => "bronze",
                        "currency_id" => "BRL",
                        "price" => utf8_encode(round($produtoFilial->getValorMercadoLivre(), 2)),
                        "available_quantity" => utf8_encode($produtoFilial->quantidade),
                        "seller_custom_field" =>utf8_encode($produtoFilial->id),
                        "condition" => $condicao,//"new",
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
                          ]

                    ];
                    //print_r($body); //die;
                    $response = $meli->post("items?access_token=" . $meliAccessToken,$body);

                    Yii::info(ArrayHelper::merge($response, ['request' => $body]), 'mercado_livre_create');
                    if ($response['httpCode'] >= 300) {
                        //Yii::error($response['body'], 'mercado_livre_create');
                        //print_r($response);
                        //print_r($body);
                        fwrite($arquivo_log, "\n".$produtoFilial->id.";;erro");
                        echo "erro";
                    } else {
                        $produtoFilial->meli_id = $response['body']->id;
                        
                        $meli_salvo = ($produtoFilial->save() ? "meli_id salvo" : "meli_id não salvo");

                        echo ArrayHelper::getValue($response, 'body.permalink')." - ok";
                        fwrite($arquivo_log, "\n".$produtoFilial->id.";".ArrayHelper::getValue($response, 'body.permalink').";ok;".$meli_salvo);
                        
                        //Cria o produto na CONTA DUPLICADA
                        $produto_filial_conta_duplicada = ProdutoFilial::find()->andWhere(['=','produto_filial_origem_id', $produtoFilial->id])->one();
                        if($produto_filial_conta_duplicada){
                            $response = $meli->post("items?access_token=" . $meliAccessToken_conta_duplicada,$body);
                            if ($response['httpCode'] >= 300) {
                                fwrite($arquivo_log, ";".$produto_filial_conta_duplicada->id.";;erro");
                                echo "erro";
                            } 
                            else {
                                $produto_filial_conta_duplicada->meli_id = $response['body']->id;
                                
                                $meli_salvo = ($produto_filial_conta_duplicada->save() ? "meli_id salvo" : "meli_id não salvo");
                                
                                echo ArrayHelper::getValue($response, 'body.permalink')." - ok";
                                fwrite($arquivo_log, ";".$produto_filial_conta_duplicada->id.";".ArrayHelper::getValue($response, 'body.permalink').";ok;".$meli_salvo);
                            }
                        }
                    }
                    //die;
                    
                    /*if($produtoFilial->produto->e_usado){
                        $condicao = "used";
                        echo "É usado!";
                        die;
                    }*/
                }
            }
            //die;
        }

	fclose($arquivo_log);

    }
}
