
(function($, Drupal, drupalSettings) {

  const updateFlagSidebar = function() {

      let nid = 0;
      if (drupalSettings.data.nid) {
        nid = drupalSettings.data.nid;
      }

      $.ajaxSetup({cache: false});
      $.getJSON('/bc_flag_extension/data', {nid: nid}, function (response) {

        let uib = $(".sidebar-bookmark-list-ul").empty();
        let uis = $(".sidebar-shortcut-list-ul").empty();
        let uiu = $(".sidebar-unread-list-ul").empty();

        if (Object.keys(response.bookmark).length > 0) {
          $(".sidebar-block.sidebar-bookmark").css("display", "flex");
          $.each(response.bookmark, function (id, item) {
            uib.append($('<li/>').addClass('bookmark-item').attr("data-id", item.flag)
              .append($('<a/>').addClass("bookmark-item-link").attr("href", item.link).text(item.title))
            );
          });
        }

        if (Object.keys(response.shortcut).length > 0) {
          $(".sidebar-block.sidebar-shortcut").css("display", "flex");
          $.each(response.shortcut, function (id, item) {
            uis.append($('<li/>').addClass('shortcut-item').attr("data-id", item.flag)
              .append($('<a/>').addClass("shortcut-item-link").attr("href", item.link).text(item.title))
            );
          });
        }

        if (Object.keys(response.unread).length > 0) {
          $(".sidebar-block.sidebar-unread").css("display", "flex");
          $(".unread-number").html(Object.keys(response.unread).length);
          $.each(response.unread, function (id, item) {

            uiu.append($('<li/>').addClass('unread-item').attr("data-id", item.flag)
              .append($('<a/>').addClass("unread-item-link").attr("href", item.link).text(item.title))
            );



          });
        }



      }).done(function(response) {

        if ( typeof(response.unreads_count) !== "undefined") {
          if (typeof(response.unreads_count.og_group_content) !== "undefined") {
            let unreads = response.unreads_count.og_group_content.count;
            let menuitem = $('.menu-item *[data-drupal-link-system-path="node/12"]').first();
            if (unreads && menuitem.length == 1) {
              menuitem.text( menuitem.text() + ' (' + unreads + ')');
            }
          }
        }
      });

  };

  updateFlagSidebar();

  Drupal.behaviors.flagAction = { attach: function attach(context) {
    jQuery('.flag a').once('flagAction').on('click', function() {
      setTimeout(updateFlagSidebar, 500);
    });
  }};


})(jQuery, Drupal, drupalSettings);

