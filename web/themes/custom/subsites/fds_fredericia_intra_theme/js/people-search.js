(function (Drupal, once) {
  Drupal.behaviors.fredericiaPeopleSearch = {
    attach(context) {
      once('fredericia-people-search', '.view-people-search .views-field', context).forEach((field) => {
        if (field.querySelector('img')) {
          return;
        }

        const content = field.querySelector('.field-content');
        if (!content || content.textContent.trim() === '') {
          field.remove();
        }
      });
    },
  };
})(Drupal, once);
