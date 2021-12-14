<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Comprador */

$this->title = $model->nome;
$this->params['breadcrumbs'][] = ['label' => 'Compradors', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="comprador-view">

    <div class="portlet light" id="pedido">
        <div class="portlet-body">
            <form class="form-horizontal" style="margin-bottom: 5%">
                <div class="form-group">
                    <label class="col-sm-3 control-label">Comprador</label>
                    <div class="col-sm-9">
                        <p class="form-control-static"><?= $model['nome'] ?></p>
                    </div>

                    <label class="col-sm-3 control-label">CPF / CNPJ</label>
                    <div class="col-sm-9">
                        <p class="form-control-static"><?= strlen($model['empresa']['documento']) == 11 ? Yii::$app->formatter->asCpf($model['empresa']['documento']) : Yii::$app->formatter->asCNPJ($model['empresa']['documento']) ?></p>
                    </div>

                    <label class="col-sm-3 control-label">Endereço</label>
                    <div class="col-sm-9">
                        <p class="form-control-static">
                            <?= $model['empresa']['enderecosEmpresa'][0] ?>
                        </p>
                    </div>

                    <label class="col-sm-3 control-label">E-mail</label>
                    <div class="col-sm-9">
                        <p class="form-control-static">
                            <?= $model['email'] ?>
                        </p>
                    </div>

                    <label class="col-sm-3 control-label">Telefone</label>
                    <div class="col-sm-9">
                        <p class="form-control-static">
                            <?= Yii::$app->formatter->asTelefone($model['empresa']['telefone']) ?>
                        </p>
                    </div>
                </div>
            </form>
            <a class="btn btn-primary" href="<?= Url::to(['/compradores']) ?>" role="button">Voltar</a>
        </div>
    </div>
</div>
