console.log('webdav js loaded');

(function($, Drupal, drupalSettings) {
  $(document).on("click", ".webdav-button.webdav-history", function(event) {
    event.preventDefault();
    var fileId = $(this).data('id');
    fetchHistoryData(fileId);
  });

  function fetchHistoryData(fileId) {
    $.ajax({
      url: '/webdavhandler/?action=history&id=' + fileId,
      dataType: 'json',
      success: function(response) {
        showBcWebdavLogData(response);
      },
      error: function() {
        alert('Error fetching history');
      }
    });
  }

  function showBcWebdavLogData(json) {
    if (json.success) {
      var modalContent = '';
      json.data.forEach((row) => {
        modalContent += '<p>User: ' + row.user + ', Action: ' + row.action + ', Time: ' + row.time + '</p>';
      });
      $('#customHistoryModal .modal-body').html(modalContent);
      $('#customHistoryModal').show();
    }
  }

  $(document).ready(function() {
    $('#customHistoryModal .close').on('click', function() {
      $('#customHistoryModal').hide();
    });
  });
})(jQuery, Drupal, drupalSettings);
