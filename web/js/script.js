// JS файл с AJAX запросом к скрипту (с интервалом 10сек)
window.onload = function() {
    rateRequest();
};

function rateRequest() {
    $.ajax({
        'url': "../rate/index",
        cache : 'false',
        dataType : 'json'
    }).done(function (data){
        $('#my_currency_block').html(data.rate);
        setTimeout(rateRequest, 10000);
    });
}

