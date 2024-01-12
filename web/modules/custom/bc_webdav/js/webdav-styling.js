
(function($, Drupal, drupalSettings) {

  $(".webdav-buttons .webdav-button").on("click", function() {
    $("#webdavframe").attr("src", '/webdavhandler/?action=' + $(this).data('action') + '&id=' + $(this).data('id'));
  });


})(jQuery, Drupal, drupalSettings);


function showBcWebdavLogData(json) {
  var modalBody = $('#customHistoryModal .modal-body');

  if (json.success) {
    var contentHtml = '';
    json.data.forEach((row) => {
      // Construct the HTML content from each row
      contentHtml += '<div class="history-entry">';
      contentHtml += '<p>Time: ' + row.time + '</p>'; // Use the 'time' field
      contentHtml += '<p>User: ' + row.user + '</p>'; // Use the 'user' field
      contentHtml += '<p>Action: ' + row.action + '</p>'; // Use the 'action' field
      contentHtml += '</div>';
    });

    // Set the modal body's content
    modalBody.html(contentHtml);

    // Open the modal
    $('#customHistoryModal').show();
  } else {
    modalBody.html('<p>No history data available.</p>');
  }
}

