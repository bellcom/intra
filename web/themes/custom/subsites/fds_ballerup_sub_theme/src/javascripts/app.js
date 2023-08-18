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

  function generateValue(text) {
    return 'Hent: ' + text;
  }

  for (var i = 0; i < links.length; i++) {
    var link = links[i];

    link.innerHTML = generateValue(link.innerHTML);
    link.setAttribute('target', '_blank');
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


const layoutSidebarRight = document.querySelector('.layout-sidebar-right');
const layoutContent = document.querySelector('.layout-content');
const fieldOs2webPageHeading = document.querySelector('.field--name-field-os2web-page-heading h1');
const fieldOs2webPagePrimaryimage = document.querySelector('.field--name-field-os2web-page-primaryimage');
function changeBlockPadding() {
  if (layoutSidebarRight && fieldOs2webPageHeading && layoutContent && window.innerWidth > 767 && !fieldOs2webPagePrimaryimage) {
    const paddingTop = parseInt(window.getComputedStyle(layoutContent).getPropertyValue("padding-top"))
    const marginTop = parseInt(window.getComputedStyle(fieldOs2webPageHeading).getPropertyValue("margin-top"))
    const marginBottom = parseInt(window.getComputedStyle(fieldOs2webPageHeading).getPropertyValue("margin-bottom"))
    const height =  parseInt(window.getComputedStyle(fieldOs2webPageHeading).getPropertyValue("height"))
    layoutSidebarRight.style.paddingTop = paddingTop + marginTop + marginBottom + height + "px"
  } else {
    layoutSidebarRight.removeAttribute("style");
  }
}

changeBlockPadding()
window.addEventListener("resize", () => {
  changeBlockPadding()
});

// Tooltips on mailto: links.
(function() {
  var links = document.querySelectorAll('a[href^="mailto:"]');

  tippy(links, {
    content: '<div style="text-align: center;">Du er nu ved at sende en almindelig e-mail. <br />Hvis din besked indeholder personoplysninger, bør du i stedet sende den som en <br /><a href="/sikkerbesked" style="color: #fff;">sikker besked</a></div>',
    allowHTML: true,
    interactive: true,
  });
}());

// menu bar search
(function($) {
  let menusearch = $("button.menu-search");
  if (menusearch.length) {
    $("button.menu-search").on("click", function() {
      let searchvalue = $("input.menu-search").val();
      if (searchvalue.length > 1) {
        document.location.href = '/search/node?keys=' + searchvalue;
      }
    });
    $("input.menu-search").on("keyup", function(e) {
      let code = (e.keyCode ? e.keyCode : e.which);
      if (code==13) {
        let searchvalue = $("input.menu-search").val();
        if (searchvalue.length > 1) {
          document.location.href = '/search/node?keys=' + searchvalue;
        }
      }
    });
  }
})(jQuery);

(function($, Drupal, drupalSettings) {
  var selector = '.field--name-field-os2web-page-paragraph-bann';
  var count = document.querySelectorAll('.field--name-field-os2web-page-paragraph-bann > .field__item');
  if (document.querySelector(selector) !== null && count.length > 1) {
    var items = count.length;
    tns({
      container: selector,
      items: 1,
      autoplay: true,
      autoplayHoverPause: true,
      autoplayButtonOutput: false,
      gutter: 32,
      rewind: false,
      nav: true,
      speed: 600,
      controls: false
    });
  }
})(jQuery, Drupal, drupalSettings);
