<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
<!------ Include the above in your HEAD tag ---------->


<form class="form-horizontal">
    <fieldset>
        <div class="panel panel-primary">
            <div class="panel-heading">Gerar Relátorios</div>

            <div class="panel-body">

                <!-- Text filial-->
                <div class="form-group">
                    <label class="col-md-2 control-label" for="selectbasic">Filial</label>

                    <div class="col-md-3">
                        <select required name="" class="form-control">
                            <option value=""></option>
                            <option value="">Peça Agora fisica</option>
                            <option value="">Peça Agora diadema</option>
                            <option value="">Peça agora RS</option>
                            <option value=" ">V Importados</option>
                        </select>
                    </div>
                </div>

                <!-- Text radios-->
                <div class="form-group">
                    <label class="col-md-2 control-label" for="radios">Códiso NCM </label>
                    <div class="col-md-4">
                        <label required="" class="radio-inline" for="radios-0" >
                            <input name="1"  value="" type="radio" required>
                            Todos
                        </label>
                        <label class="radio-inline" for="radios-1">
                            <input name="2"  value="" type="radio">
                            Com NCM
                        </label>
                        <label class="radio-inline" for="radios-1">
                            <input name="3"  value="" type="radio">
                            Sem NCM
                        </label>
                    </div>

                    <label class="col-md-1 control-label" for="radios">Descrição </label>
                    <div class="col-md-4">
                        <label required="" class="radio-inline" for="radios-0" >
                            <input name="sexo" id="sexo" value="feminino" type="radio" required>
                            Normal
                        </label>
                        <label class="radio-inline" for="radios-1">
                            <input name="sexo" id="sexo" value="masculino" type="radio">
                            Resumida
                        </label>
                    </div>
                </div>

                <!--  maior e menor-->
                <div class="form-group">
                    <label class="col-md-2 control-label" for="prependedtext">Quantidade maior que :</label>
                    <div class="col-md-2">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-chevron-left"></i></span>
                            <input id="prependedtext" name="prependedtext" class="form-control"  required="" type="number">
                        </div>
                    </div>

                    <label class="col-md-2 control-label" for="prependedtext">Quantidade menor que:</label>
                    <div class="col-md-4">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="	glyphicon glyphicon-chevron-right"></i></span>
                                <input id="prependedtext" name="prependedtext" class="form-control"  required="" type="number">
                            </div>
                        </div>
                    </div>
                </div>

                <!--  ativos inativos-->
                <div class="form-group">

                    <label class="col-md-2 control-label" for="radios">Produtos </label>
                    <div class="col-md-6">
                        <label required="" class="radio-inline" for="radios-0" >
                            <input name="1"  value="" type="radio" required>
                            Todos
                        </label>
                        <label class="radio-inline" for="radios-1">
                            <input name="2"  value="" type="radio">
                            Ativos
                        </label>
                        <label class="radio-inline" for="radios-1">
                            <input name="3"  value="" type="radio">
                            Inativos
                        </label>
                    </div>
                </div>

                <!-- tipo de codigo-->
                <div class="form-group">
                    <label class="col-md-2 control-label" for="selectbasic">Tipo de codigo</label>

                    <div class="col-md-3">
                        <select required name="" class="form-control">
                            <option value=""></option>
                            <option value="">PA</option>
                            <option value=" ">Global</option>
                            <option value=" ">Fornecedor</option>
                            <option value=" ">Todos</option>
                        </select>
                    </div>
                </div>

                <!-- valor de cusot/ valor de venda-->
                <div class="form-group">

                    <label class="col-md-4 control-label" for="radios">Valor de Custo / Valor de venda </label>
                    <div class="col-md-6">
                        <label required="" class="radio-inline" for="radios-0" >
                            <input name="1" value="" type="checkbox" required>
                           Igual a 0
                        </label>
                        <label class="radio-inline" for="radios-1">
                            <input name="2"  value="" type="checkbox">
                            Maior que 0
                        </label>
                    </div>
                </div>

                <!-- Tipo de relatorio-->
                <div class="form-group">
                    <label class="col-md-2 control-label" for="selectbasic">Tipo de Relátorio</label>

                    <div class="col-md-3">
                        <select required id="" name="" class="form-control">
                            <option value=""></option>
                            <option value="Analfabeto">Normal</option>
                            <option value="">Completo</option>
                            <option value="">Simplificado</option>

                        </select>
                    </div>
                    <label class="col-md-2 control-label" for="selectbasic">Ordenar Por:</label>

                    <div class="col-md-3">
                        <select required id="" name="" class="form-control">
                            <option value=""></option>
                            <option value="Analfabeto">Descrição do Produto</option>
                            <option value="">Codigo Global</option>
                            <option value="">Codigo Fornecedor</option>


                        </select>
                    </div>
                </div>


                <!-- Tipo de relatorio-->
                <div class="form-group">
                    <label class="col-md-2 control-label" for="Nome">Data</label>
                    <div class="col-md-2">
                        <input id="dtnasc" name="dtnasc" placeholder="DD/MM/AAAA" class="form-control input-md" required="" type="date" maxlength="10" OnKeyPress="formatar('##/##/####', this)">
                    </div>

                    <label class="col-md-1 control-label" for="Nome">á</label>
                    <div class="col-md-2">
                        <input id="dtnasc" name="dtnasc" placeholder="DD/MM/AAAA" class="form-control input-md" required="" type="date" maxlength="10" OnKeyPress="formatar('##/##/####', this)">
                    </div>
                </div>


                <!-- botão) -->
                <div class="form-group">
                    <label class="col-md-2 control-label" for="Cadastrar"></label>
                    <div class="col-md-8">
                        <button id="Cadastrar" name="Cadastrar" class="btn btn-success" type="Submit">Gerar Relátorio</button>
                        <button id="Cancelar" name="Cancelar" class="btn btn-danger" type="Reset">Cancelar</button>
                    </div>
                </div>

            </div>
        </div>


    </fieldset>
</form>




