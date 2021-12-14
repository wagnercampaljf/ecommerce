<div class="panel panel-primary" xmlns="http://www.w3.org/1999/html">
    <div class="panel-heading">Formas de Envio:</div>
    <div class="panel-body">
        <?php foreach ($fretes as $label => $servicos): ?>
            <ul class="list-group">
                <?php foreach ($servicos as $servico) { ?>
                    <li class="list-group-item">
                        <div class="radio">
                            <label>
                                <input type="radio" name="radio_frete[<?= $servico->filial->id ?>]"
                                       class="radio_frete"
                                       id="radio_frete_<?= $servico->filial->id ?>"
                                       value="<?= $servico->getValor() ?>"
                                       onclick="changeValor($(this))"
                                       data-valor="<?= $servico->getValor() ?>"
                                       data-prevista="<?= $servico->getDataEsperada() ?>"
                                       data-filial-id="<?= $servico->filial->id ?>"
                                       data-transportadora-id="<?= $servico->id ?>">
                                <?= $servico->getLabel() ?>
                            </label>
                        </div>
                    </li>
                <?php } ?>
            </ul>
        <?php endforeach; ?>
        <p class="alert alert-warning">
            <i class="glyphicon glyphicon-exclamation-sign"></i>
            ATENÇÃO: O prazo de entrega terá início somente após a confirmação do pagamento.
        </p>
    </div>
</div>