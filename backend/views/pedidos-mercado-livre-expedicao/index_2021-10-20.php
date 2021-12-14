<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use backend\models\PedidoMercadoLivreSearch;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $skyhubDataProvider yii\data\ActiveDataProvider */
/* @var $filterModel \common\models\PedidoSearch */
/* @var $skyhubFilterModel \common\models\PedidoSkyhubSearch */

$this->title = 'Pedidos';
$this->params['breadcrumbs'][] = $this->title;
?>

<script>
	    function quantidade_etiqueta(pedido_mercado_livre_id){
            
            var texto = document.getElementById(pedido_mercado_livre_id).innerText;
            var texto = texto.replace('(','');
            var texto = texto.replace(')','');
            var inteiro = parseInt(texto);       
			
			console.log(inteiro);
			
			if(isNaN(inteiro)){
				document.getElementById(pedido_mercado_livre_id).innerHTML = '(1)';
			}
			else{
				var quantidade = inteiro + 1;
			
				document.getElementById(pedido_mercado_livre_id).innerHTML = '('+quantidade+')';
			}
        }
        
        function quantidade_nota_fiscal(pedido_mercado_livre_id){
            
            var texto = document.getElementById(pedido_mercado_livre_id).innerText;
            var texto = texto.replace('(','');
            var texto = texto.replace(')','');
            var inteiro = parseInt(texto);       
			
			console.log(inteiro);
			
			if(isNaN(inteiro)){
				document.getElementById(pedido_mercado_livre_id).innerHTML = '(1)';
			}
			else{
				var quantidade = inteiro + 1;
			
				document.getElementById(pedido_mercado_livre_id).innerHTML = '('+quantidade+')';
			}
        }
</script>

<div class="pedido-index">

    <div class="container">
      
		<?php  echo $this->render('index/_mercado-livre-principal', [
		        "filtro" => $filtro,

            "filtro_status_pedido_enviado" => $filtro_status_pedido_enviado,
            'filtro_status_pedido_nao_enviado'=> $filtro_status_pedido_nao_enviado,

            'filtro_status_etiqueta_impressa'=> $filtro_status_etiqueta_impressa,
            'filtro_status_etiqueta_nao_impressa'=> $filtro_status_etiqueta_nao_impressa,

	    'data_inicial' => $data_inicial,
	    'data_final' => $data_final,

        ]) ?>


    </div>
      
    </div>




</div>
















