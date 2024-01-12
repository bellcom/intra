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
          var contentHtml = '';
          json.data.forEach((row) => {
            contentHtml += '<div class="history-entry">';
            contentHtml += '<p>Time: ' + row.time + '</p>';
            contentHtml += '<p>User: ' + row.user + '</p>';
            contentHtml += '<p>Action: ' + row.action + '</p>';
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
