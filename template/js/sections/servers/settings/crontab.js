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

$('#form').ajaxForm({
    dataType: 'json',
    success: function (data) {
        $.each(data, function (i, val) {
            if (i == 'e') {
                bootbox.dialog('<h3 class="red">Ошибка</h3>' + val,
                    [{
                        "label": "OK",
                        "class": "btn-small btn-primary",
                    }]
                );
            }
            if (i == 's')
                location.reload();
        });
        loading(0)
    }
});

function crontab_delete(id) {
    loading(1);

    $.ajax({
        type: 'POST',
        url: home + 'servers/id/' + server + '/section/settings/subsection/crontab/action/delete/go',
        data: 'task=' + id,
        dataType: 'json',
        success: function (data) {
            $.each(data, function (i, val) {
                if (i == 'e')
                    bootbox.dialog('<h3 class="red">Ошибка</h3>' + val,
                        [{
                            "label": "OK",
                            "class": "btn-small btn-primary",
                        }]
                    );
                if (i == 's')
                    location.reload();
            });

            loading(0)
        }
    });
}

function cron_check_type() {
    if ($('#task').val() == 'console')
        $('#console').css('display', 'table-row');
    else
        $('#console').css('display', 'none');
}