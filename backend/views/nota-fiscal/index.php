<?php

error_reporting(E_ALL);

ini_set("display_errors", 1);

use backend\models\NotaFiscalProduto;
use backend\models\NotaFiscalProdutoSearch;
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\grid\GridView;
use kartik\editable\Editable;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\NotaFiscalSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Notas Fiscais';
$this->params['breadcrumbs'][] = $this->title;
$url_notas = "'/nota-fiscal/reverter-nota'";
$columns = '';
if (isset($erro)) {
    echo '<div class="text-primary h4" style="font-size: 20px; color: #1E90FF">' . $erro . '</div>';
}
?>

<div class="nota-fiscal-index">

    <?php

    if ($tela !== 'tela_validada') {
        $url_notas = "'/nota-fiscal/validar-nota'";
    ?>
        <div class="row ">
            <div class="col-sm-5 ">
                <form id="main-search" class="form form-inline sombra" role="form" action="<?= Url::to(['/nota-fiscal/baixar-nota']) ?>">
                    <div class="input-group col-md-12 ">
                        <input type="text" name="cChaveNFe" id="main-search-product" class="form-control form-control-search input-lg data-hj-whitelist" placeholder="Chave de Acesso Nota ..." value="">
                        <span class="input-group-btn">
                            <button type="submit" name="enviar-formulario" class="btn btn-default btn-lg control" style="background-color: #007576" id="main-search-btn"><span class="glyphicon glyphicon-download-alt"></span></button>
                        </span>
                    </div>
                </form>
            </div>

            <div class="col-sm-5 ">
                <form action="<?= Url::to(['/nota-fiscal/receber-xml']) ?>" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="_csrf" value="<?= Yii::$app->request->getCsrfToken() ?>" />
                    <div class="input-group col-md-12 ">
                        Selecione o arquivo: <input type="file" name="arquivo" />
                        <span class="input-group-btn">
                            <input type="submit" name="enviar-formulario" class="btn btn-default btn-lg control" style="background-color: #007576" id="main-search-btn" />
                        </span>
                    </div>
                </form>
            </div>
        </div>

        <h1><?= Html::encode($this->title) ?></h1>
        <form id="main-search" class="form form-inline sombra" role="form" action="<?= Url::to(['/nota-fiscal/search']) ?>">
            <div class="input-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <input type="text" name="filtro" id="main-search-product" class="form-control form-control-search input-lg data-hj-whitelist" placeholder="Procure por dados da NF." value="">
                <input type="text" name="validada" class="hidden" value="0">
                <span class="input-group-btn">
                    <button type="submit" class="btn btn-default btn-lg control " style="background-color: #007576" id="main-search-btn"><i class="fa fa-search" style="color: white"></i></button>
                </span>
            </div>

        </form>
        <?php

        $columns = [
            ['class' => 'yii\grid\SerialColumn'],
            'fornecedor',
            [
                'attribute' =>   'data_nf',
                'header' => 'Data NF',
                'content' => function ($dataProvider) {
                    return date("d/m/Y", strtotime($dataProvider->data_nf));
                },
            ],
            'numero_nf',
            'valor_nf',
            [
                'class' => 'yii\grid\CheckboxColumn',
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
                'buttons' => [
                    'view' => function ($url, $dataProvider) {
                        $url = "../nota-fiscal-produto/index?id=" . $dataProvider->id;
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>  ', $url, ['title' => Yii::t('yii', 'Alterar'), 'target' => '_blank', 'data-pjax' => "0"]);
                    },
                ],
            ],
        ];
    } else {
        ?>
        <h1><?= Html::encode($this->title) ?></h1>
        <form id="main-search" class="form form-inline sombra" role="form" action="<?= Url::to(['/nota-fiscal/search']) ?>">
            <div class="input-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <input type="text" name="filtro" id="main-search-product" class="form-control form-control-search input-lg data-hj-whitelist" placeholder="Procure por dados da NF." value="">
                <input type="text" name="validada" class="hidden" value="1">
                <span class="input-group-btn">
                    <button type="submit" class="btn btn-default btn-lg control " style="background-color: #007576" id="main-search-btn"><i class="fa fa-search" style="color: white"></i></button>
                </span>
            </div>

        </form>
    <?php

        $columns = [
            ['class' => 'yii\grid\SerialColumn'],
            'fornecedor',
            [
                'attribute' =>   'data_nf',
                'header' => 'Data NF',
                'content' => function ($dataProvider) {
                    return date("d/m/Y", strtotime($dataProvider->data_nf));
                },
            ],
            'numero_nf',
            'valor_nf',
            [
                'class' => 'yii\grid\CheckboxColumn',
            ],
        ];
    }

    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'id' => 'griditems',
        'rowOptions' => function ($dataProvider) {
            return [
                'data' => ['key' => $dataProvider['id']],
            ];
        },
        'columns' => $columns,
        'containerOptions' => ['style' => 'overflow: auto'], // only set when $responsive = false
        'pjax' => true,
        'responsive' => true,
        'hover' => true,
    ]); ?>

    <?= Html::button('Validar Notas', [
        'class' => 'btn btn-success',
        'id' => 'btn_validar',
    ]) ?>

    <?php

    $js = <<< JS
        $('#btn_validar').click(function(){
            var keys = $('#griditems').yiiGridView('getSelectedRows');
            var url = $url_notas;
            $.post({
                url: baseUrl+url, // your controller action
                data: {keylist:keys},
                success: function(data) {
                if (data.status === 'success') {
                    alert('I did it! Processed checked rows.');
                }
            },
        });
    });

JS;

    $this->registerJs($js);
    ?>
</div>

<style>
    .container-xxl {
        width: 90%;
        margin: auto;
    }
</style>