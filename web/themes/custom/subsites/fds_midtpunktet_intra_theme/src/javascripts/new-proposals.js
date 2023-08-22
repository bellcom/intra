// Proposals form toggle.
(function($) {
  "use strict";
  $(document).ready(function ($) {
    $('#create_proposal_link').on('click', function (evt) {
      evt.preventDefault();
      $('.new-proposal-form').toggleClass('d-none');
    });
  });
})(jQuery);
