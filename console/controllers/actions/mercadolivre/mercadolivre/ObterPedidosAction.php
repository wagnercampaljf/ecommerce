<?php

namespace console\controllers\actions\mercadolivre;

use yii\helpers\ArrayHelper;
use function GuzzleHttp\json_encode;
use function GuzzleHttp\json_decode;
use Livepixel\MercadoLivre\Meli;
use common\models\ProdutoFilial;

class ObterPedidosAction extends Action
{
    
    public function run($global_id = 1){
       
        echo "INÍCIO\n\n";
        
        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        //$user = $meli->refreshAccessToken('TG-5b5f1c7be4b09e746623a2ca-193724256');
        $user = $meli->refreshAccessToken('TG-5c4f26ef9b69e60006493768-193724256');
        
        $response = ArrayHelper::getValue($user, 'body');
        
        $produto_frete['0'] = 0;

        if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {
            $meliAccessToken = $response->access_token;
            //print_r('https://api.mercadolibre.com/orders/search?seller=193724256&offset='.'&order.date_created.from=2019-01-22T00:00:00.000-00:00&order.date_created.to='.'T23:59:59.000-00:00&access_token=' . $meliAccessToken);die;
            $x = 0;
            $y = 0;
            
            $data_atual = date('Y-m-d');

            $response_order = $meli->get('https://api.mercadolibre.com/orders/search?seller=193724256&offset='.$x.'&order.date_created.from=2019-01-22T00:00:00.000-00:00&order.date_created.to='.$data_atual.'T23:59:59.000-00:00&access_token=' . $meliAccessToken);
            //print_r($response_order);
            while (ArrayHelper::getValue($response_order, 'body.results') != null){
                foreach (ArrayHelper::getValue($response_order, 'body.results') as $k => $venda){
                    //echo "==>";echo ArrayHelper::getValue($venda, 'id'); echo "\n\n";print_r($venda); die;
                    
                    if(ArrayHelper::getValue($venda, 'shipping.shipping_mode')=='me2'){
                        $y++;
                        //echo "\n".$y." - ";print_r(ArrayHelper::getValue($venda, 'id')); 
                        
                        //print_r($venda);
                        foreach(ArrayHelper::getValue($venda, 'shipping.shipping_items') as $itens){
                            if(ArrayHelper::getValue($itens, 'id') != $global_id){continue;}
                            
                            $response_valor_dimensao = $meli->get("/users/193724256/shipping_options/free?dimensions=".str_replace(".0","",ArrayHelper::getValue($itens, 'dimensions')));
                            //print_r($response_valor_dimensao);
                            $produto_frete[ArrayHelper::getValue($itens, 'id')] = ArrayHelper::getValue($response_valor_dimensao, 'body.coverage.all_country.list_cost');
                        }
                    }
                }

                $x += 50;
                $response_order = $meli->get('https://api.mercadolibre.com/orders/search?seller=193724256&offset='.$x.'&order.date_created.from=2019-01-22T00:00:00.000-00:00&order.date_created.to='.$data_atual.'T23:59:59.000-00:00&access_token=' . $meliAccessToken);
            }
            print_r($produto_frete);
            //var_dump(ArrayHelper::keyExists('MLB864729660', $produto_frete, false));
            //var_dump(ArrayHelper::getValue($produto_frete, 'MLB864729660'));
        }
        
        echo "\n\nFIM!\n\n";
    }
}