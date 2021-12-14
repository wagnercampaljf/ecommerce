<?php

namespace console\controllers\actions\mercadolivre;

use yii\helpers\ArrayHelper;
use function GuzzleHttp\json_encode;
use function GuzzleHttp\json_decode;
use Livepixel\MercadoLivre\Meli;
use common\models\ProdutoFilial;
use common\models\Produto;

class AnaliseConcorrenciaMLPrecoCompraAction extends Action
{

    
    public function run($cliente = 1){
       
        echo "INÍCIO\n\n";
        
        $arquivo_log = fopen("/var/tmp/analise_concorrente_preco_compra.csv", "a");
        fwrite($arquivo_log, "PEÇA AGORA Nome;ALGOMAISPECAS Nome;Peça Agora Preço;ALGOMAISPECAS Preço;Peça Agora Data Criação;ALGOMAISPECAS Data Criação;Peça Agora Quantidade Vendida;ALGOMAISPECAS Quantidade Vendidas;Peça Agora URL;ALGOMAISPECAS URL;Preço Compra BR\n");
        
        $LinhasArray = Array();
        $file = fopen('/var/tmp/analise_concorrente_ALGOMAISPECAS.csv', 'r');
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
        }
        fclose($file);
        
        $LinhasArrayBR = Array();
        $file = fopen('/var/tmp/preco_br_22-07-2019.csv', 'r');
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArrayBR[] = $line;
        }
        fclose($file);
        
        foreach ($LinhasArray as $k => &$linhaArray ){
            
            if ($k <=0 ){
                continue;
            }
            
            $meli_id        = explode("-",$linhaArray[8]);
            $meli_id_final  = str_replace("https://produto.mercadolivre.com.br/","",$meli_id[0]).$meli_id[1];
            $produto_filial = ProdutoFilial::find()->andWhere(['=','meli_id',$meli_id_final])->one();

            echo "\n".$k." - ".$meli_id_final;
            
            if($produto_filial){
                foreach ($LinhasArrayBR as $k => &$linhaArrayBR ){
                    
                    if($linhaArrayBR[1] == $produto_filial->produto->codigo_fabricante ){
                        fwrite($arquivo_log, $linhaArray[0].";".$linhaArray[1].";".$linhaArray[2].";".$linhaArray[3].";".$linhaArray[4].";".$linhaArray[5].";".$linhaArray[6].";".$linhaArray[7].";".$linhaArray[8].";".$linhaArray[9].";".$linhaArrayBR[3]."\n");
                        break;
                    }
                }
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




 