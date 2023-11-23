(function ($, Drupal) {
  Drupal.behaviors.customBehavior = {
    attach: function (context, settings) {
      var url = window.location.href;

      // If we are on the 'document-handle' page and have the #edit-is-allowed-link element, then open Word for the users.
      if (url.indexOf('document-handle') > -1) {
        if ($('#edit-is-allowed-link', context).length > 0) {
          // Click on the link instead of opening in a popup.
          // Because browsers may block popups.
          $('#edit-is-allowed-link', context).get(0).click();
        }

        // Navigate the user back to the previous page after 5 seconds.
        setTimeout(function () {
          history.back();
        }, 5000);
      }
    }
  };
})(jQuery, Drupal);
