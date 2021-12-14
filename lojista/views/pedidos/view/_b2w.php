<?php
/**
 * Created by PhpStorm.
 * User: tercio
 * Date: 13/09/17
 * Time: 18:32
 */

use common\models\Pedido;
use yii\grid\GridView;
use yii\helpers\Url;

$this->params['active'] = 'pedidos';

$this->title = 'Pedido #' . $model->id;
$this->registerCssFile("https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css");
$this->registerJsFile("https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js");

?>

<div class="portlet light" id="pedido">
    <div class="portlet-body">
        <form class="form-horizontal" style="margin-bottom: 5%">
            <div class="form-group">
                <label class="col-sm-3 control-label">Número do Pedido</label>

                <div class="col-sm-9">
                    <p class="form-control-static"><?= '#' . $model->id ?></p>
                </div>
                <label class="col-sm-3 control-label">Data</label>

                <div class="col-sm-9">
                    <p class="form-control-static"><?= Yii::$app->formatter->asDate($model->dt_referencia) ?></p>
                </div>
                <label class="col-sm-3 control-label">Status</label>

                <div class="col-sm-9">
                    <p class="form-control-static"><?= Pedido::$statusClasses[Pedido::$statusSkyhub[$model->status]]::getLabel() ?></p>
                </div>
                <label class="col-sm-3 control-label">Forma de Pagamento</label>

                <div class="col-sm-9">
                    <ul>
                        <?php
                        foreach ($model->pagamento as $pagamento) {
                            echo \yii\helpers\Html::tag('li', $pagamento['method']);
                        }
                        ?>
                    </ul>
                </div>
                <label class="col-sm-3 control-label">Forma de Frete</label>

                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= $model->transportadora ?>
                    </p>
                </div>
                <label class="col-sm-3 control-label">Endereço</label>

                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?php
                        $endereco = $model->endereco;
                        echo $endereco['street'] . ' ' . $endereco['number'];
                        if ($endereco['detail']) {
                            echo '-' . $endereco['detail'];
                        }
                        echo ' ' . $endereco['neighborhood'] . ' ' . $endereco['postcode'];
                        echo ' ' . $endereco['city'] . "(" . $endereco['region'] . ")";
                        ?>
                    </p>
                </div>

                <label class="col-sm-3 control-label">E-mail</label>

                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= $model->email ?>
                    </p>
                </div>
                <label class="col-sm-3 control-label">Telefone</label>

                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= Yii::$app->formatter->asTelefone($model->telefone) ?>
                    </p>
                </div>
            </div>
        </form>

        <?php
        $dataProvider = new \yii\data\ArrayDataProvider(['models' => $model['items'], 'pagination' => false]);
        echo GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                [
                    'attribute' => 'Produto',
                    'value' => 'name'
                ],
                'qty:text:Quantidade',
                [

                    'format' => 'text',
                    'header' => 'Valor Unitário',
                    'value' => function ($data) {
                        return Yii::$app->formatter->asCurrency($data['special_price']);

                    },
                ],
                [

                    'format' => 'text',
                    'header' => 'Valor Total',
                    'value' => function ($data) {
                        return Yii::$app->formatter->asCurrency($data['special_price'] * $data['qty']);

                    },
                ],


            ],
        ]); ?>

        <span class="price">
            <p align="right" class="bg-success">
                Frete: <?= Yii::$app->formatter->asCurrency($model->valor_frete) ?>
            </p>
        </span>
        <span class="price lead">
            <p align="right" class="bg-success">
                Total: <?= Yii::$app->formatter->asCurrency($model->valor_total) ?>
            </p>
        </span>
        <a class="btn btn-primary" href="<?= Url::to(['/pedidos']) ?>" role="button">Voltar</a>
    </div>

    <div class="clearfix"></div>

    <div class="container-fluid text-center">
        <div class="bs-wizard" style="border-bottom:0;">
            <?php
            $state = '';
            foreach (Pedido::$statusClasses as $key => $status) {
                if ($status::isCompleted(Pedido::$statusSkyhub[$model->status])) {
                    $state = 'complete';
                } else {
                    if ($status::isNext(Pedido::$statusSkyhub[$model->status])) {
                        $state = 'active';
                    } else {
                        $state = 'disabled';
                    }
                }
                ?>

                <div class="col-xs-2 bs-wizard-step <?= $state ?>">
                    <div class="text-center bs-wizard-stepnum"><?= $status::getLabel() ?></div>
                    <div class="progress">
                        <div class="progress-bar"></div>
                    </div>

                    <a href='<?= Url::to(['mudar-status-skyhub', 'id' => $model->id,
                        'status' => array_search($key, Pedido::$statusSkyhub)]) ?>'
                       class="bs-wizard-dot mudarstatus"></a>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

