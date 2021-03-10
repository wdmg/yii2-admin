$(document).ready(function() {

    // Configuration
    const config = {
        mainnav: {
            expandOnHover: false
        },
        sidebar: {
            expandOnHover: true
        },
        ajaxFade: true,
        ajaxProgress: true,
        spinner: false,
        debug: false
    };

    // Definition of variables and elements
    var $body = $('body');
    var $dashboard = $('body.dashboard');
    var $welcomeScreen = $('body.welcome');
    var $requestProgress = $dashboard.find('#requestProgress');
    var $sidebar = $dashboard.find('.sidebar');
    var $mainNav = $dashboard.find('#mainNav');
    var $sidebarNav = $sidebar.find('#sidebarNav');
    var $spinner = $('<svg class="spinner" viewBox="0 0 50 50"><circle class="path" cx="25" cy="25" r="20" fill="none" stroke-width="5"></circle></svg>');
    var viewport = $(window).viewport();
    var breakpoints = {
        xs: 480,
        sm: 768,
        md: 992,
        lg: 1200
    };

    // Language selector of admin interface
    if ($welcomeScreen.find('#languageSelector').length > 0 || $dashboard.find('#languageSelector').length > 0) {
        var $languageSelector = $('#languageSelector');
        if ($languageSelector.find('.dropdown-menu > li.active').length > 0) {
            var label = $languageSelector.find('.dropdown-menu > li.active > a').data('label');
            $languageSelector.find('.dropdown-toggle').html(label + ' <span class="caret"></span>');
        }
        $body.delegate('#languageSelector .dropdown-menu > li > a', 'click', function() {
            var label = $(this).data('label');
            $languageSelector.find('.dropdown-toggle').html(label + ' <span class="caret"></span>');
        });

        if (config.debug)
            console.log('Dashboard: click by `#languageSelector`');

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
        'pjax:start': function (event) {

            if (config.ajaxProgress) {
                setProgress($requestProgress, 0);
                $requestProgress.show();
            }

            if (config.debug)
                console.log('Dashboard: pjax change state to `start`');

        },
        'pjax:beforeSend': function (event) {

            if (config.ajaxProgress)
                setProgress($requestProgress, 15);

            if (config.debug)
                console.log('Dashboard: pjax change state to `beforeSend`');

        },
        'pjax:send': function (event) {

            if (config.ajaxFade)
                $(this).attr('data-pjax-state', "send");

            if (config.spinner)
                $(this).append($spinner);

            if (config.ajaxProgress)
                setProgress($requestProgress, 35);

            if (config.debug)
                console.log('Dashboard: pjax change state to `send`');

        },
        'pjax:beforeReplace': function (event) {

            if (config.ajaxProgress)
                setProgress($requestProgress, 75);

            if (config.debug)
                console.log('Dashboard: pjax change state to `beforeReplace`');

        },
        'pjax:complete': function (event) {

            if (config.ajaxFade)
                $(this).attr('data-pjax-state', "complete");

            if (config.ajaxProgress) {
                setProgress($requestProgress, 100);
                setTimeout(function () {
                    $requestProgress.hide();
                }, 1200);
            }

            if ($welcomeScreen.find('#languageSelector').length > 0 || $dashboard.find('#languageSelector').length > 0) {
                var $languageSelector = $('#languageSelector');
                if ($languageSelector.find('.dropdown-menu > li.active').length > 0) {
                    var label = $languageSelector.find('.dropdown-menu > li.active > a').data('label');
                    $languageSelector.find('.dropdown-toggle').html(label + ' <span class="caret"></span>');
                }
            }

            if (config.debug)
                console.log('Dashboard: pjax change state to `complete`');

        }
    });


    // Show/hide dropdown in mainnav on hover
    if (config.mainnav.expandOnHover) {
        $mainNav.find(".dropdown").each(function () {
            var $this = $(this);
            $this.click(function () {
                if (!$(this).find('.dropdown-menu').is(':visible')) {
                    $(this).find('.dropdown-menu').stop(true, true).slideToggle("fast");

                    if (config.debug)
                        console.log('Dashboard: dropdown in mainnav is visible by click');

                } else {
                    $(this).find('.dropdown-menu').stop(true, true).slideUp("fast");

                    if (config.debug)
                        console.log('Dashboard: dropdown in mainnav is hidding by click');
                }
            });
            $this.hover(function () {
                var $dropdown = $(this);
                if (!$dropdown.find('.dropdown-menu').is(':visible')) {
                    $dropdown.find('.dropdown-menu').stop(true, true).delay(300).slideToggle("fast");
                }

                if (config.debug)
                    console.log('Dashboard: dropdown in mainnav is visible by hover');

            }, function () {
                var $dropdown = $(this);
                if ($dropdown.find('.dropdown-menu').is(':visible')) {
                    $dropdown.find('.dropdown-menu').stop(true, true).delay(100).slideUp("fast");

                    if (config.debug)
                        console.log('Dashboard: dropdown in mainnav is hidding by hover');

                }
            });
        });
    }

    // Admin sidebar menu management
    if ($sidebarNav.length > 0) {

        // Disable click on dropdown element with empty link
        $sidebarNav.find('.dropdown-menu > li > a[href="#"]').on('click', function (event) {
            event.preventDefault();
            event.stopPropagation();

            if (config.debug)
                console.log('Dashboard: click by `.dropdown-menu > li > a[href="#"]` in sidebar');

        });

        // Disable click on popover element
        $sidebarNav.find('.dropdown-submenu > a').on('click', function (event) {
            event.preventDefault();
            event.stopPropagation();

            if (config.debug)
                console.log('Dashboard: click by `.dropdown-submenu > a` in sidebar');

        });

        // Show/hide dropdown in sidebar on hover
        if (config.sidebar.expandOnHover) {
            $sidebarNav.find(".dropdown").each(function () {
                var $this = $(this);
                $this.click(function () {
                    if (!$(this).find('.dropdown-menu').is(':visible')) {
                        $(this).find('.dropdown-menu').stop(true, true).slideToggle("fast");
                        $(this).find('.dropdown-toggle .fa-angle-down').removeClass('fa-angle-down').addClass('fa-angle-up');

                        if (config.debug)
                            console.log('Dashboard: dropdown in sidebar is visible by click');

                    } else {
                        $(this).find('.dropdown-menu').stop(true, true).slideUp("fast");
                        $(this).find('.dropdown-toggle .fa-angle-up').removeClass('fa-angle-up').addClass('fa-angle-down');

                        if (config.debug)
                            console.log('Dashboard: dropdown in sidebar is hidding by click');
                    }
                    $sidebarNav.find(".dropdown:not(.active):not(:hover)").find('.dropdown-menu').slideUp("fast");
                });
                $this.hover(function () {

                    var $dropdown = $(this);
                    if (!$dropdown.find('.dropdown-menu').is(':visible')) {
                        $dropdown.find('.dropdown-menu').stop(true, true).delay(500).slideToggle("fast");
                        setTimeout(function() {
                            $dropdown.find('.dropdown-toggle .fa-angle-down').removeClass('fa-angle-down').addClass('fa-angle-up');
                        }, 200);
                    }
                    $sidebarNav.find(".dropdown:not(.active):not(:hover)").find('.dropdown-menu').slideUp("fast");

                    if (config.debug)
                        console.log('Dashboard: dropdown in sidebar is visible by hover');

                }, function () {

                    var $dropdown = $(this);

                    if (!$dropdown.hasClass('popover-show')) {

                        $dropdown.find('.dropdown-menu').stop(true, true).delay(200).slideUp("fast");
                        setTimeout(function() {
                            $dropdown.find('.dropdown-toggle .fa-angle-up').removeClass('fa-angle-up').addClass('fa-angle-down');
                        }, 200);

                        if (config.debug)
                            console.log('Dashboard: dropdown in sidebar is hidding by hover');

                    }
                    // Fixed: Dropdown menu hidding by popover is show
                    //$sidebarNav.find(".dropdown:not(.active):not(:hover)").find('.dropdown-menu').slideUp("fast");
                });
            });
            $sidebarNav.find(".dropdown.active").find('.dropdown-toggle .fa-angle-down').toggleClass('fa-angle-down fa-angle-up');
            $sidebarNav.find(".dropdown.active .dropdown-toggle").click();
        } else {
            $sidebarNav.find(".dropdown").on('shown.bs.dropdown', function(event) {
                $(event.target).find('.dropdown-toggle .fa-angle-down').toggleClass('fa-angle-down fa-angle-up');
            }).on('hidden.bs.dropdown', function(event) {
                $(event.target).find('.dropdown-toggle .fa-angle-up').toggleClass('fa-angle-up fa-angle-down');
            });
            //$sidebarNav.find(".dropdown.active").dropdown('toggle');
            $sidebarNav.find(".dropdown.active .dropdown-toggle").click();
        }

        // Init popover menu in sidebar
        $sidebarNav.find('.dropdown-submenu > a').each(function() {
            var $this = $(this);
            var $dropdown = $(this).parents('.dropdown');

            var trigger = 'click';
            if (config.sidebar.expandOnHover)
                trigger = 'manual';

            $this.popover({
                placement: 'auto right',
                trigger: trigger,
                container: 'body',
                title: false,
                html: true,
                template: '<div class="popover nav-popover" role="tooltip"><div class="arrow"></div><div class="popover-content"></div></div>',
                content: function() {
                    return $this.parent('.dropdown-submenu').find('ul').addClass('nav').outerHtml();
                }
            });

            if (config.sidebar.expandOnHover) {
                $this.on("mouseenter", function () {
                    var _this = this;
                    $(this).popover("show");
                    $dropdown.addClass('popover-show');

                    $(".nav-popover").on("mouseleave", function () {
                        $(_this).popover('hide');
                        $dropdown.removeClass('popover-show');

                        if (config.debug)
                            console.log('Dashboard: sidebar popover is hidding by mouseleave');

                    }).on("mousedown", function () {
                        $(_this).popover('hide');
                        $dropdown.removeClass('popover-show');

                        if (config.debug)
                            console.log('Dashboard: sidebar popover is hidding by mousedown');
                    });

                    if (config.debug)
                        console.log('Dashboard: sidebar popover is visible by mouseenter');

                }).on("mouseleave", function () {
                    var _this = this;
                    setTimeout(function () {
                        if (!$(".nav-popover:hover").length) {
                            $(_this).popover('hide');
                            $dropdown.removeClass('popover-show');

                            if (config.debug)
                                console.log('Dashboard: sidebar popover is hidding by mouseleave and timeout');
                        }
                    }, 200);
                });
            }

        });

        // Add sidebar nav to main navbar for sm and xs displays
        if (viewport.width <= breakpoints.sm) {
            var $sidebar = $sidebarNav.clone();
            $sidebar.attr('class', 'nav navbar-nav hidden-md hidden-lg');
            $sidebar.find('li').each(function() {
                $(this).find('.fa-stack').removeClass('fa-stack').removeClass('fa-lg');
                $(this).find('.fa').removeClass('fa-stack-1x');
            });
            $items = $sidebar.outerHtml();
            $mainNav.before($items);

            if (config.debug)
                console.log('Dashboard: added sidebar nav to main navbar for sm and xs displays');
        }
    }

    // Dropdown`s
    $('body').delegate('.dropdown-toggle, [data-toggle="dropdown"]', 'click', function (event) {
        if (($(document).width() > breakpoints.sm) && $(this).is("a")) {
            event.preventDefault();
            var url = $(this).attr('href');
            if (url !== '#')
                window.location.href = url;
        }

        if (config.debug)
            console.log('Dashboard: click on .dropdown-toggle');

    });
    $('body').delegate($('.dropdown-toggle, [data-toggle="dropdown"]').parent(), 'show.bs.dropdown', function (event) {
        var $button = $(event.relatedTarget);
        var $dropdown = $(event.target).find('.dropdown-menu');
        var viewporHeight = $(document).height();
        var buttonOffset = $button.offset().top + $button.height();
        var dropdownHeight = $dropdown.height();
        var dropdownOffset = buttonOffset + dropdownHeight;

        if (dropdownOffset > (viewporHeight - 45) && (buttonOffset - 55) > dropdownHeight) {
            $(event.target).addClass('dropup');
        } else {
            $(event.target).removeClass('dropup');
        }
    });

    // Hot keys for pagination
    $(window).keydown(function(event) {

        var $pagination = $dashboard.find('.pagination');
        let ctrlKey = (getOS() == "Windows") ? event.ctrlKey : (getOS() == "Mac OS") ? event.altKey : null;
        let keyCode = event.keyCode ? event.keyCode : event.which ? event.which : null;

        if (ctrlKey && keyCode && $pagination.length > 0) {
            event.preventDefault();

            let link = null;
            switch (keyCode) {
                case 37:
                    link = $pagination.find('li > a[rel="prev"], li.prev > a').attr('href');
                    break;
                case 39:
                    link = $pagination.find('li > a[rel="next"], li.next > a').attr('href');
                    break;
            }

            if (link) {

                let $pjax = $pagination.closest('[data-pjax-container]');
                if ($pjax.length > 0) {

                    let timeout = 5000;
                    if ($pjax.data("pjax-timeout"))
                        timeout = parseInt($pjax.data("pjax-timeout"));

                    $.pjax.reload({
                        container: ($pjax.attr('id')) ? '#' + $pjax.attr('id') : null,
                        timeout: timeout,
                        url: link
                    });

                } else {
                    document.location = link;
                }
            }
        }
    });

    // Modals and buttons loading state
    $('body').delegate('a, button', 'click', function(event) {
        if ($(this).data('toggle') == "modal") {
            $('body').addClass('loading');
        } else if ($(this).data('loading-text')) {

            var hasErrors = false;

            var $form = $(event.target).parents('form:first');
            $form.find('input[aria-required]').each(function () {
                if ($(this).val().length == 0) {
                    hasErrors = true;
                }
            });

            $form.find('[aria-invalid]').each(function () {
                if ($(this).attr('aria-invalid') == "true") {
                    hasErrors = true;
                }
            });

            if (!hasErrors) {
                $(this).addClass('loading');
                $(this).button('loading');
            } else {
                $(this).removeClass('loading');
                $(this).button('reset');
            }
        }
    });
    $('body').delegate('.modal', 'show.bs.modal', function() {
        $('body').addClass('loading');
    });
    $('body').delegate('.modal', 'shown.bs.modal', function() {
        $('body').removeClass('loading');
    });
    $('body').delegate('.modal', 'hide.bs.modal', function() {
        $('body').removeClass('loading');
    });
    $('body').delegate('.modal', 'loaded.bs.modal', function() {
        $('body').removeClass('loading');
    });

});