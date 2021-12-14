





<?php if (!empty($produto->descricao)) { ?>
    <div id="descricao-produto" class="panel-product-descricao ">
        <h4>Descrição</h4>
        <div class="texto-descricao">
            <p style="text-align: justify">
            <h3 class="descricaoprod"><?= $produto->descricao ?></h3>
            </p>
        </div>
    </div>
<?php } ?>

<style>
    p {
        text-align: left;
        text-indent: 0px;
        font-size: 14px; !important;
        font-weight: initial;
    }


</style>
<div id="descricao-complementar" class="panel-product-descricao ">

    
    <?php if (!empty($produto->aplicacao)) {


        ?>
        <div id="aplicacao-produto" class="panel-product-descricao">
            <h4 style="font-weight: bold;">- Aplicação da Peça</h4>
            <p style="text-align: justify">
            <ul>
                <li style="list-style-type: disc;"><?= $produto->aplicacao ?></li>
                <li> <?= $produto->aplicacao_complementar ?></li>
            </ul>

            </p>
        </div>
        <hr class="hidden-xs">
    <?php } ?>
    <?php if (!empty($produto->codigo_similar)) { ?>
        <div id="aplicacao-produto" class="panel-product-descricao">
            <h4 style="font-weight: bold;">- Códigos Similares</h4>
            <p style="text-align: justify">
            <h2 class="compatibilidade"><?= $produto->codigo_similar ?></h2>
            </p>
        </div>

    <?php } ?>

    <?php if (!empty($produto->codigo_similar)) { ?>

    <?php } ?>
    <div id="especificacao-produto" class="panel-product-descricao row ">
    <ul>
        <?php
        if (!empty($produto->subcategoria->descricao)) { ?>
        <h4 style="font-weight: bold;">- Descrição</h4>
        <li style="list-style-type: disc;">  <?= $produto->subcategoria->descricao ;} ?> </li>
    </ul><hr>

    </div>
    <div id="especificacao-produto" class="panel-product-descricao row hidden-xs">
        <h4 style="font-weight: bold;">- Especificações</h4>

        <div class="especificacao-produto">
            <form class="form-horizontal" role="form">
                <div class="row">
                    <ul>
                        <li style="list-style-type: disc;"> Peso : <?= $produto->peso . ' kg ' ?><?php echo $produto->e_medidas_conferidas =='crm'?'<i class="fa fa-check" style="display: contents "!important></i>':'';?></p>
                        </li>
                        <li style="list-style-type: disc;"> Altura : <?= $produto->altura . ' cm' ?><?php echo $produto->e_medidas_conferidas =='crm'?' <i class="fa fa-check" style="display: contents "!important></i>':'';?></p>
                        </li>
                        <li style="list-style-type: disc;"> Largura : <?= $produto->largura . ' cm' ?><?php echo $produto->e_medidas_conferidas =='crm'?' <i class="fa fa-check" style="display: contents "!important></i>':'';?></p>
                        </li>
                        <li style="list-style-type: disc;"> Profundidade : <?= $produto->profundidade . ' cm' ?><?php echo $produto->e_medidas_conferidas =='crm'?' <i class="fa fa-check" style="display: contents "!important></i>':'';?></p>


                        </li>
                    </ul>
                </div>
                <?php foreach ($produto->atributosProduto as $atributoProduto) { ?>
                    <div class="form-group  col-xs-2 col-sm-2 col-md-2 col-lg-2">
                        <label class=" col-xs-12 col-sm-12 col-md-12 col-lg-12 control-label"><?= $atributoProduto->atributo->nome ?></label>
                        <div class=" col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <p class="form-control-static"><?= $atributoProduto->getValor(); ?></p>
                        </div>
                    </div>
                <?php } ?>
            </form>
        </div>
    </div><hr>

    <li>
            ATENÇÃO!!! Prezado Cliente, caso ainda tenha dúvidas de que este é o produto correto para você, ANTES DE COMPRAR nos informe o chassi do seu veículo no chat para
            termos certeza que esta é a peça certa. Dessa forma evitamos transtornos de trocas e devoluções.
        </li><hr>

    <div id="especificacao-produto" class="panel-product-descricao row ">

    <h4 style="font-weight: bold;">- Dicas</h4>
    <ul>
        <li style="list-style-type: disc;"> Lado Esquerdo é o do Motorista.</li>
        <li style="list-style-type: disc;">Lado Direito é o do Passageiro.</li>
    </ul>
    <hr>

    <ul>
        <li style="list-style-type: disc;">GARANTIA: Garantia de DEFEITOS DE FABRICAÇÃO</li>
    </ul>


</div></div>

