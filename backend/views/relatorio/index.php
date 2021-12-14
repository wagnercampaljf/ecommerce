<?php

use common\models\Marca;
use common\models\MarcaProduto;
use kartik\date\DatePicker;
use yii\helpers\Html;
use kartik\select2\Select2;

$this->title = 'Relatórios';
$this->params['breadcrumbs'][] = $this->title;
$marcas = MarcaProduto::find()->all();
$marca = array();
foreach ($marcas as $value) {
    $marca[$value->id] = $value->nome;
}

?>
<div class="produto-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <div class="panel panel-default">
        <div class="panel-body">
            <form action="relatorios">

                <div class="row">
                    <div class="col-md-3">
                        <p>
                            <?php echo Select2::widget([
                                'name' => 'filial',
                                'data' =>  [94 => 'Minas Gerais', 96 => 'São Paulo'],
                                'options' => ['placeholder' => 'Selecione a Filial'],
                                'options' => [
                                    'id' => 'select_filial',
                                ],
                            ]); ?>
                            </br></br>
                        </p>
                    </div>

                    <div class="col-md-2">
                        <p>
                            <?php echo Select2::widget([
                                'name' => 'relatorio',
                                'data' =>  [
                                    1 => 'Relatório de Estoque',
                                    2 => 'Relatório de Estoque Por Marca',
                                    3 => 'Relatório de Venda Por Marca',
                                    4 => 'Relatório de Unif. Estoque e Venda Por Marca'
                                ],
                                'options' => ['placeholder' => 'Selecione o relatório'],
                                'options' => [
                                    'id' => 'select_relatorio',
                                ],
                            ]); ?>
                            </br></br>
                        </p>
                    </div>

                    <div class="col-md-3">

                        <p>
                            <?php echo Select2::widget([
                                'name' => 'marca',
                                'data' => $marca,
                                'options' => ['placeholder' => 'Selecione a marca do Produto', 'id' => 'select_marcas'],
                            ]); ?>
                            </br></br>
                        </p>
                    </div>
                    <div class="col-md-2">
                        <p>
                            <?php echo DatePicker::widget([
                                'name' => 'dt_inicial',
                                'removeButton' => false,
                                'pluginOptions' => [
                                    'autoclose' => true,
                                    'format' => 'dd/mm/yyyy'
                                ]
                            ]); ?>
                            </br></br>
                        </p>
                    </div>

                    <div class="col-md-2">
                        <p>
                            <?php echo DatePicker::widget([
                                'name' => 'dt_final',
                                'removeButton' => false,
                                'pluginOptions' => [
                                    'autoclose' => true,
                                    'format' => 'dd/mm/yyyy'
                                ]
                            ]); ?>
                            </br></br>
                        </p>
                    </div>
                </div>
                <div class="row">
                    <input type="submit" value="Gerar Relatório" class='btn btn-success'>
                </div>

            </form>
        </div>
    </div>

</div>