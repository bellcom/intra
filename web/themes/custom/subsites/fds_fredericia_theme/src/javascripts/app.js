// Search.
document.addEventListener('DOMContentLoaded', function() {
  function toggle(event) {
    var element = this;
    var parent = element.closest('.searchy');

    parent.classList.toggle('searchy--visible-form');
  }

  var buttons = document.querySelectorAll('.js-toggle-searchy');

  for (var i = 0; i < buttons.length; i++) {
    var button = buttons[i];

    button.addEventListener('click', toggle);
  }
});

// Open all file-links in a new window.
(function() {
  var links = document.querySelectorAll('.field--type-file .file a');

  for (var i = 0; i < links.length; i++) {
    var link = links[i];

    link.setAttribute('target', '_blank');
  }
})();

// Custom mobile navigation.
(function() {
  function handleToggle(event) {
    var menu = document.querySelector('.custom-mobile-navigation');

    menu.classList.toggle('custom-mobile-navigation--open');
  }

  var buttons = document.querySelectorAll('.js-custom-mobile-navigation-toggle');

  for (var i = 0; i < buttons.length; i += 1) {
    var button = buttons[i];

    button.addEventListener('click', handleToggle);
  }
})();

// Items for "Senest besøgte indhold".
(function($, Drupal, drupalSettings) {
  function addToLocalStorage(path) {
    var heading = document.querySelectorAll('h1');
    var currentLocalStorage = JSON.parse(localStorage.getItem('visitedContent')) || [];

    // Filter away current path if its already there.
    const filteredLocalStorage = currentLocalStorage.filter(function(item) {
      if (item.path === path) {
        return false;
      }

      return true;
    });

    // Add new path.
    filteredLocalStorage.push({
      label: (heading[0] && heading[0].innerText) || document.title,
      path: path,
    });

    // Convert back into a string.
    var updatedLocalStorageObj = JSON.stringify(filteredLocalStorage);

    return localStorage.setItem('visitedContent', updatedLocalStorageObj);
  }

  var allowedNodeTypeClassnames = [
    'page-node-type-os2web-news',
    'page-node-type-os2web-page',
  ];

  // Run through allowed classes.
  for (var i = 0; i < allowedNodeTypeClassnames.length; i += 1) {
    var allowedClassname = allowedNodeTypeClassnames[i];

    // The current page is supposed to be logged.
    if (document.body.classList.contains(allowedClassname)) {
      var currentPath = drupalSettings.path.currentPath;

      addToLocalStorage(currentPath);
    }
  }

  var wrapper = document.getElementById('js-visited-content');
  var items = JSON.parse(localStorage.getItem('visitedContent'));
  var listNode = document.createElement('UL');
  var noOfItemsToDisplay = 6;

  for (var i = 0; i < items.length && i < noOfItemsToDisplay; i += 1) {
    var item = items[i];
    var listItemNode = document.createElement('LI');
    listItemNode.innerHTML = '<a href=' + item.path + '>' + item.label + '</a>';

    listNode.prepend(listItemNode);
  }

  wrapper.innerHTML = '';
  wrapper.prepend(listNode);
})(jQuery, Drupal, drupalSettings);



  function toggleVisibility(id) {
    var btn2 = document.getElementById('btn2');
    var e = document.getElementById(id);
    if (e.style.display == 'none') {
      e.style.display = 'flex';
      btn2.innerText = 'Skjul';


    } else {
      e.style.display = 'none';
      btn2.innerText = 'Åben';
    }
    return;
  }


