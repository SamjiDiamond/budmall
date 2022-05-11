function triggerOwl() {

  var sync1 = $(document).find(".sync1");
  var sync2 = $(document).find(".sync2");
  var thumbnailItemClass = '.owl-item';

  var slides = sync1.owlCarousel({
    items: 1,
    loop: true,
    margin: 0,
    mouseDrag: true,
    touchDrag: true,
    pullDrag: false,
    scrollPerPage: true,
    nav: false,
    dots: false,
  }).on('changed.owl.carousel', syncPosition);

  function syncPosition(el) {
    $owl_slider = $(this).data('owl.carousel');
    var loop = $owl_slider.options.loop;

    if (loop) {
      var count = el.item.count - 1;
      var current = Math.round(el.item.index - (el.item.count / 2) - .5);
      if (current < 0) {
        current = count;
      }
      if (current > count) {
        current = 0;
      }
    } else {
      var current = el.item.index;
    }

    var owl_thumbnail = sync2.data('owl.carousel');
    var itemClass = "." + owl_thumbnail.options.itemClass;

    var thumbnailCurrentItem = sync2
      .find(itemClass)
      .removeClass("synced")
      .eq(current);
    thumbnailCurrentItem.addClass('synced');

    if (!thumbnailCurrentItem.hasClass('active')) {
      var duration = 500;
      sync2.trigger('to.owl.carousel', [current, duration, true]);
    }
  }
  var thumbs = sync2.owlCarousel({
    items: 3,
    loop: false,
    margin: 0,
    nav: false,
    dots: false,
    responsive: {
      500: {
        items: 4,
      },
      768: {
        items: 5,
      },
      992: {
        items: 4,
      },
      1200: {
        items: 5,
      },
    },
    onInitialized: function (e) {
      var thumbnailCurrentItem = $(e.target).find(thumbnailItemClass).eq(this._current);
      thumbnailCurrentItem.addClass('synced');
    },
  })
    .on('click', thumbnailItemClass, function (e) {
      e.preventDefault();
      var duration = 500;
      var itemIndex = $(e.target).parents(thumbnailItemClass).index();
      sync1.trigger('to.owl.carousel', [itemIndex, duration, true]);
    }).on("changed.owl.carousel", function (el) {
      var number = el.item.index;
      $owl_slider = sync1.data('owl.carousel');
      $owl_slider.to(number, 500, true);
    });
  sync1.owlCarousel();
}

