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

function system_load(go) {
    if (go)
        loading(1);

    $.getJSON(home + 'system/go/',
        function (data) {
            $.each(data, function (i, val) {
                $('#' + i).html(val);
            });

            loading(0);

            if (!go)
                setTimeout(function () {
                    system_load(false)
                }, 3000);
        });
}

function system_restart(service) {
    switch (service) {
        case 'apache2':
            type = 'apache2';
            break;
        case 'nginx':
            type = 'nginx';
            break;
        case 'mysql':
            type = 'mysql';
            break;
        case 'unit':
            type = 'локацию';
            break;
    }

    bootbox.dialog('<h3 class="green">Внимание</h3>Вы уверены что хотите перезагруить <u>' + type + '</u>',
        [{
            "label": "Перезагрузить",
            callback: function () {
                system_restart_go(service)
            }
        }, {
            "label": "Отмена"
        }]);

    return false;
}

function system_restart_go(id, service) {
    loading(1);

    $.getJSON(home + 'system/service/' + service,
        function (data) {
            $.each(data, function (i, val) {
                if (i == 'e')
                    bootbox.dialog('<h3 class="red">Ошибка</h3>' + val,
                        [{
                            "label": "Продолжить"
                        }]
                    );

                if (i == 's')
                    system_load(true);
            });

            loading(0);
        });
}