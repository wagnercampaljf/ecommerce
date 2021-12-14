<?php

use yii\helpers\Html;
use common\models\ValorProdutoFilialSearch;

/* @var $this yii\web\View */
/* @var $model common\models\ValorProdutoFilial */

$this->title = 'Editar Valor Produto Filial: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Valor Produto Filials', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Atualizar';
?>
<div class="valor-produto-filial-update">

    <!--<h1><?= ""//Html::encode($this->title) ?></h1>-->

    <?php 
        $searchModel = new ValorProdutoFilialSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
    ?>
      
    <?=   
        $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    
        //$this->render('_form', ['model' => $model,]) 
    ?>

</div>
