
<?php

    use backend\models\PedidoMercadoLivreSearch;
    use yii\widgets\ListView;
    use yii\helpers\Url;

    \yii\widgets\Pjax::begin(['timeout' => 5000]);

    
?>

	<div class="container">
    	<form id="main-search" class="form form-inline sombra" role="form" action="<?= Url::to(['/pedidos-mercado-livre-expedicao']) ?>">
            <div class="input-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <input type="text" 
                	   name="filtro"
                       id="main-search-product" 
                       class="form-control form-control-search input-lg data-hj-whitelist"
                       placeholder="Procure por dados do pedido ..."
                       value="<?= $filtro?>" style="width: 1126px">
            <span class="input-group-btn">
            	<button type="submit" class="btn btn-default btn-lg control" style="background-color: #007576" id="main-search-btn"><i class="fa fa-search" style="color: white"></i></button>
            </span>
            </div>
    	</form>
    </div><br>

<form action="<?= Url::to(['/pedidos-mercado-livre-expedicao/' ]) ?>" >
    <!--<button type="submit" name="" value = "" class="btn-outline default"><span>Limpar filtro</span></button>
    <button type="submit" name="filtro_status" value = 'false' class="btn btn-primary" >Pago </button>
    <button type="submit" name="filtro_status" value = "true" class="btn btn-success">Cancelado</button>
    <input type="checkbox" id="s_item1" name="filtro_status_true"  value="true"  onchange="this.form.submit()">-->

    <button type="submit" name="" value = "" class="btn-outline default"><span>Limpar filtro</span></button>
    <button type="submit" name="filtro_status_cancelado" value = "false" class="btn btn-secondary" style="background-color: rgba(36,138,169,0.67); color: white" >Pedido não cancelado</button>
    <button type="submit" name="filtro_status_cancelado"  value = "true" class="btn btn-danger" >Pedido Cancelado</button>
    <button type="submit" name="filtro_status_pedido_faturado" value = "true" class="btn btn-dark" style="background-color: #2f323e; color: white">Pedido Faturado</button>
    <button type="submit" name="filtro_status_pedido_faturado"  value = "false" class="btn btn-info">Pedido não Faturado</button>
    <button type="submit" name="filtro_status_pedido_envido" value = "true" class="btn btn-primary" >Pedido Enviado</button>
    <button type="submit" name="filtro_status_pedido_envido"  value = "false" class="btn btn-success">Pedido Nâo enviado</button>


</form><br>
<form>


<?php
    
    
    $searchModel = new PedidoMercadoLivreSearch();
    //$dataProvider = $searchModel->search(['PedidoMercadoLivreSearch'=> ['pedido_meli_id' => $filtro, 'e_xml_subido' => true, "e_pedido_enviado" => false]]);
    $dataProvider = $searchModel->search(['PedidoMercadoLivreSearch'=> [
    'pedido_meli_id' => $filtro,
    //'e_xml_subido' => false,
    'e_pedido_cancelado' => $filtro_status_cancelado,
    'e_pedido_faturado' => $filtro_status_pedido_faturado,
    'e_pedido_enviado' => $filtro_status_pedido_envido,


]]);



if ($filtro_status_cancelado== 'false'){

    echo "<br>". "<p style='font-size: 30px;color: #1b6d85'>" . "Pedido não cancelado" ."</p><br>";

}elseif ($filtro_status_cancelado== 'true'){
    echo "<br>". "<p style='font-size: 30px;color: #d90010'>" . "Pedido Cancelado" ."</p><br>";

}elseif ($filtro_status_pedido_faturado== 'true'){

    echo "<br>". "<p style='font-size: 30px;color: #1b6d85'>" . "Pedido Faturado" ."</p><br>";

}elseif ($filtro_status_pedido_faturado== 'false'){

    echo "<br>". "<p style='font-size: 30px;color: #1b6d85'>" . "Pedido não Faturado" ."</p><br>";

}elseif ($filtro_status_pedido_envido== 'false'){

    echo "<br>". "<p style='font-size: 30px;color: #1b6d85'>" . "Pedido não enviado" ."</p><br>";

}elseif ($filtro_status_pedido_envido== 'true'){

    echo "<br>". "<p style='font-size: 30px;color: #1b6d85'>" . "Pedido enviado" ."</p><br>";

}


//$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

   

    echo  ListView::widget([
        'dataProvider' => $dataProvider,
        'itemView' => 'listaPedidoMercadoLivre',
    ]);



    \yii\widgets\Pjax::end();

?>


    <script>


        document.addEventListener("DOMContentLoaded", function(){

            var checkbox = document.querySelectorAll("input[type='checkbox']");

            for(var item of checkbox){
                item.addEventListener("click", function(){
                    localStorage.s_item ? // verifico se existe localStorage
                        localStorage.s_item = localStorage.s_item.indexOf(this.id+",") == -1 // verifico de localStorage contém o id
                            ? localStorage.s_item+this.id+"," // não existe. Adiciono a id no loaclStorage
                            : localStorage.s_item.replace(this.id+",","") : // já existe, apago do localStorage
                        localStorage.s_item = this.id+",";  // não existe. Crio com o id do checkbox
                });
            }


            if(localStorage.s_item){// verifico se existe localStorage
                for(var item of checkbox){ // existe, percorro as checkbox
                    item.checked = localStorage.s_item.indexOf(item.id+",") != -1 ? true : false;




                    // marco true nas ids que existem no localStorage
                }
            }
        });


    </script>
