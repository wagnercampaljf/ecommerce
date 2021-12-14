<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ValorProdutoFilial */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Valor Produto Filials', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="valor-produto-filial-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php

    if (isset($erro)) {
        echo $erro;
    }

    if (isset($mercadoLivre)) {
        if ($mercadoLivre !== '') {
            foreach ($mercadoLivre as $y => $indices) {
                echo '<div class="text-primary" style="font-size: 20px; color: #1E90FF">Conta - ' . strtoupper($y) . '</div>';
                foreach ($indices as $k => $indice) {
                    echo '<div class="text-primary" style="font-size: 20px; color: #1E90FF">Tipo Anúncio - ' . strtoupper($k) . '</div>';
                    if (isset($indice['permalink'])) {
                        echo '<div class="h4"><a class="text-primary" href="' . $indice['permalink'] . '">' . $indice['permalink'] . '</a></div>';
                    }
                }
            }
        }
    }
    ?>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ])
        ?>

    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'valor',
            'produtoFilial.produto.codigo_global',
            'produtoFilial.produto.codigo_fabricante',
            'dt_inicio',
            'produto_filial_id',
            'dt_fim',
            'promocao:boolean',
            'valor_cnpj',
            'valor_compra',
            'produtoFilial.filial.id',
        ],
    ]) ?>

</div>