(function ($) {
  "user strict";
  // Preloader Js
  $(window).on('load', function () {
    $('.preloader').fadeOut(1000);
    var img = $('.bg_img');
    img.css('background-image', function () {
      var bg = ('url(' + $(this).data('background') + ')');
      return bg;
    });
    galleryMasonary();
  });
  // Gallery Masonary
  function galleryMasonary() {
    // filter functions
    var $grid = $(".product-wrapper");
    var filterFns = {};
    $grid.isotope({
      itemSelector: '.product-item',
      masonry: {
        columnWidth: 0,
      }
    });
    // bind filter button click
    $('ul.filter').on('click', 'li', function () {
      var filterValue = $(this).attr('data-filter');
      // use filterFn if matches value
      filterValue = filterFns[filterValue] || filterValue;
      $grid.isotope({
        filter: filterValue
      });
    });
    // change is-checked class on buttons
    $('ul.filter').each(function (i, buttonGroup) {
      var $buttonGroup = $(buttonGroup);
      $buttonGroup.on('click', 'li', function () {
        $buttonGroup.find('.active').removeClass('active');
        $(this).addClass('active');
      });
    });
  }

  $(document).ready(function () {
    $(':radio').change(function () {
      console.log('New star rating: ' + this.value);
    });
    $(".zoom_img").imagezoomsl({
      zoomrange: [3, 3],
      magnifierborder: "none",
      innerzoom: true,
      wheelDelta: true,
    });
    // Nice Select
    $('.select-bar:not(.false)').niceSelect();
    // PoPuP 
    $('.popup').magnificPopup({
      disableOn: 700,
      type: 'iframe',
      mainClass: 'mfp-fade',
      removalDelay: 160,
      preloader: false,
      fixedContentPos: false,
      disableOn: 300
    });
    $("body").each(function () {
      $(this).find(".img-pop").magnificPopup({
        type: "image",
        gallery: {
          enabled: true
        }
      });
    });
    // aos js active
    new WOW().init()
    //Faq
    $('.faq-wrapper .faq-title').on('click', function (e) {
      var element = $(this).parent('.faq-item');
      if (element.hasClass('open')) {
        element.removeClass('open');
        element.find('.faq-content').removeClass('open');
        element.find('.faq-content').slideUp(200, "swing");
      } else {
        element.addClass('open');
        element.children('.faq-content').slideDown(200, "swing");
        element.siblings('.faq-item').children('.faq-content').slideUp(200, "swing");
        element.siblings('.faq-item').removeClass('open');
        element.siblings('.faq-item').find('.faq-title').removeClass('open');
        element.siblings('.faq-item').find('.faq-content').slideUp(200, "swing");
      }
    });

    //Menu Dropdown Icon Adding
    $("ul>li>.submenu").parent("li").addClass("menu-item-has-children");
    $("ul>li>.category-sublink").parent("li").addClass("cate-icon");
    $("ul>li>.sub-category").parent("li").addClass("cate-icon2");

    // drop down menu width overflow problem fix
    $('ul').parent('li').hover(function () {
      var menu = $(this).find("ul");
      var menupos = $(menu).offset();
      if (menupos.left + menu.width() > $(window).width()) {
        var newpos = -$(menu).width();
        menu.css({
          left: newpos
        });
      }
    });

    $('.menu li a').on('click', function (e) {
      var element = $(this).parent('li');
      if (element.hasClass('open')) {
        element.removeClass('open');
        element.find('li').removeClass('open');
        element.find('ul').slideUp(300, "swing");
      } else {
        element.addClass('open');
        element.children('ul').slideDown(300, "swing");
        element.siblings('li').children('ul').slideUp(300, "swing");
        element.siblings('li').removeClass('open');
        element.siblings('li').find('li').removeClass('open');
        element.siblings('li').find('ul').slideUp(300, "swing");
      }
    });

    $('.categories li .open-links').on('click', function (e) {
      var element = $(this).parent('li');
      if (element.hasClass('open')) {
        element.removeClass('open');
        element.find('li').removeClass('open');
        element.children('ul').slideUp(300, "swing");
      } else {
        element.addClass('open');
        element.children('ul').slideDown(300, "swing");
        element.siblings('li').children('ul').slideUp(300, "swing");
        element.siblings('li').removeClass('open');
        element.siblings('li').find('li').removeClass('open');
        element.siblings('li').find('ul').slideUp(300, "swing");
      }
    });

    //Click event to scroll to top
    $('.scrollToTop').on('click', function () {
      $('html, body').animate({
        scrollTop: 0
      }, 500);
      return false;
    });
    //Header Bar
    $('.header-bar').on('click', function () {
      $(this).toggleClass('active');
      $('.body-overlay').toggleClass('active');
      $('.mobile-menu').toggleClass('active');
    })
    $('.mobile-menu-close').on('click', function () {
      $('.body-overlay').removeClass('active');
      $('.mobile-menu').removeClass('active');
      $('.header-bar').removeClass('active');
    })
    //Tab Section
    $('.tab ul.tab-menu').addClass('active').find('> li:eq(0)').addClass('active');
    $('.tab ul.tab-menu li').on('click', function (g) {
      var tab = $(this).closest('.tab'),
        index = $(this).closest('li').index();
      tab.find('li').siblings('li').removeClass('active');
      $(this).closest('li').addClass('active');
      tab.find('.tab-area').find('div.tab-item').not('div.tab-item:eq(' + index + ')').hide(10);
      tab.find('.tab-area').find('div.tab-item:eq(' + index + ')').fadeIn(10);
      g.preventDefault();
    });
    //Related Slider
    $('.related-slider').owlCarousel({
      loop: false,
      margin: 0,
      responsiveClass: true,
      items: 2,
      nav: true,
      dots: false,
      autoplay: true,
      responsive: {
        400: {
          items: 3,
        },
        550: {
          items: 4,
        },
        768: {
          items: 5,
        },
        992: {
          items: 7,
        },
        1200: {
          items: 4,
        },
        1300: {
          items: 5,
        },
        1400: {
          items: 6,
        },
        1500: {
          items: 8,
        },
      }
    })
    //Category Slider
    $('.category-slider').owlCarousel({
      loop: false,
      margin: 0,
      responsiveClass: true,
      items: 2,
      nav: true,
      dots: false,
      autoplay: true,
      responsive: {
        550: {
          items: 3,
        },
        768: {
          items: 4,
        },
        992: {
          items: 3,
        },
        1200: {
          items: 4,
        },
      }
    })
    var owl = $('.product-slider-2').owlCarousel({
      loop: false,
      margin: 0,
      responsiveClass: true,
      items: 1,
      nav: true,
      dots: false,
      autoplay: true,
      responsive: {
        500: {
          items: 2,
        },
        992: {
          items: 3,
        },
        1200: {
          items: 4,
        },
      }
    })
    $('.related-products-slider').owlCarousel({
      loop: false,
      margin: 0,
      responsiveClass: true,
      items: 1,
      nav: true,
      dots: false,
      autoplay: true,
      responsive: {
        500: {
          items: 2,
        },
        992: {
          items: 3,
        }
        ,
        1200: {
          items: 4,
        }
      }
    })
    //Odometer
    $(".dashboard-item").each(function () {
      $(this).isInViewport(function (status) {
        if (status === "entered") {
          for (var i = 0; i < document.querySelectorAll(".odometer").length; i++) {
            var el = document.querySelectorAll('.odometer')[i];
            el.innerHTML = el.getAttribute("data-odometer-final");
          }
        }
      });
    });
    $('.product-slider-1').owlCarousel({
      loop: false,
      margin: 30,
      responsiveClass: true,
      items: 1,
      nav: false,
      dots: false,
      autoplay: true,
      responsive: {
        576: {
          items: 2,
        },
        992: {
          items: 3,
        },
        1200: {
          items: 4,
        }
      }
    })

    initSlider();

    function initSlider() {
      $(".banner__slider").owlCarousel({
        items: 1,
        loop: false,
        autoplay: true,
        nav: false,
        autoHeight: true,
        onInitialized: startProgressBar,
        onTranslate: resetProgressBar,
        onTranslated: startProgressBar,
        animateIn: 'fadeIn',
        animateOut: 'fadeOut',
      });
    }

    function startProgressBar() {
      $(".slide-progress").css({
        width: "100%",
        transition: "width 5000ms"
      });
    }

    function resetProgressBar() {
      $(".slide-progress").css({
        width: 0,
        transition: "width 0s"
      });
    }

    $('.remove-cart').on('click', function (e) {
      e.preventDefault();
      $(this).parent().parent().hide(300);
    });

    $('.body-overlay').on('click', function () {
      $('.cart-sidebar-area').removeClass('active');
      $('.body-overlay').removeClass('active');
      $('.header-bar').removeClass('active');
      $('.mobile-menu').removeClass('active');
      $('.category-sidebar').removeClass('active');
      $('.dashboard-menu').removeClass('active');
    });

    $('.side-sidebar-close-btn').on('click', function (e) {
      e.preventDefault();
      $('.cart-sidebar-area').removeClass('active');
      $('.body-overlay').removeClass('active');
      $('.dashboard-menu').removeClass('active');
    });

    $('#account-btn').on('click', function (e) {
      e.preventDefault();
      $('#account-sidebar-area').addClass('active');
      $('.body-overlay').addClass('active');
    });

    $('#wish-button').on('click', function (e) {
      e.preventDefault();
      $('#wish-sidebar-area').addClass('active');
      $('.body-overlay').addClass('active');
    });

    $('#cart-button').on('click', function (e) {
      e.preventDefault();
      $('#cart-sidebar-area').addClass('active');
      $('.body-overlay').addClass('active');
    });

    $('.view-category>a').on('click', function () {
      $('.left-category').toggleClass('active');
    });

    $('.filter-in').on('click', function () {
      $('.category-sidebar').addClass('active');
      $('.body-overlay').addClass('active');
    });

    $('.close-sidebar').on('click', function () {
      $('.category-sidebar').removeClass('active');
      $('.body-overlay').removeClass('active');
    });

    $('.change-grid-to-6').on('click', function () {
      $(this).parent().children().removeClass('active');
      $(this).addClass('active');
      $('#grid-view .grid-control').removeClass('list_view_active');
      $('#grid-view').find('.grid-control').removeClass('col-lg-4 col-lg-6 col-lg-3').addClass('col-lg-6');
    });
    $('.change-grid-to-4').on('click', function () {
      $(this).parent().children().removeClass('active');
      $(this).addClass('active');
      $('#grid-view .grid-control').removeClass('list_view_active');
      $('#grid-view').find('.grid-control').removeClass('col-lg-4 col-lg-6 col-lg-3').addClass('col-lg-4');
    });
    $('.change-grid-to-3').on('click', function () {
      $(this).parent().children().removeClass('active');
      $(this).addClass('active');
      $('#grid-view .grid-control').removeClass('list_view_active');
      $('#grid-view').find('.grid-control').removeClass('col-lg-4 col-lg-6 col-lg-3').addClass('col-lg-3');
    });

    $(".owl-prev").html('<i class="las la-angle-left"></i>');
    $(".owl-next").html('<i class="las la-angle-right"></i>');



    $('.hidden_form_show').on('click', function () {
      if ($(this).prop("checked") == true) {
        $(this).parent('.checkgroup').siblings('.hidden-form').slideDown();
      }
      else if ($(this).prop("checked") == false) {
        $(this).parent('.checkgroup').siblings('.hidden-form').slideUp();
      }
    });
    $('.active_lang').on('click', function () {
      $('.language_setting_list').toggleClass('active');
    })
    $('.view-list-style').on('click', function () {
      $('.view-grid-style').removeClass('active');
      $(this).addClass('active');
      $('#grid-view .grid-control').addClass('list_view_active');
      $('#grid-view .grid-control .single_content').show();
    })
    $('.view-grid-style').on('click', function () {
      $('.view-list-style').removeClass('active');
      $(this).addClass('active');
      $('#grid-view .grid-control').removeClass('list_view_active');
      $('#grid-view .grid-control .single_content').hide();
    })
    $('.close-menu').on('click', function () {
      $(".mega-menu-area li").addClass('open');
    })
    $('.mega-menu-area li').on('mouseover', function () {
      $(this).removeClass('open');
    })
  });
})(jQuery);