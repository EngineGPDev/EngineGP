var lastScrollPosition = 0;
var scrollUpClicked = false;

$(document).ready(function() {
    $('#scroll-up').click(function() {
        lastScrollPosition = $(document).scrollTop();
        $('html, body').animate({ scrollTop: 0 }, 1000);
        scrollUpClicked = true;
    });

    $('#scroll-down').click(function() {
        if (scrollUpClicked) {
            $('html, body').animate({ scrollTop: lastScrollPosition }, 1000);
        } else {
            $('html, body').animate({ scrollTop: $(document).height() }, 1000);
        }
    });

    $(document).scroll(function() {
        var scrollTop = $(document).scrollTop();

        if (scrollTop > 0) {
            $('#scroll-up').fadeIn();
        } else {
            $('#scroll-up').fadeOut();
        }

        if (scrollTop < $(document).height() - $(window).height()) {
            $('#scroll-down').fadeIn();
        } else {
            $('#scroll-down').fadeOut();
        }
    });

    // Убедитесь, что кнопка "Наверх" скрыта при загрузке страницы
    $('#scroll-up').hide();

    // Проверяем начальное положение страницы при загрузке для кнопки "Вниз"
    if ($(document).scrollTop() < $(document).height() - $(window).height()) {
        $('#scroll-down').show();
    } else {
        $('#scroll-down').hide();
    }
});
