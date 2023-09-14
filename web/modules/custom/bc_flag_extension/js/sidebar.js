
(function($, Drupal, drupalSettings) {

  const updateFlagSidebar = function() {
    let bookmark = $(".sidebar-block.sidebar-bookmark");
    if (bookmark.length && bookmark.is(":visible")) {
      let nid = 0;
      if (drupalSettings.data.nid) nid = drupalSettings.data.nid;
      $.ajaxSetup({cache: false});
      $.getJSON('/bc_flag_extension/data', {nid: nid}, function (response) {

        let uib = $(".sidebar-bookmark-list-ul").empty();
        let uis = $(".sidebar-shortcut-list-ul").empty();
        let uiu = $(".sidebar-unread-list-ul").empty();

        if (Object.keys(response.bookmarks).length > 0) {
          $.each(response.bookmarks, function (id, item) {
            uib.append($('<li/>').addClass('bookmark-item').attr("data-id", item.flag)
              .append($('<a/>').addClass("bookmark-item-link").attr("href", item.link).text(item.title))
            );
          });
        }

        if (Object.keys(response.shortcuts).length > 0) {
          $.each(response.shortcuts, function (id, item) {
            uis.append($('<li/>').addClass('shortcut-item').attr("data-id", item.flag)
              .append($('<a/>').addClass("shortcut-item-link").attr("href", item.link).text(item.title))
            );
          });
        }

        if (Object.keys(response.unreads).length > 0) {
          $.each(response.unreads, function (id, item) {
            uiu.append($('<li/>').addClass('unread-item').attr("data-id", item.flag)
              .append($('<a/>').addClass("unread-item-link").attr("href", item.link).text(item.title))
            );
          });
        }

      }).done(function() {  });
    }
  };

  updateFlagSidebar();

  Drupal.behaviors.flagAction = { attach: function attach(context) {
    jQuery('.flag a').once('flagAction').on('click', function() {
      setTimeout(updateFlagSidebar, 500);
    });
  }};


})(jQuery, Drupal, drupalSettings);


(function ($) {
  Drupal.behaviors.countLiElements = {
    attach: function (context, settings) {
      // Wait for the DOM to be ready.
      $(document).ready(function () {
        // Find the <ul> element with a specific class or ID.
        var $ul = $('.sidebar-unread-list-ul'); // Update the selector accordingly.

        // Count the <li> elements inside the <ul>.
        var liCount = $ul.find('li').length;

        // Display the count in your desired location (e.g., a <span>).
        $('.li-count').text(liCount); // Update the selector accordingly.
      });
    }
  };
})(jQuery, Drupal);