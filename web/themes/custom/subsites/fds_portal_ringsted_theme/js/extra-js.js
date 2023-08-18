// Toggle search filters on search-page on mobile
(function($) {
    "use strict";
    $(document).ready(function(){
        $('.btn-filters').on('click', function(e){
          $('.region-sidebar-second').toggleClass('filterVisible');
          $('.col-md-4').toggleClass('filterVisible');
        });
      });
  })(jQuery);