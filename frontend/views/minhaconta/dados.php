<?php
use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = 'Meus Dados';
$this->params['active'] = 'dados';
$this->params['breadcrumbs'][] = ['label' => 'Minha Conta', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tab-pane active col-md-9 col-sm-12" id="dados">
    <div class="panel panel-primary comprador-view">
        <div class="panel-heading">
            <?= Html::encode($this->title) ?>
        </div>
        <div class="panel-body">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'nome',
                    [
                        'attribute' => 'Empresa',
                        'value' => $model->empresa->nome,
                    ],
                    'cpf',
                    'dt_criacao:datetime',
                    'email:email',
                    'cargo',
                ],
            ]) ?>
            <div class="form-actions text-right">
                <?= Html::a('Editar Dados', ['update'], ['class' => 'btn btn-primary']); ?>
                <?= Html::a('Trocar Senha', ['change-password'], ['class' => 'btn btn-primary']); ?>
            </div>
        </div>
    </div>
</div>
