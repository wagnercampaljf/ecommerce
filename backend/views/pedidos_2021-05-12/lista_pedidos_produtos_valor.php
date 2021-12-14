<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

use common\models\ProdutoFilial;
use yii\widgets\ListView;

?>



<tr>
    <th scope="row" style="font-size: 14px; border:none !important;">Produto</p></th>
    <td style="border:none !important;"></td>
    <td style="border:none !important;"></td>
    <td style="font-size: 14px; border:none !important;"><?= Yii::$app->formatter->asCurrency($model['valor'] * $model['quantidade']) ?></td>
</tr>



