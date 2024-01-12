(function($, Drupal, drupalSettings) {
  $(document).ready(function() {
    $(document).on("click", ".webdav-buttons .webdav-button", function(event) {
      var action = $(this).data('action');
      var fileId = $(this).data('id');

      if (action === 'history') {
        event.preventDefault();
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
          if (response.success) {
            showHistoryModal(response.data);
          }
        },
        error: function() {
          alert('Error fetching history');
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

    $('#customHistoryModal .close').on('click', function() {
      $('#customHistoryModal').hide();
    });
  });
})(jQuery, Drupal, drupalSettings);
