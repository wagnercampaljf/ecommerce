/**
 * Created by Otávio on 09/07/2015.
 */

function topSell(arr) {

    var topsellContent = "";
    $.each(arr, function (index, value) {
        var link =  frontendBaseUrl + "/p/" + value.id + "/" + value.slug;
        $tr = $("<tr>");
        $td = $("<td>");
        $td.html("<a target='_blank' href=" + link + ">" + value.nome + ' (#' + value.codigo_global + ')' + "</a>");
        $tr.append($td);
        $td = $("<td>");
        $td.html(value.valor);
        $tr.append($td);
        $td = $("<td>");
        $td.html(value.qtd);
        $tr.append($td);
        //$td = $("<td>")
        //$td.html("<a class='btn default green-stripe' target='_blank' href="+link+">Ver Produto</a>");
        //$tr.append($td);
        topsellContent += $tr[0].outerHTML;
    });

    $("#topsell > tbody").html(topsellContent);
}