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
        function quantidade_pre_nota(pedido_mercado_livre_id){
            
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
      	
		<?php  echo $this->render('index/_mercado-livre-principal-separacao', [ "filtro" => $filtro, "e_mail" => $e_mail, "e_apenas_nao_impressos" => $e_apenas_nao_impressos]) ?>            </div>
      
    </div>




</div>
