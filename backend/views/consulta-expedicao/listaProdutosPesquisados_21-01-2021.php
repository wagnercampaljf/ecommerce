<?php

use yii\helpers\Url;
use backend\models\PedidoMercadoLivreProdutoSearch;
use yii\widgets\ListView;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use console\controllers\actions\omie\Omie;


?>





<div class="col-sm-12">
    <article class="card card_main" style="background-color: #ffffff; border-radius: 10px">
        <div class="card__body">
            <div class="card__content">
                <p style="color: #b10c10; font-weight: bold;"></p>
                <div class="row">

                    <div class="col-sm-3">
                        <div class="row">
                            <div class="col-sm-12">
                                <a href=<?= Url::to(['/consulta-expedicao/busca', 'codigo_pa' => $model->descricao])?>
                                   <b>PA Pesquisado:</b>

                                    <?= $model->descricao ?>
                                </a>
                            </div>
                        </div>

                    </div>
                    <div class="col-sm-4">
                        <div class="row">
                            <div class="col-sm-10">
                                <b>Data Da Pesquisa:</b>
                                <?= $model->salvo_em?>
                            </div>
                        </div>

                    </div>
                    <div class="col-sm-5">
                        <div class="row">
                            <div class="col-sm-6">
                                <b>Quem Fez a Pesquisa</b>
                            </div>
                            <!--<div class="col-sm-8">
                                <?= $model->salvo_por?>
                            </div>-->
                        </div>
                        <!--<div class="row">
                            <div class="col-sm-4">
                                <b>Quem Fez a Pesquisa</b>
                            </div>
                            <div class="col-sm-8">
                                <?= $model->salvo_por?>
                            </div>
                        </div>-->
                    </div>
                </div>
            </div>

        </div>
    </article><br>
</div>

