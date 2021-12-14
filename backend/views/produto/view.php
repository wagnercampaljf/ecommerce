<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Produto */

$this->title = $model->nome;
$this->params['breadcrumbs'][] = ['label' => 'Produtos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="produto-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Atualizar Produto', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Excluir', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Tem certeza que deseja exclui esse item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
//            [
//                'attribute' => 'imagem',
//                'format' => 'raw',
//                'value' => Html::img('imagens/get-img?id=' . $model->id, [
//                    'class' => 'img-responsive thumbnail',
//                    'style' => 'width:100%'
//                ]),
////                    Html::img('data:image;base64,' . stream_get_contents($model->imagem),
////                    ['width' => '128', 'height' => '128']),
//            ],
            'id',
            'nome',
            'descricao:ntext',
            'peso',
            'altura',
            'largura',
            'profundidade',
//            'imagem',
            'codigo_global',
            'codigo_montadora',
            'codigo_fabricante',
            'fabricante.nome:text:Fabricante',
            'slug',
//            'micro_descricao',
            'subcategoria.nome:text:Subcategoria',
//            'aplicacao:ntext',
//            'texto_vetor',
        ],
    ]) ?>

</div>
