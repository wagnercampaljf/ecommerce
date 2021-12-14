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
            if (isset($erro))
            {
                echo $erro;
            } 
            elseif (isset($mensagem))
            {
                echo $mensagem;
                if (isset($link_mercado_livre)){
                    echo $link_mercado_livre;
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
        
        
        <?php $urlml = "criarml?id=".$model->id; //Botão de criação do produto no MercadoLivre?> 
        <a class="btn  btn-success" id="sinc-ml2" href="<?php echo $urlml; ?>">
        <i class="fa fa-shopping-cart" aria-hidden="true" ></i> Criar (Mercado Livre)
        </a>
        <?php $urlml = "atualizarml?id=".$model->id;//Botão de alteração do produto no MercadoLivre?>
        <a class="btn  btn-warning" id="sinc-ml2" href="<?php echo $urlml; ?>">
        <i class="fa fa-shopping-cart" aria-hidden="true" ></i> Atualizar (Mercado Livre)
        </a>
        
        
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'valor',
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
