<?php
//1111

namespace console\controllers\actions\mercadolivre;

use yii\helpers\ArrayHelper;
use Livepixel\MercadoLivre\Meli;
use common\models\PedidoMercadoLivre;
use common\models\Filial;

class PedidosCriarAlterarAction extends Action
{        
    public function run(){
       
        echo "INÍCIO\n\n";

	$minutos = date('i');
	$e_atualizar_pedido = (($minutos % 50 == 0)) ? true : false;

        $APP_KEY_OMIE_SP                   = '468080198586';
        $APP_SECRET_OMIE_SP                = '7b3fb2b3bae35eca3b051b825b6d9f43';
        
        $APP_KEY_OMIE_MG                   = '469728530271';
        $APP_SECRET_OMIE_MG                = '6b63421c9bb3a124e012a6bb75ef4ace';
        
        $APP_KEY_OMIE_CONTA_DUPLICADA      = '1017311982687';
        $APP_SECRET_OMIE_CONTA_DUPLICADA  = '78ba33370fac6178da52d42240591291';
        
        // Escreve no log
        $arquivo_log = fopen("/var/tmp/ml_pedidos_criar_alterar/pedidos_criar_alterar_".date("Y-m-d_H-i-s").".csv", "a");
        fwrite($arquivo_log, "pedido;status\n");
        
        $data_atual = date('Y-m-d');
        $data_inicial = date('Y-m-d', strtotime("-15 days",strtotime(date('Y-m-d'))));
        
        $filial = Filial::find()->andWhere(['=','id',72])->one();
        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        $user = $meli->refreshAccessToken($filial->refresh_token_meli);
        $response = ArrayHelper::getValue($user, 'body');
        if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {
            $meliAccessToken = $response->access_token;
            
            $x = 0;
            $y = 0;
            
            $response_order = $meli->get('https://api.mercadolibre.com/orders/search?seller=193724256&offset='.$x.'&order.date_created.from='.$data_inicial.'T00:00:00.000-00:00&order.date_created.to='.$data_atual.'T23:59:59.000-00:00&access_token=' . $meliAccessToken);
            
            while (ArrayHelper::getValue($response_order, 'body.results') != null){
                //break;
                echo "\nX: ".$x;
                
                if($x>=0){
                    foreach (ArrayHelper::getValue($response_order, 'body.results') as $k => $venda){
                        echo "\n";
			print_r($venda->id); 
			//continue;
			echo " - ".ArrayHelper::getValue($venda, 'date_created');

                        $pedido_mercado_livre = PedidoMercadoLivre::find()->andWhere(["=", "pedido_meli_id", $venda->id])->one();

                        if($pedido_mercado_livre){
                           if($e_atualizar_pedido){
				echo PedidoMercadoLivre::AtualizarPedidoML($venda->id);
			   }
			   else{ 
                                echo " - Pedido já cadastrado";
                           }
                           fwrite($arquivo_log, "\n".$venda->id.";Pedido já cadastrado");
                        }
                        else{
                            echo PedidoMercadoLivre::baixarPedidoML($venda->id);
                            fwrite($arquivo_log, "\n".$venda->id.";Pedido criado");
                        }
                    }
                }
                
                $x += 50;
                $response_order = $meli->get('https://api.mercadolibre.com/orders/search?seller=193724256&offset='.$x.'&order.date_created.from='.$data_inicial.'T00:00:00.000-00:00&order.date_created.to='.$data_atual.'T23:59:59.000-00:00&access_token=' . $meliAccessToken);
            }
        }
        
        echo "\n\n<<<<<<<<<<<<<<<<<<FILIAL>>>>>>>>>>>>>>>>>\n\n";
        fwrite($arquivo_log, "\n\nFILIAL\n\n");
        
        $filial = Filial::find()->andWhere(['=','id',98])->one();
        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        $user = $meli->refreshAccessToken($filial->refresh_token_meli);
        $response = ArrayHelper::getValue($user, 'body');
        if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {
            $meliAccessToken = $response->access_token;
            
            $x = 0;
            $y = 0;
            
            $response_order = $meli->get('https://api.mercadolibre.com/orders/search?seller=435343067&offset='.$x.'&order.date_created.from='.$data_inicial.'T00:00:00.000-00:00&order.date_created.to='.$data_atual.'T23:59:59.000-00:00&access_token=' . $meliAccessToken);
            //print_r($response_order); die;
            while (ArrayHelper::getValue($response_order, 'body.results') != null){
                //break;
                echo "\nX: ".$x;
                
                //print_r($response_order);
                
                if($x>=0){
                    foreach (ArrayHelper::getValue($response_order, 'body.results') as $k => $venda){
                        echo "\n";print_r($venda->id); echo " - ".ArrayHelper::getValue($venda, 'date_created');

                        $pedido_mercado_livre = PedidoMercadoLivre::find()->andWhere(["=", "pedido_meli_id", $venda->id])->one();

                        if($pedido_mercado_livre){
			    if($e_atualizar_pedido){
                                echo PedidoMercadoLivre::AtualizarPedidoML($venda->id);
                            }
			    else{
				echo " - Pedido já cadastrado";
			    }
                            fwrite($arquivo_log, "\n".$venda->id.";Pedido já cadastrado");
                        }
                        else{
                            echo PedidoMercadoLivre::baixarPedidoML($venda->id);
                            fwrite($arquivo_log, "\n".$venda->id.";Pedido criado");
                        }
                    }
                }
                
                $x += 50;
                $response_order = $meli->get('https://api.mercadolibre.com/orders/search?seller=435343067&offset='.$x.'&order.date_created.from='.$data_inicial.'T00:00:00.000-00:00&order.date_created.to='.$data_atual.'T23:59:59.000-00:00&access_token=' . $meliAccessToken);
            }
        }
        
        fwrite($arquivo_log, "\n\nFIM");
        fclose($arquivo_log);
        
        echo "\n\nFIM!\n\n";
    }
}




 
