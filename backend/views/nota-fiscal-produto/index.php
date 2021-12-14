<?php

use yii\helpers\Html;
use yii\helpers\Url;
use backend\models\NotaFiscal;
use backend\models\NotaFiscalProduto;
use kartik\grid\GridView;
use yii\widgets\Pjax;

$this->title = 'Notas Fiscais';
$this->params['breadcrumbs'][] = $this->title;

$nota = NotaFiscal::findOne($idnota);

?>


<h1><?= Html::encode("Validação Produtos: Nota Fiscal nº $nota->numero_nf") ?></h1>

<?php Pjax::begin(['id' => 'pedidos']) ?>

<?php echo GridView::widget([
    'dataProvider' => $dataProvider,
    'id' => 'griditenss',
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'class' => 'kartik\grid\ExpandRowColumn',
            'width' => '50px',
            'value' => function ($model, $key, $index, $column) {
                return GridView::ROW_COLLAPSED;
            },
            'detail' => function ($dataProvider, $key, $index, $column) {
                $id_nota = NotaFiscalProduto::findOne($dataProvider->id)->nota_fiscal_id;
                $id = $dataProvider->id;
                $modelProduto = new NotaFiscalProduto();
                $dataProviderProduto = $modelProduto->ValidacaoNotaProduto($dataProvider->id);
                return Yii::$app->controller->renderPartial('detail-pedido', ['dataProvider' => $dataProviderProduto, 'id' => $id, 'id_nota' => $id_nota]);

            },
            'expandOneOnly' => true
        ],
        [
            'attribute' =>   'codigo_produto_original',
            'header' => 'Código Produto',
        ],
        [
            'attribute' =>   'pa_produto',
            'header' => 'PA',
        ],
        [
            'attribute' =>   'descricao',
            'header' => 'Descrição',
        ],
        [
            'attribute' =>   'valor_unitario_tributacao',
            'header' => 'Valor Unit.',
        ],
        [
            'attribute' =>   'valor_icms',
            'header' => 'ICMS ST',
        ],
        [
            'attribute' =>   'valor_ipi',
            'header' => 'IPI',
        ],
        [
            'attribute' =>   'valor_total_frete',
            'header' => 'Frete',
        ],
        [
            'attribute' =>   'valor_desconto',
            'header' => 'Desconto',
        ],
        [
            'attribute' =>   'outras_despesas',
            'header' => 'Outras Desp.',
        ],
        [
            'attribute' =>   'valor_seguro',
            'header' => 'Seguro',
        ],
        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => 'qtd_comercial',
            'header' => 'Qtd.',
            'editableOptions' => [
                'inputType' => \kartik\editable\Editable::INPUT_TEXT,
                'options' => ['pluginOptions' => ['min' => 0, 'max' => 100000]]
            ],
            'hAlign' => 'right',
            'vAlign' => 'middle',
            'width' => '100px',
            'pageSummary' => true
        ],
        [
            'header' => 'Valor Total',
            'content' => function ($dataProviderProduto) {
                return number_format((($dataProviderProduto->valor_unitario_tributacao * $dataProviderProduto->qtd_comercial) + $dataProviderProduto->valor_icms + $dataProviderProduto->valor_ipi + $dataProviderProduto->valor_total_frete + $dataProviderProduto->outras_despesas + $dataProviderProduto->valor_seguro - $dataProviderProduto->valor_desconto) / $dataProviderProduto->qtd_comercial, 2, '.', '');
            },
        ],
        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => 'valor_real_produto',
            'header' => 'Valor Real',
            'editableOptions' => [
                'inputType' => \kartik\editable\Editable::INPUT_TEXT,
                'options' => ['pluginOptions' => ['min' => 0, 'max' => 100000]]
            ],
            'hAlign' => 'right',
            'vAlign' => 'middle',
            'width' => '100px',
            'format' => ['decimal', 2],
            'pageSummary' => true
        ],
        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => 'email_produto',
            'header' => 'Email Prod.',
            'editableOptions' => [
                'inputType' => \kartik\editable\Editable::INPUT_TEXT,
            ],
            'hAlign' => 'left',
            'vAlign' => 'middle',
            'width' => '100px',
            'pageSummary' => true
        ],
    ],
    'responsive' => true,
    'hover' => true,
    'pjax' => true,

]);

?>

<?php Pjax::end() ?>
<?= Html::button('Validar Produtos', ['id' => 'btn_validar', 'class' => 'btn btn-success']); ?>

<style>
    .container-xxl {
        width: 90%;
        margin: auto;
    }
</style>