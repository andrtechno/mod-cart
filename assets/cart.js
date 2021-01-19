/**
 * Requires compatibility with common.js
 *
 * @author PIXELION CMS development team <dev@pixelion.com.ua>
 * @copyright (c) PIXELION CMS
 *
 *
 * @param boolean cart.spinnerRecount Статичный пересчет и/или с ajax
 * @function recountTotalPrice Пересчет общей стоимости
 * @function renderBlockCart Перезагрузка блока корзины (ajax response)
 * @function remove Удаление обэекта с корзины (ajax response)
 * @function add Добавление обэекта в корзину (ajax response)
 * @function recount Пересчет корзины (ajax response)
 * @function notifier Сообщить о появление (ajax response)
 * @function init Инициализация jquery spinner
 */
var cart_recount_xhr;
var cart = window.cart || {};
cart = {
    /**
     * @return boolean
     */
    spinnerRecount: true,
    selectorTotal: '#total',
    skin: 'default',
    /**
     * @param that
     */
    recountTotalPrice: function (that) {

        //var total = parseFloat(orderTotalPrice);
        var total = orderTotalPrice;
        var delivery_price = parseFloat($(that).attr('data-price'));
        var free_from = parseFloat($(that).attr('data-free-from'));
        if (delivery_price > 0) {
            if (free_from > 0 && total > free_from) {
                // free delivery
            } else {
                total = total + delivery_price;
            }
        }

        // $(cart.selectorTotal).html(price_format(total.toFixed(2)));
        $(cart.selectorTotal).html(total);
    },
    renderBlockCart: function () {
        $(".cart").load(common.url('/cart/render-small-cart'), {skin: cart.skin});
    },
    /**
     * @param product_id ИД обэекта

     remove: function (product_id) {


        $.ajax({
            url: common.url('/cart/remove/' + product_id),
            type: 'GET',
            dataType: 'html',
            success: function () {
                cart.renderBlockCart();
            }
        });
        return false;
    },*/
    /**
     * @param set_id Id товара
     */
    add_set: function (set_id) {
        $.ajax({
            url: common.url('/cart/add-set'),
            type: 'POST',
            dataType: 'json',
            data: form.serialize(),
            success: function (data) {
                if (data.errors) {
                    common.notify(data.errors, 'error');
                } else {
                    cart.renderBlockCart();
                    common.notify(data.message, 'success');
                    common.removeLoader();
                    $('body,html').animate({
                        // scrollTop: 0
                    }, 500, function () {
                        $(".cart").fadeOut().fadeIn();
                    });
                }
            },
            complete: function () {

//common.notify_list[0].close();
            }
        });

    },
    add: function (that) {
        //var form = $("#form-add-cart-" + product_id);

        var form = $(that).closest('form');
console.log('zzz',form);
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            dataType: 'json',
            data: form.serialize(),
            success: function (data, textStatus, xhr) {
                if (data.errors) {
                    common.notify(data.errors, 'error');
                } else {
                    cart.renderBlockCart();
                    $.notify({
                        message: data.message,
                        url: data.url,
                    }, {
                        type: 'success',
                        allow_dismiss: false,
                        placement: {
                            from: "top",
                            align: "right"
                        },
                        template: '<div data-notify="container" class="alert alert-{0}" role="alert"><button type="button" aria-hidden="true" class="close" data-notify="dismiss">&times;</button><span data-notify="icon"></span> <span data-notify="title">{1}</span> <span data-notify="message">{2}</span><div class="progress" data-notify="progressbar"><div class="progress-bar progress-bar-{0}" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div></div><a href="{3}" target="{4}" data-notify="url"></a></div>'
                    });
                    common.removeLoader();
                    $('body,html').animate({
                        // scrollTop: 0
                    }, 500, function () {
                        $(".cart").fadeOut().fadeIn();
                    });
                }
            },
            complete: function () {

            }
        });
    },
    /**
     * @param product_id ИД обэекта
     * @param quantities Количество
     */
    recount: function (quantities, product_id) {
        var disum = Number($('#balance').attr('data-sum'));

        if (cart_recount_xhr !== undefined)
            cart_recount_xhr.abort();

        cart_recount_xhr = $.ajax({
            type: 'POST',
            url: common.url('/cart/recount'),
            data: {
                product_id: product_id,
                quantities: quantities
            },
            dataType: 'json',
            success: function (data) {
                $('#row-total-price' + product_id).find('span:first-child').html(data.rowTotal);
                $('#price-unit-' + product_id).find('span:first-child').html(data.unit_price);
                //var delprice = 0;
                //if ($('.delivery-choose').prop("checked")) { //for april
                //    delprice = parseInt($('.delivery-choose:checked').attr("data-price"));
                //}
                var total = data.total_price;
                //var total = parseInt(test.replace(separator_thousandth, '').replace(separator_hundredth, '')) + delprice;
                // }


                // $('#balance').text(data.balance);
                //$('#balance').text((Number(data.total_price) * disum / 100));

                common.removeLoader();
                //$(cart.selectorTotal).text(price_format(total));
                $(cart.selectorTotal).html(total);
                cart.renderBlockCart();
            }
        });
    },
    /**
     * @param product_id ИД обэекта

     notifier: function (product_id) {
        $('body').append($('<div/>', {
            'id': 'dialog'
        }));
        $('#dialog').dialog({
            title: 'Сообщить о появлении',
            modal: true,
            resizable: false,
            draggable: false,
            responsive: true,
            open: function () {
                var that = this;
                common.ajax(common.url('/shop/notify'), {
                    product_id: product_id
                }, function (data, textStatus, xhr) {
                    $(that).html(data.data);
                }, 'json');
            },
            close: function () {
                $('#dialog').remove();
                $('a.btn-danger').removeClass(':focus');
            },
            buttons: [{
                text: common.message.cancel,
                'class': 'btn btn-link',
                click: function () {
                    $(this).remove();
                }
            }, {
                text: common.message.send,
                'class': 'btn btn-primary',
                click: function () {
                    common.ajax(common.url('/notify'), $('#notify-form').serialize(), function (data, textStatus, xhr) {
                        if (data.status === 'OK') {
                            $('#dialog').remove();
                            //common.report(data.message);
                            common.notify(data.message, 'success');
                        } else {
                            $('#dialog').html(data.data);
                        }
                    }, 'json');
                }
            }]
        });
    },*/

    delivery:function(that){

        //if ($('#ordercreateform-delivery_id').val() == 1) {
            console.log('init','delivery');
            //$('#user-city, #user-address').hide();
            $.ajax({
                url: common.url('/cart/delivery/process?id='+$(that).val()),
                type: 'GET',
                // dataType:'json',
                dataType:'html',
                success: function (data) {
                    $('#delivery-form').html(data);
                    //console.log(data);
                    /*if(data.cities){
                        $('#delivery-form').append('<select id="ordercreateform-delivery_city" name="OrderCreateForm[delivery_city]" class="form-control">' +
                            '<option value="address">Доставка на адрес</option>' +
                            '<option value="warehouse">Доставка на отделение</option>' +
                            '</select>');
                    }*/
                }
            });
       // }else{
       //     $('#delivery-form').html('');
        //    $('#user-city, #user-address').show();
        //}
    },

    init: function () {
        console.log('cart.init');
        $(function () {
            $('.cart-spinner').spinner({
                max: 999,
                min: 1,
                mouseWheel: false,
                /*icons: {
                 down: "btn btn-default",
                 up: "btn btn-default"
                 },*/
                //клик по стрелочкам spinner
                spin: function (event, ui) {
                    var max = $(this).spinner('option', 'max');
                    var product_id = $(this).attr('product_id');
                    if (ui.value >= 1 && cart.spinnerRecount) {
                        cart.recount(ui.value, product_id);
                    }
                    // && max > ui.value
                },
                stop: function (event, ui) { //запрещаем ввод числа больше 999;
                    var max = $(this).spinner('option', 'max');
                    if ($(this).val() > max)
                        $(this).val(max);
                },
                change: function (event, ui) {
                    var product_id = $(this).attr('product_id');
                    if ($(this).val() < 1) {
                        $(this).val(1);
                    }
                    if (cart.spinnerRecount) {
                        cart.recount($(this).val(), product_id);
                    }
                }
            });
            $('.ui-spinner-down').html('-');
            $('.ui-spinner-up').html('+');
        });
    }
}

