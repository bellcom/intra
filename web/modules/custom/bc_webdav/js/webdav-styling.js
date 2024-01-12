(function($, Drupal, drupalSettings) {
  // Using event delegation to ensure event binding even on dynamically generated elements
  $(document).on("click", ".webdav-buttons .webdav-button", function(event) {
    var action = $(this).data('action');
    var fileId = $(this).data('id');

    console.log('Button clicked, Action: ' + action + ', File ID: ' + fileId);

    if (action === 'history') {
      event.preventDefault();
      console.log('Fetching history for File ID: ' + fileId);
      fetchHistoryData(fileId);
    } else {
      $("#webdavframe").attr("src", '/webdavhandler/?action=' + action + '&id=' + fileId);
    }
  });

  function fetchHistoryData(fileId) {
    $.ajax({
      url: '/webdavhandler/?action=history&id=' + fileId,
      dataType: 'json',
      success: function(response) {
        console.log('History data received:', response);
        if (response.success) {
          showHistoryModal(response.data);
        }
      },
      error: function(jqXHR, textStatus, errorThrown) {
        console.error('Error fetching history:', textStatus, errorThrown);
      }
    });
  }

  function showHistoryModal(data) {
    var modalContent = '';
    data.forEach(function(row) {
      modalContent += '<p>User: ' + row.user + ', Action: ' + row.action + ', Time: ' + row.time + '</p>';
    });
    $('#customHistoryModal .modal-body').html(modalContent);
    $('#customHistoryModal').show();
  }

  // Close modal functionality
  $(document).on('click', '#customHistoryModal .close', function() {
    $('#customHistoryModal').hide();
  });

})(jQuery, Drupal, drupalSettings);
