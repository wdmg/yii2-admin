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
        $this.hover(function() {

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

        }, function() {

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
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImFkbWluLmpzIl0sIm5hbWVzIjpbXSwibWFwcGluZ3MiOiJBQUFBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBIiwiZmlsZSI6ImFkbWluLmpzIiwic291cmNlc0NvbnRlbnQiOlsiJChkb2N1bWVudCkucmVhZHkoZnVuY3Rpb24oKSB7XG5cbiAgICAvLyBDb25maWd1cmF0aW9uXG4gICAgY29uc3QgY29uZmlnID0ge1xuICAgICAgICBtYWlubmF2OiB7XG4gICAgICAgICAgICBleHBhbmRPbkhvdmVyOiBmYWxzZVxuICAgICAgICB9LFxuICAgICAgICBzaWRlYmFyOiB7XG4gICAgICAgICAgICBleHBhbmRPbkhvdmVyOiB0cnVlXG4gICAgICAgIH0sXG4gICAgICAgIGFqYXhGYWRlOiB0cnVlLFxuICAgICAgICBhamF4UHJvZ3Jlc3M6IHRydWUsXG4gICAgICAgIHNwaW5uZXI6IGZhbHNlLFxuICAgICAgICBkZWJ1ZzogZmFsc2VcbiAgICB9O1xuXG4gICAgLy8gRGVmaW5pdGlvbiBvZiB2YXJpYWJsZXMgYW5kIGVsZW1lbnRzXG4gICAgdmFyICRib2R5ID0gJCgnYm9keScpO1xuICAgIHZhciAkZGFzaGJvYXJkID0gJCgnYm9keS5kYXNoYm9hcmQnKTtcbiAgICB2YXIgJHdlbGNvbWVTY3JlZW4gPSAkKCdib2R5LndlbGNvbWUnKTtcbiAgICB2YXIgJHJlcXVlc3RQcm9ncmVzcyA9ICRkYXNoYm9hcmQuZmluZCgnI3JlcXVlc3RQcm9ncmVzcycpO1xuICAgIHZhciAkc2lkZWJhciA9ICRkYXNoYm9hcmQuZmluZCgnLnNpZGViYXInKTtcbiAgICB2YXIgJG1haW5OYXYgPSAkZGFzaGJvYXJkLmZpbmQoJyNtYWluTmF2Jyk7XG4gICAgdmFyICRzaWRlYmFyTmF2ID0gJHNpZGViYXIuZmluZCgnI3NpZGViYXJOYXYnKTtcbiAgICB2YXIgJHNwaW5uZXIgPSAkKCc8c3ZnIGNsYXNzPVwic3Bpbm5lclwiIHZpZXdCb3g9XCIwIDAgNTAgNTBcIj48Y2lyY2xlIGNsYXNzPVwicGF0aFwiIGN4PVwiMjVcIiBjeT1cIjI1XCIgcj1cIjIwXCIgZmlsbD1cIm5vbmVcIiBzdHJva2Utd2lkdGg9XCI1XCI+PC9jaXJjbGU+PC9zdmc+Jyk7XG4gICAgdmFyIHZpZXdwb3J0ID0gJCh3aW5kb3cpLnZpZXdwb3J0KCk7XG4gICAgdmFyIGJyZWFrcG9pbnRzID0ge1xuICAgICAgICB4czogNDgwLFxuICAgICAgICBzbTogNzY4LFxuICAgICAgICBtZDogOTkyLFxuICAgICAgICBsZzogMTIwMFxuICAgIH07XG5cbiAgICAvLyBMYW5ndWFnZSBzZWxlY3RvciBvZiBhZG1pbiBpbnRlcmZhY2VcbiAgICBpZiAoJHdlbGNvbWVTY3JlZW4uZmluZCgnI2xhbmd1YWdlU2VsZWN0b3InKS5sZW5ndGggPiAwIHx8ICRkYXNoYm9hcmQuZmluZCgnI2xhbmd1YWdlU2VsZWN0b3InKS5sZW5ndGggPiAwKSB7XG4gICAgICAgIHZhciAkbGFuZ3VhZ2VTZWxlY3RvciA9ICQoJyNsYW5ndWFnZVNlbGVjdG9yJyk7XG4gICAgICAgIGlmICgkbGFuZ3VhZ2VTZWxlY3Rvci5maW5kKCcuZHJvcGRvd24tbWVudSA+IGxpLmFjdGl2ZScpLmxlbmd0aCA+IDApIHtcbiAgICAgICAgICAgIHZhciBsYWJlbCA9ICRsYW5ndWFnZVNlbGVjdG9yLmZpbmQoJy5kcm9wZG93bi1tZW51ID4gbGkuYWN0aXZlID4gYScpLmRhdGEoJ2xhYmVsJyk7XG4gICAgICAgICAgICAkbGFuZ3VhZ2VTZWxlY3Rvci5maW5kKCcuZHJvcGRvd24tdG9nZ2xlJykuaHRtbChsYWJlbCArICcgPHNwYW4gY2xhc3M9XCJjYXJldFwiPjwvc3Bhbj4nKTtcbiAgICAgICAgfVxuICAgICAgICAkYm9keS5kZWxlZ2F0ZSgnI2xhbmd1YWdlU2VsZWN0b3IgLmRyb3Bkb3duLW1lbnUgPiBsaSA+IGEnLCAnY2xpY2snLCBmdW5jdGlvbigpIHtcbiAgICAgICAgICAgIHZhciBsYWJlbCA9ICQodGhpcykuZGF0YSgnbGFiZWwnKTtcbiAgICAgICAgICAgICRsYW5ndWFnZVNlbGVjdG9yLmZpbmQoJy5kcm9wZG93bi10b2dnbGUnKS5odG1sKGxhYmVsICsgJyA8c3BhbiBjbGFzcz1cImNhcmV0XCI+PC9zcGFuPicpO1xuICAgICAgICB9KTtcblxuICAgICAgICBpZiAoY29uZmlnLmRlYnVnKVxuICAgICAgICAgICAgY29uc29sZS5sb2coJ0Rhc2hib2FyZDogY2xpY2sgYnkgYCNsYW5ndWFnZVNlbGVjdG9yYCcpO1xuXG4gICAgfVxuXG4gICAgLy8gQ2hhbmdpbmcgdGhlIHZpc2liaWxpdHkvaGlkaW5nIG9mIHR5cGVkIHBhc3N3b3JkXG4gICAgJGJvZHkuZGVsZWdhdGUoJyNzaG93SW5wdXRQYXNzd29yZCcsICdjbGljaycsIGZ1bmN0aW9uICgpIHtcbiAgICAgICAgdmFyICRwYXNzd29yZElucHV0ID0gJCh0aGlzKS5wcmV2KCdpbnB1dFt0eXBlXScpO1xuICAgICAgICBpZiAoJHBhc3N3b3JkSW5wdXQuYXR0cigndHlwZScpID09IFwicGFzc3dvcmRcIikge1xuICAgICAgICAgICAgJHBhc3N3b3JkSW5wdXQuYXR0cigndHlwZScsIFwidGV4dFwiKTtcbiAgICAgICAgICAgICQodGhpcykuZmluZCgnc3Bhbi5mYScpLnRvZ2dsZUNsYXNzKCdmYS1leWUgZmEtZXllLXNsYXNoJyk7XG4gICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAkcGFzc3dvcmRJbnB1dC5hdHRyKCd0eXBlJywgXCJwYXNzd29yZFwiKTtcbiAgICAgICAgICAgICQodGhpcykuZmluZCgnc3Bhbi5mYScpLnRvZ2dsZUNsYXNzKCdmYS1leWUtc2xhc2ggZmEtZXllJyk7XG4gICAgICAgIH1cbiAgICB9KTtcblxuICAgIC8qKlxuICAgICAqIENoYW5nZSBvZiBjdXJyZW50IHByb2dyZXNzIGZvciBwcm9ncmVzcyBiYXJzXG4gICAgICpcbiAgICAgKiBAcHVibGljXG4gICAgICogQHBhcmFtIHtTdHJpbmcvT2JqZWN0fSBzZWxlY3RvciAtIFNlbGVjdG9yIG9mIEJvb3RzdHJhcC5Qcm9ncmVzc2Jhci5cbiAgICAgKiBAcGFyYW0ge0ludGVnZXJ9IHZhbHVlbm93IC0gVmFsdWUgd2lsbCBiZSBjaGFuZ2VkLlxuICAgICAqIEBwYXJhbSB7Qm9vbGVhbn0gYXBwZW5kIC0gRmxhZyBvZiBhcHBlbmQgb3Igc2V0IGN1cnJlbnQgdmFsdWUgb2YgcHJvZ3Jlc3MuXG4gICAgICovXG4gICAgZnVuY3Rpb24gc2V0UHJvZ3Jlc3Moc2VsZWN0b3IsIHZhbHVlbm93LCBhcHBlbmQgPSBmYWxzZSkge1xuXG4gICAgICAgIGlmICh0eXBlb2Ygc2VsZWN0b3IgPT09IFwib2JqZWN0XCIpXG4gICAgICAgICAgICB2YXIgJHByb2dyZXNzID0gc2VsZWN0b3I7XG4gICAgICAgIGVsc2UgaWYgKHR5cGVvZiBzZWxlY3RvciA9PT0gXCJzdHJpbmdcIilcbiAgICAgICAgICAgIHZhciAkcHJvZ3Jlc3MgPSAkKHNlbGVjdG9yKTtcblxuICAgICAgICB2YXIgJHByb2dyZXNzQmFyID0gJHByb2dyZXNzLmZpbmQoJy5wcm9ncmVzcy1iYXInKTtcbiAgICAgICAgdmFyIGN1cnJlbnQgPSAkcHJvZ3Jlc3NCYXIuYXR0cihcImFyaWEtdmFsdWVub3dcIik7XG5cbiAgICAgICAgaWYgKGFwcGVuZCkge1xuICAgICAgICAgICAgdmFyIHN0ZXBzID0gMTA7XG4gICAgICAgICAgICB2YXIgdmFsdWUgPSAodmFsdWVub3cgLyBzdGVwcyk7XG4gICAgICAgICAgICB2YXIgaW50ZXJ2YWwgPSBzZXRJbnRlcnZhbChmdW5jdGlvbigpIHtcbiAgICAgICAgICAgICAgICBjdXJyZW50ICs9IHZhbHVlO1xuICAgICAgICAgICAgICAgICRwcm9ncmVzc0Jhci5jc3MoXCJ3aWR0aFwiLCBjdXJyZW50ICsgXCIlXCIpLmF0dHIoXCJhcmlhLXZhbHVlbm93XCIsIGN1cnJlbnQpO1xuXG4gICAgICAgICAgICAgICAgaWYgKCRwcm9ncmVzc0Jhci5maW5kKCdzcGFuJykubGVuZ3RoID4gMClcbiAgICAgICAgICAgICAgICAgICAgJHByb2dyZXNzQmFyLmZpbmQoJ3NwYW4nKS50ZXh0KGN1cnJlbnQgKyBcIiUgQ29tcGxldGVcIik7XG5cbiAgICAgICAgICAgICAgICBpZiAoY3VycmVudCA+PSAxMDAgfHwgc3RlcHMgPT0gMClcbiAgICAgICAgICAgICAgICAgICAgY2xlYXJJbnRlcnZhbChpbnRlcnZhbCk7XG5cbiAgICAgICAgICAgICAgICBzdGVwcy0tO1xuICAgICAgICAgICAgfSwgMTAwKTtcblxuICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgY3VycmVudCA9IHZhbHVlbm93O1xuICAgICAgICAgICAgJHByb2dyZXNzQmFyLmNzcyhcIndpZHRoXCIsIGN1cnJlbnQgKyBcIiVcIikuYXR0cihcImFyaWEtdmFsdWVub3dcIiwgY3VycmVudCk7XG5cbiAgICAgICAgICAgIGlmICgkcHJvZ3Jlc3NCYXIuZmluZCgnc3BhbicpLmxlbmd0aCA+IDApXG4gICAgICAgICAgICAgICAgJHByb2dyZXNzQmFyLmZpbmQoJ3NwYW4nKS50ZXh0KGN1cnJlbnQgKyBcIiUgQ29tcGxldGVcIik7XG5cbiAgICAgICAgfVxuICAgIH1cblxuICAgIC8vIFRyYWNraW5nIHBhZ2UgbG9hZGluZyBldmVudHMgd2l0aCBwQWpheFxuICAgICQoZG9jdW1lbnQpLm9uKHtcbiAgICAgICAgJ3BqYXg6c3RhcnQnOiBmdW5jdGlvbiAoZXZlbnQpIHtcblxuICAgICAgICAgICAgaWYgKGNvbmZpZy5hamF4UHJvZ3Jlc3MpIHtcbiAgICAgICAgICAgICAgICBzZXRQcm9ncmVzcygkcmVxdWVzdFByb2dyZXNzLCAwKTtcbiAgICAgICAgICAgICAgICAkcmVxdWVzdFByb2dyZXNzLnNob3coKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgaWYgKGNvbmZpZy5kZWJ1ZylcbiAgICAgICAgICAgICAgICBjb25zb2xlLmxvZygnRGFzaGJvYXJkOiBwamF4IGNoYW5nZSBzdGF0ZSB0byBgc3RhcnRgJyk7XG5cbiAgICAgICAgfSxcbiAgICAgICAgJ3BqYXg6YmVmb3JlU2VuZCc6IGZ1bmN0aW9uIChldmVudCkge1xuXG4gICAgICAgICAgICBpZiAoY29uZmlnLmFqYXhQcm9ncmVzcylcbiAgICAgICAgICAgICAgICBzZXRQcm9ncmVzcygkcmVxdWVzdFByb2dyZXNzLCAxNSk7XG5cbiAgICAgICAgICAgIGlmIChjb25maWcuZGVidWcpXG4gICAgICAgICAgICAgICAgY29uc29sZS5sb2coJ0Rhc2hib2FyZDogcGpheCBjaGFuZ2Ugc3RhdGUgdG8gYGJlZm9yZVNlbmRgJyk7XG5cbiAgICAgICAgfSxcbiAgICAgICAgJ3BqYXg6c2VuZCc6IGZ1bmN0aW9uIChldmVudCkge1xuXG4gICAgICAgICAgICBpZiAoY29uZmlnLmFqYXhGYWRlKVxuICAgICAgICAgICAgICAgICQodGhpcykuYXR0cignZGF0YS1wamF4LXN0YXRlJywgXCJzZW5kXCIpO1xuXG4gICAgICAgICAgICBpZiAoY29uZmlnLnNwaW5uZXIpXG4gICAgICAgICAgICAgICAgJCh0aGlzKS5hcHBlbmQoJHNwaW5uZXIpO1xuXG4gICAgICAgICAgICBpZiAoY29uZmlnLmFqYXhQcm9ncmVzcylcbiAgICAgICAgICAgICAgICBzZXRQcm9ncmVzcygkcmVxdWVzdFByb2dyZXNzLCAzNSk7XG5cbiAgICAgICAgICAgIGlmIChjb25maWcuZGVidWcpXG4gICAgICAgICAgICAgICAgY29uc29sZS5sb2coJ0Rhc2hib2FyZDogcGpheCBjaGFuZ2Ugc3RhdGUgdG8gYHNlbmRgJyk7XG5cbiAgICAgICAgfSxcbiAgICAgICAgJ3BqYXg6YmVmb3JlUmVwbGFjZSc6IGZ1bmN0aW9uIChldmVudCkge1xuXG4gICAgICAgICAgICBpZiAoY29uZmlnLmFqYXhQcm9ncmVzcylcbiAgICAgICAgICAgICAgICBzZXRQcm9ncmVzcygkcmVxdWVzdFByb2dyZXNzLCA3NSk7XG5cbiAgICAgICAgICAgIGlmIChjb25maWcuZGVidWcpXG4gICAgICAgICAgICAgICAgY29uc29sZS5sb2coJ0Rhc2hib2FyZDogcGpheCBjaGFuZ2Ugc3RhdGUgdG8gYGJlZm9yZVJlcGxhY2VgJyk7XG5cbiAgICAgICAgfSxcbiAgICAgICAgJ3BqYXg6Y29tcGxldGUnOiBmdW5jdGlvbiAoZXZlbnQpIHtcblxuICAgICAgICAgICAgaWYgKGNvbmZpZy5hamF4RmFkZSlcbiAgICAgICAgICAgICAgICAkKHRoaXMpLmF0dHIoJ2RhdGEtcGpheC1zdGF0ZScsIFwiY29tcGxldGVcIik7XG5cbiAgICAgICAgICAgIGlmIChjb25maWcuYWpheFByb2dyZXNzKSB7XG4gICAgICAgICAgICAgICAgc2V0UHJvZ3Jlc3MoJHJlcXVlc3RQcm9ncmVzcywgMTAwKTtcbiAgICAgICAgICAgICAgICBzZXRUaW1lb3V0KGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICAgICAgJHJlcXVlc3RQcm9ncmVzcy5oaWRlKCk7XG4gICAgICAgICAgICAgICAgfSwgMTIwMCk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGlmICgkd2VsY29tZVNjcmVlbi5maW5kKCcjbGFuZ3VhZ2VTZWxlY3RvcicpLmxlbmd0aCA+IDAgfHwgJGRhc2hib2FyZC5maW5kKCcjbGFuZ3VhZ2VTZWxlY3RvcicpLmxlbmd0aCA+IDApIHtcbiAgICAgICAgICAgICAgICB2YXIgJGxhbmd1YWdlU2VsZWN0b3IgPSAkKCcjbGFuZ3VhZ2VTZWxlY3RvcicpO1xuICAgICAgICAgICAgICAgIGlmICgkbGFuZ3VhZ2VTZWxlY3Rvci5maW5kKCcuZHJvcGRvd24tbWVudSA+IGxpLmFjdGl2ZScpLmxlbmd0aCA+IDApIHtcbiAgICAgICAgICAgICAgICAgICAgdmFyIGxhYmVsID0gJGxhbmd1YWdlU2VsZWN0b3IuZmluZCgnLmRyb3Bkb3duLW1lbnUgPiBsaS5hY3RpdmUgPiBhJykuZGF0YSgnbGFiZWwnKTtcbiAgICAgICAgICAgICAgICAgICAgJGxhbmd1YWdlU2VsZWN0b3IuZmluZCgnLmRyb3Bkb3duLXRvZ2dsZScpLmh0bWwobGFiZWwgKyAnIDxzcGFuIGNsYXNzPVwiY2FyZXRcIj48L3NwYW4+Jyk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICBpZiAoY29uZmlnLmRlYnVnKVxuICAgICAgICAgICAgICAgIGNvbnNvbGUubG9nKCdEYXNoYm9hcmQ6IHBqYXggY2hhbmdlIHN0YXRlIHRvIGBjb21wbGV0ZWAnKTtcblxuICAgICAgICB9XG4gICAgfSk7XG5cblxuICAgIC8vIFNob3cvaGlkZSBkcm9wZG93biBpbiBtYWlubmF2IG9uIGhvdmVyXG4gICAgaWYgKGNvbmZpZy5tYWlubmF2LmV4cGFuZE9uSG92ZXIpIHtcbiAgICAgICAgJG1haW5OYXYuZmluZChcIi5kcm9wZG93blwiKS5lYWNoKGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgIHZhciAkdGhpcyA9ICQodGhpcyk7XG4gICAgICAgICAgICAkdGhpcy5jbGljayhmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAgICAgaWYgKCEkKHRoaXMpLmZpbmQoJy5kcm9wZG93bi1tZW51JykuaXMoJzp2aXNpYmxlJykpIHtcbiAgICAgICAgICAgICAgICAgICAgJCh0aGlzKS5maW5kKCcuZHJvcGRvd24tbWVudScpLnN0b3AodHJ1ZSwgdHJ1ZSkuc2xpZGVUb2dnbGUoXCJmYXN0XCIpO1xuXG4gICAgICAgICAgICAgICAgICAgIGlmIChjb25maWcuZGVidWcpXG4gICAgICAgICAgICAgICAgICAgICAgICBjb25zb2xlLmxvZygnRGFzaGJvYXJkOiBkcm9wZG93biBpbiBtYWlubmF2IGlzIHZpc2libGUgYnkgY2xpY2snKTtcblxuICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgICQodGhpcykuZmluZCgnLmRyb3Bkb3duLW1lbnUnKS5zdG9wKHRydWUsIHRydWUpLnNsaWRlVXAoXCJmYXN0XCIpO1xuXG4gICAgICAgICAgICAgICAgICAgIGlmIChjb25maWcuZGVidWcpXG4gICAgICAgICAgICAgICAgICAgICAgICBjb25zb2xlLmxvZygnRGFzaGJvYXJkOiBkcm9wZG93biBpbiBtYWlubmF2IGlzIGhpZGRpbmcgYnkgY2xpY2snKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9KTtcbiAgICAgICAgICAgICR0aGlzLmhvdmVyKGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICB2YXIgJGRyb3Bkb3duID0gJCh0aGlzKTtcbiAgICAgICAgICAgICAgICBpZiAoISRkcm9wZG93bi5maW5kKCcuZHJvcGRvd24tbWVudScpLmlzKCc6dmlzaWJsZScpKSB7XG4gICAgICAgICAgICAgICAgICAgICRkcm9wZG93bi5maW5kKCcuZHJvcGRvd24tbWVudScpLnN0b3AodHJ1ZSwgdHJ1ZSkuZGVsYXkoMzAwKS5zbGlkZVRvZ2dsZShcImZhc3RcIik7XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgaWYgKGNvbmZpZy5kZWJ1ZylcbiAgICAgICAgICAgICAgICAgICAgY29uc29sZS5sb2coJ0Rhc2hib2FyZDogZHJvcGRvd24gaW4gbWFpbm5hdiBpcyB2aXNpYmxlIGJ5IGhvdmVyJyk7XG5cbiAgICAgICAgICAgIH0sIGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICB2YXIgJGRyb3Bkb3duID0gJCh0aGlzKTtcbiAgICAgICAgICAgICAgICBpZiAoJGRyb3Bkb3duLmZpbmQoJy5kcm9wZG93bi1tZW51JykuaXMoJzp2aXNpYmxlJykpIHtcbiAgICAgICAgICAgICAgICAgICAgJGRyb3Bkb3duLmZpbmQoJy5kcm9wZG93bi1tZW51Jykuc3RvcCh0cnVlLCB0cnVlKS5kZWxheSgxMDApLnNsaWRlVXAoXCJmYXN0XCIpO1xuXG4gICAgICAgICAgICAgICAgICAgIGlmIChjb25maWcuZGVidWcpXG4gICAgICAgICAgICAgICAgICAgICAgICBjb25zb2xlLmxvZygnRGFzaGJvYXJkOiBkcm9wZG93biBpbiBtYWlubmF2IGlzIGhpZGRpbmcgYnkgaG92ZXInKTtcblxuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0pO1xuICAgICAgICB9KTtcbiAgICB9XG5cbiAgICAvLyBBZG1pbiBzaWRlYmFyIG1lbnUgbWFuYWdlbWVudFxuICAgIGlmICgkc2lkZWJhck5hdi5sZW5ndGggPiAwKSB7XG5cbiAgICAgICAgLy8gRGlzYWJsZSBjbGljayBvbiBkcm9wZG93biBlbGVtZW50IHdpdGggZW1wdHkgbGlua1xuICAgICAgICAkc2lkZWJhck5hdi5maW5kKCcuZHJvcGRvd24tbWVudSA+IGxpID4gYVtocmVmPVwiI1wiXScpLm9uKCdjbGljaycsIGZ1bmN0aW9uIChldmVudCkge1xuICAgICAgICAgICAgZXZlbnQucHJldmVudERlZmF1bHQoKTtcbiAgICAgICAgICAgIGV2ZW50LnN0b3BQcm9wYWdhdGlvbigpO1xuXG4gICAgICAgICAgICBpZiAoY29uZmlnLmRlYnVnKVxuICAgICAgICAgICAgICAgIGNvbnNvbGUubG9nKCdEYXNoYm9hcmQ6IGNsaWNrIGJ5IGAuZHJvcGRvd24tbWVudSA+IGxpID4gYVtocmVmPVwiI1wiXWAgaW4gc2lkZWJhcicpO1xuXG4gICAgICAgIH0pO1xuXG4gICAgICAgIC8vIERpc2FibGUgY2xpY2sgb24gcG9wb3ZlciBlbGVtZW50XG4gICAgICAgICRzaWRlYmFyTmF2LmZpbmQoJy5kcm9wZG93bi1zdWJtZW51ID4gYScpLm9uKCdjbGljaycsIGZ1bmN0aW9uIChldmVudCkge1xuICAgICAgICAgICAgZXZlbnQucHJldmVudERlZmF1bHQoKTtcbiAgICAgICAgICAgIGV2ZW50LnN0b3BQcm9wYWdhdGlvbigpO1xuXG4gICAgICAgICAgICBpZiAoY29uZmlnLmRlYnVnKVxuICAgICAgICAgICAgICAgIGNvbnNvbGUubG9nKCdEYXNoYm9hcmQ6IGNsaWNrIGJ5IGAuZHJvcGRvd24tc3VibWVudSA+IGFgIGluIHNpZGViYXInKTtcblxuICAgICAgICB9KTtcblxuICAgICAgICAvLyBTaG93L2hpZGUgZHJvcGRvd24gaW4gc2lkZWJhciBvbiBob3ZlclxuICAgICAgICBpZiAoY29uZmlnLnNpZGViYXIuZXhwYW5kT25Ib3Zlcikge1xuICAgICAgICAgICAgJHNpZGViYXJOYXYuZmluZChcIi5kcm9wZG93blwiKS5lYWNoKGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICB2YXIgJHRoaXMgPSAkKHRoaXMpO1xuICAgICAgICAgICAgICAgICR0aGlzLmNsaWNrKGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICAgICAgaWYgKCEkKHRoaXMpLmZpbmQoJy5kcm9wZG93bi1tZW51JykuaXMoJzp2aXNpYmxlJykpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICQodGhpcykuZmluZCgnLmRyb3Bkb3duLW1lbnUnKS5zdG9wKHRydWUsIHRydWUpLnNsaWRlVG9nZ2xlKFwiZmFzdFwiKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICQodGhpcykuZmluZCgnLmRyb3Bkb3duLXRvZ2dsZSAuZmEtYW5nbGUtZG93bicpLnJlbW92ZUNsYXNzKCdmYS1hbmdsZS1kb3duJykuYWRkQ2xhc3MoJ2ZhLWFuZ2xlLXVwJyk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIGlmIChjb25maWcuZGVidWcpXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgY29uc29sZS5sb2coJ0Rhc2hib2FyZDogZHJvcGRvd24gaW4gc2lkZWJhciBpcyB2aXNpYmxlIGJ5IGNsaWNrJyk7XG5cbiAgICAgICAgICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICQodGhpcykuZmluZCgnLmRyb3Bkb3duLW1lbnUnKS5zdG9wKHRydWUsIHRydWUpLnNsaWRlVXAoXCJmYXN0XCIpO1xuICAgICAgICAgICAgICAgICAgICAgICAgJCh0aGlzKS5maW5kKCcuZHJvcGRvd24tdG9nZ2xlIC5mYS1hbmdsZS11cCcpLnJlbW92ZUNsYXNzKCdmYS1hbmdsZS11cCcpLmFkZENsYXNzKCdmYS1hbmdsZS1kb3duJyk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIGlmIChjb25maWcuZGVidWcpXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgY29uc29sZS5sb2coJ0Rhc2hib2FyZDogZHJvcGRvd24gaW4gc2lkZWJhciBpcyBoaWRkaW5nIGJ5IGNsaWNrJyk7XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgJHNpZGViYXJOYXYuZmluZChcIi5kcm9wZG93bjpub3QoLmFjdGl2ZSk6bm90KDpob3ZlcilcIikuZmluZCgnLmRyb3Bkb3duLW1lbnUnKS5zbGlkZVVwKFwiZmFzdFwiKTtcbiAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgICAgICAkdGhpcy5ob3ZlcihmdW5jdGlvbiAoKSB7XG5cbiAgICAgICAgICAgICAgICAgICAgdmFyICRkcm9wZG93biA9ICQodGhpcyk7XG4gICAgICAgICAgICAgICAgICAgIGlmICghJGRyb3Bkb3duLmZpbmQoJy5kcm9wZG93bi1tZW51JykuaXMoJzp2aXNpYmxlJykpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICRkcm9wZG93bi5maW5kKCcuZHJvcGRvd24tbWVudScpLnN0b3AodHJ1ZSwgdHJ1ZSkuZGVsYXkoNTAwKS5zbGlkZVRvZ2dsZShcImZhc3RcIik7XG4gICAgICAgICAgICAgICAgICAgICAgICBzZXRUaW1lb3V0KGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICRkcm9wZG93bi5maW5kKCcuZHJvcGRvd24tdG9nZ2xlIC5mYS1hbmdsZS1kb3duJykucmVtb3ZlQ2xhc3MoJ2ZhLWFuZ2xlLWRvd24nKS5hZGRDbGFzcygnZmEtYW5nbGUtdXAnKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH0sIDIwMCk7XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgJHNpZGViYXJOYXYuZmluZChcIi5kcm9wZG93bjpub3QoLmFjdGl2ZSk6bm90KDpob3ZlcilcIikuZmluZCgnLmRyb3Bkb3duLW1lbnUnKS5zbGlkZVVwKFwiZmFzdFwiKTtcblxuICAgICAgICAgICAgICAgICAgICBpZiAoY29uZmlnLmRlYnVnKVxuICAgICAgICAgICAgICAgICAgICAgICAgY29uc29sZS5sb2coJ0Rhc2hib2FyZDogZHJvcGRvd24gaW4gc2lkZWJhciBpcyB2aXNpYmxlIGJ5IGhvdmVyJyk7XG5cbiAgICAgICAgICAgICAgICB9LCBmdW5jdGlvbiAoKSB7XG5cbiAgICAgICAgICAgICAgICAgICAgdmFyICRkcm9wZG93biA9ICQodGhpcyk7XG5cbiAgICAgICAgICAgICAgICAgICAgaWYgKCEkZHJvcGRvd24uaGFzQ2xhc3MoJ3BvcG92ZXItc2hvdycpKSB7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICRkcm9wZG93bi5maW5kKCcuZHJvcGRvd24tbWVudScpLnN0b3AodHJ1ZSwgdHJ1ZSkuZGVsYXkoMjAwKS5zbGlkZVVwKFwiZmFzdFwiKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIHNldFRpbWVvdXQoZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgJGRyb3Bkb3duLmZpbmQoJy5kcm9wZG93bi10b2dnbGUgLmZhLWFuZ2xlLXVwJykucmVtb3ZlQ2xhc3MoJ2ZhLWFuZ2xlLXVwJykuYWRkQ2xhc3MoJ2ZhLWFuZ2xlLWRvd24nKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH0sIDIwMCk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIGlmIChjb25maWcuZGVidWcpXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgY29uc29sZS5sb2coJ0Rhc2hib2FyZDogZHJvcGRvd24gaW4gc2lkZWJhciBpcyBoaWRkaW5nIGJ5IGhvdmVyJyk7XG5cbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICAvLyBGaXhlZDogRHJvcGRvd24gbWVudSBoaWRkaW5nIGJ5IHBvcG92ZXIgaXMgc2hvd1xuICAgICAgICAgICAgICAgICAgICAvLyRzaWRlYmFyTmF2LmZpbmQoXCIuZHJvcGRvd246bm90KC5hY3RpdmUpOm5vdCg6aG92ZXIpXCIpLmZpbmQoJy5kcm9wZG93bi1tZW51Jykuc2xpZGVVcChcImZhc3RcIik7XG4gICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICB9KTtcbiAgICAgICAgICAgICRzaWRlYmFyTmF2LmZpbmQoXCIuZHJvcGRvd24uYWN0aXZlXCIpLmZpbmQoJy5kcm9wZG93bi10b2dnbGUgLmZhLWFuZ2xlLWRvd24nKS50b2dnbGVDbGFzcygnZmEtYW5nbGUtZG93biBmYS1hbmdsZS11cCcpO1xuICAgICAgICAgICAgJHNpZGViYXJOYXYuZmluZChcIi5kcm9wZG93bi5hY3RpdmUgLmRyb3Bkb3duLXRvZ2dsZVwiKS5jbGljaygpO1xuICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgJHNpZGViYXJOYXYuZmluZChcIi5kcm9wZG93blwiKS5vbignc2hvd24uYnMuZHJvcGRvd24nLCBmdW5jdGlvbihldmVudCkge1xuICAgICAgICAgICAgICAgICQoZXZlbnQudGFyZ2V0KS5maW5kKCcuZHJvcGRvd24tdG9nZ2xlIC5mYS1hbmdsZS1kb3duJykudG9nZ2xlQ2xhc3MoJ2ZhLWFuZ2xlLWRvd24gZmEtYW5nbGUtdXAnKTtcbiAgICAgICAgICAgIH0pLm9uKCdoaWRkZW4uYnMuZHJvcGRvd24nLCBmdW5jdGlvbihldmVudCkge1xuICAgICAgICAgICAgICAgICQoZXZlbnQudGFyZ2V0KS5maW5kKCcuZHJvcGRvd24tdG9nZ2xlIC5mYS1hbmdsZS11cCcpLnRvZ2dsZUNsYXNzKCdmYS1hbmdsZS11cCBmYS1hbmdsZS1kb3duJyk7XG4gICAgICAgICAgICB9KTtcbiAgICAgICAgICAgIC8vJHNpZGViYXJOYXYuZmluZChcIi5kcm9wZG93bi5hY3RpdmVcIikuZHJvcGRvd24oJ3RvZ2dsZScpO1xuICAgICAgICAgICAgJHNpZGViYXJOYXYuZmluZChcIi5kcm9wZG93bi5hY3RpdmUgLmRyb3Bkb3duLXRvZ2dsZVwiKS5jbGljaygpO1xuICAgICAgICB9XG5cbiAgICAgICAgLy8gSW5pdCBwb3BvdmVyIG1lbnUgaW4gc2lkZWJhclxuICAgICAgICAkc2lkZWJhck5hdi5maW5kKCcuZHJvcGRvd24tc3VibWVudSA+IGEnKS5lYWNoKGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgdmFyICR0aGlzID0gJCh0aGlzKTtcbiAgICAgICAgICAgIHZhciAkZHJvcGRvd24gPSAkKHRoaXMpLnBhcmVudHMoJy5kcm9wZG93bicpO1xuXG4gICAgICAgICAgICB2YXIgdHJpZ2dlciA9ICdjbGljayc7XG4gICAgICAgICAgICBpZiAoY29uZmlnLnNpZGViYXIuZXhwYW5kT25Ib3ZlcilcbiAgICAgICAgICAgICAgICB0cmlnZ2VyID0gJ21hbnVhbCc7XG5cbiAgICAgICAgICAgICR0aGlzLnBvcG92ZXIoe1xuICAgICAgICAgICAgICAgIHBsYWNlbWVudDogJ2F1dG8gcmlnaHQnLFxuICAgICAgICAgICAgICAgIHRyaWdnZXI6IHRyaWdnZXIsXG4gICAgICAgICAgICAgICAgY29udGFpbmVyOiAnYm9keScsXG4gICAgICAgICAgICAgICAgdGl0bGU6IGZhbHNlLFxuICAgICAgICAgICAgICAgIGh0bWw6IHRydWUsXG4gICAgICAgICAgICAgICAgdGVtcGxhdGU6ICc8ZGl2IGNsYXNzPVwicG9wb3ZlciBuYXYtcG9wb3ZlclwiIHJvbGU9XCJ0b29sdGlwXCI+PGRpdiBjbGFzcz1cImFycm93XCI+PC9kaXY+PGRpdiBjbGFzcz1cInBvcG92ZXItY29udGVudFwiPjwvZGl2PjwvZGl2PicsXG4gICAgICAgICAgICAgICAgY29udGVudDogZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICAgICAgICAgIHJldHVybiAkdGhpcy5wYXJlbnQoJy5kcm9wZG93bi1zdWJtZW51JykuZmluZCgndWwnKS5hZGRDbGFzcygnbmF2Jykub3V0ZXJIdG1sKCk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgIGlmIChjb25maWcuc2lkZWJhci5leHBhbmRPbkhvdmVyKSB7XG4gICAgICAgICAgICAgICAgJHRoaXMub24oXCJtb3VzZWVudGVyXCIsIGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICAgICAgdmFyIF90aGlzID0gdGhpcztcbiAgICAgICAgICAgICAgICAgICAgJCh0aGlzKS5wb3BvdmVyKFwic2hvd1wiKTtcbiAgICAgICAgICAgICAgICAgICAgJGRyb3Bkb3duLmFkZENsYXNzKCdwb3BvdmVyLXNob3cnKTtcblxuICAgICAgICAgICAgICAgICAgICAkKFwiLm5hdi1wb3BvdmVyXCIpLm9uKFwibW91c2VsZWF2ZVwiLCBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAkKF90aGlzKS5wb3BvdmVyKCdoaWRlJyk7XG4gICAgICAgICAgICAgICAgICAgICAgICAkZHJvcGRvd24ucmVtb3ZlQ2xhc3MoJ3BvcG92ZXItc2hvdycpO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICBpZiAoY29uZmlnLmRlYnVnKVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGNvbnNvbGUubG9nKCdEYXNoYm9hcmQ6IHNpZGViYXIgcG9wb3ZlciBpcyBoaWRkaW5nIGJ5IG1vdXNlbGVhdmUnKTtcblxuICAgICAgICAgICAgICAgICAgICB9KS5vbihcIm1vdXNlZG93blwiLCBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAkKF90aGlzKS5wb3BvdmVyKCdoaWRlJyk7XG4gICAgICAgICAgICAgICAgICAgICAgICAkZHJvcGRvd24ucmVtb3ZlQ2xhc3MoJ3BvcG92ZXItc2hvdycpO1xuXG4gICAgICAgICAgICAgICAgICAgICAgICBpZiAoY29uZmlnLmRlYnVnKVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGNvbnNvbGUubG9nKCdEYXNoYm9hcmQ6IHNpZGViYXIgcG9wb3ZlciBpcyBoaWRkaW5nIGJ5IG1vdXNlZG93bicpO1xuICAgICAgICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgICAgICAgICBpZiAoY29uZmlnLmRlYnVnKVxuICAgICAgICAgICAgICAgICAgICAgICAgY29uc29sZS5sb2coJ0Rhc2hib2FyZDogc2lkZWJhciBwb3BvdmVyIGlzIHZpc2libGUgYnkgbW91c2VlbnRlcicpO1xuXG4gICAgICAgICAgICAgICAgfSkub24oXCJtb3VzZWxlYXZlXCIsIGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICAgICAgdmFyIF90aGlzID0gdGhpcztcbiAgICAgICAgICAgICAgICAgICAgc2V0VGltZW91dChmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBpZiAoISQoXCIubmF2LXBvcG92ZXI6aG92ZXJcIikubGVuZ3RoKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgJChfdGhpcykucG9wb3ZlcignaGlkZScpO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICRkcm9wZG93bi5yZW1vdmVDbGFzcygncG9wb3Zlci1zaG93Jyk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBpZiAoY29uZmlnLmRlYnVnKVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBjb25zb2xlLmxvZygnRGFzaGJvYXJkOiBzaWRlYmFyIHBvcG92ZXIgaXMgaGlkZGluZyBieSBtb3VzZWxlYXZlIGFuZCB0aW1lb3V0Jyk7XG4gICAgICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgIH0sIDIwMCk7XG4gICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgfSk7XG5cbiAgICAgICAgLy8gQWRkIHNpZGViYXIgbmF2IHRvIG1haW4gbmF2YmFyIGZvciBzbSBhbmQgeHMgZGlzcGxheXNcbiAgICAgICAgaWYgKHZpZXdwb3J0LndpZHRoIDw9IGJyZWFrcG9pbnRzLnNtKSB7XG4gICAgICAgICAgICB2YXIgJHNpZGViYXIgPSAkc2lkZWJhck5hdi5jbG9uZSgpO1xuICAgICAgICAgICAgJHNpZGViYXIuYXR0cignY2xhc3MnLCAnbmF2IG5hdmJhci1uYXYgaGlkZGVuLW1kIGhpZGRlbi1sZycpO1xuICAgICAgICAgICAgJHNpZGViYXIuZmluZCgnbGknKS5lYWNoKGZ1bmN0aW9uKCkge1xuICAgICAgICAgICAgICAgICQodGhpcykuZmluZCgnLmZhLXN0YWNrJykucmVtb3ZlQ2xhc3MoJ2ZhLXN0YWNrJykucmVtb3ZlQ2xhc3MoJ2ZhLWxnJyk7XG4gICAgICAgICAgICAgICAgJCh0aGlzKS5maW5kKCcuZmEnKS5yZW1vdmVDbGFzcygnZmEtc3RhY2stMXgnKTtcbiAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgJGl0ZW1zID0gJHNpZGViYXIub3V0ZXJIdG1sKCk7XG4gICAgICAgICAgICAkbWFpbk5hdi5iZWZvcmUoJGl0ZW1zKTtcblxuICAgICAgICAgICAgaWYgKGNvbmZpZy5kZWJ1ZylcbiAgICAgICAgICAgICAgICBjb25zb2xlLmxvZygnRGFzaGJvYXJkOiBhZGRlZCBzaWRlYmFyIG5hdiB0byBtYWluIG5hdmJhciBmb3Igc20gYW5kIHhzIGRpc3BsYXlzJyk7XG4gICAgICAgIH1cbiAgICB9XG5cbiAgICAvLyBEcm9wZG93bmBzXG4gICAgJCgnYm9keScpLmRlbGVnYXRlKCcuZHJvcGRvd24tdG9nZ2xlLCBbZGF0YS10b2dnbGU9XCJkcm9wZG93blwiXScsICdjbGljaycsIGZ1bmN0aW9uIChldmVudCkge1xuICAgICAgICBpZiAoKCQoZG9jdW1lbnQpLndpZHRoKCkgPiBicmVha3BvaW50cy5zbSkgJiYgJCh0aGlzKS5pcyhcImFcIikpIHtcbiAgICAgICAgICAgIGV2ZW50LnByZXZlbnREZWZhdWx0KCk7XG4gICAgICAgICAgICB2YXIgdXJsID0gJCh0aGlzKS5hdHRyKCdocmVmJyk7XG4gICAgICAgICAgICBpZiAodXJsICE9PSAnIycpXG4gICAgICAgICAgICAgICAgd2luZG93LmxvY2F0aW9uLmhyZWYgPSB1cmw7XG4gICAgICAgIH1cblxuICAgICAgICBpZiAoY29uZmlnLmRlYnVnKVxuICAgICAgICAgICAgY29uc29sZS5sb2coJ0Rhc2hib2FyZDogY2xpY2sgb24gLmRyb3Bkb3duLXRvZ2dsZScpO1xuXG4gICAgfSk7XG4gICAgJCgnYm9keScpLmRlbGVnYXRlKCQoJy5kcm9wZG93bi10b2dnbGUsIFtkYXRhLXRvZ2dsZT1cImRyb3Bkb3duXCJdJykucGFyZW50KCksICdzaG93LmJzLmRyb3Bkb3duJywgZnVuY3Rpb24gKGV2ZW50KSB7XG4gICAgICAgIHZhciAkYnV0dG9uID0gJChldmVudC5yZWxhdGVkVGFyZ2V0KTtcbiAgICAgICAgdmFyICRkcm9wZG93biA9ICQoZXZlbnQudGFyZ2V0KS5maW5kKCcuZHJvcGRvd24tbWVudScpO1xuICAgICAgICB2YXIgdmlld3BvckhlaWdodCA9ICQoZG9jdW1lbnQpLmhlaWdodCgpO1xuICAgICAgICB2YXIgYnV0dG9uT2Zmc2V0ID0gJGJ1dHRvbi5vZmZzZXQoKS50b3AgKyAkYnV0dG9uLmhlaWdodCgpO1xuICAgICAgICB2YXIgZHJvcGRvd25IZWlnaHQgPSAkZHJvcGRvd24uaGVpZ2h0KCk7XG4gICAgICAgIHZhciBkcm9wZG93bk9mZnNldCA9IGJ1dHRvbk9mZnNldCArIGRyb3Bkb3duSGVpZ2h0O1xuXG4gICAgICAgIGlmIChkcm9wZG93bk9mZnNldCA+ICh2aWV3cG9ySGVpZ2h0IC0gNDUpICYmIChidXR0b25PZmZzZXQgLSA1NSkgPiBkcm9wZG93bkhlaWdodCkge1xuICAgICAgICAgICAgJChldmVudC50YXJnZXQpLmFkZENsYXNzKCdkcm9wdXAnKTtcbiAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICQoZXZlbnQudGFyZ2V0KS5yZW1vdmVDbGFzcygnZHJvcHVwJyk7XG4gICAgICAgIH1cbiAgICB9KTtcblxuICAgIC8vIEhvdCBrZXlzIGZvciBwYWdpbmF0aW9uXG4gICAgJCh3aW5kb3cpLmtleWRvd24oZnVuY3Rpb24oZXZlbnQpIHtcblxuICAgICAgICB2YXIgJHBhZ2luYXRpb24gPSAkZGFzaGJvYXJkLmZpbmQoJy5wYWdpbmF0aW9uJyk7XG4gICAgICAgIGxldCBjdHJsS2V5ID0gKGdldE9TKCkgPT0gXCJXaW5kb3dzXCIpID8gZXZlbnQuY3RybEtleSA6IChnZXRPUygpID09IFwiTWFjIE9TXCIpID8gZXZlbnQuYWx0S2V5IDogbnVsbDtcbiAgICAgICAgbGV0IGtleUNvZGUgPSBldmVudC5rZXlDb2RlID8gZXZlbnQua2V5Q29kZSA6IGV2ZW50LndoaWNoID8gZXZlbnQud2hpY2ggOiBudWxsO1xuXG4gICAgICAgIGlmIChjdHJsS2V5ICYmIGtleUNvZGUgJiYgJHBhZ2luYXRpb24ubGVuZ3RoID4gMCkge1xuICAgICAgICAgICAgZXZlbnQucHJldmVudERlZmF1bHQoKTtcblxuICAgICAgICAgICAgbGV0IGxpbmsgPSBudWxsO1xuICAgICAgICAgICAgc3dpdGNoIChrZXlDb2RlKSB7XG4gICAgICAgICAgICAgICAgY2FzZSAzNzpcbiAgICAgICAgICAgICAgICAgICAgbGluayA9ICRwYWdpbmF0aW9uLmZpbmQoJ2xpID4gYVtyZWw9XCJwcmV2XCJdLCBsaS5wcmV2ID4gYScpLmF0dHIoJ2hyZWYnKTtcbiAgICAgICAgICAgICAgICAgICAgYnJlYWs7XG4gICAgICAgICAgICAgICAgY2FzZSAzOTpcbiAgICAgICAgICAgICAgICAgICAgbGluayA9ICRwYWdpbmF0aW9uLmZpbmQoJ2xpID4gYVtyZWw9XCJuZXh0XCJdLCBsaS5uZXh0ID4gYScpLmF0dHIoJ2hyZWYnKTtcbiAgICAgICAgICAgICAgICAgICAgYnJlYWs7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGlmIChsaW5rKSB7XG5cbiAgICAgICAgICAgICAgICBsZXQgJHBqYXggPSAkcGFnaW5hdGlvbi5jbG9zZXN0KCdbZGF0YS1wamF4LWNvbnRhaW5lcl0nKTtcbiAgICAgICAgICAgICAgICBpZiAoJHBqYXgubGVuZ3RoID4gMCkge1xuXG4gICAgICAgICAgICAgICAgICAgIGxldCB0aW1lb3V0ID0gNTAwMDtcbiAgICAgICAgICAgICAgICAgICAgaWYgKCRwamF4LmRhdGEoXCJwamF4LXRpbWVvdXRcIikpXG4gICAgICAgICAgICAgICAgICAgICAgICB0aW1lb3V0ID0gcGFyc2VJbnQoJHBqYXguZGF0YShcInBqYXgtdGltZW91dFwiKSk7XG5cbiAgICAgICAgICAgICAgICAgICAgJC5wamF4LnJlbG9hZCh7XG4gICAgICAgICAgICAgICAgICAgICAgICBjb250YWluZXI6ICgkcGpheC5hdHRyKCdpZCcpKSA/ICcjJyArICRwamF4LmF0dHIoJ2lkJykgOiBudWxsLFxuICAgICAgICAgICAgICAgICAgICAgICAgdGltZW91dDogdGltZW91dCxcbiAgICAgICAgICAgICAgICAgICAgICAgIHVybDogbGlua1xuICAgICAgICAgICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgIGRvY3VtZW50LmxvY2F0aW9uID0gbGluaztcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9XG4gICAgICAgIH1cbiAgICB9KTtcblxuICAgIC8vIE1vZGFscyBhbmQgYnV0dG9ucyBsb2FkaW5nIHN0YXRlXG4gICAgJCgnYm9keScpLmRlbGVnYXRlKCdhLCBidXR0b24nLCAnY2xpY2snLCBmdW5jdGlvbihldmVudCkge1xuICAgICAgICBpZiAoJCh0aGlzKS5kYXRhKCd0b2dnbGUnKSA9PSBcIm1vZGFsXCIpIHtcbiAgICAgICAgICAgICQoJ2JvZHknKS5hZGRDbGFzcygnbG9hZGluZycpO1xuICAgICAgICB9IGVsc2UgaWYgKCQodGhpcykuZGF0YSgnbG9hZGluZy10ZXh0JykpIHtcblxuICAgICAgICAgICAgdmFyIGhhc0Vycm9ycyA9IGZhbHNlO1xuXG4gICAgICAgICAgICB2YXIgJGZvcm0gPSAkKGV2ZW50LnRhcmdldCkucGFyZW50cygnZm9ybTpmaXJzdCcpO1xuICAgICAgICAgICAgJGZvcm0uZmluZCgnaW5wdXRbYXJpYS1yZXF1aXJlZF0nKS5lYWNoKGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICBpZiAoJCh0aGlzKS52YWwoKS5sZW5ndGggPT0gMCkge1xuICAgICAgICAgICAgICAgICAgICBoYXNFcnJvcnMgPSB0cnVlO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAkZm9ybS5maW5kKCdbYXJpYS1pbnZhbGlkXScpLmVhY2goZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgICAgIGlmICgkKHRoaXMpLmF0dHIoJ2FyaWEtaW52YWxpZCcpID09IFwidHJ1ZVwiKSB7XG4gICAgICAgICAgICAgICAgICAgIGhhc0Vycm9ycyA9IHRydWU7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgIGlmICghaGFzRXJyb3JzKSB7XG4gICAgICAgICAgICAgICAgJCh0aGlzKS5hZGRDbGFzcygnbG9hZGluZycpO1xuICAgICAgICAgICAgICAgICQodGhpcykuYnV0dG9uKCdsb2FkaW5nJyk7XG4gICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICQodGhpcykucmVtb3ZlQ2xhc3MoJ2xvYWRpbmcnKTtcbiAgICAgICAgICAgICAgICAkKHRoaXMpLmJ1dHRvbigncmVzZXQnKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfVxuICAgIH0pO1xuICAgICQoJ2JvZHknKS5kZWxlZ2F0ZSgnLm1vZGFsJywgJ3Nob3cuYnMubW9kYWwnLCBmdW5jdGlvbigpIHtcbiAgICAgICAgJCgnYm9keScpLmFkZENsYXNzKCdsb2FkaW5nJyk7XG4gICAgfSk7XG4gICAgJCgnYm9keScpLmRlbGVnYXRlKCcubW9kYWwnLCAnc2hvd24uYnMubW9kYWwnLCBmdW5jdGlvbigpIHtcbiAgICAgICAgJCgnYm9keScpLnJlbW92ZUNsYXNzKCdsb2FkaW5nJyk7XG4gICAgfSk7XG4gICAgJCgnYm9keScpLmRlbGVnYXRlKCcubW9kYWwnLCAnaGlkZS5icy5tb2RhbCcsIGZ1bmN0aW9uKCkge1xuICAgICAgICAkKCdib2R5JykucmVtb3ZlQ2xhc3MoJ2xvYWRpbmcnKTtcbiAgICB9KTtcbiAgICAkKCdib2R5JykuZGVsZWdhdGUoJy5tb2RhbCcsICdsb2FkZWQuYnMubW9kYWwnLCBmdW5jdGlvbigpIHtcbiAgICAgICAgJCgnYm9keScpLnJlbW92ZUNsYXNzKCdsb2FkaW5nJyk7XG4gICAgfSk7XG5cbn0pOyJdfQ==
