<?php

use kartik\export\ExportMenu;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel \lojista\models\ProdutoFilialSearch */
/* @var $uploadForm common\models\UploadForm */

$this->title = 'Estoque';
$this->params['breadcrumbs'][] = $this->title;

$this->registerJsFile(
    Url::to(['js/estoque.js']),
    ['depends' => [lojista\assets\AppAsset::className()]]
);

$statusArray = [
    [
        'label' => 'Status',
        'value' => function ($data) {
            return !isset($data->valorAtual) ? "Expirado" : "Atual";
        }
    ]
];

$gridColumns = [
    [
        'attribute' => 'cod_globalProduto',
        'value' => 'produto.codigo_global',
    ],
    [
        'attribute' => 'nome_produto',
        'value' => 'produto.nome',
    ],
    [
        'attribute' => 'quantidade',
        'format' => 'raw',
        'value' => function ($data) {
            $class = $data->quantidade > 10 ? 'text-success' : 'text-danger';

            return Html::tag('span', $data->quantidade, ['class' => $class]);
        }
    ],
    [
        'label' => 'Valor (R$)',
        'attribute' => 'valor',
        'value' => function ($data) {
            if ($data->valorMaisRecente) {
                return $data->valorMaisRecente->valor;
            }

            return null;
        }
    ],
    [
        'label' => 'Valor CNPJ (R$)',
        'attribute' => 'valor_cnpj',
        'value' => function ($data) {
            if ($data->valorMaisRecente) {
                return $data->valorMaisRecente->valor_cnpj;
            }

            return null;
        }
    ],
];
?>
<div class="produto-filial-index">
    <div class="col-lg-12 col-sm-12 col-12 margin-bottom-5">
        <div class="col-lg-1 col-sm-1 col-12 text-right pull-right">
            <span class="file-input btn btn-success btn-file exportar">
            <?= ExportMenu::widget([
                'dataProvider' => $dataProvider,
                'columns' => $gridColumns,
                'fontAwesome' => true,
                'asDropdown' => false,
                'target' => ExportMenu::TARGET_SELF,
                'showConfirmAlert' => false,
                'showColumnSelector' => false,
                'exportConfig' => [
                    ExportMenu::FORMAT_HTML => false,
                    ExportMenu::FORMAT_TEXT => false,
                    ExportMenu::FORMAT_CSV => false,
                    ExportMenu::FORMAT_EXCEL => false,
                    ExportMenu::FORMAT_PDF => false,
                ],
                'filename' => 'estoque_' . date('d-m-Y_H:i')
            ]); ?>
            </span>
        </div>
        <div class="col-lg-1 col-sm-1 col-12 text-right pull-right" style="padding: 0">
            <?php
            $form = ActiveForm::begin([
                'action' => Url::to(['upload']),
                'id' => 'upload',
                'options' => [
                    'enctype' => 'multipart/form-data',
                ],
            ]);
            ?>
            <span class="file-input btn btn-success btn-file" id="importar">
                <i class="glyphicon glyphicon-import" id="importar-icon"></i>
                <span id="importar-label">Importar</span> <?= Html::activeFileInput(
                    $uploadForm,
                    'file',
                    [
                        'accept' => '.csv',
                        'onChange' => '$("#upload").submit()'
                    ]
                ); ?>
            </span>
            <?php ActiveForm::end() ?>
        </div>
    </div>
    <div class="col-md-12">
        <?=
        GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => array_merge($gridColumns, $statusArray)
        ]); ?>
    </div>
</div>
