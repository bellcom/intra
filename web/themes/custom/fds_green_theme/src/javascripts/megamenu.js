// Megamenu toggle.
(function() {
  function handleToggle(e) {
    var body = document.querySelector('body');

    body.classList.toggle('custom-megamenu--visible');
  }

  var toggles = document.querySelectorAll('.js-toggle-megamenu');

  for (var i = 0; i < toggles.length; i++) {
    var toggle = toggles[i];

    toggle.addEventListener('click', handleToggle);
  }
})();

// Reposition selfservice region in megamenu.
(function() {
  var region = document.querySelector('.region-megamenu__selfservice');
  var listItem = document.querySelector('.region-megamenu__navigation .menu-level-0 > li:last-child');

  if (region === null || listItem === null) {
    return;
  }

  listItem.appendChild(region);
})();

// Toggle submenu.
(function() {
  var links = document.querySelectorAll('.region-megamenu__navigation .menu-level-0 > li > a');

  function handleToggle(event) {
    event.preventDefault();

    var element = this;
    var currentListItem = element.parentElement;

    currentListItem.classList.toggle('open');
  }

  for (var i = 0; i < links.length; i += 1) {
    var link = links[i];

    link.addEventListener('click', handleToggle);
  }
})();

// Open active tree.
(function() {
  var activeLink = document.querySelector('.region-megamenu__navigation .menu-level-1 a.is-active');

  if (activeLink) {
    const listItem = activeLink.parentElement.parentElement.parentElement.parentElement;

    if (listItem) {
      listItem.classList.add('open');
    }
  }
})();
