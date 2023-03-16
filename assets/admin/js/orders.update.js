/**
 * Show dialog
 * @param order_id

function openAddProductDialog(order_id) {
    $("#dialog-modal").dialog({
        width: '80%',
        modal: true,
        responsive: true,
        resizable: false,
        height: 'auto',
        draggable: false,
        open: function () {
            $('.ui-widget-overlay').bind('click', function () {
                $('#dialog-modal').dialog('close');
            });
        },
        close: function () {
            $('#dialog-modal').dialog('close');
        }
    });
    $('.ui-dialog').position({
        my: 'center',
        at: 'center',
        of: window,
        collision: 'fit'
    });
}*/


//Custom spinner
$(document).on('keyup', '.spinner input', function () {
    var input = $(this);
    var step = (input.data("step")) ? parseInt(input.data("step")) : 1;
    var value = (parseInt(input.val())) ? parseInt(input.val()) : step;
    var product_id = parseInt(input.data("product_id"));

    value = Math.round(value / step) * step;
    input.val(value);
    console.log(product_id);
    if (product_id) {
        if (value >= 1 && cart.spinnerRecount) {
            cart.recount(value, product_id);
        }
    }
});
$(document).on('click', '.spinner button', function () {
    var input = $(this).parent().find('input');
    var step = (input.data("step")) ? parseInt(input.data("step")) : 1;
    var value = (parseInt(input.val())) ? parseInt(input.val()) : step;
    var product_id = parseInt(input.data("product_id"));
    var event = $(this).data("event");

    var min = parseInt(input.data("min"));
    var max = parseInt(input.data("max"));

    if (event === 'down') {
        value = value - step;
    } else {
        value = value + step;
    }
    if (value > max) {
        value = max;
    } else if (value <= min) {
        value = min;
    }
    if (product_id && value >= 1 && cart.spinnerRecount) {
        cart.recount(value, product_id);
    }
    input.val(value);
});
//Custom spinner end


/**
 * Add product to order
 * @param el
 * @param order_id
 * @returns {boolean}
 */
function addProductToOrder(el, order_id) {
    var product_id = $(el).attr('href');

    var quantity = $('#count_' + product_id).val();
    var price = $('#price_' + product_id).val();
    var csrfParam = yii.getCsrfParam();
    var csrfToken = yii.getCsrfToken();
    console.log(csrfToken,csrfParam);
    $.ajax({
        url: "/admin/cart/default/add-product",
        type: "POST",
        data: {
           // '"+csrfParam+"': csrfToken,
            order_id: order_id,
            product_id: product_id,
            quantity: quantity,
            price: price
        },
        dataType: "json",
        success: function (data) {
            if (data.success) {
                reloadOrderedProducts(order_id);
                common.notify(data.message, 'success');
            } else {
                common.notify(data.message, 'error');
            }
        },
        error: function (xhr, textStatus, errorThrown) {
            if (xhr.status !== 200) {
                common.notify(xhr.status, 'error');
            }
        }
    });

    return false;
}

/**
 * Delete ordered product
 * @param id
 * @param order_id
 */
function deleteOrderedProduct(id, order_id) {
    if (confirm(deleteQuestion)) {
        $.ajax({
            url: "/admin/cart/default/delete-product",
            type: "POST",
            data: {
                token: common.token,
                id: id,
                order_id: order_id
            },
            dataType: "html",
            success: function () {
                reloadOrderedProducts(order_id);
            }
        });
    }
}

/**
 * Update products list
 */
function reloadOrderedProducts(order_id) {
    $('#orderedProducts').load('/admin/cart/default/render-ordered-products?order_id=' + order_id);
}

/**
 * Recount total price on change delivery method
 * @param el
 */
function recountOrderTotalPrice(el) {
    var deliveryMethod = searchDeliveryMethodById($(el).val());

    if (!deliveryMethod) {
        return;
    }

    var total = parseFloat(orderTotalPrice);
    var delivery_price = parseFloat(deliveryMethod.price);
    var free_from = parseFloat(deliveryMethod.free_from);

    if (delivery_price > 0) {
        if (free_from > 0 && total > free_from) {
            $("#orderDeliveryPrice").html('0.00');
        } else {
            total = total + delivery_price;
            $("#orderDeliveryPrice").html(delivery_price.toFixed(2));
        }
    } else {
        $("#orderDeliveryPrice").html('0.00');
    }

    $('#orderSummary').html(total.toFixed(2));
}

/**
 * @param id
 */
function searchDeliveryMethodById(id) {
    var result = false;
    $(deliveryMethods).each(function () {
        if (parseInt(this.id) === parseInt(id)) {
            result = this;
        }
    });

    return result;
}

