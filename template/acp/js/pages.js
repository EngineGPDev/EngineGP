/*
 * EngineGP   (https://enginegp.ru or https://enginegp.com)
 *
 * @link      https://github.com/EngineGPDev/EngineGP
 * @link      https://gitforge.ru/EngineGP/EngineGP
 * @copyright Copyright (c) Solovev Sergei <inbox@seansolovev.ru>
 * @license   https://github.com/EngineGPDev/EngineGP/blob/main/LICENSE
 * @license   https://gitforge.ru/EngineGP/EngineGP/src/branch/main/LICENSE
 */

$('#page').ajaxForm({
    dataType: 'json',
    success: function (data) {
        $.each(data, function (i, val) {
            if (i == 'e')
                bootbox.dialog('<h3 class="red">Ошибка</h3>' + val,
                    [{
                        "label": "Продолжить"
                    }]
                );

            if (i == 's')
                location.reload();
        });

        loading(0)
    }
});

function page_delete(id) {
    bootbox.dialog('<h3 class="green">Внимание</h3> Вы уверены, что хотите удалить страницу?',
        [{
            "label": "Удалить",
            callback: function () {
                page_delete_go(id)
            }
        }, {
            "label": "Отмена",
        }]
    );

    return false;
}

function page_delete_go(id) {
    loading(1);

    $.ajax({
        type: 'POST',
        url: home + 'pages/section/delete/id/' + id,
        dataType: 'json',
        success: function (data) {
            $.each(data, function (i, val) {
                if (i == 'e')
                    bootbox.dialog('<h3 class="red">Ошибка</h3>' + val,
                        [{
                            "label": "Продолжить"
                        }]
                    );

                if (i == 's')
                    location.reload()
            });

            loading(0);
        }
    });
}