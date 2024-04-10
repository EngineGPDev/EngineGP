/*
 * EngineGP   (https://enginegp.ru or https://enginegp.com)
 *
 * @copyright Copyright (c) 2018-present Solovev Sergei <inbox@seansolovev.ru>
 *
 * @link      https://github.com/EngineGPDev/EngineGP for the canonical source repository
 * @link      https://gitforge.ru/EngineGP/EngineGP for the canonical source repository
 *
 * @license   https://github.com/EngineGPDev/EngineGP/blob/main/LICENSE MIT License
 * @license   https://gitforge.ru/EngineGP/EngineGP/src/branch/main/LICENSE MIT License
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