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
      var label = $languageSelector.find('.dropdown-menu > li.active > a').first().text();
      $languageSelector.find('.dropdown-toggle').html(label + ' <span class="caret"></span>');
    }
    $body.delegate('#languageSelector .dropdown-menu > li > a', 'click', function() {
      var label = $(this).text();
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
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImFkbWluLmpzIl0sIm5hbWVzIjpbXSwibWFwcGluZ3MiOiJBQUFBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EiLCJmaWxlIjoiYWRtaW4uanMiLCJzb3VyY2VzQ29udGVudCI6WyIkKGRvY3VtZW50KS5yZWFkeShmdW5jdGlvbigpIHtcblxuICAgIC8vIENvbmZpZ3VyYXRpb25cbiAgICBjb25zdCBjb25maWcgPSB7XG4gICAgICAgIG1haW5uYXY6IHtcbiAgICAgICAgICAgIGV4cGFuZE9uSG92ZXI6IGZhbHNlXG4gICAgICAgIH0sXG4gICAgICAgIHNpZGViYXI6IHtcbiAgICAgICAgICAgIGV4cGFuZE9uSG92ZXI6IHRydWVcbiAgICAgICAgfSxcbiAgICAgICAgYWpheEZhZGU6IHRydWUsXG4gICAgICAgIGFqYXhQcm9ncmVzczogdHJ1ZSxcbiAgICAgICAgc3Bpbm5lcjogZmFsc2UsXG4gICAgICAgIGRlYnVnOiBmYWxzZVxuICAgIH07XG5cbiAgICAvLyBEZWZpbml0aW9uIG9mIHZhcmlhYmxlcyBhbmQgZWxlbWVudHNcbiAgICB2YXIgJGJvZHkgPSAkKCdib2R5Jyk7XG4gICAgdmFyICRkYXNoYm9hcmQgPSAkKCdib2R5LmRhc2hib2FyZCcpO1xuICAgIHZhciAkd2VsY29tZVNjcmVlbiA9ICQoJ2JvZHkud2VsY29tZScpO1xuICAgIHZhciAkcmVxdWVzdFByb2dyZXNzID0gJGRhc2hib2FyZC5maW5kKCcjcmVxdWVzdFByb2dyZXNzJyk7XG4gICAgdmFyICRzaWRlYmFyID0gJGRhc2hib2FyZC5maW5kKCcuc2lkZWJhcicpO1xuICAgIHZhciAkbWFpbk5hdiA9ICRkYXNoYm9hcmQuZmluZCgnI21haW5OYXYnKTtcbiAgICB2YXIgJHNpZGViYXJOYXYgPSAkc2lkZWJhci5maW5kKCcjc2lkZWJhck5hdicpO1xuICAgIHZhciAkc3Bpbm5lciA9ICQoJzxzdmcgY2xhc3M9XCJzcGlubmVyXCIgdmlld0JveD1cIjAgMCA1MCA1MFwiPjxjaXJjbGUgY2xhc3M9XCJwYXRoXCIgY3g9XCIyNVwiIGN5PVwiMjVcIiByPVwiMjBcIiBmaWxsPVwibm9uZVwiIHN0cm9rZS13aWR0aD1cIjVcIj48L2NpcmNsZT48L3N2Zz4nKTtcbiAgICB2YXIgdmlld3BvcnQgPSAkKHdpbmRvdykudmlld3BvcnQoKTtcbiAgICB2YXIgYnJlYWtwb2ludHMgPSB7XG4gICAgICAgIHhzOiA0ODAsXG4gICAgICAgIHNtOiA3NjgsXG4gICAgICAgIG1kOiA5OTIsXG4gICAgICAgIGxnOiAxMjAwXG4gICAgfTtcblxuICAgIC8vIExhbmd1YWdlIHNlbGVjdG9yIG9mIGFkbWluIGludGVyZmFjZVxuICAgIGlmICgkd2VsY29tZVNjcmVlbi5maW5kKCcjbGFuZ3VhZ2VTZWxlY3RvcicpLmxlbmd0aCA+IDAgfHwgJGRhc2hib2FyZC5maW5kKCcjbGFuZ3VhZ2VTZWxlY3RvcicpLmxlbmd0aCA+IDApIHtcbiAgICAgICAgdmFyICRsYW5ndWFnZVNlbGVjdG9yID0gJCgnI2xhbmd1YWdlU2VsZWN0b3InKTtcbiAgICAgICAgaWYgKCRsYW5ndWFnZVNlbGVjdG9yLmZpbmQoJy5kcm9wZG93bi1tZW51ID4gbGkuYWN0aXZlJykubGVuZ3RoID4gMCkge1xuICAgICAgICAgICAgdmFyIGxhYmVsID0gJGxhbmd1YWdlU2VsZWN0b3IuZmluZCgnLmRyb3Bkb3duLW1lbnUgPiBsaS5hY3RpdmUgPiBhJykuZmlyc3QoKS50ZXh0KCk7XG4gICAgICAgICAgICAkbGFuZ3VhZ2VTZWxlY3Rvci5maW5kKCcuZHJvcGRvd24tdG9nZ2xlJykuaHRtbChsYWJlbCArICcgPHNwYW4gY2xhc3M9XCJjYXJldFwiPjwvc3Bhbj4nKTtcbiAgICAgICAgfVxuICAgICAgICAkYm9keS5kZWxlZ2F0ZSgnI2xhbmd1YWdlU2VsZWN0b3IgLmRyb3Bkb3duLW1lbnUgPiBsaSA+IGEnLCAnY2xpY2snLCBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICB2YXIgbGFiZWwgPSAkKHRoaXMpLnRleHQoKTtcbiAgICAgICAgICAgICRsYW5ndWFnZVNlbGVjdG9yLmZpbmQoJy5kcm9wZG93bi10b2dnbGUnKS5odG1sKGxhYmVsICsgJyA8c3BhbiBjbGFzcz1cImNhcmV0XCI+PC9zcGFuPicpO1xuICAgICAgICB9KTtcblxuICAgICAgICBpZiAoY29uZmlnLmRlYnVnKVxuICAgICAgICAgICAgY29uc29sZS5sb2coJ0Rhc2hib2FyZDogY2xpY2sgYnkgYCNsYW5ndWFnZVNlbGVjdG9yYCcpO1xuXG4gICAgfVxuXG4gICAgLy8gQ2hhbmdpbmcgdGhlIHZpc2liaWxpdHkvaGlkaW5nIG9mIHR5cGVkIHBhc3N3b3JkXG4gICAgJGJvZHkuZGVsZWdhdGUoJyNzaG93SW5wdXRQYXNzd29yZCcsICdjbGljaycsIGZ1bmN0aW9uICgpIHtcbiAgICAgICAgdmFyICRwYXNzd29yZElucHV0ID0gJCh0aGlzKS5wcmV2KCdpbnB1dFt0eXBlXScpO1xuICAgICAgICBpZiAoJHBhc3N3b3JkSW5wdXQuYXR0cigndHlwZScpID09IFwicGFzc3dvcmRcIikge1xuICAgICAgICAgICAgJHBhc3N3b3JkSW5wdXQuYXR0cigndHlwZScsIFwidGV4dFwiKTtcbiAgICAgICAgICAgICQodGhpcykuZmluZCgnc3Bhbi5mYScpLnRvZ2dsZUNsYXNzKCdmYS1leWUgZmEtZXllLXNsYXNoJyk7XG4gICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAkcGFzc3dvcmRJbnB1dC5hdHRyKCd0eXBlJywgXCJwYXNzd29yZFwiKTtcbiAgICAgICAgICAgICQodGhpcykuZmluZCgnc3Bhbi5mYScpLnRvZ2dsZUNsYXNzKCdmYS1leWUtc2xhc2ggZmEtZXllJyk7XG4gICAgICAgIH1cbiAgICB9KTtcblxuICAgIC8qKlxuICAgICAqIENoYW5nZSBvZiBjdXJyZW50IHByb2dyZXNzIGZvciBwcm9ncmVzcyBiYXJzXG4gICAgICpcbiAgICAgKiBAcHVibGljXG4gICAgICogQHBhcmFtIHtTdHJpbmcvT2JqZWN0fSBzZWxlY3RvciAtIFNlbGVjdG9yIG9mIEJvb3RzdHJhcC5Qcm9ncmVzc2Jhci5cbiAgICAgKiBAcGFyYW0ge0ludGVnZXJ9IHZhbHVlbm93IC0gVmFsdWUgd2lsbCBiZSBjaGFuZ2VkLlxuICAgICAqIEBwYXJhbSB7Qm9vbGVhbn0gYXBwZW5kIC0gRmxhZyBvZiBhcHBlbmQgb3Igc2V0IGN1cnJlbnQgdmFsdWUgb2YgcHJvZ3Jlc3MuXG4gICAgICovXG4gICAgZnVuY3Rpb24gc2V0UHJvZ3Jlc3Moc2VsZWN0b3IsIHZhbHVlbm93LCBhcHBlbmQgPSBmYWxzZSkge1xuXG4gICAgICAgIGlmICh0eXBlb2Ygc2VsZWN0b3IgPT09IFwib2JqZWN0XCIpXG4gICAgICAgICAgICB2YXIgJHByb2dyZXNzID0gc2VsZWN0b3I7XG4gICAgICAgIGVsc2UgaWYgKHR5cGVvZiBzZWxlY3RvciA9PT0gXCJzdHJpbmdcIilcbiAgICAgICAgICAgIHZhciAkcHJvZ3Jlc3MgPSAkKHNlbGVjdG9yKTtcblxuICAgICAgICB2YXIgJHByb2dyZXNzQmFyID0gJHByb2dyZXNzLmZpbmQoJy5wcm9ncmVzcy1iYXInKTtcbiAgICAgICAgdmFyIGN1cnJlbnQgPSAkcHJvZ3Jlc3NCYXIuYXR0cihcImFyaWEtdmFsdWVub3dcIik7XG5cbiAgICAgICAgaWYgKGFwcGVuZCkge1xuICAgICAgICAgICAgdmFyIHN0ZXBzID0gMTA7XG4gICAgICAgICAgICB2YXIgdmFsdWUgPSAodmFsdWVub3cgLyBzdGVwcyk7XG4gICAgICAgICAgICB2YXIgaW50ZXJ2YWwgPSBzZXRJbnRlcnZhbChmdW5jdGlvbigpIHtcbiAgICAgICAgICAgICAgICBjdXJyZW50ICs9IHZhbHVlO1xuICAgICAgICAgICAgICAgICRwcm9ncmVzc0Jhci5jc3MoXCJ3aWR0aFwiLCBjdXJyZW50ICsgXCIlXCIpLmF0dHIoXCJhcmlhLXZhbHVlbm93XCIsIGN1cnJlbnQpO1xuXG4gICAgICAgICAgICAgICAgaWYgKCRwcm9ncmVzc0Jhci5maW5kKCdzcGFuJykubGVuZ3RoID4gMClcbiAgICAgICAgICAgICAgICAgICAgJHByb2dyZXNzQmFyLmZpbmQoJ3NwYW4nKS50ZXh0KGN1cnJlbnQgKyBcIiUgQ29tcGxldGVcIik7XG5cbiAgICAgICAgICAgICAgICBpZiAoY3VycmVudCA+PSAxMDAgfHwgc3RlcHMgPT0gMClcbiAgICAgICAgICAgICAgICAgICAgY2xlYXJJbnRlcnZhbChpbnRlcnZhbCk7XG5cbiAgICAgICAgICAgICAgICBzdGVwcy0tO1xuICAgICAgICAgICAgfSwgMTAwKTtcblxuICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgY3VycmVudCA9IHZhbHVlbm93O1xuICAgICAgICAgICAgJHByb2dyZXNzQmFyLmNzcyhcIndpZHRoXCIsIGN1cnJlbnQgKyBcIiVcIikuYXR0cihcImFyaWEtdmFsdWVub3dcIiwgY3VycmVudCk7XG5cbiAgICAgICAgICAgIGlmICgkcHJvZ3Jlc3NCYXIuZmluZCgnc3BhbicpLmxlbmd0aCA+IDApXG4gICAgICAgICAgICAgICAgJHByb2dyZXNzQmFyLmZpbmQoJ3NwYW4nKS50ZXh0KGN1cnJlbnQgKyBcIiUgQ29tcGxldGVcIik7XG5cbiAgICAgICAgfVxuICAgIH1cblxuICAgIC8vIFRyYWNraW5nIHBhZ2UgbG9hZGluZyBldmVudHMgd2l0aCBwQWpheFxuICAgICQoZG9jdW1lbnQpLm9uKHtcbiAgICAgICAgJ3BqYXg6c3RhcnQnOiBmdW5jdGlvbiAoZXZlbnQpIHtcblxuICAgICAgICAgICAgaWYgKGNvbmZpZy5hamF4UHJvZ3Jlc3MpIHtcbiAgICAgICAgICAgICAgICBzZXRQcm9ncmVzcygkcmVxdWVzdFByb2dyZXNzLCAwKTtcbiAgICAgICAgICAgICAgICAkcmVxdWVzdFByb2dyZXNzLnNob3coKTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgaWYgKGNvbmZpZy5kZWJ1ZylcbiAgICAgICAgICAgICAgICBjb25zb2xlLmxvZygnRGFzaGJvYXJkOiBwamF4IGNoYW5nZSBzdGF0ZSB0byBgc3RhcnRgJyk7XG5cbiAgICAgICAgfSxcbiAgICAgICAgJ3BqYXg6YmVmb3JlU2VuZCc6IGZ1bmN0aW9uIChldmVudCkge1xuXG4gICAgICAgICAgICBpZiAoY29uZmlnLmFqYXhQcm9ncmVzcylcbiAgICAgICAgICAgICAgICBzZXRQcm9ncmVzcygkcmVxdWVzdFByb2dyZXNzLCAxNSk7XG5cbiAgICAgICAgICAgIGlmIChjb25maWcuZGVidWcpXG4gICAgICAgICAgICAgICAgY29uc29sZS5sb2coJ0Rhc2hib2FyZDogcGpheCBjaGFuZ2Ugc3RhdGUgdG8gYGJlZm9yZVNlbmRgJyk7XG5cbiAgICAgICAgfSxcbiAgICAgICAgJ3BqYXg6c2VuZCc6IGZ1bmN0aW9uIChldmVudCkge1xuXG4gICAgICAgICAgICBpZiAoY29uZmlnLmFqYXhGYWRlKVxuICAgICAgICAgICAgICAgICQodGhpcykuYXR0cignZGF0YS1wamF4LXN0YXRlJywgXCJzZW5kXCIpO1xuXG4gICAgICAgICAgICBpZiAoY29uZmlnLnNwaW5uZXIpXG4gICAgICAgICAgICAgICAgJCh0aGlzKS5hcHBlbmQoJHNwaW5uZXIpO1xuXG4gICAgICAgICAgICBpZiAoY29uZmlnLmFqYXhQcm9ncmVzcylcbiAgICAgICAgICAgICAgICBzZXRQcm9ncmVzcygkcmVxdWVzdFByb2dyZXNzLCAzNSk7XG5cbiAgICAgICAgICAgIGlmIChjb25maWcuZGVidWcpXG4gICAgICAgICAgICAgICAgY29uc29sZS5sb2coJ0Rhc2hib2FyZDogcGpheCBjaGFuZ2Ugc3RhdGUgdG8gYHNlbmRgJyk7XG5cbiAgICAgICAgfSxcbiAgICAgICAgJ3BqYXg6YmVmb3JlUmVwbGFjZSc6IGZ1bmN0aW9uIChldmVudCkge1xuXG4gICAgICAgICAgICBpZiAoY29uZmlnLmFqYXhQcm9ncmVzcylcbiAgICAgICAgICAgICAgICBzZXRQcm9ncmVzcygkcmVxdWVzdFByb2dyZXNzLCA3NSk7XG5cbiAgICAgICAgICAgIGlmIChjb25maWcuZGVidWcpXG4gICAgICAgICAgICAgICAgY29uc29sZS5sb2coJ0Rhc2hib2FyZDogcGpheCBjaGFuZ2Ugc3RhdGUgdG8gYGJlZm9yZVJlcGxhY2VgJyk7XG5cbiAgICAgICAgfSxcbiAgICAgICAgJ3BqYXg6Y29tcGxldGUnOiBmdW5jdGlvbiAoZXZlbnQpIHtcblxuICAgICAgICAgICAgaWYgKGNvbmZpZy5hamF4RmFkZSlcbiAgICAgICAgICAgICAgICAkKHRoaXMpLmF0dHIoJ2RhdGEtcGpheC1zdGF0ZScsIFwiY29tcGxldGVcIik7XG5cbiAgICAgICAgICAgIGlmIChjb25maWcuYWpheFByb2dyZXNzKSB7XG4gICAgICAgICAgICAgICAgc2V0UHJvZ3Jlc3MoJHJlcXVlc3RQcm9ncmVzcywgMTAwKTtcbiAgICAgICAgICAgICAgICBzZXRUaW1lb3V0KGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICAgICAgJHJlcXVlc3RQcm9ncmVzcy5oaWRlKCk7XG4gICAgICAgICAgICAgICAgfSwgMTIwMCk7XG4gICAgICAgICAgICB9XG5cbiAgICAgICAgICAgIGlmIChjb25maWcuZGVidWcpXG4gICAgICAgICAgICAgICAgY29uc29sZS5sb2coJ0Rhc2hib2FyZDogcGpheCBjaGFuZ2Ugc3RhdGUgdG8gYGNvbXBsZXRlYCcpO1xuXG4gICAgICAgIH1cbiAgICB9KTtcblxuXG4gICAgLy8gU2hvdy9oaWRlIGRyb3Bkb3duIGluIG1haW5uYXYgb24gaG92ZXJcbiAgICBpZiAoY29uZmlnLm1haW5uYXYuZXhwYW5kT25Ib3Zlcikge1xuICAgICAgICAkbWFpbk5hdi5maW5kKFwiLmRyb3Bkb3duXCIpLmVhY2goZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgdmFyICR0aGlzID0gJCh0aGlzKTtcbiAgICAgICAgICAgICR0aGlzLmNsaWNrKGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICBpZiAoISQodGhpcykuZmluZCgnLmRyb3Bkb3duLW1lbnUnKS5pcygnOnZpc2libGUnKSkge1xuICAgICAgICAgICAgICAgICAgICAkKHRoaXMpLmZpbmQoJy5kcm9wZG93bi1tZW51Jykuc3RvcCh0cnVlLCB0cnVlKS5zbGlkZVRvZ2dsZShcImZhc3RcIik7XG5cbiAgICAgICAgICAgICAgICAgICAgaWYgKGNvbmZpZy5kZWJ1ZylcbiAgICAgICAgICAgICAgICAgICAgICAgIGNvbnNvbGUubG9nKCdEYXNoYm9hcmQ6IGRyb3Bkb3duIGluIG1haW5uYXYgaXMgdmlzaWJsZSBieSBjbGljaycpO1xuXG4gICAgICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAgICAgJCh0aGlzKS5maW5kKCcuZHJvcGRvd24tbWVudScpLnN0b3AodHJ1ZSwgdHJ1ZSkuc2xpZGVVcChcImZhc3RcIik7XG5cbiAgICAgICAgICAgICAgICAgICAgaWYgKGNvbmZpZy5kZWJ1ZylcbiAgICAgICAgICAgICAgICAgICAgICAgIGNvbnNvbGUubG9nKCdEYXNoYm9hcmQ6IGRyb3Bkb3duIGluIG1haW5uYXYgaXMgaGlkZGluZyBieSBjbGljaycpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgJHRoaXMuaG92ZXIoZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgICAgIHZhciAkZHJvcGRvd24gPSAkKHRoaXMpO1xuICAgICAgICAgICAgICAgIGlmICghJGRyb3Bkb3duLmZpbmQoJy5kcm9wZG93bi1tZW51JykuaXMoJzp2aXNpYmxlJykpIHtcbiAgICAgICAgICAgICAgICAgICAgJGRyb3Bkb3duLmZpbmQoJy5kcm9wZG93bi1tZW51Jykuc3RvcCh0cnVlLCB0cnVlKS5kZWxheSgzMDApLnNsaWRlVG9nZ2xlKFwiZmFzdFwiKTtcbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICBpZiAoY29uZmlnLmRlYnVnKVxuICAgICAgICAgICAgICAgICAgICBjb25zb2xlLmxvZygnRGFzaGJvYXJkOiBkcm9wZG93biBpbiBtYWlubmF2IGlzIHZpc2libGUgYnkgaG92ZXInKTtcblxuICAgICAgICAgICAgfSwgZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgICAgIHZhciAkZHJvcGRvd24gPSAkKHRoaXMpO1xuICAgICAgICAgICAgICAgIGlmICgkZHJvcGRvd24uZmluZCgnLmRyb3Bkb3duLW1lbnUnKS5pcygnOnZpc2libGUnKSkge1xuICAgICAgICAgICAgICAgICAgICAkZHJvcGRvd24uZmluZCgnLmRyb3Bkb3duLW1lbnUnKS5zdG9wKHRydWUsIHRydWUpLmRlbGF5KDEwMCkuc2xpZGVVcChcImZhc3RcIik7XG5cbiAgICAgICAgICAgICAgICAgICAgaWYgKGNvbmZpZy5kZWJ1ZylcbiAgICAgICAgICAgICAgICAgICAgICAgIGNvbnNvbGUubG9nKCdEYXNoYm9hcmQ6IGRyb3Bkb3duIGluIG1haW5uYXYgaXMgaGlkZGluZyBieSBob3ZlcicpO1xuXG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfSk7XG4gICAgICAgIH0pO1xuICAgIH1cblxuICAgIC8vIEFkbWluIHNpZGViYXIgbWVudSBtYW5hZ2VtZW50XG4gICAgaWYgKCRzaWRlYmFyTmF2Lmxlbmd0aCA+IDApIHtcblxuICAgICAgICAvLyBEaXNhYmxlIGNsaWNrIG9uIGRyb3Bkb3duIGVsZW1lbnQgd2l0aCBlbXB0eSBsaW5rXG4gICAgICAgICRzaWRlYmFyTmF2LmZpbmQoJy5kcm9wZG93bi1tZW51ID4gbGkgPiBhW2hyZWY9XCIjXCJdJykub24oJ2NsaWNrJywgZnVuY3Rpb24gKGV2ZW50KSB7XG4gICAgICAgICAgICBldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgICAgICAgZXZlbnQuc3RvcFByb3BhZ2F0aW9uKCk7XG5cbiAgICAgICAgICAgIGlmIChjb25maWcuZGVidWcpXG4gICAgICAgICAgICAgICAgY29uc29sZS5sb2coJ0Rhc2hib2FyZDogY2xpY2sgYnkgYC5kcm9wZG93bi1tZW51ID4gbGkgPiBhW2hyZWY9XCIjXCJdYCBpbiBzaWRlYmFyJyk7XG5cbiAgICAgICAgfSk7XG5cbiAgICAgICAgLy8gRGlzYWJsZSBjbGljayBvbiBwb3BvdmVyIGVsZW1lbnRcbiAgICAgICAgJHNpZGViYXJOYXYuZmluZCgnLmRyb3Bkb3duLXN1Ym1lbnUgPiBhJykub24oJ2NsaWNrJywgZnVuY3Rpb24gKGV2ZW50KSB7XG4gICAgICAgICAgICBldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgICAgICAgZXZlbnQuc3RvcFByb3BhZ2F0aW9uKCk7XG5cbiAgICAgICAgICAgIGlmIChjb25maWcuZGVidWcpXG4gICAgICAgICAgICAgICAgY29uc29sZS5sb2coJ0Rhc2hib2FyZDogY2xpY2sgYnkgYC5kcm9wZG93bi1zdWJtZW51ID4gYWAgaW4gc2lkZWJhcicpO1xuXG4gICAgICAgIH0pO1xuXG4gICAgICAgIC8vIFNob3cvaGlkZSBkcm9wZG93biBpbiBzaWRlYmFyIG9uIGhvdmVyXG4gICAgICAgIGlmIChjb25maWcuc2lkZWJhci5leHBhbmRPbkhvdmVyKSB7XG4gICAgICAgICAgICAkc2lkZWJhck5hdi5maW5kKFwiLmRyb3Bkb3duXCIpLmVhY2goZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgICAgIHZhciAkdGhpcyA9ICQodGhpcyk7XG4gICAgICAgICAgICAgICAgJHRoaXMuY2xpY2soZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgICAgICAgICBpZiAoISQodGhpcykuZmluZCgnLmRyb3Bkb3duLW1lbnUnKS5pcygnOnZpc2libGUnKSkge1xuICAgICAgICAgICAgICAgICAgICAgICAgJCh0aGlzKS5maW5kKCcuZHJvcGRvd24tbWVudScpLnN0b3AodHJ1ZSwgdHJ1ZSkuc2xpZGVUb2dnbGUoXCJmYXN0XCIpO1xuICAgICAgICAgICAgICAgICAgICAgICAgJCh0aGlzKS5maW5kKCcuZHJvcGRvd24tdG9nZ2xlIC5mYS1hbmdsZS1kb3duJykucmVtb3ZlQ2xhc3MoJ2ZhLWFuZ2xlLWRvd24nKS5hZGRDbGFzcygnZmEtYW5nbGUtdXAnKTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgaWYgKGNvbmZpZy5kZWJ1ZylcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBjb25zb2xlLmxvZygnRGFzaGJvYXJkOiBkcm9wZG93biBpbiBzaWRlYmFyIGlzIHZpc2libGUgYnkgY2xpY2snKTtcblxuICAgICAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICAgICAgJCh0aGlzKS5maW5kKCcuZHJvcGRvd24tbWVudScpLnN0b3AodHJ1ZSwgdHJ1ZSkuc2xpZGVVcChcImZhc3RcIik7XG4gICAgICAgICAgICAgICAgICAgICAgICAkKHRoaXMpLmZpbmQoJy5kcm9wZG93bi10b2dnbGUgLmZhLWFuZ2xlLXVwJykucmVtb3ZlQ2xhc3MoJ2ZhLWFuZ2xlLXVwJykuYWRkQ2xhc3MoJ2ZhLWFuZ2xlLWRvd24nKTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgaWYgKGNvbmZpZy5kZWJ1ZylcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBjb25zb2xlLmxvZygnRGFzaGJvYXJkOiBkcm9wZG93biBpbiBzaWRlYmFyIGlzIGhpZGRpbmcgYnkgY2xpY2snKTtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICAkc2lkZWJhck5hdi5maW5kKFwiLmRyb3Bkb3duOm5vdCguYWN0aXZlKTpub3QoOmhvdmVyKVwiKS5maW5kKCcuZHJvcGRvd24tbWVudScpLnNsaWRlVXAoXCJmYXN0XCIpO1xuICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgICAgICR0aGlzLmhvdmVyKGZ1bmN0aW9uICgpIHtcblxuICAgICAgICAgICAgICAgICAgICB2YXIgJGRyb3Bkb3duID0gJCh0aGlzKTtcbiAgICAgICAgICAgICAgICAgICAgaWYgKCEkZHJvcGRvd24uZmluZCgnLmRyb3Bkb3duLW1lbnUnKS5pcygnOnZpc2libGUnKSkge1xuICAgICAgICAgICAgICAgICAgICAgICAgJGRyb3Bkb3duLmZpbmQoJy5kcm9wZG93bi1tZW51Jykuc3RvcCh0cnVlLCB0cnVlKS5kZWxheSg1MDApLnNsaWRlVG9nZ2xlKFwiZmFzdFwiKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIHNldFRpbWVvdXQoZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgJGRyb3Bkb3duLmZpbmQoJy5kcm9wZG93bi10b2dnbGUgLmZhLWFuZ2xlLWRvd24nKS5yZW1vdmVDbGFzcygnZmEtYW5nbGUtZG93bicpLmFkZENsYXNzKCdmYS1hbmdsZS11cCcpO1xuICAgICAgICAgICAgICAgICAgICAgICAgfSwgMjAwKTtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICAkc2lkZWJhck5hdi5maW5kKFwiLmRyb3Bkb3duOm5vdCguYWN0aXZlKTpub3QoOmhvdmVyKVwiKS5maW5kKCcuZHJvcGRvd24tbWVudScpLnNsaWRlVXAoXCJmYXN0XCIpO1xuXG4gICAgICAgICAgICAgICAgICAgIGlmIChjb25maWcuZGVidWcpXG4gICAgICAgICAgICAgICAgICAgICAgICBjb25zb2xlLmxvZygnRGFzaGJvYXJkOiBkcm9wZG93biBpbiBzaWRlYmFyIGlzIHZpc2libGUgYnkgaG92ZXInKTtcblxuICAgICAgICAgICAgICAgIH0sIGZ1bmN0aW9uICgpIHtcblxuICAgICAgICAgICAgICAgICAgICB2YXIgJGRyb3Bkb3duID0gJCh0aGlzKTtcblxuICAgICAgICAgICAgICAgICAgICBpZiAoISRkcm9wZG93bi5oYXNDbGFzcygncG9wb3Zlci1zaG93JykpIHtcblxuICAgICAgICAgICAgICAgICAgICAgICAgJGRyb3Bkb3duLmZpbmQoJy5kcm9wZG93bi1tZW51Jykuc3RvcCh0cnVlLCB0cnVlKS5kZWxheSgyMDApLnNsaWRlVXAoXCJmYXN0XCIpO1xuICAgICAgICAgICAgICAgICAgICAgICAgc2V0VGltZW91dChmdW5jdGlvbigpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAkZHJvcGRvd24uZmluZCgnLmRyb3Bkb3duLXRvZ2dsZSAuZmEtYW5nbGUtdXAnKS5yZW1vdmVDbGFzcygnZmEtYW5nbGUtdXAnKS5hZGRDbGFzcygnZmEtYW5nbGUtZG93bicpO1xuICAgICAgICAgICAgICAgICAgICAgICAgfSwgMjAwKTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgaWYgKGNvbmZpZy5kZWJ1ZylcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBjb25zb2xlLmxvZygnRGFzaGJvYXJkOiBkcm9wZG93biBpbiBzaWRlYmFyIGlzIGhpZGRpbmcgYnkgaG92ZXInKTtcblxuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgIC8vIEZpeGVkOiBEcm9wZG93biBtZW51IGhpZGRpbmcgYnkgcG9wb3ZlciBpcyBzaG93XG4gICAgICAgICAgICAgICAgICAgIC8vJHNpZGViYXJOYXYuZmluZChcIi5kcm9wZG93bjpub3QoLmFjdGl2ZSk6bm90KDpob3ZlcilcIikuZmluZCgnLmRyb3Bkb3duLW1lbnUnKS5zbGlkZVVwKFwiZmFzdFwiKTtcbiAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgJHNpZGViYXJOYXYuZmluZChcIi5kcm9wZG93bi5hY3RpdmVcIikuZmluZCgnLmRyb3Bkb3duLXRvZ2dsZSAuZmEtYW5nbGUtZG93bicpLnRvZ2dsZUNsYXNzKCdmYS1hbmdsZS1kb3duIGZhLWFuZ2xlLXVwJyk7XG4gICAgICAgICAgICAkc2lkZWJhck5hdi5maW5kKFwiLmRyb3Bkb3duLmFjdGl2ZSAuZHJvcGRvd24tdG9nZ2xlXCIpLmNsaWNrKCk7XG4gICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAkc2lkZWJhck5hdi5maW5kKFwiLmRyb3Bkb3duXCIpLm9uKCdzaG93bi5icy5kcm9wZG93bicsIGZ1bmN0aW9uKGV2ZW50KSB7XG4gICAgICAgICAgICAgICAgJChldmVudC50YXJnZXQpLmZpbmQoJy5kcm9wZG93bi10b2dnbGUgLmZhLWFuZ2xlLWRvd24nKS50b2dnbGVDbGFzcygnZmEtYW5nbGUtZG93biBmYS1hbmdsZS11cCcpO1xuICAgICAgICAgICAgfSkub24oJ2hpZGRlbi5icy5kcm9wZG93bicsIGZ1bmN0aW9uKGV2ZW50KSB7XG4gICAgICAgICAgICAgICAgJChldmVudC50YXJnZXQpLmZpbmQoJy5kcm9wZG93bi10b2dnbGUgLmZhLWFuZ2xlLXVwJykudG9nZ2xlQ2xhc3MoJ2ZhLWFuZ2xlLXVwIGZhLWFuZ2xlLWRvd24nKTtcbiAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgLy8kc2lkZWJhck5hdi5maW5kKFwiLmRyb3Bkb3duLmFjdGl2ZVwiKS5kcm9wZG93bigndG9nZ2xlJyk7XG4gICAgICAgICAgICAkc2lkZWJhck5hdi5maW5kKFwiLmRyb3Bkb3duLmFjdGl2ZSAuZHJvcGRvd24tdG9nZ2xlXCIpLmNsaWNrKCk7XG4gICAgICAgIH1cblxuICAgICAgICAvLyBJbml0IHBvcG92ZXIgbWVudSBpbiBzaWRlYmFyXG4gICAgICAgICRzaWRlYmFyTmF2LmZpbmQoJy5kcm9wZG93bi1zdWJtZW51ID4gYScpLmVhY2goZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICB2YXIgJHRoaXMgPSAkKHRoaXMpO1xuICAgICAgICAgICAgdmFyICRkcm9wZG93biA9ICQodGhpcykucGFyZW50cygnLmRyb3Bkb3duJyk7XG5cbiAgICAgICAgICAgIHZhciB0cmlnZ2VyID0gJ2NsaWNrJztcbiAgICAgICAgICAgIGlmIChjb25maWcuc2lkZWJhci5leHBhbmRPbkhvdmVyKVxuICAgICAgICAgICAgICAgIHRyaWdnZXIgPSAnbWFudWFsJztcblxuICAgICAgICAgICAgJHRoaXMucG9wb3Zlcih7XG4gICAgICAgICAgICAgICAgcGxhY2VtZW50OiAnYXV0byByaWdodCcsXG4gICAgICAgICAgICAgICAgdHJpZ2dlcjogdHJpZ2dlcixcbiAgICAgICAgICAgICAgICBjb250YWluZXI6ICdib2R5JyxcbiAgICAgICAgICAgICAgICB0aXRsZTogZmFsc2UsXG4gICAgICAgICAgICAgICAgaHRtbDogdHJ1ZSxcbiAgICAgICAgICAgICAgICB0ZW1wbGF0ZTogJzxkaXYgY2xhc3M9XCJwb3BvdmVyIG5hdi1wb3BvdmVyXCIgcm9sZT1cInRvb2x0aXBcIj48ZGl2IGNsYXNzPVwiYXJyb3dcIj48L2Rpdj48ZGl2IGNsYXNzPVwicG9wb3Zlci1jb250ZW50XCI+PC9kaXY+PC9kaXY+JyxcbiAgICAgICAgICAgICAgICBjb250ZW50OiBmdW5jdGlvbigpIHtcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuICR0aGlzLnBhcmVudCgnLmRyb3Bkb3duLXN1Ym1lbnUnKS5maW5kKCd1bCcpLmFkZENsYXNzKCduYXYnKS5vdXRlckh0bWwoKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgaWYgKGNvbmZpZy5zaWRlYmFyLmV4cGFuZE9uSG92ZXIpIHtcbiAgICAgICAgICAgICAgICAkdGhpcy5vbihcIm1vdXNlZW50ZXJcIiwgZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgICAgICAgICB2YXIgX3RoaXMgPSB0aGlzO1xuICAgICAgICAgICAgICAgICAgICAkKHRoaXMpLnBvcG92ZXIoXCJzaG93XCIpO1xuICAgICAgICAgICAgICAgICAgICAkZHJvcGRvd24uYWRkQ2xhc3MoJ3BvcG92ZXItc2hvdycpO1xuXG4gICAgICAgICAgICAgICAgICAgICQoXCIubmF2LXBvcG92ZXJcIikub24oXCJtb3VzZWxlYXZlXCIsIGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICQoX3RoaXMpLnBvcG92ZXIoJ2hpZGUnKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICRkcm9wZG93bi5yZW1vdmVDbGFzcygncG9wb3Zlci1zaG93Jyk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIGlmIChjb25maWcuZGVidWcpXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgY29uc29sZS5sb2coJ0Rhc2hib2FyZDogc2lkZWJhciBwb3BvdmVyIGlzIGhpZGRpbmcgYnkgbW91c2VsZWF2ZScpO1xuXG4gICAgICAgICAgICAgICAgICAgIH0pLm9uKFwibW91c2Vkb3duXCIsIGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICQoX3RoaXMpLnBvcG92ZXIoJ2hpZGUnKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICRkcm9wZG93bi5yZW1vdmVDbGFzcygncG9wb3Zlci1zaG93Jyk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIGlmIChjb25maWcuZGVidWcpXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgY29uc29sZS5sb2coJ0Rhc2hib2FyZDogc2lkZWJhciBwb3BvdmVyIGlzIGhpZGRpbmcgYnkgbW91c2Vkb3duJyk7XG4gICAgICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAgICAgICAgIGlmIChjb25maWcuZGVidWcpXG4gICAgICAgICAgICAgICAgICAgICAgICBjb25zb2xlLmxvZygnRGFzaGJvYXJkOiBzaWRlYmFyIHBvcG92ZXIgaXMgdmlzaWJsZSBieSBtb3VzZWVudGVyJyk7XG5cbiAgICAgICAgICAgICAgICB9KS5vbihcIm1vdXNlbGVhdmVcIiwgZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgICAgICAgICB2YXIgX3RoaXMgPSB0aGlzO1xuICAgICAgICAgICAgICAgICAgICBzZXRUaW1lb3V0KGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGlmICghJChcIi5uYXYtcG9wb3Zlcjpob3ZlclwiKS5sZW5ndGgpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAkKF90aGlzKS5wb3BvdmVyKCdoaWRlJyk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgJGRyb3Bkb3duLnJlbW92ZUNsYXNzKCdwb3BvdmVyLXNob3cnKTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGlmIChjb25maWcuZGVidWcpXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGNvbnNvbGUubG9nKCdEYXNoYm9hcmQ6IHNpZGViYXIgcG9wb3ZlciBpcyBoaWRkaW5nIGJ5IG1vdXNlbGVhdmUgYW5kIHRpbWVvdXQnKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgfSwgMjAwKTtcbiAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgIH1cblxuICAgICAgICB9KTtcblxuICAgICAgICAvLyBBZGQgc2lkZWJhciBuYXYgdG8gbWFpbiBuYXZiYXIgZm9yIHNtIGFuZCB4cyBkaXNwbGF5c1xuICAgICAgICBpZiAodmlld3BvcnQud2lkdGggPD0gYnJlYWtwb2ludHMuc20pIHtcbiAgICAgICAgICAgIHZhciAkc2lkZWJhciA9ICRzaWRlYmFyTmF2LmNsb25lKCk7XG4gICAgICAgICAgICAkc2lkZWJhci5hdHRyKCdjbGFzcycsICduYXYgbmF2YmFyLW5hdiBoaWRkZW4tbWQgaGlkZGVuLWxnJyk7XG4gICAgICAgICAgICAkc2lkZWJhci5maW5kKCdsaScpLmVhY2goZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICAgICAgJCh0aGlzKS5maW5kKCcuZmEtc3RhY2snKS5yZW1vdmVDbGFzcygnZmEtc3RhY2snKS5yZW1vdmVDbGFzcygnZmEtbGcnKTtcbiAgICAgICAgICAgICAgICAkKHRoaXMpLmZpbmQoJy5mYScpLnJlbW92ZUNsYXNzKCdmYS1zdGFjay0xeCcpO1xuICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICAkaXRlbXMgPSAkc2lkZWJhci5vdXRlckh0bWwoKTtcbiAgICAgICAgICAgICRtYWluTmF2LmJlZm9yZSgkaXRlbXMpO1xuXG4gICAgICAgICAgICBpZiAoY29uZmlnLmRlYnVnKVxuICAgICAgICAgICAgICAgIGNvbnNvbGUubG9nKCdEYXNoYm9hcmQ6IGFkZGVkIHNpZGViYXIgbmF2IHRvIG1haW4gbmF2YmFyIGZvciBzbSBhbmQgeHMgZGlzcGxheXMnKTtcbiAgICAgICAgfVxuICAgIH1cblxuICAgIC8vIERyb3Bkb3duYHNcbiAgICAkKCdib2R5JykuZGVsZWdhdGUoJy5kcm9wZG93bi10b2dnbGUsIFtkYXRhLXRvZ2dsZT1cImRyb3Bkb3duXCJdJywgJ2NsaWNrJywgZnVuY3Rpb24gKGV2ZW50KSB7XG4gICAgICAgIGlmICgoJChkb2N1bWVudCkud2lkdGgoKSA+IGJyZWFrcG9pbnRzLnNtKSAmJiAkKHRoaXMpLmlzKFwiYVwiKSkge1xuICAgICAgICAgICAgZXZlbnQucHJldmVudERlZmF1bHQoKTtcbiAgICAgICAgICAgIHZhciB1cmwgPSAkKHRoaXMpLmF0dHIoJ2hyZWYnKTtcbiAgICAgICAgICAgIGlmICh1cmwgIT09ICcjJylcbiAgICAgICAgICAgICAgICB3aW5kb3cubG9jYXRpb24uaHJlZiA9IHVybDtcbiAgICAgICAgfVxuXG4gICAgICAgIGlmIChjb25maWcuZGVidWcpXG4gICAgICAgICAgICBjb25zb2xlLmxvZygnRGFzaGJvYXJkOiBjbGljayBvbiAuZHJvcGRvd24tdG9nZ2xlJyk7XG5cbiAgICB9KTtcbiAgICAkKCdib2R5JykuZGVsZWdhdGUoJCgnLmRyb3Bkb3duLXRvZ2dsZSwgW2RhdGEtdG9nZ2xlPVwiZHJvcGRvd25cIl0nKS5wYXJlbnQoKSwgJ3Nob3cuYnMuZHJvcGRvd24nLCBmdW5jdGlvbiAoZXZlbnQpIHtcbiAgICAgICAgdmFyICRidXR0b24gPSAkKGV2ZW50LnJlbGF0ZWRUYXJnZXQpO1xuICAgICAgICB2YXIgJGRyb3Bkb3duID0gJChldmVudC50YXJnZXQpLmZpbmQoJy5kcm9wZG93bi1tZW51Jyk7XG4gICAgICAgIHZhciB2aWV3cG9ySGVpZ2h0ID0gJChkb2N1bWVudCkuaGVpZ2h0KCk7XG4gICAgICAgIHZhciBidXR0b25PZmZzZXQgPSAkYnV0dG9uLm9mZnNldCgpLnRvcCArICRidXR0b24uaGVpZ2h0KCk7XG4gICAgICAgIHZhciBkcm9wZG93bkhlaWdodCA9ICRkcm9wZG93bi5oZWlnaHQoKTtcbiAgICAgICAgdmFyIGRyb3Bkb3duT2Zmc2V0ID0gYnV0dG9uT2Zmc2V0ICsgZHJvcGRvd25IZWlnaHQ7XG5cbiAgICAgICAgaWYgKGRyb3Bkb3duT2Zmc2V0ID4gKHZpZXdwb3JIZWlnaHQgLSA0NSkgJiYgKGJ1dHRvbk9mZnNldCAtIDU1KSA+IGRyb3Bkb3duSGVpZ2h0KSB7XG4gICAgICAgICAgICAkKGV2ZW50LnRhcmdldCkuYWRkQ2xhc3MoJ2Ryb3B1cCcpO1xuICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgJChldmVudC50YXJnZXQpLnJlbW92ZUNsYXNzKCdkcm9wdXAnKTtcbiAgICAgICAgfVxuICAgIH0pO1xuXG4gICAgLy8gSG90IGtleXMgZm9yIHBhZ2luYXRpb25cbiAgICAkKHdpbmRvdykua2V5ZG93bihmdW5jdGlvbihldmVudCkge1xuXG4gICAgICAgIHZhciAkcGFnaW5hdGlvbiA9ICRkYXNoYm9hcmQuZmluZCgnLnBhZ2luYXRpb24nKTtcbiAgICAgICAgbGV0IGN0cmxLZXkgPSAoZ2V0T1MoKSA9PSBcIldpbmRvd3NcIikgPyBldmVudC5jdHJsS2V5IDogKGdldE9TKCkgPT0gXCJNYWMgT1NcIikgPyBldmVudC5hbHRLZXkgOiBudWxsO1xuICAgICAgICBsZXQga2V5Q29kZSA9IGV2ZW50LmtleUNvZGUgPyBldmVudC5rZXlDb2RlIDogZXZlbnQud2hpY2ggPyBldmVudC53aGljaCA6IG51bGw7XG5cbiAgICAgICAgaWYgKGN0cmxLZXkgJiYga2V5Q29kZSAmJiAkcGFnaW5hdGlvbi5sZW5ndGggPiAwKSB7XG4gICAgICAgICAgICBldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuXG4gICAgICAgICAgICBsZXQgbGluayA9IG51bGw7XG4gICAgICAgICAgICBzd2l0Y2ggKGtleUNvZGUpIHtcbiAgICAgICAgICAgICAgICBjYXNlIDM3OlxuICAgICAgICAgICAgICAgICAgICBsaW5rID0gJHBhZ2luYXRpb24uZmluZCgnbGkgPiBhW3JlbD1cInByZXZcIl0sIGxpLnByZXYgPiBhJykuYXR0cignaHJlZicpO1xuICAgICAgICAgICAgICAgICAgICBicmVhaztcbiAgICAgICAgICAgICAgICBjYXNlIDM5OlxuICAgICAgICAgICAgICAgICAgICBsaW5rID0gJHBhZ2luYXRpb24uZmluZCgnbGkgPiBhW3JlbD1cIm5leHRcIl0sIGxpLm5leHQgPiBhJykuYXR0cignaHJlZicpO1xuICAgICAgICAgICAgICAgICAgICBicmVhaztcbiAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgaWYgKGxpbmspIHtcblxuICAgICAgICAgICAgICAgIGxldCAkcGpheCA9ICRwYWdpbmF0aW9uLmNsb3Nlc3QoJ1tkYXRhLXBqYXgtY29udGFpbmVyXScpO1xuICAgICAgICAgICAgICAgIGlmICgkcGpheC5sZW5ndGggPiAwKSB7XG5cbiAgICAgICAgICAgICAgICAgICAgbGV0IHRpbWVvdXQgPSA1MDAwO1xuICAgICAgICAgICAgICAgICAgICBpZiAoJHBqYXguZGF0YShcInBqYXgtdGltZW91dFwiKSlcbiAgICAgICAgICAgICAgICAgICAgICAgIHRpbWVvdXQgPSBwYXJzZUludCgkcGpheC5kYXRhKFwicGpheC10aW1lb3V0XCIpKTtcblxuICAgICAgICAgICAgICAgICAgICAkLnBqYXgucmVsb2FkKHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGNvbnRhaW5lcjogKCRwamF4LmF0dHIoJ2lkJykpID8gJyMnICsgJHBqYXguYXR0cignaWQnKSA6IG51bGwsXG4gICAgICAgICAgICAgICAgICAgICAgICB0aW1lb3V0OiB0aW1lb3V0LFxuICAgICAgICAgICAgICAgICAgICAgICAgdXJsOiBsaW5rXG4gICAgICAgICAgICAgICAgICAgIH0pO1xuXG4gICAgICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAgICAgZG9jdW1lbnQubG9jYXRpb24gPSBsaW5rO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH1cbiAgICAgICAgfVxuICAgIH0pO1xuXG4gICAgLy8gTW9kYWxzIGFuZCBidXR0b25zIGxvYWRpbmcgc3RhdGVcbiAgICAkKCdib2R5JykuZGVsZWdhdGUoJ2EsIGJ1dHRvbicsICdjbGljaycsIGZ1bmN0aW9uKGV2ZW50KSB7XG4gICAgICAgIGlmICgkKHRoaXMpLmRhdGEoJ3RvZ2dsZScpID09IFwibW9kYWxcIikge1xuICAgICAgICAgICAgJCgnYm9keScpLmFkZENsYXNzKCdsb2FkaW5nJyk7XG4gICAgICAgIH0gZWxzZSBpZiAoJCh0aGlzKS5kYXRhKCdsb2FkaW5nLXRleHQnKSkge1xuXG4gICAgICAgICAgICB2YXIgaGFzRXJyb3JzID0gZmFsc2U7XG5cbiAgICAgICAgICAgIHZhciAkZm9ybSA9ICQoZXZlbnQudGFyZ2V0KS5wYXJlbnRzKCdmb3JtOmZpcnN0Jyk7XG4gICAgICAgICAgICAkZm9ybS5maW5kKCdpbnB1dFthcmlhLXJlcXVpcmVkXScpLmVhY2goZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgICAgIGlmICgkKHRoaXMpLnZhbCgpLmxlbmd0aCA9PSAwKSB7XG4gICAgICAgICAgICAgICAgICAgIGhhc0Vycm9ycyA9IHRydWU7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgICRmb3JtLmZpbmQoJ1thcmlhLWludmFsaWRdJykuZWFjaChmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAgICAgaWYgKCQodGhpcykuYXR0cignYXJpYS1pbnZhbGlkJykgPT0gXCJ0cnVlXCIpIHtcbiAgICAgICAgICAgICAgICAgICAgaGFzRXJyb3JzID0gdHJ1ZTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgaWYgKCFoYXNFcnJvcnMpIHtcbiAgICAgICAgICAgICAgICAkKHRoaXMpLmFkZENsYXNzKCdsb2FkaW5nJyk7XG4gICAgICAgICAgICAgICAgJCh0aGlzKS5idXR0b24oJ2xvYWRpbmcnKTtcbiAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgJCh0aGlzKS5yZW1vdmVDbGFzcygnbG9hZGluZycpO1xuICAgICAgICAgICAgICAgICQodGhpcykuYnV0dG9uKCdyZXNldCcpO1xuICAgICAgICAgICAgfVxuICAgICAgICB9XG4gICAgfSk7XG4gICAgJCgnYm9keScpLmRlbGVnYXRlKCcubW9kYWwnLCAnc2hvdy5icy5tb2RhbCcsIGZ1bmN0aW9uKCkge1xuICAgICAgICAkKCdib2R5JykuYWRkQ2xhc3MoJ2xvYWRpbmcnKTtcbiAgICB9KTtcbiAgICAkKCdib2R5JykuZGVsZWdhdGUoJy5tb2RhbCcsICdzaG93bi5icy5tb2RhbCcsIGZ1bmN0aW9uKCkge1xuICAgICAgICAkKCdib2R5JykucmVtb3ZlQ2xhc3MoJ2xvYWRpbmcnKTtcbiAgICB9KTtcbiAgICAkKCdib2R5JykuZGVsZWdhdGUoJy5tb2RhbCcsICdoaWRlLmJzLm1vZGFsJywgZnVuY3Rpb24oKSB7XG4gICAgICAgICQoJ2JvZHknKS5yZW1vdmVDbGFzcygnbG9hZGluZycpO1xuICAgIH0pO1xuICAgICQoJ2JvZHknKS5kZWxlZ2F0ZSgnLm1vZGFsJywgJ2xvYWRlZC5icy5tb2RhbCcsIGZ1bmN0aW9uKCkge1xuICAgICAgICAkKCdib2R5JykucmVtb3ZlQ2xhc3MoJ2xvYWRpbmcnKTtcbiAgICB9KTtcblxufSk7Il19
