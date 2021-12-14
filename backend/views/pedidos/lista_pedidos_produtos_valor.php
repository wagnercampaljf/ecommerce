<?php

use common\models\ProdutoFilial;

$produto_filial = ProdutoFilial::findOne($model['produto_filial_id']);
?>

<tr>
    <th scope="row" style="font-size: 14px; border:none !important;"><?= $produto_filial->produto->nome ?></p>
    </th>
    <td style="border:none !important;"></td>
    <td style="border:none !important;"></td>
    <td style="font-size: 14px; border:none !important;"><?= Yii::$app->formatter->asCurrency($model['valor'] * $model['quantidade']) ?></td>
</tr>