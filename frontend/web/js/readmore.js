function goTop(b) { $("html, body").animate({ scrollTop: 0 }, 800, null, b) }

function soma(h, j, a, e) {
    var i = parseInt($(h).val()) + parseInt(j);
    i >= a && $(h).val(i), $(h).trigger("change")
}

function mudaBadgeCarrinho(b) { void 0 !== b && ($cart = $(".cart-count"), $cart.fadeOut(300, function() { $cart.html(b), $cart.fadeIn(300) })) }

function getEndereco(c, d) {
    8 == c.length && ($(".fa-spinner").show(), $.ajax({
        type: "GET",
        url: baseUrl + "/lojista/get-endereco",
        data: { cep: c },
        dataType: "JSON",
        error: function(a) { toastr.error("Erro ao processar o cálculo. Tente novamente em alguns segundos."), $(".fa-spinner").hide() },
        success: function(a) {

            //alert("TESTANDO");

            var resultslog = a.logradouro;
            var resultsloc = a.localidade;
            var resultsbairro = a.bairro;


            if (resultslog === undefined) {
                resultslog = '';
            }
            if (resultsloc === undefined) {
                resultsloc = '';
            }
            if (resultsbairro === undefined) {
                resultsbairro = '';
            }

            document.getElementById(d + "-logradouro").value = resultslog,
                document.getElementById(d + "-cidade").value = resultsloc,
                document.getElementById(d + "-cidade_id").value = a.ibge,
                document.getElementById(d + "-bairro").value = resultsbairro,
                document.getElementById(d + "-estado").value = a.uf, $(".fa-spinner").hide()



            //document.getElementById(d + "-logradouro").value = a.logradouro, document.getElementById(d + "-cidade").value = a.localidade, document.getElementById(d + "-cidade_id").value = a.ibge, document.getElementById(d + "-bairro").value = a.bairro, document.getElementById(d + "-estado").value = a.uf, $(".fa-spinner").hide()
        }
    }))
}
$(document).ready(function() {
    function c() { return "undefined" != typeof window.ontouchstart }

    function d(a) {
        var b = {};
        return $(a).filter(function() {
            for (var j = this.attributes, l = 0; l < j.length; l++) {
                if (0 == j[l].name.indexOf("select-dep-send-")) {
                    var e = j[l].name.replace("select-dep-send-", ""),
                        k = j[l].value;
                    void 0 === $(k).val() ? b[e] = k : b[e] = $(k).val()
                }
            }
        }), b
    }
    viewPortW = Math.max(document.documentElement.clientWidth, window.innerWidth || 0), !(viewPortW < 960 && c()), $(".sliders").each(function() { $(this).noUiSlider({ start: [0, 3000], connect: !0, step: 500, orientation: "horizontal", range: { min: [0], max: [10000] }, format: wNumb({ decimals: 0 }) }), $(this).noUiSlider_pips({ mode: "positions", values: [0, 10, 50, 75, 100], density: 6, stepped: !0 }) }), $(".select-dep").each(function() {
        $(this).change(function() {
            $this = $(this);
            var b = $this.val(),
                h = $this.attr("select-dep-url"),
                a = $this.attr("select-dep-target"),
                e = d($this);
            e.id = b, $.ajax({
                url: h,
                type: "POST",
                dataType: "json",
                data: e,
                success: function(f) {
                    $(a).html(""), $.each(f, function(g, l) {
                        var m = "" == g ? "selected" : "";
                        $(a).append($("<option/>", { value: g, html: l, selected: m })), m && $(a).select2("val", g)
                    })
                }
            })
        })
    }), $.urlParamChange = function(a, b) { return search = $(location).attr("search"), search = search.replace("?", ""), search.length > 0 ? (search = search.split("&"), eh_novo = !0, $.each(search, function(h, g) { param = g.split("="), (param[0] == a || param[0] == "?" + a) && (param[1] = b, search[h] = param.join("="), eh_novo = !1) }), eh_novo && search.push(a + "=" + b)) : (search = [], search.push(a + "=" + b)), $(location).attr("pathname") + "?" + search.join("&") }
});
