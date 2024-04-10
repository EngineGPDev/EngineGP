/*
 * EngineGP   (https://enginegp.ru or https://enginegp.com)
 *
 * @link      https://github.com/EngineGPDev/EngineGP
 * @link      https://gitforge.ru/EngineGP/EngineGP
 * @copyright Copyright (c) Solovev Sergei <inbox@seansolovev.ru>
 * @license   https://github.com/EngineGPDev/EngineGP/blob/main/LICENSE
 * @license   https://gitforge.ru/EngineGP/EngineGP/src/branch/main/LICENSE
 */

function settings_save(type) {
    loading(1);
    $.getJSON(home + 'servers/id/' + server + '/section/settings/subsection/start/save/' + type + '/value/' + $('#' + type).val() + '/go/',
        function (data) {
            $.each(data, function (i, val) {
                if (i == 'e')
                    bootbox.dialog('<h3 class="red">Ошибка</h3>' + val,
                        [{
                            "label": "OK",
                            "class": "btn-small btn-primary",
                        }]
                    );
                if (i == 's')
                    bootbox.dialog('<h3 class="green">Внимание</h3> Внесенные изменения сохранены.',
                        [{
                            "label": "OK",
                            "class": "btn-small btn-primary",
                        }]
                    );

                loading(0)
            });
        });
}

function maplist() {
    $.getJSON(home + 'servers/id/' + server + '/section/settings/subsection/start/maps', function (data) {
        $.each(data, function (i, val) {
            if (i == 'maps')
                $('#map').html(val);
        });
    });
}