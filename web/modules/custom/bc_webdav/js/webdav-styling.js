console.log('loaded');

(function($, Drupal, drupalSettings) {

  $(".webdav-buttons .webdav-button").on("click", function() {
    $("#webdavframe").attr("src", '/webdavhandler/?action=' + $(this).data('action') + '&id=' + $(this).data('id'));
  });


})(jQuery, Drupal, drupalSettings);


function showBcWebdavLogData(json) {
  if (json.success) {
    var modalContent = '';
    json.data.forEach((row) => {
      // Construct the content to be displayed in the modal
      modalContent += '<p>User: ' + row.user + ', Action: ' + row.action + ', Time: ' + row.time + '</p>';
    });

    // Populate the modal's body with the constructed content
    $('#customHistoryModal .modal-body').html(modalContent);

    // Show the modal
    $('#customHistoryModal').show();
  }
}

// Add event listener for closing the modal
$('#customHistoryModal .close').on('click', function() {
  $('#customHistoryModal').hide();
});

