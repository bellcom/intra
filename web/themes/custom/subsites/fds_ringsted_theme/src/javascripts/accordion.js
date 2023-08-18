// Accordion - go to item in hash.
(function() {
  document.addEventListener("DOMContentLoaded", function() {
    var identifier = window.location.hash;
// debugger;
    if (identifier) {
      var accordionItem = document.getElementById(`${identifier}`);
      // var accordionItem = document.querySelector(identifier);

      if (accordionItem) {
        var listItem = accordionItem.closest('li');
        var content = accordionItem.closest('div');
        var button = listItem.querySelector('.accordion-button');

        // debugger
        // Expand item.
        content.setAttribute('aria-expanded', 'true');
        content.setAttribute('aria-hidden', 'false');

        button.setAttribute('aria-expanded', 'true');

        // Scroll into view.
        setTimeout(function() {
          button.scrollIntoView({ behavior: "smooth" });
        }, 200);
      }
    }
  });
})();

// Accordion - add link on click.
(function() {
  var buttons = document.querySelectorAll('.js-accordion-add-link');

  function handleClick(event) {
    var element = this;
    var elementID = element.getAttribute('aria-controls');

    history.pushState({}, '', '#' + elementID);
  }

  for (var i = 0; i < buttons.length; i += 1) {
    var button = buttons[i];

    button.addEventListener('click', handleClick);
  }
})();
