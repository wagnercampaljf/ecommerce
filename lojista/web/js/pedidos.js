$('.mudarstatus').click(function (event) {
    event.preventDefault();
    var r = confirm("Tem certeza que deseja alterar o status do pedido?");
    if (r == true) {
        window.location = $(this).attr('href');
    }

});
$('.rastro').click(function (event) {
    event.preventDefault();
    $(this).toggleClass('active');
    $('#eventos').toggleClass('hide');
});
