/**
 * Created by smart_i9 on 13/10/2015.
 */

$(document).ready(function () {
    var $button = $('#importar');
    var $buttonIcon = $('#importar-icon');
    var $buttonLabel = $('#importar-label');
    $('#upload').submit(function () {
        $buttonIcon.removeClass('glyphicon glyphicon-import');
        $buttonIcon.addClass('fa no-color fa-refresh fa-spin');
        $buttonLabel.html('Importando...');
    });
});