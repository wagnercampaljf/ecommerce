<?php
/**
 * Created by PhpStorm.
 * User: igorm
 * Date: 27/06/2016
+ * Time: 18:54
 */
namespace console\controllers\actions\mercadolivre;


use common\models\Filial;
use common\models\ProdutoFilial;
use common\models\Produto;
use Livepixel\MercadoLivre\Meli;
use Yii;
use yii\helpers\ArrayHelper;

class AtualizarDimensoesAction extends Action
{
    public function run()
    {
	   //echo "=>1<=";
	   $meli = new Meli(static::APP_ID, static::SECRET_KEY);

	   //Código de criação da tabela de preços baseadas no ME
	   $user = $meli->refreshAccessToken('TG-5d3ee920d98a8e0006998e2b-193724256');
	   $response = ArrayHelper::getValue($user, 'body');
	   
	   $produto_frete['0'] = 0;
	   
	   if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {
	       $meliAccessToken = $response->access_token;
	       
	       $x = 0;
	       $y = 0;
	       
	       $data_atual = date('Y-m-d');
	       
	       $response_order = $meli->get('https://api.mercadolibre.com/orders/search?seller=193724256&offset='.$x.'&order.date_created.from=2017-01-01T00:00:00.000-00:00&order.date_created.to='.$data_atual.'T23:59:59.000-00:00&access_token=' . $meliAccessToken);
	       while (ArrayHelper::getValue($response_order, 'body.results') != null){
	           foreach (ArrayHelper::getValue($response_order, 'body.results') as $k => $venda){
	               //if(ArrayHelper::getValue($venda, 'id') != 'MLB1063732490'){continue;}
	               
	               //print_r($venda);die;
	               
	               if(ArrayHelper::getValue($venda, 'shipping.shipping_mode')=='me2'){
	                   $y++;
	                   echo "\n".$y." - ";print_r(ArrayHelper::getValue($venda, 'id'));
	                   foreach(ArrayHelper::getValue($venda, 'shipping.shipping_items') as $itens){
	                       //if(ArrayHelper::getValue($itens, 'id') == 'MLB867584925'){
	                       print_r($itens);
	                       //}
	                       echo " - "; print_r(ArrayHelper::getValue($itens, 'id'));
	                       echo "\n"; print_r(ArrayHelper::getValue($itens, 'dimensions'));
	                       $dimensoes = explode("x",ArrayHelper::getValue($itens, 'dimensions'));
	                       echo "\nML - ".str_pad(number_format($dimensoes[0], 2, ',', '.'), 7, " ", STR_PAD_LEFT);
	                       echo " - ".str_pad(number_format($dimensoes[1], 2, ',', '.'), 7, " ", STR_PAD_LEFT);
	                       $profundidade_peso = explode(",",$dimensoes[2]);
	                       echo " - ".str_pad(number_format($profundidade_peso[0], 2, ',', '.'), 7, " ", STR_PAD_LEFT);
	                       echo " - ".str_pad(number_format($profundidade_peso[1], 2, ',', '.'), 9, " ", STR_PAD_LEFT);
	                       
	                       $produto_filial = ProdutoFilial::find()->andWhere(['=','meli_id',ArrayHelper::getValue($itens, 'id')])->one();
	                       if(isset($produto_filial)){
	                           echo "\nPA - ".str_pad(number_format($produto_filial->produto->altura, 2, ',', '.'), 7, " ", STR_PAD_LEFT);
	                           echo " - ".str_pad(number_format($produto_filial->produto->largura, 2, ',', '.'), 7, " ", STR_PAD_LEFT);
	                           echo " - ".str_pad(number_format($produto_filial->produto->profundidade, 2, ',', '.'), 7, " ", STR_PAD_LEFT);
	                           echo " - ".str_pad(number_format($produto_filial->produto->peso*1000, 2, ',', '.'), 9, " ", STR_PAD_LEFT);
	                           
	                           $produto = Produto::find()->andWhere(['=','id',$produto_filial->produto_id])->one();
	                           if($produto)
	                           {
	                               echo "\nProduto Encontrado!";
	                               
	                               $produto->altura        = number_format(($dimensoes[0]*1.2), 2, '.', '');
	                               $produto->largura       = number_format(($dimensoes[1]*1.2), 2, '.', '');
	                               $produto->profundidade  = number_format(($profundidade_peso[0]*1.2), 2, '.', '');
	                               $produto->peso          = number_format((($profundidade_peso[1]/1000)*1.2), 2, '.', '');
	                               $produto->descricao     .= " (Medidas Conferidas)";
	                               //print_r($produto);
	                               var_dump($produto->save());
	                           }
	                           else{
	                               echo "\nProduto Não encontrado";
	                           }
	                           
	                       }
	                       else {
	                           echo " - Produto Filial não encontrado!"; 
	                       }
	                       
	                       $response_valor_dimensao = $meli->get("/users/193724256/shipping_options/free?dimensions=".str_replace(".0","",ArrayHelper::getValue($itens, 'dimensions')));
	                       $produto_frete[ArrayHelper::getValue($itens, 'id')] = ArrayHelper::getValue($response_valor_dimensao, 'body.coverage.all_country.list_cost');
	                   }
	                   
	               }
	           }
	           $x += 50;
	           $response_order = $meli->get('https://api.mercadolibre.com/orders/search?seller=193724256&offset='.$x.'&order.date_created.from=2017-01-01T00:00:00.000-00:00&order.date_created.to='.$data_atual.'T23:59:59.000-00:00&access_token=' . $meliAccessToken);
	       }
	       //var_dump(ArrayHelper::keyExists('MLB864729660', $produto_frete, false));
	       //var_dump(ArrayHelper::getValue($produto_frete, 'MLB864729660'));
	   }
	   echo "Tabela de preços gerada!\n";
        //Código de criação da tabela de preços baseadas no ME

        
    }
}
