
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

        if (Object.keys(response.bookmarks).length > 0) {
          $(".sidebar-block.sidebar-bookmark").css("display", "flex");
          $.each(response.bookmarks, function (id, item) {
            uib.append($('<li/>').addClass('bookmark-item').attr("data-id", item.flag)
              .append($('<a/>').addClass("bookmark-item-link").attr("href", item.link).text(item.title))
            );
          });
        }

        if (Object.keys(response.shortcuts).length > 0) {
          $(".sidebar-block.sidebar-shortcut").css("display", "flex");
          $.each(response.shortcuts, function (id, item) {
            uis.append($('<li/>').addClass('shortcut-item').attr("data-id", item.flag)
              .append($('<a/>').addClass("shortcut-item-link").attr("href", item.link).text(item.title))
            );
          });
        }

        if (Object.keys(response.unreads).length > 0) {
          $(".sidebar-block.sidebar-unread").css("display", "flex");
          $(".unread-number").html(Object.keys(response.unreads).length);
          $.each(response.unreads, function (id, item) {
            uiu.append($('<li/>').addClass('unread-item').attr("data-id", item.flag)
              .append($('<a/>').addClass("unread-item-link").attr("href", item.link).text(item.title))
            );
          });

          if (uiu.find("li").length > 0) {
              uiu.prepend( $('<li/>')
                  .append( $('<span/>')
                    .addClass('unread-read-all')
                    .text("Markér alt som læst") )
              );

              $(".unread-read-all").on("click", function() {
                $.getJSON('/bc_flag_extension/set/unread', {action: "unreadall"}, function (response) {
                  if (response.success) {
                    uiu.empty();
                    $(".sidebar-block.sidebar-unread").css("display", "none");
                  }
                });
              });
          }
        }

      }).done(function(response) {

        if (typeof(response.menu_counters) !== "undefined") {
          $.each(response.menu_counters, function(id, item) {
            if (item.count > 0) {
              let menuitem = $('.menu-item *[data-drupal-link-system-path="node/' + item.node + '"]').first();
              if (menuitem.length === 1) {
                // Create a new span element for the counter
                let counterSpan = $('<span/>').addClass('menu-counter').text(item.count);

                // Clear previous appended elements to avoid duplication
                menuitem.find('.menu-counter').remove();

                // Append the new span to the menu item
                menuitem.append(counterSpan);
              }
            }
          });
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

