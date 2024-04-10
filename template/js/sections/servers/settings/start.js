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