<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model \common\models\Usuario */
/* @var $filial \common\models\Filial */
/* @var $enderecoFilial \common\models\EnderecoFilial */

$this->title = Yii::t('app', 'Minha Conta');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 usuario-index">
    <div class="portlet light grey-cararra" id="dados_usuario">
        <div class="portlet-title">
            <div class="caption caption-md">
                <span class="text-info h3"><?= Yii::t('app', 'Dados do Representante') ?></span>
            </div>
            <div class="actions">
                <?= Html::a("<i class='fa fa-edit'></i> " . Yii::t('app', 'Editar'), ['update-usuario'],
                    ['class' => 'btn grey-cascade']) ?>
                <?= Html::a('<i class="fa fa-key"></i> ' . Yii::t('app', 'Mudar Senha'), ['change-password'],
                    ['class' => 'btn btn-warning']) ?>
            </div>
        </div>
        <div class="portlet-body">
            <?= DetailView::widget([
                'options' => ['class' => 'table'],
                'model' => $model,
                'template' => '<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 row"><p class="text-primary h4">{label}</p><p>{value}</p></div>',
                'attributes' => [
                    'nome',
                    'cpf',
                    'cargo',
                    'email:email',
                ]
            ]) ?>
        </div>
    </div>
    <div class="portlet light grey-cararra" id="dados_filial">
        <div class="portlet-title">
            <div class="caption caption-md">
                <span class="text-info h3"><?= Yii::t('app', 'Dados da Empresa') ?></span>
            </div>
            <div class="actions">
                <?= Html::a("<i class='fa fa-edit'></i> " . Yii::t('app', 'Editar'), ['update-filial'],
                    ['class' => 'btn grey-cascade']) ?>
            </div>
        </div>
        <div class="portlet-body">
            <?= DetailView::widget([
                'options' => ['class' => 'table'],
                'model' => $filial,
                'template' => '<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 row"><p class="text-primary h4">{label}</p><p>{value}</p></div>',
                'attributes' => [
                    'nome',
                    'razao',
                    'documento',
                    'tipoEmpresa.nome:text:Tipo de Empresa',
                    'telefone',
                    'telefone_alternativo',
                ]
            ]) ?>
        </div>
    </div>
    <div class="portlet light grey-cararra" id="endereco_empresa">
        <div class="portlet-title">
            <div class="caption caption-md">
                <span class="text-info h3"><?= Yii::t('app', 'Endereço da Empresa') ?></span>
            </div>
            <div class="actions">
                <?= Html::a("<i class='fa fa-edit'></i> " . Yii::t('app', 'Editar'),
                    ['update-endereco', 'id' => $enderecoFilial->id],
                    ['class' => 'btn grey-cascade']) ?>
            </div>
        </div>
        <div class="portlet-body">
            <?= DetailView::widget([
                'options' => ['class' => 'table'],
                'model' => $enderecoFilial,
                'template' => '<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 row"><p class="text-primary h4">{label}</p><p>{value}</p></div>',
                'attributes' => [
                    'cep',
                    'logradouro',
                    'numero',
                    'complemento',
                    'bairro',
                    'cidade.label:text:Cidade',
                    'referencia'
                ]
            ]) ?>
        </div>
    </div>
</div>
