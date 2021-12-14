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
use yii\helpers\Html;
use common\models\ProdutoFilial;

$this->params['active'] = 'pedidos';

$this->title = 'Pedido #' . $model['code'];
?>

<div class="portlet light" id="pedido">
    <div class="portlet-body">
        <form class="form-horizontal" style="margin-bottom: 5%">
            <div class="form-group">
                <label class="col-sm-3 control-label">Número do Pedido</label>

                <div class="col-sm-9">
                    <p class="form-control-static"><?= '#' . $model['code'] ?></p>
                </div>
                <label class="col-sm-3 control-label">Data</label>

                <div class="col-sm-9">
                    <p class="form-control-static"><?= Yii::$app->formatter->asDate($model['placed_at']) ?></p>
                </div>
                <label class="col-sm-3 control-label">Status</label>

                <div class="col-sm-9">
                    <p class="form-control-static"><?= Pedido::$statusClasses[Pedido::$statusSkyhub[$model['status']['type']]]::getLabel() ?></p>
                </div>
                <label class="col-sm-3 control-label">Forma de Pagamento</label>

                <div class="col-sm-9">
                    <ul>
                        <?php
                        foreach ($model['payments'] as $pagamento) {
                            echo \yii\helpers\Html::tag('li', $pagamento['method']);
                        }
                        ?>
                    </ul>
                </div>
                <label class="col-sm-3 control-label">Forma de Frete</label>

                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= $model['shipping_method'] ?>
                    </p>
                </div>
                <label class="col-sm-3 control-label">Endereço</label>

                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?php
                        $endereco = $model['shipping_address'];
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
                        <?= $model['customer']['email'] ?>
                    </p>
                </div>
                <label class="col-sm-3 control-label">Telefone</label>

                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= ""//Yii::$app->formatter->asTelefone($model['customer']['phones'][0]) ?>
			<?php 
                            foreach($model['customer']['phones'] as $telefones){
                                echo Yii::$app->formatter->asTelefone(str_replace(" ", "", $telefones))."<br>";
                            }
                        ?>
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
                    //'value' => 'name'
		    'format'      =>'raw',
                    'value'       => function($data){
                        //print_r($data);
			$produto_filial = ProdutoFilial::find()->andWhere(['=', 'id', $data['id']])->one();
                        //return Html::a($data['name'], Url::to(['/produto/update', 'id' => $produto_filial->produto_id]));
			if($produto_filial){
				return Html::a($data['name'], 'https://www.pecaagora.com/p/'. $produto_filial->produto_id);
			}
			else{
				 return $data['name'];
			}
                    },
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
                Frete: <?= Yii::$app->formatter->asCurrency($model['shipping_cost']) ?>
            </p>
        </span>
        <span class="price lead">
            <p align="right" class="bg-success">
                Total: <?= Yii::$app->formatter->asCurrency($model['total_ordered']) ?>
            </p>
        </span>
        <a class="btn btn-primary" href="<?= Url::to(['/pedidos']) ?>" role="button">Voltar</a>
    </div>
</div>

