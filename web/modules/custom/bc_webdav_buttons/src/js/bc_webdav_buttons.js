(function ($, Drupal, drupalSettings) {

  Drupal.behaviors.bcWebdavButtons = {
    attach: function (context, settings) {
      // Function to retrieve the log.
      function getLog(fid) {
        $.getJSON('/webdav_get_log?fid=' + fid, function (data) {
          var log = $('#bc-webdav-button-log');
          var faded = $('#bc-webdav-button-faded');

          // Clear log.
          log.html('');
          log.append($('<div>').addClass('close_button').text('X'));

          // Show headline.
          console.log(data);
          log.append($('<h1>').text(data[0].filename));
          log.append($('<div>').addClass('info').text('Denne log gælder fra juni 2017 og fremefter.'));

          if (data[0].name === null) {
            log.append($('<span>').text('Intet fundet'));
          } else {
            // Show log items.
            $.each(data, function (key, row) {
              var itemWrapper = $('<div>').addClass('bc-webdav-button-log-item-wrapper');
              itemWrapper.append($('<a>').attr('href', row.user_url).addClass('name').text(row.name));
              itemWrapper.append($('<span>').text(row.timestamp));
              log.append(itemWrapper);
            });
          }

          // Show log.
          log.show();
          faded.fadeIn(200);
        });
      }

      // Function to hide the log.
      function hideLog() {
        var log = $('#bc-webdav-button-log');
        var faded = $('#bc-webdav-button-faded');
        log.hide();
        faded.fadeOut();
      }

      // Function to save log.
      function saveLog(action, element) {
        $.ajax({
          method: 'GET',
          url: '/webdav_log?nid=' + element.data('nid') + '&fid=' + element.data('fid') + '&action=' + action
        });
      }

      // Event handlers.
      $('.bc-webdav-button-edit', context).once('bc-webdav-button-edit').click(function (e) {
        if ($(this).hasClass('bc-webdav-button-disabled')) {
          e.preventDefault();
          return;
        }
        saveLog('edit', $(this));
      });

      $('.bc-webdav-button-view', context).once('bc-webdav-button-view').click(function () {
        saveLog('view', $(this));
      });

      $('.bc-webdav-button-history', context).once('bc-webdav-button-history').click(function (e) {
        e.preventDefault();
        getLog($(this).data('fid'));
      });

      $(document).keydown(function (e) {
        if (e.keyCode === 27) {
          // "ESC" key pressed.
          hideLog();
        }
      });

      $(document).on('click', '#bc-webdav-button-faded', function () {
        // Clicking outside of the log (the faded area) will close it.
        hideLog();
      });

      $(document).on('click', '#bc-webdav-button-log .close_button', function () {
        hideLog();
      });
    }
  };

})(jQuery, Drupal, drupalSettings);
