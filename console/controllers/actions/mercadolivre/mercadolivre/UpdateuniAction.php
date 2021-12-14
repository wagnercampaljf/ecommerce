<?php
/**
 * Created by PhpStorm.
 * User: igorm
 * Date: 27/06/2016
 * Time: 18:54
 */
/* SELECT id from produto_filial where produto_id = (SELECT id from produto WHERE codigo_global='242337'); */
namespace console\controllers\actions\mercadolivre;


use common\models\Filial;
use common\models\ProdutoFilial;
use Livepixel\MercadoLivre\Meli;
use Yii;
use yii\helpers\ArrayHelper;

class UpdateuniAction extends Action
{
    public function run($global_id)
    {
        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        $filials = Filial::find()
            ->andWhere(['IS NOT', 'refresh_token_meli', null])
            //->andWhere(['id' => 62])
            ->all();

        /* @var $filial Filial */
        foreach ($filials as $filial) {


            $user = $meli->refreshAccessToken($filial->refresh_token_meli);
            $response = ArrayHelper::getValue($user, 'body');
           
            if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {
                $meliAccessToken = $response->access_token;
                
                $produtoFilials = $filial->getProdutoFilials()
                    //->andWhere(['is not', 'meli_id', null])
		            ->andWhere(['=','meli_id', $global_id])
                    ->all();
                /* @var $produtoFilial ProdutoFilial */
                foreach ($produtoFilials as $produtoFilial) {

                    //Início das outras contas do ML
                    
                    //$filiais_outros = Filial::find()->andWhere(['=', 'mercado_livre_secundario', 'true'])->all();
                    
                    //foreach ($filiais_outros as $filial_outro){
                    //$produtos_filiais_outros = ProdutoFilial::find()->andWhere(['=','filial_id',$filial_outro->id])
                    //                                                ->andWhere(['=','produto_id', $produtoFilial->produto_id])
                    //                                                ->all();
                    /*$produtos_filiais_outros = ProdutoFilial::find()->andWhere(['=','produto_filial_origem_id',$produtoFilial->id])->all();
                    
                    foreach ($produtos_filiais_outros as $produto_filial_outro){
                        
                        $imagens_outro = array();
                        
                        if($produto_filial_outro->filial->mercado_livre_logo){
                            $imagens_outro = $produtoFilial->produto->getUrlImagesML();
                        }
                        else{
                            $imagens_outro = $produtoFilial->produto->getUrlImagesMLSemLogo();
                        }
                        
                        $user_outro     = $meli->refreshAccessToken($produto_filial_outro->filial->refresh_token_meli);
                        $response_outro = ArrayHelper::getValue($user_outro, 'body');
                        
                        if (is_object($response_outro) && ArrayHelper::getValue($user_outro, 'httpCode') < 400) {
                            $meliAccessToken_outro = $response_outro->access_token;
                            if($produto_filial_outro->meli_id != null){
                                $body = [ "pictures" => $imagens_outro,
                                    "available_quantity" => $produtoFilial->quantidade,
                                    "price" => $preco,
                                ];
                                $response = $meli->put("items/{$produto_filial_outro->meli_id}?access_token=" . $meliAccessToken_outro, $body, []);
                                echo "\nAlterado";
                            }
                            else{
                                $body = [   "title" => (strlen($title) <= 60) ? $title : substr($title, 0, 60),
                                    "category_id" => utf8_encode($subcategoriaMeli),
                                    "listing_type_id" => "bronze",
                                    "currency_id" => "BRL",
                                    "price" => $preco,
                                    "available_quantity" => utf8_encode($produtoFilial->quantidade),
                                    "seller_custom_field" => utf8_encode($produto_filial_outro->id),
                                    "condition" => "new",
                                    "description" => ["plain_text" => $page],
                                    "pictures" => $imagens_outro,//$produtoFilial->produto->getUrlImagesML(),
                                    "shipping" => [
                                        "mode" => $modo,
                                        "local_pick_up" => true,
                                        "free_shipping" => false,
                                        "free_methods" => [],
                                    ],
                                    "warranty" => "6 meses",
                                ];
                                $response = $meli->post("items?access_token=" . $meliAccessToken_outro, $body);
                                if ($response['httpCode'] < 300) {
                                    $produto_filial_outro->meli_id = $response['body']->id;
                                    $produto_filial_outro->save();
                                }
                                echo "\nCriado";
                            }
                        }
                    }*/
                    //}
                    
                    //Fim das outras contas do ML


                    $subcategoriaMeli = $produtoFilial->produto->subcategoria->meli_id;
                    if (!isset($subcategoriaMeli)) {
                        continue;
                    }
                    if (is_null($produtoFilial->valorMaisRecente)) {
                        Yii::error("Produto Filial: {$produtoFilial->produto->nome}({$produtoFilial->id}), não possui valor",
                            'error_yii');
                        continue;
                    }
                    
                    $page = $this->controller->renderFile(__DIR__ . '/../../../../lojista/views/mercado-livre/produto.php',
                        ['produto' => $produtoFilial]);

                    $title = Yii::t('app', '{nome} ({cod})', [
                        'cod' => $produtoFilial->produto->codigo_global,
                        'nome' => $produtoFilial->produto->nome
                    ]);
                    
                    //OBTER DADOS DA GARANTIA
                    //$response = $meli->get("categories/MLB47126/sale_terms?access_token=" . $meliAccessToken);
                    $response = $meli->get("categories/MLB251640/attributes?access_token=" . $meliAccessToken);
                    print_r($response); die;
                    
                    
                    //Update Item
                    $body = [
                        "title" => ((strlen($title) <= 60) ? $title : substr($title, 0, 60)),
                        "price" => round($produtoFilial->getValorMercadoLivre(), 2),
                        "available_quantity" => $produtoFilial->quantidade,
                        "description" => ["plain_text" => utf8_encode($page)],
                        'attributes' =>[
                            [
                                'id' => 'PART_NUMBER',
                                'name' => 'Número da peça',
                                'value_id' => NULL,
                                'value_name' => $produtoFilial->produto->codigo_global,
                                'value_struct' => NULL,
                                'attribute_group_id' => 'DFLT',
                                'attribute_group_name' => 'Outros',
                            ],
                            [
                                "id"=> "BRAND",
                                "name"=> "Marca",
                                "value_id"=> null,
                                "value_name"=> $produtoFilial->produto->fabricante->nome,
                                "value_struct"=> null,
                                "attribute_group_id"=> "OTHERS",
                                "attribute_group_name"=> "Outros"
                            ],
                            [
                                "id" => "EAN",
                                "value_name" => $produtoFilial->produto->codigo_barras
                            ],

                        ]

                    ];
                    $response = $meli->put(
                        "items/{$produtoFilial->meli_id}?access_token=" . $meliAccessToken,
                        $body,
                        []
                    );
                    
                    Yii::info($response, 'mercado_livre_update');
                    if ($response['httpCode'] >= 300) {
                        Yii::error($response['body'], 'mercado_livre_update');
                    }

                    //Update Descrição
                    $body = [
                        'text' => $page
                    ];
                    $response = $meli->put(
                        "items/{$produtoFilial->meli_id}/description?access_token=" . $meliAccessToken,
                        $body,
                        []
                    );
                    Yii::info($response, 'mercado_livre_update');
                    if ($response['httpCode'] >= 300) {
                        Yii::error($response['body'], 'mercado_livre_update');
                    }

		    //1 para me2 (Mercado Envios)
	            //2 para not_especified (a combinar)
	            //3 para customizado

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
		    
		    
		    $body = [
                        //"pictures" => $produtoFilial->produto->getUrlImagesML(),
			"pictures" => $imagemTeste,
                        "shipping" => [
                            "mode"=> $modo,
                            "local_pick_up" => true,
                            "free_shipping" => false,
                            "free_methods" => [],
                        ],
                        "warranty" => "6 meses",
                    ];
		    print_r($body);	
            $response = $meli->put(
                "items/{$produtoFilial->meli_id}?access_token=" . $meliAccessToken,
                $body,
                []
            );
		    print_r($response);
                    Yii::info($response, 'mercado_livre_update');
		    //print_r($response);
                    if ($response['httpCode'] >= 300) {
                        Yii::error($response['body'], 'mercado_livre_update');
                    }
                }
            }

        }
    }
    
}
