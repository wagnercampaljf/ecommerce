<?php

use yii\grid\GridView;
use yii\helpers\Url;

$this->title = 'Pedidos para Expedição';

echo '  <div class="container">
            <div class="row">
                <div class="col-sm-3">
                    <h1 style="color:green; font-weight: bold;">Contador: '.(isset($contador) ? $contador : "0").' </h1>
                </div>
                <div class="col-sm-2">
                    <a href="'.Url::to(['/pedidos-mercado-livre-expedicao/zerar-contador']).'"> <button type="button" class="btn btn-primary btn-block">Zerar Contador</button></a>
                </div>
                <div class="col-sm-7">
                </div>
            </div>
        </div>';



?>

<div class="pedido-index">

    <div class="container">
        <ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#pedidos">Pedidos</a></li>
            <li><a data-toggle="tab" href="#log">Log</a></li>
        </ul>

        <div class="tab-content">
            <div id="pedidos" class="tab-pane fade in active">
                <div class="container">
            		<div class="row">
            			<?= $status?>
            		</div>
            		<div class="row">
            			<div class="col-xs-12 col-md-12" style="padding-top: 6px">  
            			<form id="main-search" class="form form-inline sombra" role="form" action="<?= Url::to(['/pedidos-mercado-livre-expedicao/expedicao']) ?>">
            				<div class="input-group col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding-left: 0px !important;padding-right: 0px !important;">
            					<input type="text"
                                       name="chave"
                                       id="chave" class="form-control form-control-search input-lg data-hj-whitelist"
                                       placeholder="Chave da nota ..."
                                       autofocus="true">
                                <span class="input-group-btn">
            						<button type="submit" class="btn btn-default btn-lg control" style="background-color: #007576" id="main-search-btn"><i class="fa fa-search" style="color: white"></i></button>
            	                </span>
            				</div>
            			</form>
            		</div>
            		</div>
                </div><br>
            
                <div class="container">
                  
            		<?php 
            
            		echo GridView::widget([
                    'dataProvider' => $dataProvider,
                    'columns' => [
                        'id',
                        'pedido_meli_id',
                	    'shipping_id',
                        'buyer_first_name',
                        'buyer_last_name',
                        'buyer_doc_type',
                        'buyer_doc_number',
                    ],
                ]); ?>
                </div>
            </div>
            <div id="log" class="tab-pane fade">
                <?php  echo $this->render('index-log', ["searchModelLog" =>$searchModelLog, "dataProviderLog" => $dataProviderLog]); ?>
            </div>
        </div>
    </div>
</div>









