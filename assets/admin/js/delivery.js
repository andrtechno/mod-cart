$(document).ready(function () {
    var sel = $('#delivery-system');
    sel.on('change', function () {
        $('#delivery_configuration').load('/admin/cart/delivery/render-configuration-form?system=' + $(this).val() + '&delivery_id=' + $(this).data('id'));
    });
    sel.change();

});