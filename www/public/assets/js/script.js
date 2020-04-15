$(document).on('change', '.choice-type', function () {
    var val = $(this).val();
    var showed = $('[data-type="'+val+'"]');
    $('.change-type').hide();
    $('.change-type input').val('').removeAttr('required');
    showed.attr('required','required');
    showed.closest('.change-type').show();
});
$(document).on('submit', '#order_content form',function (e) {
    e.preventDefault();
    var form = $(this);
    var data = form.serializeArray();
    $.ajax({
        type: form.attr('method'),
        url: form.attr('action'),
        data: data
    }).done(function (data) {
        $('#order_content').html(data.content);
    });
});
$(document).on('submit', '[action="/cart/add"]',function (e) {
    e.preventDefault();
    var form = $(this);
    var data = form.serializeArray();
    $.ajax({
        type: form.attr('method'),
        url: form.attr('action'),
        data: data
    }).done(function (data) {
        $('.side-nav__summ span').html(data.summ);
    });
});
$(document).on('change', '[name="quantity"]',function (e) {
    e.preventDefault();
    var product = $(this).parent().find('[name="product"]').val();
    var quantity = $(this).val();
    var data = {product:product, quantity:quantity};
    $.ajax({
        type: 'POST',
        url: '/cart/add',
        data: data
    }).done(function (data) {
        $('.side-nav__summ span').html(data.summ);
    });
});
$(document).on('click', '[name="pay"]',function (e) {
    e.preventDefault();
    var $this = $(this);
    var parent = $this.closest('tr');
    var id = $this.data('id');
    var data = {id:id};
    $.ajax({
        type: 'POST',
        url: '/personal/pay',
        data: data
    }).done(function (data) {
        parent.find('.paidText').html(data.order.paidText);
        $this.remove();
        $('#curBudgetAsside span').html(data.account.finance);
        $('#curOverdraft span').html(data.account.overdraft);
        console.log(data);
    });
});