$(document).ready(function() {
    $(".quantidade-field").change(function() {
        var a = $(this);
        var c = a.data("id");
        var b = a.val();
        $.ajax({
            type: "GET",
            url: baseUrl + "/product/add-to-cart",
            data: {
                id: c,
                qte: b,
                replace: true
            },
            dataType: "JSON",
            success: function(d) {
                if (d.error_code != 0) {
                    toastr.error(d.msg);
                    a.val(d.qt_max)
                }
                $("#total_produto_" + c).html(d.labelValorProdutoTotal)
            },
            error: function(d) {
                console.log(d)
            },
            beforeSend: function(d) {}
        })
    });


    /*$("#calcula-frete").click(function() {
        var a = $("#seu_cep");
        var b = $("#calcula-frete");
        $.ajax({
            type: "GET",
            url: baseUrl + "/carrinho/get-frete",
            data: {
                cep: a.val(),
                returnType: "html"
            },
            error: function(c) {
                toastr.error("Erro ao processar o cálculo. Tente novamente em alguns segundos.")
            },
            success: function(c) {
                var d = $("#resultado-frete");
                d.slideUp("300", function() {
                    d.html(c);
                    d.slideDown(300);
                    b.html('<i class="fa no-color fa-refresh"></i> Atualizar Frete')
                })
            },
            beforeSend: function(c) {
                string = a.val();
                data = string.replace("-", "").replace("_", "");
                console.log(data);
                if ((data).length != 8) {
                    return false
                }
                b.html('<i class="fa no-color fa-refresh fa-spin"></i> Calculando...')
            }
        })
    });*/

    /*$(".trigger").popover({
        html: true,
        placement: "left",
        title: function() {
            return $(this).parent().find(".head").html()
        },
        content: function() {
            return $(this).parent().find(".content").html()
        }
    }).on("shown.bs.popover", function() {
        var a = $(this);
        $(this).next(".popover").find("button.cancel").click(function(b) {
            a.popover("hide")
        })
    })*/
});



/*function salvarCarrinho() {
    var b = $(".popover-content").find("input").val();
    var a = [];
    $(".opcao").each(function(d, e) {
        if (e.checked == true) {
            a[e.name] = e.getAttribute("data-id")
        }
    });
    var c = JSON.stringify(a);
    $.ajax({
        type: "GET",
        url: baseUrl + "/carrinho/salvar-carrinho",
        data: {
            fretes: c,
            nomeCarrinho: b
        },
        error: function(d) {
            toastr.error("Erro ao salvar carrinho. Tente novamente em alguns segundos.")
        },
        success: function(d) {
            window.location.replace(baseUrl + "/carrinho/add-carrinho?nomeCarrinho=" + d)
        },
        beforeSend: function(d) {}
    })
}*/


function removerProduto(a) {
    $.ajax({
        type: "GET",
        url: baseUrl + "/carrinho/remover-produto",
        data: {
            id: a
        },
        dataType: "JSON",
        error: function(b) {
            toastr.error("Erro ao salvar carrinho. Tente novamente em alguns segundos.")
        },
        success: function(b) {
            mudaBadgeCarrinho(b.carrinho_count);
            $.pjax.reload({
                container: "#idpjax"
            })
        },
        beforeSend: function(b) {}
    })
};

