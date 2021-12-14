<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\common\models\MarkupMestre */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Markup Mestres', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="markup-mestre-view">

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
            'data_inicio',
            'observacao:ntext',
            'e_margem_absoluta_padrao:boolean',
            'margem_padrao',
            'descricao',
            'e_markup_padrao',
        ],
    ]) ?>

</div>
