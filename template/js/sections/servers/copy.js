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

$('#copy').ajaxForm({
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

function recfull(id) {
    loading(1);

    $.getJSON(home + 'servers/id/' + server + '/section/copy/subsection/recfull/cid/' + id + '/go', function (arr) {
        $.each(arr, function (i, val) {
            if (i == 'e')
                bootbox.dialog('<h3 class="red">Ошибка</h3>' + val,
                    [{
                        "label": "Продолжить",
                    }]
                );

            if (i == 's')
                location.href = home + 'servers/id/' + server;

            loading(0)
        });
    });
}

function fullcopy() {
    loading(1);

    $.getJSON(home + 'servers/id/' + server + '/section/copy/subsection/fullcopy/go', function (arr) {
        $.each(arr, function (i, val) {
            if (i == 'e')
                bootbox.dialog('<h3 class="red">Ошибка</h3>' + val,
                    [{
                        "label": "Продолжить",
                    }]
                );

            if (i == 's')
                location.reload();

            loading(0)
        });
    });
}

function recpart(id) {
    loading(1);

    $.getJSON(home + 'servers/id/' + server + '/section/copy/subsection/recpart/cid/' + id + '/go', function (arr) {
        $.each(arr, function (i, val) {
            if (i == 'e')
                bootbox.dialog('<h3 class="red">Ошибка</h3>' + val,
                    [{
                        "label": "Продолжить",
                    }]
                );

            if (i == 's')
                location.href = home + 'servers/id/' + server;

            loading(0)
        });
    });
}

function remcopy(id) {
    loading(1);

    $.getJSON(home + 'servers/id/' + server + '/section/copy/subsection/remove/cid/' + id + '/go', function (arr) {
        $.each(arr, function (i, val) {
            if (i == 'e')
                bootbox.dialog('<h3 class="red">Ошибка</h3>' + val,
                    [{
                        "label": "Продолжить",
                    }]
                );

            if (i == 's')
                location.reload();

            loading(0)
        });
    });
}