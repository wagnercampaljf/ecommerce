function addProdutoCarrinho(b, a) {
    $.ajax({
        type: "GET", url: baseUrl + "/product/add-to-cart", data: {id: b}, dataType: "JSON", success: function (c) {
            if (c.error_code != 0) {
                //alert("Erro: " + c.msg)
            } else {
                $(a).html('<i class="fa no-color fa-shopping-cart"></i> Já Adicionado');
                $(a).addClass("disabled");
                mudaBadgeCarrinho(c.carrinho_count);

		/*$.ajax({
                    type: "GET", url: baseUrl + "/product/existe-carrinho", dataType: "JSON", 
                    success: function (c) 
                    {
                    	if(c.carrinho_count > 0){
				window.location = "/carrinho";
			}
                    }, 
                    error: function (c) {
                        console.log(c)
                    }, 
                });*/

                window.location = "/carrinho"
            }
        }, error: function (c) {
            console.log(c)
        }, beforeSend: function (c) {
            $(a).html('<i class="fa no-color fa-refresh fa-spin"></i> Adicionando...')
        }
    })
}
$(document).ready(function () {
	$("#calcula-frete").click(function () {
		var a = $("#seu_cep");
        var b = $("#calcula-frete");
        //alert(a.val()); alert($("#produto_id").val());
        $.ajax({
            type: "GET",
            url: baseUrl + "/product/get-frete",
            dataType: "JSON",
            data: {cep: a.val(), produto_id: $("#produto_id").val(), returnType: "json"},
            complete: function (c) {
                //console.log(c);
            	b.html('<i class="fa no-color fa-truck"></i> Calcular Frete');
                $("html, body").animate({scrollTop: $("#lojas").offset().top - 150}, 800);
                $("#lojas").focus();
                //alert(2);
            },
            success: function (c) {
            	console.log(c);
            	$.each(c, function (d, e) {
			console.log(d);
                    className = ".filial_" + d;
			//alert(5);
                    if (e.length > 0) {
			//alert(6);
                        msg = "";
			//alert(7);
                        $.each(e, function (f, g) {
                            msg += "<p>" + g.label + "</p>"
                        });
			//alert(className);
                        $(className).html(msg)
                    } else {
                        $(className).html("Não foi possivel calcular o Frete!")
                    }
                });
                //alert(4);
            },
            error: function (c) {
                //alert(5);
                console.log(c);
                $(".produto-filial").html("Erro ao processar o cálculo. Tente novamente.");
                toastr.error("Erro ao processar o cálculo. Tente novamente em alguns segundos.");
                //alert(6);
            },
            beforeSend: function (c) {
                //alert(7);
            	b.html('<i class="fa no-color fa-refresh fa-spin"></i> Calculando...');
                $(".produto-filial").html('<i class="fa fa-spinner fa-spin fa-large"></i> Calculando...');
                //alert(8);
            }
        });
    });
    $("#verAplicacao").click(function () {
        $("#aplicacao").trigger("click");
        $("html, body").animate({scrollTop: $($(this).attr("href")).offset().top}, 1300);
        return false
    });
    $("#outrasLojas").click(function () {
        $("#loja").trigger("click");
        $("html, body").animate({scrollTop: $($(this).attr("href")).offset().top}, 1300);
        return false
    });
});

/*(function($){
    $('#thumbcarousel').carousel(0);
    var $thumbItems = $('#thumbcarousel .item');
    $('#carousel').on('slide.bs.carousel', function (event) {
        var $slide = $(event.relatedTarget);
        var thumbIndex = $slide.data('thumb');
        var curThumbIndex = $thumbItems.index($thumbItems.filter('.active').get(0));
        if (curThumbIndex>thumbIndex) {
            $('#thumbcarousel').one('slid.bs.carousel', function (event) {
                $('#thumbcarousel').carousel(thumbIndex);
            });
            if (curThumbIndex === ($thumbItems.length-1)) {
                $('#thumbcarousel').carousel('next');
            } else {
                $('#thumbcarousel').carousel(numThumbItems-1);
            }
        } else {
            $('#thumbcarousel').carousel(thumbIndex);
        }
    });
})(jQuery);*/
