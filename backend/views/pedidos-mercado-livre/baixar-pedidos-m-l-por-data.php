<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use backend\models\PedidoMercadoLivreSearch;
use kartik\date\DatePicker;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $skyhubDataProvider yii\data\ActiveDataProvider */
/* @var $filterModel \common\models\PedidoSearch */
/* @var $skyhubFilterModel \common\models\PedidoSkyhubSearch */

$this->title = 'Pedidos';
$this->params['breadcrumbs'][] = $this->title;
?>

<script>
        //$('#btn_baixar').click(function() { alert(123); });
        $(window).load(function() { $('.preloader').fadeOut(); });
</script>

<style>
        /*   PRELOADER */
        .preloader {
            position: fixed;
            width: 100%;
            height: 100%;
            z-index: 999999;
            background-image: url("/frontend/web/assets/img/engrenagens3.gif");
            background-repeat: no-repeat;
            background-color: rgba(255, 255, 255, 0.54);
            background-position: center;
            display: block;
        }
</style>

<div class="pedido-index">

    <?php $form = ActiveForm::begin(); ?>

    <div class="container">
	<div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><h3><font color="red"><?= $erro?></font></h3></div></div>
	<div class="row">
                <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">Data Inicial</div>
                <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">Data Final</div>
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12"></div>
        </div>
	<div class="row">
		<div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
			<?= DatePicker::widget([
			    'name' => "data_inicial",
			    'value' => null,
			])?>
		</div>
		<div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
                        <?= DatePicker::widget([
                            'name' => "data_final",
                            'value' => null,
                        ])?>
                </div>
		<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
			<?= Html::submitButton('Baixar Pedidos', ['class' => 'btn btn-primary', 'id'=>"btn_baixar"]) ?>
			<script>
				$('#btn_baixar').click(function() { $('.preloader').fadeIn('slow'); });
			</script>
                </div>
	</div>
    </div>

    <div class="container">
        <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<?= "<br><br>".$resposta?>
                </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
