<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\PisCofins */

$this->title = 'Criar Dados Pis Cofins';
$this->params['breadcrumbs'][] = ['label' => 'Pis Cofins', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="panel panel-default">
    <div class="panel-body">
        <div class="pis-cofins-create">

            <h1><?= Html::encode($this->title) ?></h1>


            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>

        </div>
    </div>

</div>