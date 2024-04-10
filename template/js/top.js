/*
 * EngineGP   (https://enginegp.ru or https://enginegp.com)
 *
 * @link      https://github.com/EngineGPDev/EngineGP
 * @link      https://gitforge.ru/EngineGP/EngineGP
 * @copyright Copyright (c) Solovev Sergei <inbox@seansolovev.ru>
 * @license   https://github.com/EngineGPDev/EngineGP/blob/main/LICENSE
 * @license   https://gitforge.ru/EngineGP/EngineGP/src/branch/main/LICENSE
 */

$(document).ready(function () {
    // появление/затухание кнопки #back-top
    $(function () {
        // прячем кнопку #back-top
        $("#back-top").hide();

        $(window).scroll(function () {
            if ($(this).scrollTop() > 100) {
                $("#back-top").fadeIn();
            } else {
                $("#back-top").fadeOut();
            }
        });

        // при клике на ссылку плавно поднимаемся вверх
        $("#back-top a").click(function () {
            $("body,html").animate({
                scrollTop: 0
            }, 800);
            return false;
        });
    });
});