cart.init();


$(function () {
    $(document).on('click', '.cart-remove', function () {
        $.ajax({
            url: $(this).attr('href'),
            type: 'GET',
            dataType: 'json',
            success: function (data) {
                if (data.success) {
                    $('#product-' + data.id).remove();
                    common.notify(data.message, 'success');
                    cart.renderBlockCart();
                    $(cart.selectorTotal).html(data.total_price);
                } else {
                    common.notify('remove error', 'success');
                }
            }
        });
        return false;
    });

    var select = $('#ordercreateform-city');
    $(document).on('click', '.delivery_checkbox', function () {
        var that = $(this);
        if (that.data('system')) {
            $.ajax({
                url: common.url('/cart/delivery/process'),
                type: 'GET',
                data: {id: that.val()},
                dataType: 'html',
                success: function (data) {
                    $('#test').html(data);


                    /*select.html('');
                    $.each(data, function (index, value) {
                        console.log(value);
                        select.append('<option id="' + value + '">' + value + '</option>');
                    });
                    select.selectpicker('refresh');*/

                    //$('#ordercreateform-city').selectpicker('refresh');


                },
                complete:function () {
                   // select.selectpicker('refresh');
                }
            });
        }else{
           // select.attr('');
        }
    });


    select.on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
        console.log('CHNAGE?', clickedIndex, isSelected, previousValue, $(this).selectpicker('val'));


        $.ajax({
            url: common.url('/cart/delivery/process'),
            type: 'GET',
            data: {city: $(this).selectpicker('val')},
            dataType: 'json',
            success: function (data) {
                select.html('');
                // cada array del parametro tiene un elemento index(concepto) y un elemento value(el  valor de concepto)
                $.each(data, function (index, value) {
                    console.log(value);
                    select.append('<option id="' + value + '">' + value + '</option>');
                });
                select.selectpicker('refresh');


            }
        });

    });


});

