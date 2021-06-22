(function ($) {

  Drupal.behaviors.faq_search = {
    attach: function (context, settings) {

      $(context).find('#edit-faq-search').bind('autocompleteselect', function(event, data) {
        $('.faq-search--content--question').each(function () {
          if($(this).text().match(data.item.value)) {
            $('.faq-search--content--answer').hide();
            let selectedClass = $(this).parent().parent().attr('class').split(' ')[1];
            $('.search-menu-item').each(function () {
              if ($(this).hasClass(selectedClass)) {
                $(this).addClass('selected');
              } else {
                $(this).removeClass('selected');
              }
            });
            $('.search-content-item').each(function () {
              if ($(this).hasClass(selectedClass)) {
                $(this).show();
              } else {
                $(this).hide();
              }
            });
            $(this).parent().find('.faq-search--content--answer').show();
            $(this).parent().find('.faq-search--content--question').addClass('is-active');
            var active_top = $(this).parent().find('.faq-search--content--question').offset().top;
            $('html, body').animate({
              scrollTop: active_top - 120
            }, 500);
          }
        });
      });

      $('.search-menu-item').on('click', function (e) {
        e.preventDefault();
        $('.search-menu-item').removeClass('selected');
        $(this).addClass('selected');
        let selectedClass = $(this).attr('class').split(' ')[1];
        $('.faq-search--content--question').removeClass('is-active');
        $('.faq-search--content--answer').hide();
        $('.search-content-item').each(function () {
          if ($(this).hasClass(selectedClass)) {
            $(this).show();
          } else {
            $(this).hide();
          }
        });
      });
      $('.faq-search--content--item .faq-search--content--question').on('click', function (e) {
        e.preventDefault();
        // $('.faq-search--content--answer').slideUp();
        $(this).parent().find('.faq-search--content--answer').stop(true,true).slideToggle();
        $(this).toggleClass('is-active');

      });


    }
  };

})(jQuery);
