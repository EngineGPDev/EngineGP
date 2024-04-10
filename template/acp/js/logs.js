/*
 * EngineGP   (https://enginegp.ru or https://enginegp.com)
 *
 * @link      https://github.com/EngineGPDev/EngineGP
 * @link      https://gitforge.ru/EngineGP/EngineGP
 * @copyright Copyright (c) Solovev Sergei <inbox@seansolovev.ru>
 * @license   https://github.com/EngineGPDev/EngineGP/blob/main/LICENSE
 * @license   https://gitforge.ru/EngineGP/EngineGP/src/branch/main/LICENSE
 */

function logs_sys_search(go) {
    if ($('#search').val() == '') {
        $('#search_error').css('display', 'none');

        return;
    }

    loading(1);

    if (go) go = '/go'; else go = '';

    $.ajax({
        type: 'POST',
        url: home + 'logs/subsection/search' + go,
        data: 'text=' + $('#search').val(),
        dataType: 'json',
        success: function (data) {
            $.each(data, function (i, val) {
                if (i == 'e') {
                    if (val != '') {
                        $('#search_error').css('display', 'inline-block');
                        $('#search_error').html(val);
                    } else
                        $('#search_error').css('display', 'none');
                }

                if (i == 's') {
                    $('#search_error').css('display', 'none');
                    $('#search_result').html(val);
                }
            });

            loading(0);
        }
    });
}

function logs_search(go) {
    if ($('#search').val() == '') {
        $('#search_error').css('display', 'none');

        return;
    }

    loading(1);

    if (go) go = '/go'; else go = '';

    $.ajax({
        type: 'POST',
        url: home + 'logs/section/search' + go,
        data: 'text=' + $('#search').val(),
        dataType: 'json',
        success: function (data) {
            $.each(data, function (i, val) {
                if (i == 'e') {
                    if (val != '') {
                        $('#search_error').css('display', 'inline-block');
                        $('#search_error').html(val);
                    } else
                        $('#search_error').css('display', 'none');
                }

                if (i == 's') {
                    $('#search_error').css('display', 'none');
                    $('#search_result').html(val);
                }
            });

            loading(0);
        }
    });
}