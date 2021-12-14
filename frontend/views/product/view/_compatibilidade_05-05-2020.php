<?php if (!empty($produto->aplicacao)) {


    ?>
    <div id="aplicacao-produto" class="panel-product-descricao">
        <h4>Aplicação da Peça</h4>
        <p style="text-align: justify">
        <h2 class="compatibilidade"><?= $produto->aplicacao ?></h2>
        <h2 class="compatibilidade"><?= $produto->aplicacao_complementar ?></h2>
        </p>
    </div>
    <hr>
<?php } ?>
<?php if (!empty($produto->codigo_similar)) { ?>
    <div id="aplicacao-produto" class="panel-product-descricao">
        <h4>Códigos Similares</h4>
        <p style="text-align: justify">
        <h2 class="compatibilidade"><?= $produto->codigo_similar ?></h2>
        </p>
    </div>
    <hr>
<?php } ?>
<div id="especificacao-produto" class="panel-product-descricao row">
    <h4>Especificações</h4>







    <div class="especificacao-produto">
        <form class="form-horizontal" role="form">
            <div class="row">
                <div class="form-group col-xs-3 col-sm-3 col-md-3 col-lg-3 ">
                    <label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 control-label">Peso</label>

                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <p class="form-control-static"><?= $produto->peso . ' kg ' ?><?php echo $produto->e_medidas_conferidas =='crm'?'<i class="fa fa-check" style="display: contents "!important></i>':'';?></p>
                    </div>
                </div>
                <div class="form-group  col-xs-3 col-sm-3 col-md-3 col-lg-3">
                    <label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 control-label">Altura</label>

                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <p class="form-control-static"><?= $produto->altura . ' cm' ?><?php echo $produto->e_medidas_conferidas =='crm'?' <i class="fa fa-check" style="display: contents "!important></i>':'';?></p>
                    </div>
                </div>
                <div class="form-group  col-xs-3 col-sm-3 col-md-3 col-lg-3">
                    <label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 control-label">Largura</label>

                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <p class="form-control-static"><?= $produto->largura . ' cm' ?><?php echo $produto->e_medidas_conferidas =='crm'?' <i class="fa fa-check" style="display: contents "!important></i>':'';?></p>
                    </div>
                </div>
                <div class="form-group  col-xs-3 col-sm-3 col-md-3 col-lg-3">
                    <label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 control-label">Profundidade</label>

                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <p class="form-control-static"><?= $produto->profundidade . ' cm' ?><?php echo $produto->e_medidas_conferidas =='crm'?' <i class="fa fa-check" style="display: contents "!important></i>':'';?></p>
                    </div>
                </div>
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
</div>
<?php if (!empty($produto->descricao)) { ?>
    <hr>
    <div id="descricao-produto" class="panel-product-descricao ">
        <h4>Descrição</h4>
        <div class="texto-descricao">
            <p style="text-align: justify">
            <h3 class="descricaoprod"><?= $produto->descricao ?></h3>
            </p>
        </div>
    </div>
<?php } ?>


<div id="descricao-complementar" class="panel-product-descricao ">
    <hr>
    <p>
        ATENÇÃO!!! Prezado Cliente, caso ainda tenha dúvidas de que este é o produto correto para você, ANTES DE COMPRAR nos informe o chassi do seu veículo no chat para
        termos certeza que esta é a peça certa. Dessa forma evitamos transtornos de trocas e devoluções.
    </p>
    <BR>
    <?= $produto->nome ?>
    <br>
    <?php
    if (!empty($produto->subcategoria->descricao)) { ?>
        <BR>DESCRIÇÃO:<BR>
        <?= $produto->subcategoria->descricao. "<BR>";
    } ?>
    <br>
    <br>
    DICAS:
    <br>
    <br>
    * Lado Esquerdo é o do Motorista.<br>
    * Lado Direito é o do Passageiro.
    <br>
    <br>
    GARANTIA: Garantia de DEFEITOS DE FABRICAÇÃO.
    <br>
    <br><br><br>
</div>

