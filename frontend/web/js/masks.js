$(document).ready(function($){
    $("#cep-comprador").mask("00000000");
    $("#comprador-cpf").mask("000.000.000-00", {
        reverse: true
    });
    $("#usuario-cpf").mask("000.000.000-00", {
        reverse: true
    });
    $("#empresa-telefone").mask("(00) 000000000");
    $("#portal-telefone").mask("(00) 000000000");
    $("#empresa-telefone_alternativo").mask("(00) 000000000");
    $("#filial-telefone").mask("(00) 000000000");
    $("#filial-telefone_alternativo").mask("(00) 000000000");
    $("#enderecoempresa-numero").mask("00000");
    $("#empresa-documento").mask("00.000.000/0000-00");
    $("#filial-documento").mask("00.000.000/0000-00");
    $("#w0").submit(function() {
        $("#cep-comprador").unmask();
        $("#empresa-documento").unmask();
        $("#comprador-cpf").unmask();
        $("#empresa-telefone").unmask();
        $("#empresa-telefone_alternativo").unmask();
        $("#filial-telefone").unmask();
        $("#filial-telefone_alternativo").unmask();
        $("#filial-documento").unmask();
        $("#usuario-cpf").unmask()
    })
});