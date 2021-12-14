<?php

namespace console\controllers\actions\mercadolivre;

use yii\helpers\ArrayHelper;
use function GuzzleHttp\json_encode;
use function GuzzleHttp\json_decode;
use Livepixel\MercadoLivre\Meli;
use common\models\ProdutoFilial;
use common\models\Produto;
use common\models\PedidoProdutoFilial;
use common\models\ValorProdutoFilial;

class AlterarPrecoAlgomaisAction extends Action
{

    
    public function run($cliente = 1){
       
        echo "INÍCIO\n\n";
        
        $arquivo_log = fopen("/var/tmp/analise_concorrente_preco_compra.csv", "a");
        fwrite($arquivo_log, "PEÇA AGORA Nome;ALGOMAISPECAS Nome;Peça Agora Preço;ALGOMAISPECAS Preço;Peça Agora Data Criação;ALGOMAISPECAS Data Criação;Peça Agora Quantidade Vendida;ALGOMAISPECAS Quantidade Vendidas;Peça Agora URL;ALGOMAISPECAS URL;Preço Compra BR;Preço após desconto\n");
        
        $LinhasArray = Array();
        $file = fopen('/var/tmp/algomais_preco_correcao.csv', 'r');
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
        }
        fclose($file);

        foreach ($LinhasArray as $k => &$linhaArray ){
            
            if ($k <=0 ){
                continue;
            }
            
            $meli_id        = explode("-",$linhaArray[8]);
            $meli_id_final  = str_replace("https://produto.mercadolivre.com.br/","",$meli_id[0]).$meli_id[1];
            $produto_filial = ProdutoFilial::find()->andWhere(['=','meli_id',$meli_id_final])->one();

            echo "\n".$k." - ".$meli_id_final." - ";
            
            if($produto_filial){
                $produto_filial_origem = new ProdutoFilial;
                if ($produto_filial->produto_filial_origem_id != NULL){
                    $produto_filial_origem = ProdutoFilial::find()->andWhere(['=','id',$produto_filial->produto_filial_origem_id])->one();
                }
                else{
                    $produto_filial_origem = $produto_filial;
                }

                echo $produto_filial_origem->meli_id;
                
                $valor_produto_filial                       = new ValorProdutoFilial;
                $valor_produto_filial->dt_inicio            = date("Y-m-d H:i:s");
                $valor_produto_filial->produto_filial_id    = $produto_filial_origem->id;
                $valor_produto_filial->valor                = str_replace(",",".",$linhaArray[19]);
                $valor_produto_filial->valor_cnpj           = str_replace(",",".",$linhaArray[19]);
                $valor_produto_filial->promocao             = false;
                $valor_produto_filial->save();

            }
            else{
                echo "Não encontrado";
            }
        }
        
        fclose($arquivo_log);
        die;
        
        // Escreve no log
        $arquivo_log = fopen("/var/tmp/analise_concorrente_ALGOMAISPECAS_".date("Y-m-d_H-i-s").".csv", "a");
        fwrite($arquivo_log, "PEÇA AGORA Nome;ALGOMAISPECAS Nome;Peça Agora Preço;ALGOMAISPECAS Preço;Peça Agora Data Criação;ALGOMAISPECAS Data Criação;Peça Agora Quantidade Vendida;ALGOMAISPECAS Quantidade Vendidas;Peça Agora URL;ALGOMAISPECAS URL\n");
        
        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        $user = $meli->refreshAccessToken("TG-5c4f26ef9b69e60006493768-193724256");
        $response = ArrayHelper::getValue($user, 'body');

        if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {
            $meliAccessToken = $response->access_token;
            
            //360447035 -> ALGOMAISPECAS
            for($x=0;$x<=10000;$x+=50){
                echo "\n".$x;
                $response_order = $meli->get("sites/MLB/search?seller_id=360447035&search_type=scan&offset=".$x."&access_token=" . $meliAccessToken);
                foreach (ArrayHelper::getValue($response_order, 'body.results') as $meli_itens){
                    $response_itens = $meli->get("/items/".ArrayHelper::getValue($meli_itens, 'id'));
                    foreach(ArrayHelper::getValue($response_itens, 'body.attributes') as $atributos){
                        if (ArrayHelper::getValue($atributos, 'id') == "PART_NUMBER"){
                            echo "\n";
                            print_r(ArrayHelper::getValue($atributos, 'value_name'));
                            
                            $produto = Produto::find()  ->andWhere(['like', 'codigo_global', ArrayHelper::getValue($atributos, 'value_name')])
                                                        ->one();
                            if ($produto){
                                $produto_filial = ProdutoFilial::find() ->andWhere(['=','produto_id', $produto->id])
                                                                        ->andWhere(['is not','meli_id',null])
                                                                        ->one();
                                if($produto_filial){
                                    $response_itens_peca_agora = $meli->get("/items/".$produto_filial->meli_id);
                                    if (ArrayHelper::getValue($response_itens_peca_agora, 'httpCode') < 300 && array_key_exists('price', $response_itens_peca_agora['body'])){
                                        echo " - CONCORRENTE";
                                        fwrite($arquivo_log,ArrayHelper::getValue($response_itens_peca_agora, 'body.title').";".
                                                            ArrayHelper::getValue($response_itens, 'body.title').";".
                                                            ArrayHelper::getValue($response_itens_peca_agora, 'body.price').";".
                                                            ArrayHelper::getValue($response_itens, 'body.price').";".
                                                            ArrayHelper::getValue($response_itens_peca_agora, 'body.start_time').";".
                                                            ArrayHelper::getValue($response_itens, 'body.start_time').";".
                                                            ArrayHelper::getValue($response_itens_peca_agora, 'body.sold_quantity').";".
                                                            ArrayHelper::getValue($response_itens, 'body.sold_quantity').";".   
                                                            ArrayHelper::getValue($response_itens_peca_agora, 'body.permalink').";".
                                                            ArrayHelper::getValue($response_itens, 'body.permalink').";".
                                                            "\n");
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        
        fclose($arquivo_log);
        
        echo "\n\nFIM!\n\n";
    }
}




 