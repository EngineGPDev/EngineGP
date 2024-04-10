/*
 * EngineGP   (https://enginegp.ru or https://enginegp.com)
 *
 * @link      https://github.com/EngineGPDev/EngineGP
 * @link      https://gitforge.ru/EngineGP/EngineGP
 * @copyright Copyright (c) Solovev Sergei <inbox@seansolovev.ru>
 * @license   https://github.com/EngineGPDev/EngineGP/blob/main/LICENSE
 * @license   https://gitforge.ru/EngineGP/EngineGP/src/branch/main/LICENSE
 */

function wiki_search(go) {
    if ($('#search').val() == '') {
        $('#search_result').css('display', 'none');

        return;
    }

    loading(1);

    $.ajax({
        type: 'POST',
        url: home + 'wiki/section/search',
        data: 'text=' + $('#search').val(),
        dataType: 'json',
        success: function (data) {
            $.each(data, function (i, val) {
                if (i == 's') {
                    if (val != '') {
                        $('#search_result').css('display', 'block');
                        $('#search_result').html(val);
                    } else
                        $('#search_result').css('display', 'none');
                }
            });

            loading(0);
        }
    });
}