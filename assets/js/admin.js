$(function () {

    var $body = $('body');
    var $welcomeScreen = $('html').find('.welcome');
    if ($welcomeScreen.find('#languageSelector')) {
        var $languageSelector = $welcomeScreen.find('#languageSelector');
        if ($languageSelector.find('.dropdown-menu > li.active').length > 0) {
            var label = $languageSelector.find('.dropdown-menu > li.active > a').first().text();
            $languageSelector.find('.dropdown-toggle').html(label + ' <span class="caret"></span>');
        }
        $body.delegate('#languageSelector .dropdown-menu > li > a', 'click', function () {
            var label = $(this).text();
            $languageSelector.find('.dropdown-toggle').html(label + ' <span class="caret"></span>');
        });
    }

    $body.delegate('#showInputPassword', 'click', function () {
        var $passwordInput = $(this).prev('input[type]');
        if ($passwordInput.attr('type') == "password") {
            $passwordInput.attr('type', "text");
            $(this).find('span.fa').toggleClass('fa-eye fa-eye-slash');
        } else {
            $passwordInput.attr('type', "password");
            $(this).find('span.fa').toggleClass('fa-eye-slash fa-eye');
        }
    });

});