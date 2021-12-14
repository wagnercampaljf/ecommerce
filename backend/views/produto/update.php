<?php

use common\models\Filial;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Produto */

$this->title = 'Editar Produto: ' . ' ' . $model->nome;
$this->params['breadcrumbs'][] = ['label' => 'Produtos', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->nome, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="produto-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php

    if (isset($erro)) {
        echo $erro;
    }
    if (isset($mercadoLivre)) {
        if ($mercadoLivre !== '') {
            foreach ($mercadoLivre as $indices) {

                if (isset($indices['tipo_conta_ml'])) {
                    echo '<div class="text-success h3" style="font-size: 20px; color: #1E90FF"><b>' . $indices['tipo_conta_ml'] . '</b></div>';
                }

                foreach ($indices as $k => $indice) {
                    if ($k == 'tipo_conta_ml' || $k == 'meli_id' || $k == 'permalink') {
                        continue;
                    }
                    $key = $k;
                    $value    = $indice;
                    if ($key == 'filial_id') {
                        $filial = Filial::findOne($indice)->nome;
                        echo '<div class="text-primary h4" style="font-size: 20px; color: #1E90FF"> Filial - ' . $filial . '</div>';
                    } else {
                        echo '<div class="text-primary h4" style="font-size: 20px; color: #1E90FF">' . $value . '</div>';
                    }
                }
                if (isset($indices['permalink'])) {
                    echo '<div class="text-primary h4" style="font-size: 20px; color: #1E90FF"><a href="' . $indices['permalink'] . '">' . $indices['permalink'] . '</a></div>';
                }
            }
        }
    }
    if (isset($mensagem)) {
        echo ($mensagem);
    }
    if (isset($link_mercado_livre)) {
        echo $link_mercado_livre;
    }
    ?>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>