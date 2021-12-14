<?php
/**
 * Created by PhpStorm.
 * User: igorm
 * Date: 29/06/2016
 * Time: 16:49
 */
namespace console\controllers\actions\mercadolivre;


use common\models\Filial;
use common\models\ProdutoFilial;
use common\models\Produto;
use Livepixel\MercadoLivre\Meli;
use Yii;
use yii\helpers\ArrayHelper;

class MorelateCreateAction extends Action
{
    public function run()
    {

        $arquivo_log = fopen("/var/tmp/log_mercado_livre_morelate_create_".date("Y-m-d_H-i-s").".csv", "a");
        fwrite($arquivo_log, "produto_filial_id;permalink;status");
                
        $linhasArray = Array();

        //$file = fopen("/var/tmp/morelate_30-03-2020_MARIANA_1746.csv", 'r');
        //$file = fopen("/var/tmp/24-04_Mariana_517_produtos.csv", 'r');
        //$file = fopen("/var/tmp/20-04_Mariana_534_produtos.csv", 'r');
        $file = fopen("/var/tmp/04-05_Mariana_434_produtos.csv", 'r');
        
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $linhasArray[] = $line;
        }
        fclose($file);
        
        echo "Criando produtos...\n\n";
        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        $filial = Filial::find()->andWhere(['id' => [43]])->one();
        $user = $meli->refreshAccessToken($filial->refresh_token_meli);
        $response = ArrayHelper::getValue($user, 'body');
        
        $filial_outro = Filial::find()->andWhere(['id' => [98]])->one();
        $user_outro = $meli->refreshAccessToken($filial_outro->refresh_token_meli);
        $response_outro = ArrayHelper::getValue($user_outro, 'body');        

        if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {
            $meliAccessToken = $response->access_token;
            $meliAccessToken_outro = $response_outro->access_token;

            foreach ($linhasArray as $i => &$linhaArray){
            
                echo "\n".$i." - ".$linhaArray[8];

        		/*if($i <= 52){
        			echo " - pular";
        			continue;
        		}*/
                
                $produto = Produto::find()->andWhere(['=','codigo_fabricante', $linhaArray[8].".M" ])->one();
                
                if($produto){
                    
                    $produtoFilial = ProdutoFilial::find()  ->andWhere(['=', 'filial_id', 43])
                                                            ->andWhere(['=', 'produto_id', $produto->id])
                                                            ->andWhere(['is','meli_id',null])
                                                            ->one();
                    
                    if($produtoFilial) {
                        
                        echo " - ".$produtoFilial->id." - ".$produtoFilial->produto->codigo_fabricante." - ".$produtoFilial->produto->nome;
    
                        $subcategoriaMeli = $produtoFilial->produto->subcategoria->meli_id;
                        if (!isset($subcategoriaMeli)) {
                            echo " - SEM SUBCATEGORIA";
                            continue;
                        }
    
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
    
            		    $condicao = "new";
            		    if($produtoFilial->produto->e_usado){
                            $condicao = "used";
                        }
    
                        $body = [
                            "title" => (strlen($title) <= 60) ? $title : substr($title, 0,59),
                            "category_id" => utf8_encode($subcategoriaMeli),
                            "listing_type_id" => "bronze",
                            "currency_id" => "BRL",
                            "price" => utf8_encode(round($produtoFilial->getValorMercadoLivre(), 2)),
                            "available_quantity" => utf8_encode($produtoFilial->quantidade),
                            "seller_custom_field" =>utf8_encode($produtoFilial->id),
                            "condition" => $condicao,//"new",
                			"description" => ["plain_text" => $page],//utf8_encode($page)],
                			"pictures" => $produtoFilial->produto->getUrlImagesML(),
                            "shipping" => [
                                "mode" => $modo,
                                "local_pick_up" => true,
                                "free_shipping" => false,
                                "free_methods" => [],
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
                                    ]
                              ]
    
                        ];
    
                        $response = $meli->post("items?access_token=" . $meliAccessToken,$body);
    
                        if ($response['httpCode'] >= 300) {
                            //print_r($response);
                			//print_r($body);
                            fwrite($arquivo_log, "\n".$linhaArray[8].";".$produtoFilial->id.";;;erro");
                            echo "erro";
                        } else {
                            $produtoFilial->meli_id = $response['body']->id;
                            $estoque_status = ";meli_id não gravado no produto_filial";
                            if ($produtoFilial->save()) {
                                $estoque_status = ";meli_id gravado no produto_filial";
                            }
                            
                            echo ArrayHelper::getValue($response, 'body.permalink')." - ok";
                            fwrite($arquivo_log, "\n".$linhaArray[8].";".$produtoFilial->id.";".ArrayHelper::getValue($response, 'body.permalink').$estoque_status.";ok");

                            $produtoFilial_outro = ProdutoFilial::find()->andWhere(['=','produto_filial_origem_id',$produtoFilial->id])->one();
                            if($produtoFilial_outro){
                                echo " - produto duplicado encontrado";
                                $response = $meli->post("items?access_token=" . $meliAccessToken_outro,$body);
                                if ($response['httpCode'] >= 300) {
                                    fwrite($arquivo_log, ";".$produtoFilial_outro->id.";;;erro");
                                    echo "erro";
                                } else {
                                    $produtoFilial_outro->meli_id = $response['body']->id;
                                    $estoque_status = ";meli_id_outro não gravado no produto_filial";
                                    if ($produtoFilial_outro->save()) {
                                        $estoque_status = ";meli_id_outro gravado no produto_filial";
                                    }
                                    
                                    echo ArrayHelper::getValue($response, 'body.permalink')." - ok";
                                    fwrite($arquivo_log, ";".$produtoFilial_outro->id.";".ArrayHelper::getValue($response, 'body.permalink').$estoque_status.";ok");
                                }
                            }
                            else{
                                echo " - produto duplicado não encontrado";
                            }
                        }
                    }
                    else{
                        echo " - Estoque não encontrado";
                        fwrite($arquivo_log, "\n".$linhaArray[8].";;;;Estoque não encontrado");
                    }
                }
                else{
                    echo " - Produto não encontrado";
                    fwrite($arquivo_log, "\n".$linhaArray[8].";;;;Produto não encontrado");
                }
            }
        }

	fclose($arquivo_log);

    }
}
