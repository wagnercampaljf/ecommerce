<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use \yii\widgets\Pjax;

$gridColumns = [
    'tipo',
    [
        'label' => 'Pedido ID',
        'format' => 'raw',
        'value' => function ($dataProvider) {
            if ($dataProvider['tipo'] == 'Pedido ML') {
                return Html::a(Html::encode($dataProvider['pedido_id']), ['/pedidos-mercado-livre/mercado-livre-view', 'id' => $dataProvider['pedido_id']]);
            } else if ($dataProvider['tipo'] == 'Pedido Estoque') {
                return Html::a(Html::encode($dataProvider['pedido_id']), ['/pedido-compra-produto-filial/update', 'id' => $dataProvider['pedido_id']]);
            } else {
                return Html::a(Html::encode($dataProvider['pedido_id']), ['/pedidos/view', 'id' => $dataProvider['pedido_id']]);
            }
        },
    ],
    [
        'label' => 'Data pedido',
        'attribute' => 'data_pedido',
        'value' => function ($dataProvider) {
            $data = substr($dataProvider['data_pedido'], 8, 2) . '/' . substr($dataProvider['data_pedido'], 5, 2) . '/' . substr($dataProvider['data_pedido'], 0, 4);
            return $data;
        },
    ],

    'nome',
    'pa',
    'nome_produto',
    'codigo_global',
    'nome_filial',
    'valor',
    'quantidade',
    [
        'class' => 'yii\grid\CheckboxColumn',
        'checkboxOptions' => ['id' => 'checkbox-row', 'value' => $id],
    ],
];

?>

<div class="allegato-index">

    <?php
    Pjax::begin(['id' => 'pedidos']);
    echo GridView::widget([
        'id' => $id,
        'dataProvider' => $dataProvider,
        'rowOptions' => function ($dataProvider) {
            return [
                'data' => ['key' => $dataProvider['id']],
            ];
        },
        'columns' => $gridColumns,
        'containerOptions' => ['style' => 'overflow: auto'], // only set when $responsive = false
        'responsive' => true,
        'hover' => true,
    ]);
    Pjax::end();
    ?>
</div>

<?php

$js = <<< JS
        $('#btn_validar').click(function(){
            var arr = [];/* Cria array com todos os names dos check selecionados */
            $('#checkbox-row:checked').each(function(item){ /* Busca todos os elementos com o class .checked e que possuam um atributo checked. Nesse caso somente radio e checkbox possuem tal atributo */
                arr.push($(this).attr("value"));/* Inclui name do elemento em um array*/
            });
            var nota_fiscal_produto_id = arr[0];
            var keys = $('#'+ nota_fiscal_produto_id).yiiGridView("getSelectedRows");
            var id_nota = $id_nota;
            $.post({
                url: baseUrl+"/nota-fiscal-produto/validar-produto", // your controller action
                data: {keylist:keys, nota_fiscal_produto_id:nota_fiscal_produto_id, id_nota: id_nota},
                success: function(data) {
                if (data.status === 'success') {
                    alert('I did it! Processed checked rows.');
                }
            },
        });
    });

JS;

$this->registerJs($js, yii\web\View::POS_END);
?>