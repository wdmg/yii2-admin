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
  $body.delegate('#showInputPassword', 'click', function() {
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
    'pjax:start': function(event) {

      if (config.ajaxProgress) {
        setProgress($requestProgress, 0);
        $requestProgress.show();
      }

      if (config.debug)
        console.log('Dashboard: pjax change state to `start`');

    },
    'pjax:beforeSend': function(event) {

      if (config.ajaxProgress)
        setProgress($requestProgress, 15);

      if (config.debug)
        console.log('Dashboard: pjax change state to `beforeSend`');

    },
    'pjax:send': function(event) {

      if (config.ajaxFade)
        $(this).attr('data-pjax-state', "send");

      if (config.spinner)
        $(this).append($spinner);

      if (config.ajaxProgress)
        setProgress($requestProgress, 35);

      if (config.debug)
        console.log('Dashboard: pjax change state to `send`');

    },
    'pjax:beforeReplace': function(event) {

      if (config.ajaxProgress)
        setProgress($requestProgress, 75);

      if (config.debug)
        console.log('Dashboard: pjax change state to `beforeReplace`');

    },
    'pjax:complete': function(event) {

      if (config.ajaxFade)
        $(this).attr('data-pjax-state', "complete");

      if (config.ajaxProgress) {
        setProgress($requestProgress, 100);
        setTimeout(function() {
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
    $mainNav.find(".dropdown").each(function() {
      var $this = $(this);
      $this.click(function() {
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
      $this.hover(function() {
        var $dropdown = $(this);
        if (!$dropdown.find('.dropdown-menu').is(':visible')) {
          $dropdown.find('.dropdown-menu').stop(true, true).delay(300).slideToggle("fast");
        }

        if (config.debug)
          console.log('Dashboard: dropdown in mainnav is visible by hover');

      }, function() {
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
    $sidebarNav.find('.dropdown-menu > li > a[href="#"]').on('click', function(event) {
      event.preventDefault();
      event.stopPropagation();

      if (config.debug)
        console.log('Dashboard: click by `.dropdown-menu > li > a[href="#"]` in sidebar');

    });

    // Disable click on popover element
    $sidebarNav.find('.dropdown-submenu > a').on('click', function(event) {
      event.preventDefault();
      event.stopPropagation();

      if (config.debug)
        console.log('Dashboard: click by `.dropdown-submenu > a` in sidebar');

    });

    // Show/hide dropdown in sidebar on hover
    if (config.sidebar.expandOnHover) {
      $sidebarNav.find(".dropdown").each(function() {
        var $this = $(this);
        $this.click(function() {
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
        /*$this.hover(function () {

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
        });*/
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
        $this.on("mouseenter", function() {
          var _this = this;
          $(this).popover("show");
          $dropdown.addClass('popover-show');

          $(".nav-popover").on("mouseleave", function() {
            $(_this).popover('hide');
            $dropdown.removeClass('popover-show');

            if (config.debug)
              console.log('Dashboard: sidebar popover is hidding by mouseleave');

          }).on("mousedown", function() {
            $(_this).popover('hide');
            $dropdown.removeClass('popover-show');

            if (config.debug)
              console.log('Dashboard: sidebar popover is hidding by mousedown');
          });

          if (config.debug)
            console.log('Dashboard: sidebar popover is visible by mouseenter');

        }).on("mouseleave", function() {
          var _this = this;
          setTimeout(function() {
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
  $('body').delegate('.dropdown-toggle, [data-toggle="dropdown"]', 'click', function(event) {
    if (($(document).width() > breakpoints.sm) && $(this).is("a")) {
      event.preventDefault();
      var url = $(this).attr('href');
      if (url !== '#')
        window.location.href = url;
    }

    if (config.debug)
      console.log('Dashboard: click on .dropdown-toggle');

  });
  $('body').delegate($('.dropdown-toggle, [data-toggle="dropdown"]').parent(), 'show.bs.dropdown', function(event) {
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
      $form.find('input[aria-required]').each(function() {
        if ($(this).val().length == 0) {
          hasErrors = true;
        }
      });

      $form.find('[aria-invalid]').each(function() {
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
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImFkbWluLmpzIl0sIm5hbWVzIjpbXSwibWFwcGluZ3MiOiJBQUFBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBIiwiZmlsZSI6ImFkbWluLmpzIiwic291cmNlc0NvbnRlbnQiOlsiJChkb2N1bWVudCkucmVhZHkoZnVuY3Rpb24oKSB7XG5cbiAgICAvLyBDb25maWd1cmF0aW9uXG4gICAgY29uc3QgY29uZmlnID0ge1xuICAgICAgICBtYWlubmF2OiB7XG4gICAgICAgICAgICBleHBhbmRPbkhvdmVyOiBmYWxzZVxuICAgICAgICB9LFxuICAgICAgICBzaWRlYmFyOiB7XG4gICAgICAgICAgICBleHBhbmRPbkhvdmVyOiB0cnVlXG4gICAgICAgIH0sXG4gICAgICAgIGFqYXhGYWRlOiB0cnVlLFxuICAgICAgICBhamF4UHJvZ3Jlc3M6IHRydWUsXG4gICAgICAgIHNwaW5uZXI6IGZhbHNlLFxuICAgICAgICBkZWJ1ZzogZmFsc2VcbiAgICB9O1xuXG4gICAgLy8gRGVmaW5pdGlvbiBvZiB2YXJpYWJsZXMgYW5kIGVsZW1lbnRzXG4gICAgdmFyICRib2R5ID0gJCgnYm9keScpO1xuICAgIHZhciAkZGFzaGJvYXJkID0gJCgnYm9keS5kYXNoYm9hcmQnKTtcbiAgICB2YXIgJHdlbGNvbWVTY3JlZW4gPSAkKCdib2R5LndlbGNvbWUnKTtcbiAgICB2YXIgJHJlcXVlc3RQcm9ncmVzcyA9ICRkYXNoYm9hcmQuZmluZCgnI3JlcXVlc3RQcm9ncmVzcycpO1xuICAgIHZhciAkc2lkZWJhciA9ICRkYXNoYm9hcmQuZmluZCgnLnNpZGViYXInKTtcbiAgICB2YXIgJG1haW5OYXYgPSAkZGFzaGJvYXJkLmZpbmQoJyNtYWluTmF2Jyk7XG4gICAgdmFyICRzaWRlYmFyTmF2ID0gJHNpZGViYXIuZmluZCgnI3NpZGViYXJOYXYnKTtcbiAgICB2YXIgJHNwaW5uZXIgPSAkKCc8c3ZnIGNsYXNzPVwic3Bpbm5lclwiIHZpZXdCb3g9XCIwIDAgNTAgNTBcIj48Y2lyY2xlIGNsYXNzPVwicGF0aFwiIGN4PVwiMjVcIiBjeT1cIjI1XCIgcj1cIjIwXCIgZmlsbD1cIm5vbmVcIiBzdHJva2Utd2lkdGg9XCI1XCI+PC9jaXJjbGU+PC9zdmc+Jyk7XG4gICAgdmFyIHZpZXdwb3J0ID0gJCh3aW5kb3cpLnZpZXdwb3J0KCk7XG4gICAgdmFyIGJyZWFrcG9pbnRzID0ge1xuICAgICAgICB4czogNDgwLFxuICAgICAgICBzbTogNzY4LFxuICAgICAgICBtZDogOTkyLFxuICAgICAgICBsZzogMTIwMFxuICAgIH07XG5cbiAgICAvLyBMYW5ndWFnZSBzZWxlY3RvciBvZiBhZG1pbiBpbnRlcmZhY2VcbiAgICBpZiAoJHdlbGNvbWVTY3JlZW4uZmluZCgnI2xhbmd1YWdlU2VsZWN0b3InKS5sZW5ndGggPiAwIHx8ICRkYXNoYm9hcmQuZmluZCgnI2xhbmd1YWdlU2VsZWN0b3InKS5sZW5ndGggPiAwKSB7XG4gICAgICAgIHZhciAkbGFuZ3VhZ2VTZWxlY3RvciA9ICQoJyNsYW5ndWFnZVNlbGVjdG9yJyk7XG4gICAgICAgIGlmICgkbGFuZ3VhZ2VTZWxlY3Rvci5maW5kKCcuZHJvcGRvd24tbWVudSA+IGxpLmFjdGl2ZScpLmxlbmd0aCA+IDApIHtcbiAgICAgICAgICAgIHZhciBsYWJlbCA9ICRsYW5ndWFnZVNlbGVjdG9yLmZpbmQoJy5kcm9wZG93bi1tZW51ID4gbGkuYWN0aXZlID4gYScpLmRhdGEoJ2xhYmVsJyk7XG4gICAgICAgICAgICAkbGFuZ3VhZ2VTZWxlY3Rvci5maW5kKCcuZHJvcGRvd24tdG9nZ2xlJykuaHRtbChsYWJlbCArICcgPHNwYW4gY2xhc3M9XCJjYXJldFwiPjwvc3Bhbj4nKTtcbiAgICAgICAgfVxuICAgICAgICAkYm9keS5kZWxlZ2F0ZSgnI2xhbmd1YWdlU2VsZWN0b3IgLmRyb3Bkb3duLW1lbnUgPiBsaSA+IGEnLCAnY2xpY2snLCBmdW5jdGlvbigpIHtcbiAgICAgICAgICAgIHZhciBsYWJlbCA9ICQodGhpcykuZGF0YSgnbGFiZWwnKTtcbiAgICAgICAgICAgICRsYW5ndWFnZVNlbGVjdG9yLmZpbmQoJy5kcm9wZG93bi10b2dnbGUnKS5odG1sKGxhYmVsICsgJyA8c3BhbiBjbGFzcz1cImNhcmV0XCI+PC9zcGFuPicpO1xuICAgICAgICB9KTtcblxuICAgICAgICBpZiAoY29uZmlnLmRlYnVnKVxuICAgICAgICAgICAgY29uc29sZS5sb2coJ0Rhc2hib2FyZDogY2xpY2sgYnkgYCNsYW5ndWFnZVNlbGVjdG9yYCcpO1xuXG4gICAgfVxuXG4gICAgLy8gQ2hhbmdpbmcgdGhlIHZpc2liaWxpdHkvaGlkaW5nIG9mIHR5cGVkIHBhc3N3b3JkXG4gICAgJGJvZHkuZGVsZWdhdGUoJyNzaG93SW5wdXRQYXNzd29yZCcsICdjbGljaycsIGZ1bmN0aW9uICgpIHtcbiAgICAgICAgdmFyICRwYXNzd29yZElucHV0ID0gJCh0aGlzKS5wcmV2KCdpbnB1dFt0eXBlXScpO1xuICAgICAgICBpZiAoJHBhc3N3b3JkSW5wdXQuYXR0cigndHlwZScpID09IFwicGFzc3dvcmRcIikge1xuICAgICAgICAgICAgJHBhc3N3b3JkSW5wdXQuYXR0cigndHlwZScsIFwidGV4dFwiKTtcbiAgICAgICAgICAgICQodGhpcykuZmluZCgnc3Bhbi5mYScpLnRvZ2dsZUNsYXNzKCdmYS1leWUgZmEtZXllLXNsYXNoJyk7XG4gICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAkcGFzc3dvcmRJbnB1dC5hdHRyKCd0eXBlJywgXCJwYXNzd29yZFwiKTtcbiAgICAgICAgICAgICQodGhpcykuZmluZCgnc3Bhbi5mYScpLnRvZ2dsZUNsYXNzKCdmYS1leWUtc2xhc2ggZmEtZXllJyk7XG4gICAgICAgIH1cbiAgICB9KTtcblxuICAgIC8qKlxuICAgICAqIENoYW5nZSBvZiBjdXJyZW50IHByb2dyZXNzIGZvciBwcm9ncmVzcyBiYXJzXG4gICAgICpcbiAgICAgKiBAcHVibGljXG4gICAgICogQHBhcmFtIHtTdHJpbmcvT2JqZWN0fSBzZWxlY3RvciAtIFNlbGVjdG9yIG9mIEJvb3RzdHJhcC5Qcm9ncmVzc2Jhci5cbiAgICAgKiBAcGFyYW0ge0ludGVnZXJ9IHZhbHVlbm93IC0gVmFsdWUgd2lsbCBiZSBjaGFuZ2VkLlxuICAgICAqIEBwYXJhbSB7Qm9vbGVhbn0gYXBwZW5kIC0gRmxhZyBvZiBhcHBlbmQgb3Igc2V0IGN1cnJlbnQgdmFsdWUgb2YgcHJvZ3Jlc3MuXG4gICAgICovXG4gICAgZnVuY3Rpb24gc2V0UHJvZ3Jlc3Moc2VsZWN0b3IsIHZhbHVlbm93LCBhcHBlbmQgPSBmYWxzZSkge1xuXG4gICAgICAgIGlmICh0eXBlb2Ygc2VsZWN0b3IgPT09IFwib2JqZWN0XCIpXG4gICAgICAgICAgICB2YXIgJHByb2dyZXNzID0gc2VsZWN0b3I7XG4gICAgICAgIGVsc2UgaWYgKHR5cGVvZiBzZWxlY3RvciA9PT0gXCJzdHJpbmdcIilcbiAgICAgICAgICAgIHZhciAkcHJvZ3Jlc3MgPSAkKHNlbGVjdG9yKTtcblxuICAgICAgICB2YXIgJHByb2dyZXNzQmFyID0gJHByb2dyZXNzLmZpbmQoJy5wcm9ncmVzcy1iYXInKTtcbiAgICAgICAgdmFyIGN1cnJlbnQgPSAkcHJvZ3Jlc3NCYXIuYXR0cihcImFyaWEtdmFsdWVub3dcIik7XG5cbiAgICAgICAgaWYgKGFwcGVuZCkge1xuICAgICAgICAgICAgdmFyIHN0ZXBzID0gMTA7XG4gICAgICAgICAgICB2YXIgdmFsdWUgPSAodmFsdWVub3cgLyBzdGVwcyk7XG4gICAgICAgICAgICB2YXIgaW50ZXJ2YWwgPSBzZXRJbnRlcnZhbChmdW5jdGlvbigpIHtcbiAgICAgICAgICAgICAgICBjdXJyZW50ICs9IHZhbHVlO1xuICAgICAgICAgICAgICAgICRwcm9ncmVzc0Jhci5jc3MoXCJ3aWR0aFwiLCBjdXJyZW50ICsgXCIlXCIpLmF0dHIoXCJhcmlhLXZhbHVlbm93XCIsIGN1cnJlbnQpO1xuXG4gICAgICAgICAgICAgICAgaWYgKCRwcm9ncmVzc0Jhci5maW5kKCdzcGFuJykubGVuZ3RoID4gMClcbiAgICAgICAgICAgICAgICAgICAgJHByb2dyZXNzQmFyLmZpbmQoJ3NwYW4nKS50ZXh0KGN1cnJlbnQgKyBcIiUgQ29tcGxldGVcIik7XG5cbiAgICAgICAgICAgICAgICBpZiAoY3VycmVudCA+PSAxMDAgfHwgc3RlcHMgPT0gMClcbiAgICAgICAgICAgICAgICAgICAgY2xlYXJJbnRlcnZhbChpbnRlcnZhbCk7XG5cbiAgICAgICAgICAgICAgICBzdGVwcy0tO1xuICAgICAgICAgICAgfSwgMTAwKTtcblxuICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgY3VycmVudCA9IHZhbHVlbm93O1xuICAgICAgICAgICAgJHByb2dyZXNzQmFyLmNzcyhcIndpZHRoXCIsIGN1cnJlbnQgKyBcIiVcIikuYXR0cihcImFyaWEtdmFsdWVub3dcIiwgY3VycmVudCk7XG5cbiAgICAgICAgICAgIGlmICgkcHJvZ3Jlc3NCYXIuZmluZCgnc3BhbicpLmxlbmd0aCA+IDApXG4gICAgICAgICAgICAgICAgJHByb2dyZXNzQmFyLmZpbmQoJ3NwYW4nKS50ZXh0KGN1cnJlbnQgKyBcIiUgQ29tcGxldGVcIik7XG5cbiAgICAgICAgfVxuICAgIH1cblxuICAgIC8vIFRyYWNraW5nIHBhZ2UgbG9hZGluZyBldmVudHMgd2l0aCBwQWpheFxuICAgICQoZG9jdW1lbnQpLm9uKHtcbiAgICAgICAgJ3BqYXg6c3RhcnQnOiBmdW5jdGlvbiAoZXZlbnQpIHtcblxuICAgICAgICAgICAgaWYgKGNvbmZpZy5hamF4UHJvZ3Jlc3MpIHtcbiAgICAgICAgICAgICAgICBzZXRQcm9ncmVzcygkcmVxdWVzdFByb2dyZXNzLCAwKTtcbiAgICAgICAgICAgICAgICAkcmVxdWVzdFByb2dyZXNzLnNob3coKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgaWYgKGNvbmZpZy5kZWJ1ZylcbiAgICAgICAgICAgICAgICBjb25zb2xlLmxvZygnRGFzaGJvYXJkOiBwamF4IGNoYW5nZSBzdGF0ZSB0byBgc3RhcnRgJyk7XG5cbiAgICAgICAgfSxcbiAgICAgICAgJ3BqYXg6YmVmb3JlU2VuZCc6IGZ1bmN0aW9uIChldmVudCkge1xuXG4gICAgICAgICAgICBpZiAoY29uZmlnLmFqYXhQcm9ncmVzcylcbiAgICAgICAgICAgICAgICBzZXRQcm9ncmVzcygkcmVxdWVzdFByb2dyZXNzLCAxNSk7XG5cbiAgICAgICAgICAgIGlmIChjb25maWcuZGVidWcpXG4gICAgICAgICAgICAgICAgY29uc29sZS5sb2coJ0Rhc2hib2FyZDogcGpheCBjaGFuZ2Ugc3RhdGUgdG8gYGJlZm9yZVNlbmRgJyk7XG5cbiAgICAgICAgfSxcbiAgICAgICAgJ3BqYXg6c2VuZCc6IGZ1bmN0aW9uIChldmVudCkge1xuXG4gICAgICAgICAgICBpZiAoY29uZmlnLmFqYXhGYWRlKVxuICAgICAgICAgICAgICAgICQodGhpcykuYXR0cignZGF0YS1wamF4LXN0YXRlJywgXCJzZW5kXCIpO1xuXG4gICAgICAgICAgICBpZiAoY29uZmlnLnNwaW5uZXIpXG4gICAgICAgICAgICAgICAgJCh0aGlzKS5hcHBlbmQoJHNwaW5uZXIpO1xuXG4gICAgICAgICAgICBpZiAoY29uZmlnLmFqYXhQcm9ncmVzcylcbiAgICAgICAgICAgICAgICBzZXRQcm9ncmVzcygkcmVxdWVzdFByb2dyZXNzLCAzNSk7XG5cbiAgICAgICAgICAgIGlmIChjb25maWcuZGVidWcpXG4gICAgICAgICAgICAgICAgY29uc29sZS5sb2coJ0Rhc2hib2FyZDogcGpheCBjaGFuZ2Ugc3RhdGUgdG8gYHNlbmRgJyk7XG5cbiAgICAgICAgfSxcbiAgICAgICAgJ3BqYXg6YmVmb3JlUmVwbGFjZSc6IGZ1bmN0aW9uIChldmVudCkge1xuXG4gICAgICAgICAgICBpZiAoY29uZmlnLmFqYXhQcm9ncmVzcylcbiAgICAgICAgICAgICAgICBzZXRQcm9ncmVzcygkcmVxdWVzdFByb2dyZXNzLCA3NSk7XG5cbiAgICAgICAgICAgIGlmIChjb25maWcuZGVidWcpXG4gICAgICAgICAgICAgICAgY29uc29sZS5sb2coJ0Rhc2hib2FyZDogcGpheCBjaGFuZ2Ugc3RhdGUgdG8gYGJlZm9yZVJlcGxhY2VgJyk7XG5cbiAgICAgICAgfSxcbiAgICAgICAgJ3BqYXg6Y29tcGxldGUnOiBmdW5jdGlvbiAoZXZlbnQpIHtcblxuICAgICAgICAgICAgaWYgKGNvbmZpZy5hamF4RmFkZSlcbiAgICAgICAgICAgICAgICAkKHRoaXMpLmF0dHIoJ2RhdGEtcGpheC1zdGF0ZScsIFwiY29tcGxldGVcIik7XG5cbiAgICAgICAgICAgIGlmIChjb25maWcuYWpheFByb2dyZXNzKSB7XG4gICAgICAgICAgICAgICAgc2V0UHJvZ3Jlc3MoJHJlcXVlc3RQcm9ncmVzcywgMTAwKTtcbiAgICAgICAgICAgICAgICBzZXRUaW1lb3V0KGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICAgICAgJHJlcXVlc3RQcm9ncmVzcy5oaWRlKCk7XG4gICAgICAgICAgICAgICAgfSwgMTIwMCk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGlmICgkd2VsY29tZVNjcmVlbi5maW5kKCcjbGFuZ3VhZ2VTZWxlY3RvcicpLmxlbmd0aCA+IDAgfHwgJGRhc2hib2FyZC5maW5kKCcjbGFuZ3VhZ2VTZWxlY3RvcicpLmxlbmd0aCA+IDApIHtcbiAgICAgICAgICAgICAgICB2YXIgJGxhbmd1YWdlU2VsZWN0b3IgPSAkKCcjbGFuZ3VhZ2VTZWxlY3RvcicpO1xuICAgICAgICAgICAgICAgIGlmICgkbGFuZ3VhZ2VTZWxlY3Rvci5maW5kKCcuZHJvcGRvd24tbWVudSA+IGxpLmFjdGl2ZScpLmxlbmd0aCA+IDApIHtcbiAgICAgICAgICAgICAgICAgICAgdmFyIGxhYmVsID0gJGxhbmd1YWdlU2VsZWN0b3IuZmluZCgnLmRyb3Bkb3duLW1lbnUgPiBsaS5hY3RpdmUgPiBhJykuZGF0YSgnbGFiZWwnKTtcbiAgICAgICAgICAgICAgICAgICAgJGxhbmd1YWdlU2VsZWN0b3IuZmluZCgnLmRyb3Bkb3duLXRvZ2dsZScpLmh0bWwobGFiZWwgKyAnIDxzcGFuIGNsYXNzPVwiY2FyZXRcIj48L3NwYW4+Jyk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBpZiAoY29uZmlnLmRlYnVnKVxuICAgICAgICAgICAgICAgIGNvbnNvbGUubG9nKCdEYXNoYm9hcmQ6IHBqYXggY2hhbmdlIHN0YXRlIHRvIGBjb21wbGV0ZWAnKTtcblxuICAgICAgICB9XG4gICAgfSk7XG5cblxuICAgIC8vIFNob3cvaGlkZSBkcm9wZG93biBpbiBtYWlubmF2IG9uIGhvdmVyXG4gICAgaWYgKGNvbmZpZy5tYWlubmF2LmV4cGFuZE9uSG92ZXIpIHtcbiAgICAgICAgJG1haW5OYXYuZmluZChcIi5kcm9wZG93blwiKS5lYWNoKGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgIHZhciAkdGhpcyA9ICQodGhpcyk7XG4gICAgICAgICAgICAkdGhpcy5jbGljayhmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAgICAgaWYgKCEkKHRoaXMpLmZpbmQoJy5kcm9wZG93bi1tZW51JykuaXMoJzp2aXNpYmxlJykpIHtcbiAgICAgICAgICAgICAgICAgICAgJCh0aGlzKS5maW5kKCcuZHJvcGRvd24tbWVudScpLnN0b3AodHJ1ZSwgdHJ1ZSkuc2xpZGVUb2dnbGUoXCJmYXN0XCIpO1xuXG4gICAgICAgICAgICAgICAgICAgIGlmIChjb25maWcuZGVidWcpXG4gICAgICAgICAgICAgICAgICAgICAgICBjb25zb2xlLmxvZygnRGFzaGJvYXJkOiBkcm9wZG93biBpbiBtYWlubmF2IGlzIHZpc2libGUgYnkgY2xpY2snKTtcblxuICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgICQodGhpcykuZmluZCgnLmRyb3Bkb3duLW1lbnUnKS5zdG9wKHRydWUsIHRydWUpLnNsaWRlVXAoXCJmYXN0XCIpO1xuXG4gICAgICAgICAgICAgICAgICAgIGlmIChjb25maWcuZGVidWcpXG4gICAgICAgICAgICAgICAgICAgICAgICBjb25zb2xlLmxvZygnRGFzaGJvYXJkOiBkcm9wZG93biBpbiBtYWlubmF2IGlzIGhpZGRpbmcgYnkgY2xpY2snKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9KTtcbiAgICAgICAgICAgICR0aGlzLmhvdmVyKGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICB2YXIgJGRyb3Bkb3duID0gJCh0aGlzKTtcbiAgICAgICAgICAgICAgICBpZiAoISRkcm9wZG93bi5maW5kKCcuZHJvcGRvd24tbWVudScpLmlzKCc6dmlzaWJsZScpKSB7XG4gICAgICAgICAgICAgICAgICAgICRkcm9wZG93bi5maW5kKCcuZHJvcGRvd24tbWVudScpLnN0b3AodHJ1ZSwgdHJ1ZSkuZGVsYXkoMzAwKS5zbGlkZVRvZ2dsZShcImZhc3RcIik7XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgaWYgKGNvbmZpZy5kZWJ1ZylcbiAgICAgICAgICAgICAgICAgICAgY29uc29sZS5sb2coJ0Rhc2hib2FyZDogZHJvcGRvd24gaW4gbWFpbm5hdiBpcyB2aXNpYmxlIGJ5IGhvdmVyJyk7XG5cbiAgICAgICAgICAgIH0sIGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICB2YXIgJGRyb3Bkb3duID0gJCh0aGlzKTtcbiAgICAgICAgICAgICAgICBpZiAoJGRyb3Bkb3duLmZpbmQoJy5kcm9wZG93bi1tZW51JykuaXMoJzp2aXNpYmxlJykpIHtcbiAgICAgICAgICAgICAgICAgICAgJGRyb3Bkb3duLmZpbmQoJy5kcm9wZG93bi1tZW51Jykuc3RvcCh0cnVlLCB0cnVlKS5kZWxheSgxMDApLnNsaWRlVXAoXCJmYXN0XCIpO1xuXG4gICAgICAgICAgICAgICAgICAgIGlmIChjb25maWcuZGVidWcpXG4gICAgICAgICAgICAgICAgICAgICAgICBjb25zb2xlLmxvZygnRGFzaGJvYXJkOiBkcm9wZG93biBpbiBtYWlubmF2IGlzIGhpZGRpbmcgYnkgaG92ZXInKTtcblxuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0pO1xuICAgICAgICB9KTtcbiAgICB9XG5cbiAgICAvLyBBZG1pbiBzaWRlYmFyIG1lbnUgbWFuYWdlbWVudFxuICAgIGlmICgkc2lkZWJhck5hdi5sZW5ndGggPiAwKSB7XG5cbiAgICAgICAgLy8gRGlzYWJsZSBjbGljayBvbiBkcm9wZG93biBlbGVtZW50IHdpdGggZW1wdHkgbGlua1xuICAgICAgICAkc2lkZWJhck5hdi5maW5kKCcuZHJvcGRvd24tbWVudSA+IGxpID4gYVtocmVmPVwiI1wiXScpLm9uKCdjbGljaycsIGZ1bmN0aW9uIChldmVudCkge1xuICAgICAgICAgICAgZXZlbnQucHJldmVudERlZmF1bHQoKTtcbiAgICAgICAgICAgIGV2ZW50LnN0b3BQcm9wYWdhdGlvbigpO1xuXG4gICAgICAgICAgICBpZiAoY29uZmlnLmRlYnVnKVxuICAgICAgICAgICAgICAgIGNvbnNvbGUubG9nKCdEYXNoYm9hcmQ6IGNsaWNrIGJ5IGAuZHJvcGRvd24tbWVudSA+IGxpID4gYVtocmVmPVwiI1wiXWAgaW4gc2lkZWJhcicpO1xuXG4gICAgICAgIH0pO1xuXG4gICAgICAgIC8vIERpc2FibGUgY2xpY2sgb24gcG9wb3ZlciBlbGVtZW50XG4gICAgICAgICRzaWRlYmFyTmF2LmZpbmQoJy5kcm9wZG93bi1zdWJtZW51ID4gYScpLm9uKCdjbGljaycsIGZ1bmN0aW9uIChldmVudCkge1xuICAgICAgICAgICAgZXZlbnQucHJldmVudERlZmF1bHQoKTtcbiAgICAgICAgICAgIGV2ZW50LnN0b3BQcm9wYWdhdGlvbigpO1xuXG4gICAgICAgICAgICBpZiAoY29uZmlnLmRlYnVnKVxuICAgICAgICAgICAgICAgIGNvbnNvbGUubG9nKCdEYXNoYm9hcmQ6IGNsaWNrIGJ5IGAuZHJvcGRvd24tc3VibWVudSA+IGFgIGluIHNpZGViYXInKTtcblxuICAgICAgICB9KTtcblxuICAgICAgICAvLyBTaG93L2hpZGUgZHJvcGRvd24gaW4gc2lkZWJhciBvbiBob3ZlclxuICAgICAgICBpZiAoY29uZmlnLnNpZGViYXIuZXhwYW5kT25Ib3Zlcikge1xuICAgICAgICAgICAgJHNpZGViYXJOYXYuZmluZChcIi5kcm9wZG93blwiKS5lYWNoKGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICB2YXIgJHRoaXMgPSAkKHRoaXMpO1xuICAgICAgICAgICAgICAgICR0aGlzLmNsaWNrKGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICAgICAgaWYgKCEkKHRoaXMpLmZpbmQoJy5kcm9wZG93bi1tZW51JykuaXMoJzp2aXNpYmxlJykpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICQodGhpcykuZmluZCgnLmRyb3Bkb3duLW1lbnUnKS5zdG9wKHRydWUsIHRydWUpLnNsaWRlVG9nZ2xlKFwiZmFzdFwiKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICQodGhpcykuZmluZCgnLmRyb3Bkb3duLXRvZ2dsZSAuZmEtYW5nbGUtZG93bicpLnJlbW92ZUNsYXNzKCdmYS1hbmdsZS1kb3duJykuYWRkQ2xhc3MoJ2ZhLWFuZ2xlLXVwJyk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIGlmIChjb25maWcuZGVidWcpXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgY29uc29sZS5sb2coJ0Rhc2hib2FyZDogZHJvcGRvd24gaW4gc2lkZWJhciBpcyB2aXNpYmxlIGJ5IGNsaWNrJyk7XG5cbiAgICAgICAgICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICQodGhpcykuZmluZCgnLmRyb3Bkb3duLW1lbnUnKS5zdG9wKHRydWUsIHRydWUpLnNsaWRlVXAoXCJmYXN0XCIpO1xuICAgICAgICAgICAgICAgICAgICAgICAgJCh0aGlzKS5maW5kKCcuZHJvcGRvd24tdG9nZ2xlIC5mYS1hbmdsZS11cCcpLnJlbW92ZUNsYXNzKCdmYS1hbmdsZS11cCcpLmFkZENsYXNzKCdmYS1hbmdsZS1kb3duJyk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIGlmIChjb25maWcuZGVidWcpXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgY29uc29sZS5sb2coJ0Rhc2hib2FyZDogZHJvcGRvd24gaW4gc2lkZWJhciBpcyBoaWRkaW5nIGJ5IGNsaWNrJyk7XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgJHNpZGViYXJOYXYuZmluZChcIi5kcm9wZG93bjpub3QoLmFjdGl2ZSk6bm90KDpob3ZlcilcIikuZmluZCgnLmRyb3Bkb3duLW1lbnUnKS5zbGlkZVVwKFwiZmFzdFwiKTtcbiAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgICAgICAvKiR0aGlzLmhvdmVyKGZ1bmN0aW9uICgpIHtcblxuICAgICAgICAgICAgICAgICAgICB2YXIgJGRyb3Bkb3duID0gJCh0aGlzKTtcbiAgICAgICAgICAgICAgICAgICAgaWYgKCEkZHJvcGRvd24uZmluZCgnLmRyb3Bkb3duLW1lbnUnKS5pcygnOnZpc2libGUnKSkge1xuICAgICAgICAgICAgICAgICAgICAgICAgJGRyb3Bkb3duLmZpbmQoJy5kcm9wZG93bi1tZW51Jykuc3RvcCh0cnVlLCB0cnVlKS5kZWxheSg1MDApLnNsaWRlVG9nZ2xlKFwiZmFzdFwiKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIHNldFRpbWVvdXQoZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgJGRyb3Bkb3duLmZpbmQoJy5kcm9wZG93bi10b2dnbGUgLmZhLWFuZ2xlLWRvd24nKS5yZW1vdmVDbGFzcygnZmEtYW5nbGUtZG93bicpLmFkZENsYXNzKCdmYS1hbmdsZS11cCcpO1xuICAgICAgICAgICAgICAgICAgICAgICAgfSwgMjAwKTtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICAkc2lkZWJhck5hdi5maW5kKFwiLmRyb3Bkb3duOm5vdCguYWN0aXZlKTpub3QoOmhvdmVyKVwiKS5maW5kKCcuZHJvcGRvd24tbWVudScpLnNsaWRlVXAoXCJmYXN0XCIpO1xuXG4gICAgICAgICAgICAgICAgICAgIGlmIChjb25maWcuZGVidWcpXG4gICAgICAgICAgICAgICAgICAgICAgICBjb25zb2xlLmxvZygnRGFzaGJvYXJkOiBkcm9wZG93biBpbiBzaWRlYmFyIGlzIHZpc2libGUgYnkgaG92ZXInKTtcblxuICAgICAgICAgICAgICAgIH0sIGZ1bmN0aW9uICgpIHtcblxuICAgICAgICAgICAgICAgICAgICB2YXIgJGRyb3Bkb3duID0gJCh0aGlzKTtcblxuICAgICAgICAgICAgICAgICAgICBpZiAoISRkcm9wZG93bi5oYXNDbGFzcygncG9wb3Zlci1zaG93JykpIHtcblxuICAgICAgICAgICAgICAgICAgICAgICAgJGRyb3Bkb3duLmZpbmQoJy5kcm9wZG93bi1tZW51Jykuc3RvcCh0cnVlLCB0cnVlKS5kZWxheSgyMDApLnNsaWRlVXAoXCJmYXN0XCIpO1xuICAgICAgICAgICAgICAgICAgICAgICAgc2V0VGltZW91dChmdW5jdGlvbigpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAkZHJvcGRvd24uZmluZCgnLmRyb3Bkb3duLXRvZ2dsZSAuZmEtYW5nbGUtdXAnKS5yZW1vdmVDbGFzcygnZmEtYW5nbGUtdXAnKS5hZGRDbGFzcygnZmEtYW5nbGUtZG93bicpO1xuICAgICAgICAgICAgICAgICAgICAgICAgfSwgMjAwKTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgaWYgKGNvbmZpZy5kZWJ1ZylcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBjb25zb2xlLmxvZygnRGFzaGJvYXJkOiBkcm9wZG93biBpbiBzaWRlYmFyIGlzIGhpZGRpbmcgYnkgaG92ZXInKTtcblxuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgIC8vIEZpeGVkOiBEcm9wZG93biBtZW51IGhpZGRpbmcgYnkgcG9wb3ZlciBpcyBzaG93XG4gICAgICAgICAgICAgICAgICAgIC8vJHNpZGViYXJOYXYuZmluZChcIi5kcm9wZG93bjpub3QoLmFjdGl2ZSk6bm90KDpob3ZlcilcIikuZmluZCgnLmRyb3Bkb3duLW1lbnUnKS5zbGlkZVVwKFwiZmFzdFwiKTtcbiAgICAgICAgICAgICAgICB9KTsqL1xuICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICAkc2lkZWJhck5hdi5maW5kKFwiLmRyb3Bkb3duLmFjdGl2ZVwiKS5maW5kKCcuZHJvcGRvd24tdG9nZ2xlIC5mYS1hbmdsZS1kb3duJykudG9nZ2xlQ2xhc3MoJ2ZhLWFuZ2xlLWRvd24gZmEtYW5nbGUtdXAnKTtcbiAgICAgICAgICAgICRzaWRlYmFyTmF2LmZpbmQoXCIuZHJvcGRvd24uYWN0aXZlIC5kcm9wZG93bi10b2dnbGVcIikuY2xpY2soKTtcbiAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICRzaWRlYmFyTmF2LmZpbmQoXCIuZHJvcGRvd25cIikub24oJ3Nob3duLmJzLmRyb3Bkb3duJywgZnVuY3Rpb24oZXZlbnQpIHtcbiAgICAgICAgICAgICAgICAkKGV2ZW50LnRhcmdldCkuZmluZCgnLmRyb3Bkb3duLXRvZ2dsZSAuZmEtYW5nbGUtZG93bicpLnRvZ2dsZUNsYXNzKCdmYS1hbmdsZS1kb3duIGZhLWFuZ2xlLXVwJyk7XG4gICAgICAgICAgICB9KS5vbignaGlkZGVuLmJzLmRyb3Bkb3duJywgZnVuY3Rpb24oZXZlbnQpIHtcbiAgICAgICAgICAgICAgICAkKGV2ZW50LnRhcmdldCkuZmluZCgnLmRyb3Bkb3duLXRvZ2dsZSAuZmEtYW5nbGUtdXAnKS50b2dnbGVDbGFzcygnZmEtYW5nbGUtdXAgZmEtYW5nbGUtZG93bicpO1xuICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICAvLyRzaWRlYmFyTmF2LmZpbmQoXCIuZHJvcGRvd24uYWN0aXZlXCIpLmRyb3Bkb3duKCd0b2dnbGUnKTtcbiAgICAgICAgICAgICRzaWRlYmFyTmF2LmZpbmQoXCIuZHJvcGRvd24uYWN0aXZlIC5kcm9wZG93bi10b2dnbGVcIikuY2xpY2soKTtcbiAgICAgICAgfVxuXG4gICAgICAgIC8vIEluaXQgcG9wb3ZlciBtZW51IGluIHNpZGViYXJcbiAgICAgICAgJHNpZGViYXJOYXYuZmluZCgnLmRyb3Bkb3duLXN1Ym1lbnUgPiBhJykuZWFjaChmdW5jdGlvbigpIHtcbiAgICAgICAgICAgIHZhciAkdGhpcyA9ICQodGhpcyk7XG4gICAgICAgICAgICB2YXIgJGRyb3Bkb3duID0gJCh0aGlzKS5wYXJlbnRzKCcuZHJvcGRvd24nKTtcblxuICAgICAgICAgICAgdmFyIHRyaWdnZXIgPSAnY2xpY2snO1xuICAgICAgICAgICAgaWYgKGNvbmZpZy5zaWRlYmFyLmV4cGFuZE9uSG92ZXIpXG4gICAgICAgICAgICAgICAgdHJpZ2dlciA9ICdtYW51YWwnO1xuXG4gICAgICAgICAgICAkdGhpcy5wb3BvdmVyKHtcbiAgICAgICAgICAgICAgICBwbGFjZW1lbnQ6ICdhdXRvIHJpZ2h0JyxcbiAgICAgICAgICAgICAgICB0cmlnZ2VyOiB0cmlnZ2VyLFxuICAgICAgICAgICAgICAgIGNvbnRhaW5lcjogJ2JvZHknLFxuICAgICAgICAgICAgICAgIHRpdGxlOiBmYWxzZSxcbiAgICAgICAgICAgICAgICBodG1sOiB0cnVlLFxuICAgICAgICAgICAgICAgIHRlbXBsYXRlOiAnPGRpdiBjbGFzcz1cInBvcG92ZXIgbmF2LXBvcG92ZXJcIiByb2xlPVwidG9vbHRpcFwiPjxkaXYgY2xhc3M9XCJhcnJvd1wiPjwvZGl2PjxkaXYgY2xhc3M9XCJwb3BvdmVyLWNvbnRlbnRcIj48L2Rpdj48L2Rpdj4nLFxuICAgICAgICAgICAgICAgIGNvbnRlbnQ6IGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgICAgICAgICByZXR1cm4gJHRoaXMucGFyZW50KCcuZHJvcGRvd24tc3VibWVudScpLmZpbmQoJ3VsJykuYWRkQ2xhc3MoJ25hdicpLm91dGVySHRtbCgpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICBpZiAoY29uZmlnLnNpZGViYXIuZXhwYW5kT25Ib3Zlcikge1xuICAgICAgICAgICAgICAgICR0aGlzLm9uKFwibW91c2VlbnRlclwiLCBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAgICAgICAgIHZhciBfdGhpcyA9IHRoaXM7XG4gICAgICAgICAgICAgICAgICAgICQodGhpcykucG9wb3ZlcihcInNob3dcIik7XG4gICAgICAgICAgICAgICAgICAgICRkcm9wZG93bi5hZGRDbGFzcygncG9wb3Zlci1zaG93Jyk7XG5cbiAgICAgICAgICAgICAgICAgICAgJChcIi5uYXYtcG9wb3ZlclwiKS5vbihcIm1vdXNlbGVhdmVcIiwgZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgJChfdGhpcykucG9wb3ZlcignaGlkZScpO1xuICAgICAgICAgICAgICAgICAgICAgICAgJGRyb3Bkb3duLnJlbW92ZUNsYXNzKCdwb3BvdmVyLXNob3cnKTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgaWYgKGNvbmZpZy5kZWJ1ZylcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBjb25zb2xlLmxvZygnRGFzaGJvYXJkOiBzaWRlYmFyIHBvcG92ZXIgaXMgaGlkZGluZyBieSBtb3VzZWxlYXZlJyk7XG5cbiAgICAgICAgICAgICAgICAgICAgfSkub24oXCJtb3VzZWRvd25cIiwgZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgJChfdGhpcykucG9wb3ZlcignaGlkZScpO1xuICAgICAgICAgICAgICAgICAgICAgICAgJGRyb3Bkb3duLnJlbW92ZUNsYXNzKCdwb3BvdmVyLXNob3cnKTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgaWYgKGNvbmZpZy5kZWJ1ZylcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBjb25zb2xlLmxvZygnRGFzaGJvYXJkOiBzaWRlYmFyIHBvcG92ZXIgaXMgaGlkZGluZyBieSBtb3VzZWRvd24nKTtcbiAgICAgICAgICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgICAgICAgICAgaWYgKGNvbmZpZy5kZWJ1ZylcbiAgICAgICAgICAgICAgICAgICAgICAgIGNvbnNvbGUubG9nKCdEYXNoYm9hcmQ6IHNpZGViYXIgcG9wb3ZlciBpcyB2aXNpYmxlIGJ5IG1vdXNlZW50ZXInKTtcblxuICAgICAgICAgICAgICAgIH0pLm9uKFwibW91c2VsZWF2ZVwiLCBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAgICAgICAgIHZhciBfdGhpcyA9IHRoaXM7XG4gICAgICAgICAgICAgICAgICAgIHNldFRpbWVvdXQoZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgaWYgKCEkKFwiLm5hdi1wb3BvdmVyOmhvdmVyXCIpLmxlbmd0aCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICQoX3RoaXMpLnBvcG92ZXIoJ2hpZGUnKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAkZHJvcGRvd24ucmVtb3ZlQ2xhc3MoJ3BvcG92ZXItc2hvdycpO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgaWYgKGNvbmZpZy5kZWJ1ZylcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgY29uc29sZS5sb2coJ0Rhc2hib2FyZDogc2lkZWJhciBwb3BvdmVyIGlzIGhpZGRpbmcgYnkgbW91c2VsZWF2ZSBhbmQgdGltZW91dCcpO1xuICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICB9LCAyMDApO1xuICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgIH0pO1xuXG4gICAgICAgIC8vIEFkZCBzaWRlYmFyIG5hdiB0byBtYWluIG5hdmJhciBmb3Igc20gYW5kIHhzIGRpc3BsYXlzXG4gICAgICAgIGlmICh2aWV3cG9ydC53aWR0aCA8PSBicmVha3BvaW50cy5zbSkge1xuICAgICAgICAgICAgdmFyICRzaWRlYmFyID0gJHNpZGViYXJOYXYuY2xvbmUoKTtcbiAgICAgICAgICAgICRzaWRlYmFyLmF0dHIoJ2NsYXNzJywgJ25hdiBuYXZiYXItbmF2IGhpZGRlbi1tZCBoaWRkZW4tbGcnKTtcbiAgICAgICAgICAgICRzaWRlYmFyLmZpbmQoJ2xpJykuZWFjaChmdW5jdGlvbigpIHtcbiAgICAgICAgICAgICAgICAkKHRoaXMpLmZpbmQoJy5mYS1zdGFjaycpLnJlbW92ZUNsYXNzKCdmYS1zdGFjaycpLnJlbW92ZUNsYXNzKCdmYS1sZycpO1xuICAgICAgICAgICAgICAgICQodGhpcykuZmluZCgnLmZhJykucmVtb3ZlQ2xhc3MoJ2ZhLXN0YWNrLTF4Jyk7XG4gICAgICAgICAgICB9KTtcbiAgICAgICAgICAgICRpdGVtcyA9ICRzaWRlYmFyLm91dGVySHRtbCgpO1xuICAgICAgICAgICAgJG1haW5OYXYuYmVmb3JlKCRpdGVtcyk7XG5cbiAgICAgICAgICAgIGlmIChjb25maWcuZGVidWcpXG4gICAgICAgICAgICAgICAgY29uc29sZS5sb2coJ0Rhc2hib2FyZDogYWRkZWQgc2lkZWJhciBuYXYgdG8gbWFpbiBuYXZiYXIgZm9yIHNtIGFuZCB4cyBkaXNwbGF5cycpO1xuICAgICAgICB9XG4gICAgfVxuXG4gICAgLy8gRHJvcGRvd25gc1xuICAgICQoJ2JvZHknKS5kZWxlZ2F0ZSgnLmRyb3Bkb3duLXRvZ2dsZSwgW2RhdGEtdG9nZ2xlPVwiZHJvcGRvd25cIl0nLCAnY2xpY2snLCBmdW5jdGlvbiAoZXZlbnQpIHtcbiAgICAgICAgaWYgKCgkKGRvY3VtZW50KS53aWR0aCgpID4gYnJlYWtwb2ludHMuc20pICYmICQodGhpcykuaXMoXCJhXCIpKSB7XG4gICAgICAgICAgICBldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgICAgICAgdmFyIHVybCA9ICQodGhpcykuYXR0cignaHJlZicpO1xuICAgICAgICAgICAgaWYgKHVybCAhPT0gJyMnKVxuICAgICAgICAgICAgICAgIHdpbmRvdy5sb2NhdGlvbi5ocmVmID0gdXJsO1xuICAgICAgICB9XG5cbiAgICAgICAgaWYgKGNvbmZpZy5kZWJ1ZylcbiAgICAgICAgICAgIGNvbnNvbGUubG9nKCdEYXNoYm9hcmQ6IGNsaWNrIG9uIC5kcm9wZG93bi10b2dnbGUnKTtcblxuICAgIH0pO1xuICAgICQoJ2JvZHknKS5kZWxlZ2F0ZSgkKCcuZHJvcGRvd24tdG9nZ2xlLCBbZGF0YS10b2dnbGU9XCJkcm9wZG93blwiXScpLnBhcmVudCgpLCAnc2hvdy5icy5kcm9wZG93bicsIGZ1bmN0aW9uIChldmVudCkge1xuICAgICAgICB2YXIgJGJ1dHRvbiA9ICQoZXZlbnQucmVsYXRlZFRhcmdldCk7XG4gICAgICAgIHZhciAkZHJvcGRvd24gPSAkKGV2ZW50LnRhcmdldCkuZmluZCgnLmRyb3Bkb3duLW1lbnUnKTtcbiAgICAgICAgdmFyIHZpZXdwb3JIZWlnaHQgPSAkKGRvY3VtZW50KS5oZWlnaHQoKTtcbiAgICAgICAgdmFyIGJ1dHRvbk9mZnNldCA9ICRidXR0b24ub2Zmc2V0KCkudG9wICsgJGJ1dHRvbi5oZWlnaHQoKTtcbiAgICAgICAgdmFyIGRyb3Bkb3duSGVpZ2h0ID0gJGRyb3Bkb3duLmhlaWdodCgpO1xuICAgICAgICB2YXIgZHJvcGRvd25PZmZzZXQgPSBidXR0b25PZmZzZXQgKyBkcm9wZG93bkhlaWdodDtcblxuICAgICAgICBpZiAoZHJvcGRvd25PZmZzZXQgPiAodmlld3BvckhlaWdodCAtIDQ1KSAmJiAoYnV0dG9uT2Zmc2V0IC0gNTUpID4gZHJvcGRvd25IZWlnaHQpIHtcbiAgICAgICAgICAgICQoZXZlbnQudGFyZ2V0KS5hZGRDbGFzcygnZHJvcHVwJyk7XG4gICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAkKGV2ZW50LnRhcmdldCkucmVtb3ZlQ2xhc3MoJ2Ryb3B1cCcpO1xuICAgICAgICB9XG4gICAgfSk7XG5cbiAgICAvLyBIb3Qga2V5cyBmb3IgcGFnaW5hdGlvblxuICAgICQod2luZG93KS5rZXlkb3duKGZ1bmN0aW9uKGV2ZW50KSB7XG5cbiAgICAgICAgdmFyICRwYWdpbmF0aW9uID0gJGRhc2hib2FyZC5maW5kKCcucGFnaW5hdGlvbicpO1xuICAgICAgICBsZXQgY3RybEtleSA9IChnZXRPUygpID09IFwiV2luZG93c1wiKSA/IGV2ZW50LmN0cmxLZXkgOiAoZ2V0T1MoKSA9PSBcIk1hYyBPU1wiKSA/IGV2ZW50LmFsdEtleSA6IG51bGw7XG4gICAgICAgIGxldCBrZXlDb2RlID0gZXZlbnQua2V5Q29kZSA/IGV2ZW50LmtleUNvZGUgOiBldmVudC53aGljaCA/IGV2ZW50LndoaWNoIDogbnVsbDtcblxuICAgICAgICBpZiAoY3RybEtleSAmJiBrZXlDb2RlICYmICRwYWdpbmF0aW9uLmxlbmd0aCA+IDApIHtcbiAgICAgICAgICAgIGV2ZW50LnByZXZlbnREZWZhdWx0KCk7XG5cbiAgICAgICAgICAgIGxldCBsaW5rID0gbnVsbDtcbiAgICAgICAgICAgIHN3aXRjaCAoa2V5Q29kZSkge1xuICAgICAgICAgICAgICAgIGNhc2UgMzc6XG4gICAgICAgICAgICAgICAgICAgIGxpbmsgPSAkcGFnaW5hdGlvbi5maW5kKCdsaSA+IGFbcmVsPVwicHJldlwiXSwgbGkucHJldiA+IGEnKS5hdHRyKCdocmVmJyk7XG4gICAgICAgICAgICAgICAgICAgIGJyZWFrO1xuICAgICAgICAgICAgICAgIGNhc2UgMzk6XG4gICAgICAgICAgICAgICAgICAgIGxpbmsgPSAkcGFnaW5hdGlvbi5maW5kKCdsaSA+IGFbcmVsPVwibmV4dFwiXSwgbGkubmV4dCA+IGEnKS5hdHRyKCdocmVmJyk7XG4gICAgICAgICAgICAgICAgICAgIGJyZWFrO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBpZiAobGluaykge1xuXG4gICAgICAgICAgICAgICAgbGV0ICRwamF4ID0gJHBhZ2luYXRpb24uY2xvc2VzdCgnW2RhdGEtcGpheC1jb250YWluZXJdJyk7XG4gICAgICAgICAgICAgICAgaWYgKCRwamF4Lmxlbmd0aCA+IDApIHtcblxuICAgICAgICAgICAgICAgICAgICBsZXQgdGltZW91dCA9IDUwMDA7XG4gICAgICAgICAgICAgICAgICAgIGlmICgkcGpheC5kYXRhKFwicGpheC10aW1lb3V0XCIpKVxuICAgICAgICAgICAgICAgICAgICAgICAgdGltZW91dCA9IHBhcnNlSW50KCRwamF4LmRhdGEoXCJwamF4LXRpbWVvdXRcIikpO1xuXG4gICAgICAgICAgICAgICAgICAgICQucGpheC5yZWxvYWQoe1xuICAgICAgICAgICAgICAgICAgICAgICAgY29udGFpbmVyOiAoJHBqYXguYXR0cignaWQnKSkgPyAnIycgKyAkcGpheC5hdHRyKCdpZCcpIDogbnVsbCxcbiAgICAgICAgICAgICAgICAgICAgICAgIHRpbWVvdXQ6IHRpbWVvdXQsXG4gICAgICAgICAgICAgICAgICAgICAgICB1cmw6IGxpbmtcbiAgICAgICAgICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICBkb2N1bWVudC5sb2NhdGlvbiA9IGxpbms7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfVxuICAgICAgICB9XG4gICAgfSk7XG5cbiAgICAvLyBNb2RhbHMgYW5kIGJ1dHRvbnMgbG9hZGluZyBzdGF0ZVxuICAgICQoJ2JvZHknKS5kZWxlZ2F0ZSgnYSwgYnV0dG9uJywgJ2NsaWNrJywgZnVuY3Rpb24oZXZlbnQpIHtcbiAgICAgICAgaWYgKCQodGhpcykuZGF0YSgndG9nZ2xlJykgPT0gXCJtb2RhbFwiKSB7XG4gICAgICAgICAgICAkKCdib2R5JykuYWRkQ2xhc3MoJ2xvYWRpbmcnKTtcbiAgICAgICAgfSBlbHNlIGlmICgkKHRoaXMpLmRhdGEoJ2xvYWRpbmctdGV4dCcpKSB7XG5cbiAgICAgICAgICAgIHZhciBoYXNFcnJvcnMgPSBmYWxzZTtcblxuICAgICAgICAgICAgdmFyICRmb3JtID0gJChldmVudC50YXJnZXQpLnBhcmVudHMoJ2Zvcm06Zmlyc3QnKTtcbiAgICAgICAgICAgICRmb3JtLmZpbmQoJ2lucHV0W2FyaWEtcmVxdWlyZWRdJykuZWFjaChmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAgICAgaWYgKCQodGhpcykudmFsKCkubGVuZ3RoID09IDApIHtcbiAgICAgICAgICAgICAgICAgICAgaGFzRXJyb3JzID0gdHJ1ZTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgJGZvcm0uZmluZCgnW2FyaWEtaW52YWxpZF0nKS5lYWNoKGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICBpZiAoJCh0aGlzKS5hdHRyKCdhcmlhLWludmFsaWQnKSA9PSBcInRydWVcIikge1xuICAgICAgICAgICAgICAgICAgICBoYXNFcnJvcnMgPSB0cnVlO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICBpZiAoIWhhc0Vycm9ycykge1xuICAgICAgICAgICAgICAgICQodGhpcykuYWRkQ2xhc3MoJ2xvYWRpbmcnKTtcbiAgICAgICAgICAgICAgICAkKHRoaXMpLmJ1dHRvbignbG9hZGluZycpO1xuICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAkKHRoaXMpLnJlbW92ZUNsYXNzKCdsb2FkaW5nJyk7XG4gICAgICAgICAgICAgICAgJCh0aGlzKS5idXR0b24oJ3Jlc2V0Jyk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH1cbiAgICB9KTtcbiAgICAkKCdib2R5JykuZGVsZWdhdGUoJy5tb2RhbCcsICdzaG93LmJzLm1vZGFsJywgZnVuY3Rpb24oKSB7XG4gICAgICAgICQoJ2JvZHknKS5hZGRDbGFzcygnbG9hZGluZycpO1xuICAgIH0pO1xuICAgICQoJ2JvZHknKS5kZWxlZ2F0ZSgnLm1vZGFsJywgJ3Nob3duLmJzLm1vZGFsJywgZnVuY3Rpb24oKSB7XG4gICAgICAgICQoJ2JvZHknKS5yZW1vdmVDbGFzcygnbG9hZGluZycpO1xuICAgIH0pO1xuICAgICQoJ2JvZHknKS5kZWxlZ2F0ZSgnLm1vZGFsJywgJ2hpZGUuYnMubW9kYWwnLCBmdW5jdGlvbigpIHtcbiAgICAgICAgJCgnYm9keScpLnJlbW92ZUNsYXNzKCdsb2FkaW5nJyk7XG4gICAgfSk7XG4gICAgJCgnYm9keScpLmRlbGVnYXRlKCcubW9kYWwnLCAnbG9hZGVkLmJzLm1vZGFsJywgZnVuY3Rpb24oKSB7XG4gICAgICAgICQoJ2JvZHknKS5yZW1vdmVDbGFzcygnbG9hZGluZycpO1xuICAgIH0pO1xuXG59KTsiXX0=
