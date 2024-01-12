(function ($, Drupal) {
  // Define the showBcWebdavLogData function first
  window.showBcWebdavLogData = function (json) {
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
      $('#customHistoryModalOverlay').show();
    } else {
      modalBody.html('<p>Ingen historie er tilgængelig for denne fil.</p>');
    }
  };

  // Attach event handlers here
  Drupal.behaviors.customWebdavBehavior = {
    attach: function (context, settings) {
      $(".webdav-buttons .webdav-button", context).on("click", function() {
        $("#webdavframe").attr("src", '/webdavhandler/?action=' + $(this).data('action') + '&id=' + $(this).data('id'));
      });

      $('#customHistoryModalCloseButton', context).on('click', function () {
        $('#customHistoryModal').hide();
        $('#customHistoryModalOverlay').hide(); // Hide the overlay
      });

      // Close the modal when the overlay is clicked
      $('#customHistoryModalOverlay', context).on('click', function () {
        $('#customHistoryModal').hide();
        $(this).hide(); // Hide the overlay
      });
    }
  };
})(jQuery, Drupal);
