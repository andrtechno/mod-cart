$(document).on('change', '#meestmodel-city, #meestmodel-type, #meestmodel-area', function (e) {
    var delivery_id = $('#order-delivery_id option:selected').val();
    $.ajax({
        url: common.url('/admin/cart/delivery/process?id=' + delivery_id),
        type: 'POST',
        data: $('#order-form').serialize(),
        dataType: 'html',
        success: function (data) {
            $('#delivery-form').html(data);
            $('#delivery-form').removeClass('pjax-loader');
        },
        beforeSend: function () {
            $('#delivery-form').addClass('pjax-loader');
        },
        complete: function () {
            $('#delivery-form').removeClass('loading');
        },
        error: function () {
            $('#delivery-form').removeClass('loading');
        }
    });
});
