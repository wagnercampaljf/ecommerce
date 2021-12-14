
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
            </div><br><br>

            <div class="container">
                <div class="row">
                    <div class="col-sm-4">
                        <input type="checkbox" class="switch" id="s_item5" name="filtro_status_pedido_enviado"  value="true" onchange="this.form.submit()">
                        <label for="scales">Pedido Enviado</label>

                        <input type="checkbox" class="switch" id="s_item6" name="filtro_status_pedido_nao_enviado"  value="false" onchange="this.form.submit()">
                        <label for="horns">Pedido Nâo enviado</label>
                    </div>
                </div>
            </div>




        </form>
    </div><br>


<style>
    @supports (-webkit-appearance: none) or (-moz-appearance: none) {
        input[type=checkbox],
        input[type=radio] {
            --active: #275EFE;
            --active-inner: #fff;
            --focus: 2px rgba(39, 94, 254, .3);
            --border: #BBC1E1;
            --border-hover: #275EFE;
            --background: #fff;
            --disabled: #F6F8FF;
            --disabled-inner: #E1E6F9;
            -webkit-appearance: none;
            -moz-appearance: none;
            height: 21px;
            outline: none;
            display: inline-block;
            vertical-align: top;
            position: relative;
            margin: 0;
            cursor: pointer;
            border: 1px solid var(--bc, var(--border));
            background: var(--b, var(--background));
            transition: background 0.3s, border-color 0.3s, box-shadow 0.2s;
        }
        input[type=checkbox]:after,
        input[type=radio]:after {
            content: "";
            display: block;
            left: 0;
            top: 0;
            position: absolute;
            transition: transform var(--d-t, 0.3s) var(--d-t-e, ease), opacity var(--d-o, 0.2s);
        }
        input[type=checkbox]:checked,
        input[type=radio]:checked {
            --b: var(--active);
            --bc: var(--active);
            --d-o: .3s;
            --d-t: .6s;
            --d-t-e: cubic-bezier(.2, .85, .32, 1.2);
        }
        input[type=checkbox]:disabled,
        input[type=radio]:disabled {
            --b: var(--disabled);
            cursor: not-allowed;
            opacity: 0.9;
        }
        input[type=checkbox]:disabled:checked,
        input[type=radio]:disabled:checked {
            --b: var(--disabled-inner);
            --bc: var(--border);
        }
        input[type=checkbox]:disabled + label,
        input[type=radio]:disabled + label {
            cursor: not-allowed;
        }
        input[type=checkbox]:hover:not(:checked):not(:disabled),
        input[type=radio]:hover:not(:checked):not(:disabled) {
            --bc: var(--border-hover);
        }
        input[type=checkbox]:focus,
        input[type=radio]:focus {
            box-shadow: 0 0 0 var(--focus);
        }
        input[type=checkbox]:not(.switch),
        input[type=radio]:not(.switch) {
            width: 21px;
        }
        input[type=checkbox]:not(.switch):after,
        input[type=radio]:not(.switch):after {
            opacity: var(--o, 0);
        }
        input[type=checkbox]:not(.switch):checked,
        input[type=radio]:not(.switch):checked {
            --o: 1;
        }
        input[type=checkbox] + label,
        input[type=radio] + label {
            font-size: 14px;
            line-height: 21px;
            display: inline-block;
            vertical-align: top;
            cursor: pointer;
            margin-left: 4px;
        }

        input[type=checkbox]:not(.switch) {
            border-radius: 7px;
        }
        input[type=checkbox]:not(.switch):after {
            width: 5px;
            height: 9px;
            border: 2px solid var(--active-inner);
            border-top: 0;
            border-left: 0;
            left: 7px;
            top: 4px;
            transform: rotate(var(--r, 20deg));
        }
        input[type=checkbox]:not(.switch):checked {
            --r: 43deg;
        }
        input[type=checkbox].switch {
            width: 38px;
            border-radius: 11px;
        }
        input[type=checkbox].switch:after {
            left: 2px;
            top: 2px;
            border-radius: 50%;
            width: 15px;
            height: 15px;
            background: var(--ab, var(--border));
            transform: translateX(var(--x, 0));
        }
        input[type=checkbox].switch:checked {
            --ab: var(--active-inner);
            --x: 17px;
        }
        input[type=checkbox].switch:disabled:not(:checked):after {
            opacity: 0.6;
        }

        input[type=radio] {
            border-radius: 50%;
        }
        input[type=radio]:after {
            width: 19px;
            height: 19px;
            border-radius: 50%;
            background: var(--active-inner);
            opacity: 0;
            transform: scale(var(--s, 0.7));
        }
        input[type=radio]:checked {
            --s: .5;
        }
    }


</style>

<?php
    
    
    $searchModel = new PedidoMercadoLivreSearch();
    //$dataProvider = $searchModel->search(['PedidoMercadoLivreSearch'=> ['pedido_meli_id' => $filtro, 'e_xml_subido' => true, "e_pedido_enviado" => false]]);
    $dataProvider = $searchModel->search(['PedidoMercadoLivreSearch'=> [
    'pedido_meli_id' => $filtro,
	'e_xml_subido' => true,
        //'e_pedido_cancelado' => $filtro_status_cancelado,
        //'e_pedido_faturado' => $filtro_status_pedido_faturado,
        'filtro_status_pedido_enviado' => $filtro_status_pedido_enviado,
	'filtro_status_pedido_nao_enviado' => $filtro_status_pedido_nao_enviado,
        //'e_pedido_autorizado' => $filtro_status_pedido_autorizado,


]]);



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
