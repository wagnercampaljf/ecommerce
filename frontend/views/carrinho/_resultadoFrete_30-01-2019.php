<?php
use common\models\Filial;

?>
<div class="panel panel-primary" xmlns="http://www.w3.org/1999/html">
    <div class="panel-heading">Formas de Envio:</div>
    <div class="panel-body">
        <?php
        foreach ($fretes as $filial_id => $servicos) { ?>
            <ul class="list-group">
                <li class="list-group-item">
                    <strong><?= (($filial = Filial::findOne($filial_id)) ? $filial->nome : '') ?></strong>
                </li>
                <?php foreach ($servicos as $servico) { ?>
                    <li class="list-group-item">
                        <div class="radio">
                            <label>
                                <input type="radio" data-id="<?= $servico->id ?>" class="opcao"
                                       name="<?= $servico->filial->id ?>"
                                       id="radio_frete_<?= $servico->filial->id ?>"
                                       value="radio_frete[<?= $servico->filial->id ?>]" <?php if (isset(\Yii::$app->session['frete'][$servico->filial->id]) && $servico->id == \Yii::$app->session['frete'][$servico->filial->id]) {
                                    echo "checked";
                                } ?>>
                                <?= $servico->getLabel() ?>

                            </label>
                        </div>
                    </li>
                <?php } ?>
            </ul>
        <?php } ?>
    </div>
</div>