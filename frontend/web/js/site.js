function goTop(a) {
    $("html, body").animate({
        scrollTop: 0
    }, 800, null, a)
}

function soma(f, c, b, g) {
    var d = parseInt($(f).val()) + parseInt(c);
    d >= b && $(f).val(d), $(f).trigger("change")
}

function mudaBadgeCarrinho(a) {
    void 0 !== a && ($cart = $(".cart-count"), $cart.fadeOut(300, function() {
        $cart.html(a), $cart.fadeIn(300)
    }))
}

function getEndereco(b, a) {
    8 == b.length && ($(".fa-spinner").show(), $.ajax({
        type: "GET",
        url: baseUrl + "/lojista/get-endereco",
        data: {
            cep: b
        },
        dataType: "JSON",
        error: function(c) {
            toastr.error("Erro ao processar o cálculo. Tente novamente em alguns segundos."), $(".fa-spinner").hide()
        },

        success: function(c) {

            //alert()

            document.getElementById(a + "-logradouro").value = c.logradouro, document.getElementById(a + "-cidade").value = c.localidade, document.getElementById(a + "-cidade_id").value = c.ibge, document.getElementById(a + "-bairro").value = c.bairro, document.getElementById(a + "-estado").value = c.uf, $(".fa-spinner").hide()


        }
    }))
}

// TESTE CEP NOVO 


var inputsCEP = $('#logradouro, #bairro, #localidade, #uf, #ibge');
var inputsRUA = $('#cep, #bairro, #ibge');
var validacep = /^[0-9]{8}$/;

function limpa_formulário_cep(alerta) {
    if (alerta !== undefined) {
        alert(alerta);
    }

    inputsCEP.val('');
}

function get(url) {

    $.get(url, function(data) {

        if (!("erro" in data)) {

            if (Object.prototype.toString.call(data) === '[object Array]') {
                var data = data[0];
            }

            $.each(data, function(nome, info) {
                $('#' + nome).val(nome === 'cep' ? info.replace(/\D/g, '') : info).attr('info', nome === 'cep' ? info.replace(/\D/g, '') : info);
            });



        } else {
            limpa_formulário_cep("CEP não encontrado.");
        }

    });
}

// Digitando RUA/CIDADE/UF
$('#logradouro, #localidade, #uf').on('blur', function(e) {

    if ($('#logradouro').val() !== '' && $('#logradouro').val() !== $('#logradouro').attr('info') && $('#localidade').val() !== '' && $('#localidade').val() !== $('#localidade').attr('info') && $('#uf').val() !== '' && $('#uf').val() !== $('#uf').attr('info')) {

        inputsRUA.val('...');
        get('https://viacep.com.br/ws/' + $('#uf').val() + '/' + $('#localidade').val() + '/' + $('#logradouro').val() + '/json/');
    }

});

// Digitando CEP
$('#cep').on('blur', function(e) {

    var cep = $('#cep').val().replace(/\D/g, '');

    if (cep !== "" && validacep.test(cep)) {

        inputsCEP.val('...');
        get('https://viacep.com.br/ws/' + cep + '/json/');

    } else {
        limpa_formulário_cep(cep == "" ? undefined : "Formato de CEP inválido.");
    }
});



//---------------------------------- FIM --------------------------

$(document).ready(function() {
    function b() {
        return "undefined" != typeof window.ontouchstart
    }

    function a(d) {
        var c = {};
        return $(d).filter(function() {
            for (var h = this.attributes, f = 0; f < h.length; f++) {
                if (0 == h[f].name.indexOf("select-dep-send-")) {
                    var i = h[f].name.replace("select-dep-send-", ""),
                        g = h[f].value;
                    void 0 === $(g).val() ? c[i] = g : c[i] = $(g).val()
                }
            }
        }), c
    }
    viewPortW = Math.max(document.documentElement.clientWidth, window.innerWidth || 0), !(viewPortW < 960 && b()), $(".sliders").each(function() {
        $(this).noUiSlider({
            start: [0, 3000],
            connect: !0,
            step: 500,
            orientation: "horizontal",
            range: {
                min: [0],
                max: [10000]
            },
            format: wNumb({
                decimals: 0
            })
        }), $(this).noUiSlider_pips({
            mode: "positions",
            values: [0, 10, 50, 75, 100],
            density: 6,
            stepped: !0
        })
    }), $(".select-dep").each(function() {
        $(this).change(function() {
            $this = $(this);
            var f = $this.val(),
                c = $this.attr("select-dep-url"),
                g = $this.attr("select-dep-target"),
                d = a($this);
            d.id = f, $.ajax({
                url: c,
                type: "POST",
                dataType: "json",
                data: d,
                success: function(h) {
                    $(g).html(""), $.each(h, function(k, j) {
                        var i = "" == k ? "selected" : "";
                        $(g).append($("<option/>", {
                            value: k,
                            html: j,
                            selected: i
                        })), i && $(g).select2("val", k)
                    })
                }
            })
        })
    }), $.urlParamChange = function(d, c) {
        return search = $(location).attr("search"), search = search.replace("?", ""), search.length > 0 ? (search = search.split("&"), eh_novo = !0, $.each(search, function(e, f) {
            param = f.split("="), (param[0] == d || param[0] == "?" + d) && (param[1] = c, search[e] = param.join("="), eh_novo = !1)
        }), eh_novo && search.push(d + "=" + c)) : (search = [], search.push(d + "=" + c)), $(location).attr("pathname") + "?" + search.join("&")
    }
});