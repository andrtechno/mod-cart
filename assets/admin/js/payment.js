$(document).ready(function () {
    var sel = $('#payment-payment_system');
    sel.on('change', function () {
        $('#payment_configuration').load('/admin/cart/payment/render-configuration-form?system=' + $(this).val() + '&payment_method_id=' + $(this).attr('rel'));
    });
    sel.change();

});