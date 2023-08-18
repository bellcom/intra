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
  const buttons = document.querySelectorAll('.js-custom-mobile-navigation-toggle');
  const html =  document.querySelector('html')
  const menu = document.querySelector('.custom-mobile-navigation');
  buttons.forEach(button => {
    button.addEventListener('click', () => {
      menu.classList.toggle('custom-mobile-navigation--open');
      html.classList.toggle('mobile-menu-open');
    });
  })
  window.addEventListener("resize", () => {
    if(html.classList.contains("mobile-menu-open"))
    menu.classList.remove('custom-mobile-navigation--open');
    html.classList.remove('mobile-menu-open');
  })
})();




document.addEventListener('DOMContentLoaded', function() {
  const searchForm = document.querySelector('.custom-header-row--desktop-navigation .region-small-search form');
  const searchInput = document.querySelector('.custom-header-row--desktop-navigation .region-small-search form input');
  const searchBtnIcon = document.querySelector('.custom-header-row--desktop-navigation .region-small-search form .form-actions');
  function getPageAndElementDistance() {
    if(searchBtnIcon && searchInput && searchForm) {
      searchBtnIcon.style.right =  -window.innerWidth - -30 - -searchForm.getBoundingClientRect().right + "px" //adding position for search icon
      searchInput.style.width =  window.innerWidth - searchForm.getBoundingClientRect().right + "px" //adding width for search input
    }
  }


  getPageAndElementDistance()
  window.addEventListener("resize", () => {
    getPageAndElementDistance()
  })

});

jQuery(document).ready(function(){
  window.slinky = jQuery('.custom-mobile-navigation nav').slinky({
    title: true
  })
});

jQuery(document).ready(function(){
  if(jQuery( ".paragraph--type--os2web-accordion" )) {
    jQuery( ".paragraph--type--os2web-accordion" ).parent( ".field__item" ).addClass("paragraph--type--os2web-accordion-wrapper");
  }
  if(jQuery( ".entity-teaser--os2web-event" ) && jQuery( ".layout-content" )) {
    jQuery( ".entity-teaser--os2web-event" ).parents( ".layout-content" ).addClass("entity-teaser--os2web-event-wrapper");
  }
  if(jQuery( ".layout-content" ) && jQuery( ".node--os2web-news--full" )) {
    let paddingTopSize = parseInt(jQuery( ".layout-content" ).css('padding-top'))
    jQuery( ".node--os2web-news--full" ).css('margin-top', (paddingTopSize * -1) + "px")
  }
  if(jQuery( ".node--type-os2web-event" )) {
    jQuery( ".node--type-os2web-event" ).closest( ".layout-content" ).addClass("node--type-os2web-event-wrapper");
  }
  if(jQuery( ".entity-default--os2web-spotbox-reference" )) {
    jQuery( ".entity-default--os2web-spotbox-reference" ).parent( ".field__item" ).addClass("paragraph--type--os2web-spotbox-wrapper");
  }
  if(jQuery( ".region-content" ) && jQuery( ".field--name-field-os2web-page-paragraph-narr > .field__item") ) {
    if(jQuery( ".field--name-field-os2web-page-paragraph-narr > .field__item").last().hasClass('paragraph--type--os2web-spotbox-wrapper')) {
      jQuery( ".region-content" ).addClass("spotbox-el-is-last")
    };
  }
});


// // Items for "Senest besøgte indhold".
// (function($, Drupal, drupalSettings) {
//   function addToLocalStorage(path) {
//     var heading = document.querySelectorAll('h1');
//     var currentLocalStorage = JSON.parse(localStorage.getItem('visitedContent')) || [];

//     // Filter away current path if its already there.
//     const filteredLocalStorage = currentLocalStorage.filter(function(item) {
//       if (item.path === path) {
//         return false;
//       }

//       return true;
//     });

//     // Add new path.
//     filteredLocalStorage.push({
//       label: (heading[0] && heading[0].innerText) || document.title,
//       path: path,
//     });

//     // Convert back into a string.
//     var updatedLocalStorageObj = JSON.stringify(filteredLocalStorage);

//     return localStorage.setItem('visitedContent', updatedLocalStorageObj);
//   }

//   var allowedNodeTypeClassnames = [
//     'page-node-type-os2web-news',
//     'page-node-type-os2web-page',
//   ];

//   // Run through allowed classes.
//   for (var i = 0; i < allowedNodeTypeClassnames.length; i += 1) {
//     var allowedClassname = allowedNodeTypeClassnames[i];

//     // The current page is supposed to be logged.
//     if (document.body.classList.contains(allowedClassname)) {
//       var currentPath = drupalSettings.path.currentPath;

//       addToLocalStorage(currentPath);
//     }
//   }

//   var wrapper = document.getElementById('js-visited-content');
//   var items = JSON.parse(localStorage.getItem('visitedContent'));
//   var listNode = document.createElement('UL');
//   var noOfItemsToDisplay = 6;

//   for (var i = 0; i < items.length && i < noOfItemsToDisplay; i += 1) {
//     var item = items[i];
//     var listItemNode = document.createElement('LI');
//     listItemNode.innerHTML = '<a href=' + item.path + '>' + item.label + '</a>';

//     listNode.prepend(listItemNode);
//   }

//   wrapper.innerHTML = '';
//   wrapper.prepend(listNode);
// })(jQuery, Drupal, drupalSettings);
