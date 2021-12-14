<div class="panel panel-default panel-product-descricao">
    <div class="panel-body">
        <form class="form-horizontal form-especs-view" role="form">
            <div class="form-group col-xs-6 col-sm-6 col-md-6 col-lg-6 ">
                <label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 control-label">Peso</label>

                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <p class="form-control-static"><?= $produto->peso . ' kg' ?></p>
                </div>
            </div>
            <div class="form-group  col-xs-6 col-sm-6 col-md-6 col-lg-6">
                <label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 control-label">Altura</label>

                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <p class="form-control-static"><?= $produto->altura . ' cm' ?></p>
                </div>
            </div>
            <div class="form-group  col-xs-6 col-sm-6 col-md-6 col-lg-6">
                <label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 control-label">Largura</label>

                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <p class="form-control-static"><?= $produto->largura . ' cm' ?></p>
                </div>
            </div>
            <div class="form-group  col-xs-6 col-sm-6 col-md-6 col-lg-6">
                <label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 control-label">Profundidade</label>

                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <p class="form-control-static"><?= $produto->profundidade . ' cm' ?></p>
                </div>
            </div>
            <?php foreach ($produto->atributosProduto as $atributoProduto) { ?>
                <div class="form-group  col-xs-6 col-sm-6 col-md-6 col-lg-6">
                    <label
                            class=" col-xs-12 col-sm-12 col-md-12 col-lg-12 control-label"><?= $atributoProduto->atributo->nome ?></label>
                    <div class=" col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <p class="form-control-static"><?= $atributoProduto->getValor(); ?></p>
                    </div>
                </div>
            <?php } ?>
        </form>
    </div>
</div>
