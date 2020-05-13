window.$ = window.jQuery = require('jquery');
require('bootstrap');
import '../scss/app.scss';

$(document).ready(function(){
    $('.load-more-button').on('click', function(e){
        e.preventDefault();

        $(this).siblings('.text-danger').remove();
        var clickedButton = $(this);
        var targetTable = $(this).siblings('table').eq(0);
        var targetUrl   = targetTable.data('url') + '?offset=' + targetTable.find('tr').length;

        $.ajax({
            url: targetUrl,
            type: 'GET',
            dataType: 'JSON',
            success: function(data){
                if (data.success) {
                    if (data.html === '') {
                        clickedButton.fadeOut(500, function(){
                            $(this).removeClass('d-block');
                        });
                    } else {
                        targetTable.find('tbody').eq(0).append(data.html)
                    }
                } else {
                    clickedButton.prepend('<p class="text-danger">Something went wrong.</p>')
                }

            }
        });
    });
});
