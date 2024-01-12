console.log('loaded');

(function($, Drupal, drupalSettings) {

  $(".webdav-buttons .webdav-button").on("click", function() {
    $("#webdavframe").attr("src", '/webdavhandler/?action=' + $(this).data('action') + '&id=' + $(this).data('id'));
  });


})(jQuery, Drupal, drupalSettings);


function showBcWebdavLogData(json) {
  if (json.success) {
    json.data.forEach((row) => {
      console.log( row );
    });
  }
}
