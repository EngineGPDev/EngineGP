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

$('#signup').ajaxForm({
    dataType: 'json',
    success: function (data) {
        $.each(data, function (i, val) {
            if (i == 'e') {
                bootbox.dialog('<h3 class="red">Ошибка</h3>' + val,
                    [{
                        "label": "Продолжить",
                    }]
                );

                document.getElementById("captcha_img").src = home + 'user/section/signup/captcha?' + Math.random();

                $('#captcha').val('');
            }
            if (i == 's')
                bootbox.dialog('<h3 class="green">Внимание</h3>' + val,
                    [{
                        "label": "Продолжить",
                        callback: function () {
                            location.href = "http://" + data['mail'];
                        }
                    }]
                );
        });
        loading(0);
    }
});


$('#captcha')[0].onkeyup = function () {
    this.value = this.value.toUpperCase();
};