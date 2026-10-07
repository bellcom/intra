(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.navigateBack = {
    attach: function (context, settings) {
      function isSearchPage(url) {
        return url.includes('/s?sq=') || url.includes('/sog-selvbetjening?sq=') || url.includes('/sog-dagsorden?sq=');
      }

      if (!isSearchPage(window.location.href)) {
        localStorage.setItem('lastNonSearchPage', window.location.href);
      }

      once('navigate-back-button', '.search-close-btn', context).forEach(function (element) {
        $(element).click(function (e) {
          e.preventDefault();

          const lastNonSearchPage = localStorage.getItem('lastNonSearchPage');

          if (lastNonSearchPage) {
            window.location.href = lastNonSearchPage;
          } else {
            window.location.href = drupalSettings.path.baseUrl;
          }
        });
      });
    }
  };
})(jQuery, Drupal, drupalSettings);
