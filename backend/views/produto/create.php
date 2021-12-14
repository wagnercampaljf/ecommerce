<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Produto */

$this->title = 'Cadastrar Produto';
$this->params['breadcrumbs'][] = ['label' => 'Produtos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="produto-create">

    <h2><b><?= Html::encode($this->title) ?></b></h2>

    <?= $this->render('_form_create', [
        'model' => $model,
    ]) ?>

</div>
