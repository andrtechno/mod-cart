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
    selectorCount: '.cart-countItems',
    selectorTotal: '.cart-totalPrice',
    skin: 'default',
    /**
     * @param that
     */
    log: common.logger('Cart.js'),

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
    popupCallback: function () {
        $('#cart-modal').modal('show');
    },
    popup: function (reload = true) {
        cart.log.debug('popup','[reload: '+reload+']',);
        if (reload) {
            $("#cart-modal .modal-body").load(common.url('/cart/popup'), {}, function () {
                cart.popupCallback();
            });
        } else {
            cart.popupCallback();
        }

    },
    add: function (that) {
        var form = $(that).closest('form');

        cart.log.debug('add',that);

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            dataType: 'json',
            data: form.serialize(),
            beforeSend:function(){
                $(that).addClass('btn-loading');
            },
            success: function (data, textStatus, xhr) {
                $(that).removeClass('btn-loading');
                if (data.errors) {
                    common.notify(data.errors, 'error');
                } else {
                    cart.popup();
                    $(that).text(data.buttonText);
                    $(that).attr('onclick', 'cart.popup(false);');
                    //cart.renderBlockCart();
                    $(cart.selectorCount).html(data.countItems);
                    $(cart.selectorTotal).html(data.total_price_format);


                }
            },
            complete: function () {

            }
        });
    },
    add2: function (that) {
        //var form = $("#form-add-cart-" + product_id);

        var form = $(that).closest('form');

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            dataType: 'json',
            data: form.serialize(),
            success: function (data, textStatus, xhr) {
                if (data.errors) {
                    common.notify(data.errors, 'error');
                } else {


                    $.fancybox.open({
                        src: '/cart/popup',
                        type: 'ajax',
                        opts: {
                            touch: {
                                vertical: false,
                                momentum: false
                            },
                            ajax: {
                                settings: {
                                    method: 'POST',
                                    data: form.serialize()
                                }
                            }
                        },

                    });


                    cart.renderBlockCart();
                    $.notify({
                        message: data.message,
                        url: data.url,
                    }, {
                        type: 'success',
                        allow_dismiss: false,
                        delay: 1,
                        timer: ($(document).width() > 768) ? 700 : 500,
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
    addLoader: function () {
        cart.log.debug('addLoader');
        $('#cart-modal, #cart-table').addClass('modal-loading');
    },
    removeLoader: function () {
        cart.log.debug('removeLoader');
        $('#cart-modal, #cart-table').removeClass('modal-loading');
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
            beforeSend: function () {
                cart.addLoader();
            },
            dataType: 'json',
            success: function (data) {
                $('#row-total-price' + data.product_id).addClass('TTTTTTTTTTTTTTTTTTTT').html('dsadsa')
                $('.row-total-price' + data.product_id).find('span:first-child').html(data.rowTotal);
                $('.price-unit-' + data.product_id).find('span:first-child').html(data.unit_price);
                //var delprice = 0;
                //if ($('.delivery-choose').prop("checked")) { //for april
                //    delprice = parseInt($('.delivery-choose:checked').attr("data-price"));
                //}
                var total = data.total_price;
                //var total = parseInt(test.replace(separator_thousandth, '').replace(separator_hundredth, '')) + delprice;
                // }


                // $('#balance').text(data.balance);
                //$('#balance').text((Number(data.total_price) * disum / 100));
                cart.log.debug('recount',data);

                //$(cart.selectorTotal).text(price_format(total));
                $(cart.selectorTotal).html(data.total_price);
                $(cart.selectorCount).html(data.countItems);
                $('.product-' + data.product_id + ' .spinner input').val(data.rowQuantity)
                cart.removeLoader();
                //cart.renderBlockCart();
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

    delivery2: function (that) {

        var ser = $('#cartForm').serialize();
        //if ($('#ordercreateform-delivery_id').val() == 1) {
        cart.log.debug('init', 'delivery');
        //$('#user-city, #user-address').hide();
        $.ajax({
            url: common.url('/cart'),
            type: 'POST',
            // dataType:'json',
            dataType: 'html',
            data:ser,
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



    delivery: function (id) {
        var ser = $('#cartForm').serialize();
        //if ($('#ordercreateform-delivery_id').val() == 1) {
        cart.log.debug('init', 'delivery');
        //$('#user-city, #user-address').hide();






        var xhr = $.ajax({
            url: common.url('/cart/delivery/process?id=' + id),
            type: 'POST',
            //dataType:'json',
            data:ser,
            dataType: 'html',
            success: function (data) {
               // console.log(data);
                //$('#delivery-form').html(data);
                $('.delivery-container-1').append('<div>asdasdasd</div>');


                $('#cartForm').on("cart:delivery", { xhr: 'test' },function(event,test) { // (1)
                    console.log("Привет от ", event,test); // Привет от H1
                });

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
        cart.log.debug('Init',this);
    }
}

//cart.init();


$(function () {
    $(document).on('click', '.spinner button', function () {
        var action = $(this).data('action');
        var input = $(this).parent().find('input');
        var product = $(this).parent().data('product');
        var value = input.val();
        if (action == 'plus') {
            value++;
        } else {
            value--;
        }
        value = (value > 999) ? 999 : value;
        value = (value < 1) ? 1 : value;

        if (value >= 1 && cart.spinnerRecount) {
            cart.recount(value, product);
        }

        //update all spinner value
        $('.spinner[data-product="'+product+'"]').find('input').val(value);
        //  input.val(value);
    });

    $(document).on('keyup', '.spinner input', function () {
        var input = $(this);
        var product = $(this).parent().data('product');
        var value = input.val();

        value = (value > 999) ? 999 : value;
        value = (value < 1) ? 1 : value;

        if (value >= 1 && cart.spinnerRecount) {
            cart.recount(value, product);
        }

        //update all spinner value
        $('.spinner[data-product="'+product+'"]').find('input').val(value);

    });

    $(document).on('click', '.btn-buy222', function () {
        var that = $(this);
        var form = that.closest('form');
        var action = form.attr('action');
        cart.log.debug(form);
        var product = that.data('product');
        var configurable = that.data('configurable');
        var quantity = that.data('quantity');
        if (quantity === undefined) {
            quantity = 1;
        }
        $.fancybox.open({
            src: action,//common.url('/cart/add'),
            type: 'ajax', //ajax html

            opts: {
                gutter: 10,
                touch: {
                    vertical: false,
                    momentum: false
                },
                ajax: {
                    settings: {
                        method: 'POST',
                        //data: {product_id: product, configurable_id: configurable, quantity: quantity},
                        data: form.serialize(),
                        dataType: 'json',
                        success: function (data) {
                            if (data.errors) {
                                common.notify(data.errors, 'error');
                            } else {
                                var ins = $.fancybox.getInstance();
                                that.text(data.buttonText);
                                cart.renderBlockCart();
                                ins.setContent(ins.current, data.html);
                            }

                        }
                    }
                }
            }
        });

        return false;

    });


    $(document).on('click', '.cart-remove', function () {
        var that = this
        var product = $(this).data('product');
        var isPopup = $(this).data('ispopup');


        var success;
        $.ajax({
            url: common.url('/cart/remove'),
            type: 'POST',
            dataType: 'json',
            data: {id: product,isPopup:isPopup},
            beforeSend:function(){
              cart.addLoader();
            },
            success: function (response) {
                if (response.success) {

                    if (response.reload) {
                        cart.popup(true);
                    } else {
                        $(that).closest('#product-' + response.id).remove();
                    }

                    common.notify(response.message, 'success');

                    $(cart.selectorTotal).html(response.total_price);
                    $(cart.selectorCount).html(response.countItems);
                    var button = $('button[data-product=' + response.id + ']');
                    if (button) {
                        button.attr('onclick', 'cart.add(this)').text(response.button_text_add)
                    }
                } else {

                    common.notify(response.message, 'error');
                }
            },
            complete: function () {
                cart.removeLoader();
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
                complete: function () {
                    // select.selectpicker('refresh');
                }
            });
        } else {
            // select.attr('');
        }
    });


    select.on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
        cart.log.debug('CHNAGE?', clickedIndex, isSelected, previousValue, $(this).selectpicker('val'));


        $.ajax({
            url: common.url('/cart/delivery/process'),
            type: 'GET',
            data: {city: $(this).selectpicker('val')},
            dataType: 'json',
            success: function (data) {
                select.html('');
                // cada array del parametro tiene un elemento index(concepto) y un elemento value(el  valor de concepto)
                $.each(data, function (index, value) {
                    cart.log.debug(value);
                    select.append('<option id="' + value + '">' + value + '</option>');
                });
                select.selectpicker('refresh');


            }
        });

    });


});
