$(document).ready(function() {

    // Definition of variables and elements
    var $body = $('body');
    var $dashboard = $('body.dashboard');
    var $welcomeScreen = $('body.welcome');
    var $requestProgress = $dashboard.find('#requestProgress');

    // Language selector of admin interface
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

    // Changing the visibility/hiding of typed password
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

    /**
     * Change of current progress for progress bars
     *
     * @public
     * @param {String/Object} selector - Selector of Bootstrap.Progressbar.
     * @param {Integer} valuenow - Value will be changed.
     * @param {Boolean} append - Flag of append or set current value of progress.
     */
    function setProgress(selector, valuenow, append = false) {

        if (typeof selector === "object")
            var $progress = selector;
        else if (typeof selector === "string")
            var $progress = $(selector);

        var $progressBar = $progress.find('.progress-bar');
        var current = $progressBar.attr("aria-valuenow");

        if (append) {
            var steps = 10;
            var value = (valuenow / steps);
            var interval = setInterval(function() {
                current += value;
                $progressBar.css("width", current + "%").attr("aria-valuenow", current);

                if ($progressBar.find('span').length > 0)
                    $progressBar.find('span').text(current + "% Complete");

                if (current >= 100 || steps == 0)
                    clearInterval(interval);

                steps--;
            }, 100);

        } else {
            current = valuenow;
            $progressBar.css("width", current + "%").attr("aria-valuenow", current);

            if ($progressBar.find('span').length > 0)
                $progressBar.find('span').text(current + "% Complete");

        }
    }

    // Tracking page loading events with pAjax
    $(document).on({
        'pjax:start': function(e) {
            setProgress($requestProgress, 0);
            $requestProgress.show();
        },
        'pjax:beforeSend': function(e){
            setProgress($requestProgress, 25, true);
        },
        'pjax:send': function(e) {
            setProgress($requestProgress, 35, true);
        },
        'pjax:beforeReplace': function(e) {
            setProgress($requestProgress, 10, true);
        },
        'pjax:success': function(e) {
            setProgress($requestProgress, 100);
            setTimeout(function() {
                $requestProgress.hide();
            }, 1200);
        }
    });

});