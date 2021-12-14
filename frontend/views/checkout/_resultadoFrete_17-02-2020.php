<!--<div class="panel panel-primary" xmlns="http://www.w3.org/1999/html">
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
</div>-->
<style>
    .fonte{
        font-size: 12px;
    }

    .color{
        background-color: #007576;
        border: #f5f5f5;
    }

    .color:focus{
        background-color: #f5f5f5;
        color: #ffffff;
    }

    .col1{
        width: 100%;
        height: 50%;
    }
    .cartão{
        margin-left: -38px !important;
    }

    .btn-bubble {
        color: white;
        background-color: #b10c10;

    }
    .btn-bubble:hover, .btn-bubble:focus {
        -webkit-animation: bubbles 1s forwards ease-out;
        animation: bubbles 1s forwards ease-out;
        background: radial-gradient(circle at center, rgba(0, 0, 0, 0) 30%, #eeeeff 60%, #eeeeff 65%, rgba(0, 0, 0, 0) 70%) 59% 125% / 0.93em 0.93em, radial-gradient(circle at center, rgba(0, 0, 0, 0) 30%, #eeeeff 60%, #eeeeff 65%, rgba(0, 0, 0, 0) 70%) 69% 149% / 0.83em 0.83em, radial-gradient(circle at center, rgba(0, 0, 0, 0) 30%, #eeeeff 60%, #eeeeff 65%, rgba(0, 0, 0, 0) 70%) 42% 108% / 0.83em 0.83em, radial-gradient(circle at center, rgba(0, 0, 0, 0) 30%, #eeeeff 60%, #eeeeff 65%, rgba(0, 0, 0, 0) 70%) 101% 107% / 0.99em 0.99em, radial-gradient(circle at center, rgba(0, 0, 0, 0) 30%, #eeeeff 60%, #eeeeff 65%, rgba(0, 0, 0, 0) 70%) 9% 128% / 1.14em 1.14em, radial-gradient(circle at center, rgba(0, 0, 0, 0) 30%, #eeeeff 60%, #eeeeff 65%, rgba(0, 0, 0, 0) 70%) 73% 100% / 0.71em 0.71em, radial-gradient(circle at center, rgba(0, 0, 0, 0) 30%, #eeeeff 60%, #eeeeff 65%, rgba(0, 0, 0, 0) 70%) 25% 86% / 0.55em 0.55em, radial-gradient(circle at center, rgba(0, 0, 0, 0) 30%, #eeeeff 60%, #eeeeff 65%, rgba(0, 0, 0, 0) 70%) 23% 103% / 1.08em 1.08em, radial-gradient(circle at center, rgba(0, 0, 0, 0) 30%, #eeeeff 60%, #eeeeff 65%, rgba(0, 0, 0, 0) 70%) 30% 126% / 0.67em 0.67em, radial-gradient(circle at center, rgba(0, 0, 0, 0) 30%, #eeeeff 60%, #eeeeff 65%, rgba(0, 0, 0, 0) 70%) 36% 98% / 1.04em 1.04em, radial-gradient(circle at center, rgba(0, 0, 0, 0) 30%, #eeeeff 60%, #eeeeff 65%, rgba(0, 0, 0, 0) 70%) 44% 130% / 0.61em 0.61em;
        background-color: #b10c10;

    }

    @-webkit-keyframes bubbles {
        100% {
            background-position: 55% -82%, 69% -167%, 44% -194%, 101% -285%, 10% -171%, 70% -126%, 15% -89%, 24% -60%, 22% -140%, 43% -17%, 46% -252%;
            box-shadow: inset 0 -6.5em 0 #0072c4;
        }
    }

    @keyframes bubbles {
        100% {
            background-position: 55% -82%, 69% -167%, 44% -194%, 101% -285%, 10% -171%, 70% -126%, 15% -89%, 24% -60%, 22% -140%, 43% -17%, 46% -252%;
            box-shadow: inset 0 -6.5em 0 #0072c4;
        }
    }


    .btn {
        display: inline-block;
        text-decoration: none;
    }

</style>

<!-- Botão oficial -->

<button type="button" style="border-color:#ffffff; background-color: #b10c10 " class="btn btn-bubble" data-toggle="modal" data-target="#modal<?= $servico->filial->id ?>"  >
    Formas de Envio
</button>

<!-- Modal-->
<div class="modal fade" id="modal<?= $servico->filial->id ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Escolha o frete </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="panel-body">
                    <p>Escolha um frete</p>
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
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Salvar</button>
            </div>
        </div>
    </div>
</div>

<!--
<div class="md-select">
    <label for="ul-id"><button type="button" class="ng-binding">Formas de Envio</button></label>
    <?php foreach ($fretes as $label => $servicos): ?>
        <ul role="listbox" id="ul-id" class="md-whiteframe-z1" aria-activedescendant="state2_AK" name="ul-id">
            <?php foreach ($servicos as $servico) { ?>
                <li class="list-group-item ng-binding ng-scope active"  role="option" id="state2_AK" tabindex="-1" aria-selected="true">
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
</div>


<style>
    .md-select {
        /*Demo css do not add to your project*/
        position: absolute;
        top: 50%;
        left: 50%;
        -webkit-transform: translate(-50%, -50%);
        transform: translate(-50%, -50%);
        /*--*/
        display: block;
        margin: 10px 0 8px 0;
        padding-bottom: 2px;
         position: relative;
        min-width: 180px;
    }
    .md-select *, .md-select :after, .md-select :before {
        box-sizing: border-box;
    }
    .md-select [type=button] {
        background: #fff;
        border-color: rgba(0, 0, 0, 0.12);
        border-width: 0 0 1px 0;
        color: rgba(0, 0, 0, 0.73);
        cursor: default;
        display: block;
        line-height: 48px;
        padding: 2px 0 1px 16px;
        position: relative;
        text-align: left;
        text-shadow: none;
        width: 100%;
        z-index: 1;
        outline: none;
        overflow: hidden;
    }
    .md-select [type=button]:focus, .md-select [type=button]:hover {
        background: rgba(0, 0, 0, 0.1);
    }
    .md-select [type=button]:after {
        content: '\25be';
        float: right;
        padding-right: 16px;
    }
    .md-select ul[role=listbox] {
        background-color: white;
        cursor: default;
        list-style: none;
        line-height: 26px;
        overflow: hidden;
        margin: 0;
        max-height: 0;
        position: absolute;
        padding: 0;
        -webkit-transform: translateY(-50%);
        transform: translateY(-50%);
        -webkit-transition: all 0.15s cubic-bezier(0.35, 0, 0.25, 1);
        transition: all 0.15s cubic-bezier(0.35, 0, 0.25, 1);
        width: 100%;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12), 0 1px 2px rgba(0, 0, 0, 0.24) !important;
    }
    .md-select ul[role=listbox] li {
        height: 48px;
        margin: 0;
        padding: 10px 16px;
        outline: none;
        overflow: hidden;
    }
    .md-select ul[role=listbox] li:focus, .md-select ul[role=listbox] li:hover, .md-select ul[role=listbox] li.active {
        background: rgba(0, 0, 0, 0.1);
    }
    .md-select.active ul {
        max-height: 200px;
        overflow: auto;
        padding: 8px 0 16px 0px;
        z-index: 2;
        -webkit-transition: all 0.2s ease;
        transition: all 0.2s ease;
        max-width: 350px;


    }

</style>


<script>
    $('.md-select').on('click', function(){
        $(this).toggleClass('active')
    })

    $('.md-select ul li').on('click', function() {
        var v = $(this).text();
        $('.md-select ul li').not($(this)).removeClass('active');
        $(this).addClass('active');
        $('.md-select label button').text(v)
    })

</script>-->

<!--<style>

    .box {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }

    .box select {
        background-color: #0563af;
        color: white;
        padding: 12px;
        width: 250px;
        border: none;
        font-size: 20px;
        box-shadow: 0 5px 25px rgba(0, 0, 0, 0.2);
        -webkit-appearance: button;
        appearance: button;
        outline: none;
    }

    .box::before {
        content: "\f13a";
        font-family: FontAwesome;
        position: absolute;
        top: 0;
        right: 0;
        width: 20%;
        height: 100%;
        text-align: center;
        font-size: 28px;
        line-height: 45px;
        color: rgba(255, 255, 255, 0.5);
        background-color: rgba(255, 255, 255, 0.1);
        pointer-events: none;
    }

    .box:hover::before {
        color: rgba(255, 255, 255, 0.6);
        background-color: rgba(255, 255, 255, 0.2);
    }

    .box select option {
        padding: 30px;
    }
</style>

<div class="box">

    <select>
        <option>


        </option>

    </select>

</div>-->




