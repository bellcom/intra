document.addEventListener("DOMContentLoaded", function() {
  var accordionButtons = document.querySelectorAll('.accordion-button');

  function hideSiblingContents(currentButton) {
    var isNestedAccordion = currentButton.closest('.entity-default').parentElement.tagName === 'DIV';

    if (isNestedAccordion) {
      return;
    }

    var parentListItem = currentButton.closest('li');
    var siblingAccordions = parentListItem.parentElement.querySelectorAll(':scope > li > .entity-default > .accordion-content.opened');

    siblingAccordions.forEach(function(content) {
      if (content !== currentButton.nextElementSibling) {
        content.classList.remove('opened');
        content.previousElementSibling.setAttribute('aria-expanded', 'false');
      }
    });
  }

  function toggleAccordion(button, isOpening) {
    var contentId = button.getAttribute('aria-controls');
    var content = document.getElementById(contentId);

    if (isOpening) {
      content.classList.add('opened');
      button.setAttribute('aria-expanded', 'true');
      updateURLHash(contentId);

    } else {
      content.classList.remove('opened');
      button.setAttribute('aria-expanded', 'false');
      updateURLHash('');
    }
  }

  function updateURLHash(contentId) {
    if (history.pushState) {
      var newurl = new URL(window.location.href);
      newurl.searchParams.set('accordion', contentId);
      window.history.pushState({ path: newurl.href }, '', newurl.href);
    }
  }

  accordionButtons.forEach(function(button) {
    button.addEventListener('click', function(event) {
      var isOpening = !button.nextElementSibling.classList.contains('opened');
      hideSiblingContents(button);
      toggleAccordion(button, isOpening);
    });
  });

  function openParentAccordions(element) {
    // This function recursively opens all parent accordions of a nested accordion
    var parentAccordionContent = element.closest('.accordion-content');
    if (parentAccordionContent) {
      var parentButton = parentAccordionContent.previousElementSibling;
      if (parentButton && !parentButton.classList.contains('opened')) {
        toggleAccordion(parentButton, true);
        openParentAccordions(parentButton);
      }
    }
  }

  // Modified URL parameter handling to accommodate nested accordions with adjusted scroll
  var urlParams = new URLSearchParams(window.location.search);
  var accordionId = urlParams.get('accordion');
  if (accordionId) {
    var targetAccordionButton = document.querySelector(`[aria-controls="${accordionId}"]`);
    if (targetAccordionButton) {
      // Open all parent accordions first
      openParentAccordions(targetAccordionButton);
      // Then open the target accordion
      toggleAccordion(targetAccordionButton, true);
      // Adjusted scrolling to not align accordion at the very top
      setTimeout(function() {
        const accordionElement = document.getElementById(accordionId);
        if (accordionElement) {
          const yOffset = -200; // Adjust this offset as needed
          const y = accordionElement.getBoundingClientRect().top + window.pageYOffset + yOffset;

          window.scrollTo({top: y, behavior: 'smooth'});
        }
      }, 300); // Adjust the timeout as needed
    }
  }
});
