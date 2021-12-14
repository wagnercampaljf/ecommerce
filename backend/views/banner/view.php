<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\CategoriaBanner;
use common\models\PosicaoBanner;
use common\models\Banner;

/* @var $this yii\web\View */
/* @var $model common\models\Banner */

$this->title = $model->nome;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Banners'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="banner-view">
    <div class="portlet light">
        <div class="portlet-title">
            <div class="caption">
                <?= Html::a(Yii::t('app', 'Editar'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                <?= Html::a(Yii::t('app', 'Excluir'), ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => Yii::t('app', 'Tem certeza de que deseja excluir este item?'),
                        'method' => 'post',
                    ],
                ]) ?>
            </div>
        </div>
        <div class="portlet-body">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'nome',
                    'data_inicio',
                    'data_fim',
                    'qt_cliques',
                    'fabricante.nome:text:Fabricante',
                    'produto.nome:text:Produto',
                    [
                        'label' => 'Categoria',
                        'format' => 'raw',
                        'value' => $model->labelCategoriaBanners
                    ],
                    'posicao.nome:text:Posição',
                    'cidade.nome:text:Cidade',
                    [
                        'label' => 'Link',
                        'format' => 'raw',
                        'value' => $model->getLabelLink()
                    ],
                    [
                        'attribute' => 'imagem',
                        'format' => 'raw',
                        'value' => Html::img('data:image;base64,' . stream_get_contents($model->imagem),
                            ['width' => $model->posicao->largura, 'height' => $model->posicao->altura]),
                    ],
                    [
                        'label' => 'Subcategorias',
                        'format' => 'raw',
                        'value' => $model->labelSubcategorias
                    ],
                    'descricao',
                ],
            ]) ?>
        </div>
    </div>
</div>
