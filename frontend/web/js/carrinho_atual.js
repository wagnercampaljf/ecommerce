


function salvarCarrinho() {
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
}

function salvarCarrinho() {
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
}

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



fbq('track', 'AddToCart');
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

fbq('track', 'AddToCart');

