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