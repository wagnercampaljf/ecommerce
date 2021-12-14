<?php
/* @var $this yii\web\View */

use common\models\Filial;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\web\JsExpression;
use kartik\select2\Select2;
use kartik\file\FileInput;



$this->title = 'Planilha Preço';
$this->params['breadcrumbs'][] = ['label' => ' / ' . $this->title];

/* @var $this yii\web\View */
/* @var $model common\models\Filial */
/* @var $form yii\widgets\ActiveForm */

?>



<div class="container">
    <?php echo  \yii\helpers\Html::a('Planilha Preco',['planilha'],['class'=>'btn btn-primary'])?>
</div> <!-- /container -->
