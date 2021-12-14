<?php
/**
 * Created by PhpStorm.
 * User: tercio
 * Date: 13/09/17
 * Time: 18:32
 */

use backend\models\PedidoMercadoLivreProdutoSearch;
use yii\grid\GridView;
use yii\helpers\Url;
use common\models\PedidoMercadoLivreProduto;

//echo "<pre>"; print_r($model); echo "</pre>"; die;

$this->params['active'] = 'pedidos';

$this->title = 'Pedido #' . $model['pedido_meli_id'];
?>

<div class="portlet light" id="pedido">
    <div class="portlet-body">
        <form class="form-horizontal" style="margin-bottom: 5%">
            <div class="form-group">
                <label class="col-sm-3 control-label">Número do Pedido</label>

                <div class="col-sm-9">
                    <p class="form-control-static"><?= '#' . $model['id'] ?></p>
                </div>
                <label class="col-sm-3 control-label">Número do Pedido(MELI_ID)</label>

                <div class="col-sm-9">
                    <p class="form-control-static"><?= '#' . $model['pedido_meli_id'] ?></p>
                </div>
                <label class="col-sm-3 control-label">Nome do Comprador</label>

                <div class="col-sm-9">
                    <p class="form-control-static"><?= $model['buyer_first_name'] ?></p>
                </div>
                <label class="col-sm-3 control-label">Sobrenome do Comprador</label>

                <div class="col-sm-9">
                    <p class="form-control-static"><?= $model['buyer_last_name'] ?></p>
                </div>
                <label class="col-sm-3 control-label">Status</label>

                <div class="col-sm-9">
                    <p class="form-control-static"><?= $model['status'] ?></p>
                </div>
                <label class="col-sm-3 control-label">E-mail</label>

                <div class="col-sm-9">
                    <p class="form-control-static"><?= $model['buyer_email'] ?></p>
                </div>
                <label class="col-sm-3 control-label">Telefone</label>

                <div class="col-sm-9">
                    <p class="form-control-static"><?= $model['receiver_phone'] ?></p>
                </div>
            </div>
        </form>

        <?php
        
        //$pedido_mercado_livre_produtos = PedidoMercadoLivreProduto::find()->andWhere(['=','produto_mercado_livre_id',$model->id])->all();
        
        //$dataProvider = new \yii\data\ArrayDataProvider(['models' => $model['items'], 'pagination' => false]);
        //$dataProvider = new \yii\data\ArrayDataProvider(['models' => $pedido_mercado_livre_produtos, 'pagination' => false]);
        
        echo GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                
                'id',
                'produto_meli_id',
                'title',
                'quantity',
                'unit_price',
                'sale_fee',
                // 'condition',
                // 'quantity',
                // 'unit_price',
                // 'full_unit_price',
                // 'sale_fee',
                // 'listing_type_id',
                
                //['class' => 'yii\grid\ActionColumn'],
            ],
        ]);?>

        <span class="price">
            <p align="right" class="bg-success">
                Frete: <?= Yii::$app->formatter->asCurrency($model['shipping_option_list_cost']) ?>
            </p>
        </span>
        <span class="price lead">
            <p align="right" class="bg-success">
                Total: <?= Yii::$app->formatter->asCurrency($model['total_amount']) ?>
            </p>
        </span>
        <a class="btn btn-primary" href="<?= Url::to(['/pedidos']) ?>" role="button">Voltar</a>
    </div>
</div>

