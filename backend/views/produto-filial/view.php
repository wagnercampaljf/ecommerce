<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use Livepixel\MercadoLivre\Meli;
use yii\helpers\ArrayHelper;
use common\models\ProdutoFilial;

/* @var $this yii\web\View */
/* @var $model common\models\ProdutoFilial */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Produto Filials', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

        $meli = new Meli('3029992417140266', '1DjEfVPwkjOmqowN1ALujTJpgQh5DGdh');
        //$user = $meli->refreshAccessToken($model->filial->refresh_token_meli);
        //$response = ArrayHelper::getValue($user, 'body');
	//echo "<pre>"; print_r($response); echo "</pre>";
	echo "CONTA PRINCIPAL: ";
	if(!is_null($model->meli_id)){
		$response_item = $meli->get("/items/".$model->meli_id);
		if ($response_item['httpCode'] >= 300) {
			//echo " - ERRO Categoria Recomendada";
                }
                else {
			//echo "<pre>"; print_r($response_item); echo "</pre>";
			if(property_exists($response_item["body"], 'permalink')){
				echo "<br>Anúncio principal: ".ArrayHelper::getValue($response_item, 'body.permalink');
			}
		}
	}
	if(!is_null($model->meli_id_sem_juros)){
                $response_item = $meli->get("/items/".$model->meli_id_sem_juros);
                if ($response_item['httpCode'] >= 300) {
                        //echo " - ERRO Categoria Recomendada";
                }
                else {
                        //echo "<br>Anúncio sem juros: ".ArrayHelper::getValue($response_item, 'body.permalink');
			if(property_exists($response_item["body"], 'permalink')){
                                echo "<br>Anúncio principal: ".ArrayHelper::getValue($response_item, 'body.permalink');
                        }
                }
        }
	if(!is_null($model->meli_id_flex)){
                $response_item = $meli->get("/items/".$model->meli_id_flex);
                if ($response_item['httpCode'] >= 300) {
                        //echo " - ERRO Categoria Recomendada";
                }
                else {
                        //echo "<br>Anúncio flex: ".ArrayHelper::getValue($response_item, 'body.permalink');
			if(property_exists($response_item["body"], 'permalink')){
                                echo "<br>Anúncio principal: ".ArrayHelper::getValue($response_item, 'body.permalink');
                        }
                }
        }
	if(!is_null($model->meli_id_full)){
                $response_item = $meli->get("/items/".$model->meli_id_full);
                if ($response_item['httpCode'] >= 300) {
                        //echo " - ERRO Categoria Recomendada";
                }
                else {
                        //echo "<br>Anúncio full: ".ArrayHelper::getValue($response_item, 'body.permalink');
			if(property_exists($response_item["body"], 'permalink')){
                                echo "<br>Anúncio principal: ".ArrayHelper::getValue($response_item, 'body.permalink');
                        }
                }
        }
	
	echo "<br><br>CONTAS DUPLICADAS";
	$produtos_filiais_duplicados = ProdutoFilial::find()->andWhere(["=", "produto_filial_origem_id", $model->id])->all();

	foreach($produtos_filiais_duplicados as $k => $produto_filial_duplicado){
		if(!is_null($produto_filial_duplicado->meli_id)){
        	        $response_item = $meli->get("/items/".$produto_filial_duplicado->meli_id);
        	        if ($response_item['httpCode'] >= 300) {
        	                //echo " - ERRO Categoria Recomendada";
        	        }
        	        else {
        	                //echo "<pre>"; print_r($response_item); echo "</pre>";
        	                if(property_exists($response_item["body"], 'permalink')){
        	                        echo "<br>Anúncio principal: ".ArrayHelper::getValue($response_item, 'body.permalink');
        	                }
        	        }
        	}
        	if(!is_null($produto_filial_duplicado->meli_id_sem_juros)){
        	        $response_item = $meli->get("/items/".$produto_filial_duplicado->meli_id_sem_juros);
        	        if ($response_item['httpCode'] >= 300) {
        	                //echo " - ERRO Categoria Recomendada";
        	        }
        	        else {
        	                //echo "<br>Anúncio sem juros: ".ArrayHelper::getValue($response_item, 'body.permalink');
        	                if(property_exists($response_item["body"], 'permalink')){
        	                        echo "<br>Anúncio principal: ".ArrayHelper::getValue($response_item, 'body.permalink');
        	                }
        	        }
        	}
		if(!is_null($produto_filial_duplicado->meli_id_flex)){
        	        $response_item = $meli->get("/items/".$produto_filial_duplicado->meli_id_flex);
        	        if ($response_item['httpCode'] >= 300) {
        	                //echo " - ERRO Categoria Recomendada";
        	        }
        	        else {
        	                //echo "<br>Anúncio flex: ".ArrayHelper::getValue($response_item, 'body.permalink');
        	                if(property_exists($response_item["body"], 'permalink')){
        	                        echo "<br>Anúncio principal: ".ArrayHelper::getValue($response_item, 'body.permalink');
        	                }
        	        }
        	}
        	if(!is_null($produto_filial_duplicado->meli_id_full)){
        	        $response_item = $meli->get("/items/".$produto_filial_duplicado->meli_id_full);
        	        if ($response_item['httpCode'] >= 300) {
        	                //echo " - ERRO Categoria Recomendada";
        	        }
        	        else {
        	                //echo "<br>Anúncio full: ".ArrayHelper::getValue($response_item, 'body.permalink');
        	                if(property_exists($response_item["body"], 'permalink')){
        	                        echo "<br>Anúncio principal: ".ArrayHelper::getValue($response_item, 'body.permalink');
        	                }
        	        }
        	}
	}


?>
<div class="produto-filial-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'label'     => 'Filial',
                'attribute' => 'filial.nome',
            ],
            [
                'label'     => 'Produto',
                'attribute' => 'produto.nome',
            ],
            //'produto_id',
            //'filial_id',
            'quantidade',
            'meli_id',
            'status_b2w:boolean',
            'envio',
        ],
    ]) ?>

</div>
