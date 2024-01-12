(function ($, Drupal) {
  Drupal.behaviors.customWebdavBehavior = {
    attach: function (context, settings) {
      $(".webdav-buttons .webdav-button", context).on("click", function() {
        $("#webdavframe").attr("src", '/webdavhandler/?action=' + $(this).data('action') + '&id=' + $(this).data('id'));
      });

      $('#customHistoryModalCloseButton', context).on('click', function () {
        $('#customHistoryModal').hide();
      });

      // Close the modal when the overlay is clicked
      $('#customHistoryModalOverlay', context).on('click', function () {
        $('#customHistoryModal').hide();
        $(this).hide(); // Hide the overlay
      });

      window.showBcWebdavLogData = function(json) {
        var modalBody = $('#customHistoryModal .modal-body');

        if (json.success) {
          var filename = json.filename;
          var contentHtml = '<h2>' + filename + '</h2>'; // Display the filename
          contentHtml += '<div class="history-entries">';
          json.data.forEach((row) => {
            contentHtml += '<div class="history-entry">';
            contentHtml += '<div class="history-user"><p>' + row.user + '</p></div>';
            contentHtml += '<div class="history-time"><p>' + row.time + '</p></div>';
            contentHtml += '<div class="history-action"><p>' + row.action + '</p></div>';
            contentHtml += '</div>';
          });

          modalBody.html(contentHtml);
          $('#customHistoryModal').show();
        } else {
          modalBody.html('<p>No history data available.</p>');
        }
      };
    }
  };
})(jQuery, Drupal);
