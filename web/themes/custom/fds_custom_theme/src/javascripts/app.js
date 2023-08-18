jQuery(function ($) {
  'use strict';
});

// Language selector.
// Run through all links and truncate Danish to 2 chars. (ex. Da).
(function() {
  var links = document.querySelectorAll('.block-language ul a');

  for (var i = 0; i < links.length; i++) {
    var link = links[i];
    var text = 	link.textContent || link.innerText;
    var truncatedText = text.substring(0, 2);

    // Inject the content back into the DOM.
    if (link.textContent) {
      link.textContent = truncatedText;
    } else {
      link.innerText = truncatedText;
    }
  }
})();

// Accordion.
(function () {
  function handleClose(event) {
    var element = this;
    var listItem = element.closest('li');
    var content = listItem.querySelector('.accordion-content');
    var button = listItem.querySelector('.accordion-button');

    content.setAttribute('aria-expanded', 'false');
    content.setAttribute('aria-hidden', 'true');

    button.setAttribute('aria-expanded', 'false');
  }

  var buttons = document.querySelectorAll('.js-accordion-close-current');

  for (var i = 0; i < buttons.length; i++) {
    var button = buttons[i];

    button.addEventListener('click', handleClose);
  }
})();

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

// Content reference mobile display.
(function() {
  var selector = '.paragraph--type--os2web-content-reference .mobile-only .field--name-field-os2web-content-reference';

  if (document.querySelector(selector) !== null) {

    // Run tiny slider.
    tns({
      container: selector,
      items: 1,
      autoplay: true,
      autoplayHoverPause: true,
      gutter: 32,
      rewind: true,
    });
  }
})();

// Max height on sidenav lists.
(function() {
  function handleToggle(event) {
    var button = event.target;
    var list = button.closest('.sidenav-list');
    var listItem = button.parentNode;

    listItem.classList.add('limited-height__toggle--hidden');

    list.classList.add('limited-height--overridden');
  }

  function addToggleToList(list) {

    // Create a button.
    var textNode = document.createTextNode('Se flere');
    var buttonNode = document.createElement('BUTTON');
    buttonNode.appendChild(textNode);
    buttonNode.addEventListener('click', handleToggle);

    // Create a list item.
    var listItemNode = document.createElement('LI');
    listItemNode.classList.add('limited-height__toggle');
    listItemNode.appendChild(buttonNode);

    // Inject into list.
    list.appendChild(listItemNode);
  }

  var sidenavLists = document.querySelectorAll('.sidenav-list');

  for (var i = 0; i < sidenavLists.length; i++) {
    var list = sidenavLists[i];

    list.classList.add('limited-height');
    addToggleToList(list);
  }
})();

// Responsive restructuring.
(function() {
  var vw = Math.max(document.documentElement.clientWidth || 0, window.innerWidth || 0)
  var primaryImage = document.querySelector('.field--name-field-os2web-page-primaryimage');
  var insertInto = document.querySelector('.layout-sidebar-left');

  if (vw <= 768) {
    if (primaryImage && insertInto) {
      insertInto.insertAdjacentElement('afterbegin', primaryImage);
    }
  }
})();
