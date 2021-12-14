<?php

namespace console\controllers\actions\mercadolivre;

use yii\helpers\ArrayHelper;
use function GuzzleHttp\json_encode;
use function GuzzleHttp\json_decode;
use Livepixel\MercadoLivre\Meli;
use common\models\ProdutoFilial;
use common\models\Filial;

class AnaliseMLAction extends Action
{
    
    public function run($cliente = 1){
       
        echo "INÍCIO\n\n";
        
        $filiais_ml[0] = 0;
        //$filiais = Filial::find()->andWhere(['is not','refresh_token_meli',null])->all();
        $filiais = Filial::find()->all();
        foreach ($filiais as $j => $filial){
            $filiais_ml[$filial->id] = 0;
        }
        //print_r($filiais_ml);
        
        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        $user = $meli->refreshAccessToken('TG-5d3ee920d98a8e0006998e2b-193724256');
        $response = ArrayHelper::getValue($user, 'body');
        print_r($response);
        $produto_frete['0'] = 0;

        if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {
            $meliAccessToken = $response->access_token;
            
            $x = 0;
            $y = 0;
            
            $data_atual = date('Y-m-d');

            $response_order = $meli->get('https://api.mercadolibre.com/orders/search?seller=193724256&offset='.$x.'&order.date_created.from=2016-01-01T00:00:00.000-00:00&order.date_created.to='.$data_atual.'T23:59:59.000-00:00&access_token=' . $meliAccessToken);
            while (ArrayHelper::getValue($response_order, 'body.results') != null){
                foreach (ArrayHelper::getValue($response_order, 'body.results') as $k => $venda){
                    $y++;
                    echo "\n".$y." - ";print_r(ArrayHelper::getValue($venda, 'id')); 
                    
                    foreach(ArrayHelper::getValue($venda, 'order_items') as $itens){
                        //print_r($itens); die;
                        $produto_filial = ProdutoFilial::find()->andWhere(['=','meli_id',ArrayHelper::getValue($itens, 'item.id')])->one();
                        if ($produto_filial != null){
                            $filiais_ml[$produto_filial->filial_id] += 1;
                        }
                        //$response_valor_dimensao = $meli->get("/users/193724256/shipping_options/free?dimensions=".str_replace(".0","",ArrayHelper::getValue($itens, 'dimensions')));
                        //$produto_frete[ArrayHelper::getValue($itens, 'id')] = ArrayHelper::getValue($response_valor_dimensao, 'body.coverage.all_country.list_cost');
                    }
                }
                //print_r($filiais_ml);
                $x += 50;
                $response_order = $meli->get('https://api.mercadolibre.com/orders/search?seller=193724256&offset='.$x.'&order.date_created.from=2016-01-01T00:00:00.000-00:00&order.date_created.to='.$data_atual.'T23:59:59.000-00:00&access_token=' . $meliAccessToken);
            }
            //var_dump(ArrayHelper::keyExists('MLB864729660', $produto_frete, false));
            //var_dump(ArrayHelper::getValue($produto_frete, 'MLB864729660'));
        }
        
        //print_r($filiais_ml);
        foreach ($filiais as $j => $filial){
            echo "\n".$filial->id."|".$filial->nome."|".$filiais_ml[$filial->id]."|".$filial->refresh_token_meli;
        }
        
        echo "\n\nFIM!\n\n";
    }
}