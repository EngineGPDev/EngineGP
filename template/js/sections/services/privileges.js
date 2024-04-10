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

$('#privileges').ajaxForm({
    dataType: 'json',
    success: function (data) {
        $.each(data, function (i, val) {
            if (i == 'e')
                bootbox.dialog('<h3 class="red">Ошибка</h3>' + val,
                    [{
                        "label": "Продолжить",
                    }]
                );

            if (i == 's') {
                $('#form_pay').css('display', 'block');
                $('#pay').html(val);
            }
        });

        loading(0);
    }
});

function find_server() {
    loading(1);

    $.ajax({
        type: 'POST',
        url: home + 'services/section/privileges/select/server',
        data: 'address=' + $('#server').val(),
        dataType: 'json',
        success: function (data) {
            $.each(data, function (i, val) {
                if (i == 'e')
                    bootbox.dialog('<h3 class="red">Ошибка</h3>' + val,
                        [{
                            "label": "Продолжить",
                        }]
                    );

                if (i == 's') {
                    $('#form_privileges').css('display', 'block');
                    $('#privileges').html(val);
                }
            });

            loading(0);
        }
    });
}

function change_time() {
    $.get(home + 'services/section/privileges/select/time/service/' + $('#service').val(), function (data) {
        $('#time').html(data)
    });
}

function change_data() {
    switch ($('#type').val()) {
        case 'a':
            $('#data').attr('placeholder', 'Введите ник');
            $('#form_passwd').css('display', 'table-row');
            break;

        case 'ca':
            $('#data').attr('placeholder', 'Введите SteamID');
            $('#form_passwd').css('display', 'table-row');
            break;

        default:
            $('#data').attr('placeholder', 'Введите IP');
            $('#form_passwd').css('display', 'none');
    }
}