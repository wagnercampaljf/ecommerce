<?php

use backend\models\PedidoCompraProdutoFilial;
use yii\helpers\Html;
use kartik\form\ActiveForm;
use common\models\ProdutoFilial;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\PedidoCompraProdutoFilialSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Texto Email';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pedido-compra-produto-filial-index">

    <?php
    $dadosEmail1 = "
    DESTACAR O ST RECOLHIDO ANTERIORMENTE EM INFORMAÇÕES ADICIONAIS E TAMBÉM NO XML DA NOTA, CASO CONTRÁRIO A MESMA SERÁ RECUSADA.
    ";
    $produtos = "
    Cod: {codfabri}
    Descrição: {descricao}
    Quantidade: {quantidade} unidade(s)
    Valor: R$ {valor} * {quantidade}
    Observação: {observacao}
    PA: {codpa}
    NCM: {ncm}
    ";

    foreach ($modelPedidoCompraProdutoFilial as $produto) {

        $observacao = PedidoCompraProdutoFilial::findOne($produto->id)->observacao;
        $nomeProduto = ProdutoFilial::findOne($produto->produto_filial_id)->produto->nome . ' (' . ProdutoFilial::findOne($produto->produto_filial_id)->produto->codigo_global . ')';
        $codpa = ProdutoFilial::findOne($produto->produto_filial_id)->produto->id;
        $ncm = ProdutoFilial::findOne($produto->produto_filial_id)->produto->codigo_montadora;
        $codfabri = ProdutoFilial::findOne($produto->produto_filial_id)->produto->codigo_fabricante;

        $quantidade = $produto->quantidade;

        $valor_compra = $produto->valor_compra;

        $dadosEmail1 .= str_replace(
            ["{descricao}", "{quantidade}", "{valor}", "{codpa}", "{ncm}", "{codfabri}", "{observacao}"],
            [$nomeProduto, $quantidade, $valor_compra, $codpa, $ncm, $codfabri, $observacao],
            $produtos
        );
    }

    $dadosEmail1 .= "                    
    Envio: Carmópolis de Minas, 963, Vila Maria.                    
      
    Atenciosamente,                    
                    
    Peça Agora
    Site: https://www.pecaagora.com/
    E-mail: compras.pecaagora@gmail.com Setor de Compras: (32)3015-0023 Whatsapp: (32)988354007
    Skype: pecaagora";

    ?>

    <?php $form = ActiveForm::begin(['action' => '../pedido-compra/pedido-compra-autorizar']); ?>
    <div class="col-md-12">
        <?= $form->field($modelPedidoCompra, 'corpo_email')->textarea(['rows' => '20', 'value' => $dadosEmail1]); ?>
        <?= $form->field($modelPedidoCompra, 'id')->hiddenInput()->label(false); ?>
    </div>
    <?= Html::submitButton('Enviar', ['class' => 'btn btn-success']) ?>
    <?php ActiveForm::end(); ?>

</div>