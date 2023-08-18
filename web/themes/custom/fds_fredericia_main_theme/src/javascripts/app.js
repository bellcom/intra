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


// double banner to slider
(function($, Drupal, drupalSettings) {

  var bannerSelector = '.field--name-field-os2web-page-paragraph-bann';
  var bannerCount = document.querySelectorAll('.field--name-field-os2web-page-paragraph-bann > .field__item');
  if (document.querySelector(bannerSelector) !== null && bannerCount.length > 1) {
    tns({
      container: bannerSelector,
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


  var selector = ".field--name-field-os2web-paragraphs.field__items";
  if (document.querySelectorAll(selector).length > 0) {
    var searchContainers  = document.querySelectorAll(selector);
    searchContainers.forEach(function(container, elm) {
      var items = container.querySelectorAll(selector + " > .field__item");
      var bannerItems = container.querySelectorAll(selector + " > .field__item .banner__image-outer ");
      if (bannerItems.length > 1 && items.length == bannerItems.length) {
        tns({
          container: container,
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
    });
  }
})(jQuery, Drupal, drupalSettings);

(function($) {

  $(".multi-search").on("click", function() {
    console.log(" search ");
    let form = $(".multi-search-form");
    if ( $(form).length > 0) {
      $(form).attr("action", "/da/" + $(this).data("search"));
      $(form).submit();
    }
    return false;
  });

})(jQuery);


// Tiny Slider for spotboxes slideshow.
(function() {
  var selector = '.paragraph--type--os2web-spotbox-reference.paragraph--slider .field__items';

  if (document.querySelector(selector) !== null) {

    // Run tiny slider.
    tns({
      container: selector,
      items: 1,
      slideBy: 1,
      autoplay: false,
      autoplayHoverPause: true,
      mouseDrag: true,
      gutter: 5,
      rewind: false,
      responsive: {
        768: {
          items: 3,
          slideBy: 3,
        },
      },
    });
  }
})();



  var closeBtns = document.querySelectorAll('.status-banner-close-btn');
  for (var i = 0; i < closeBtns.length; i++) {
    closeBtns[i].addEventListener('click', function () {
      var statusBanner = this.closest('.status-banner');
      if (statusBanner) {
        statusBanner.style.display = 'none';

        // Get the node ID
        var nodeId = statusBanner.getAttribute('data-node-id');

        // Store the closed state in local storage
        localStorage.setItem('status_banner_' + nodeId, 'closed');
      }
    });
  }

  // Check local storage for closed banners
  var banners = document.querySelectorAll('.status-banner');
  for (var i = 0; i < banners.length; i++) {
    var banner = banners[i];
    var nodeId = banner.getAttribute('data-node-id');
    var bannerState = localStorage.getItem('status_banner_' + nodeId);

    if (bannerState === 'closed') {
      banner.style.display = 'none';
    }
  }





  var megamenuOverlay = document.querySelector('.megamenu-overlay');
  var megamenuWrapper = document.querySelector('.megamenu-wrapper');
  var hamburgerButton = document.querySelector('.burger-btn');
  var closeButton = document.querySelector('.megamenu-close');

  // Get a reference to the nav-search button
  var navSearchButton = document.getElementById('nav-search');

  var closeSearchButton = document.getElementById('close-search');

// Get a reference to the top-search-wrapper element
  var topSearchWrapper = document.querySelector('.top-search-wrapper');

  // Toggle megamenu visibility when the hamburger button is clicked
  hamburgerButton.addEventListener('click', function() {
    megamenuOverlay.style.display = 'block';
    megamenuWrapper.style.display = 'block';
  });

  // Hide megamenu and overlay when the close button is clicked
  closeButton.addEventListener('click', function() {
    megamenuOverlay.style.display = 'none';
    megamenuWrapper.style.display = 'none';
  });

  // Hide megamenu and overlay when the overlay area is clicked
  megamenuOverlay.addEventListener('click', function() {
    megamenuOverlay.style.display = 'none';
    megamenuWrapper.style.display = 'none';
  });

  function toggleScrolling() {
    document.body.classList.toggle('no-scroll');
  }

// Add an event listener to the nav-search button
  navSearchButton.addEventListener('click', function() {
    // Toggle the 'transformed' class on the top-search-wrapper element
    topSearchWrapper.classList.toggle('transformed');
    toggleScrolling();
  });

  closeSearchButton.addEventListener('click', function() {
    topSearchWrapper.classList.toggle('transformed');
    toggleScrolling();
  });



// (function ($, Drupal) {
//   Drupal.behaviors.customExternalLinks = {
//     attach: function (context, settings) {
//       var $banner = $('.custom-external-links-banner');
//       var $button = $('.custom-external-links-toggle');
//
//       $banner.hide(); // Hide the banner initially
//
//       $button.on('click', function () {
//         $banner.toggle(); // Toggle the visibility of the banner on button click
//
//         // Change the FontAwesome icon
//         var $icon = $(this).find('.fa-solid');
//         $icon.toggleClass('fa-link fa-link-slash');
//       });
//
//       $('.custom-external-links-button', context).on('click', function () {
//         var url = $('.custom-external-links-select', context).val();
//         if (url) {
//           window.open(url, '_blank');
//         }
//       });
//     }
//   };
// })(jQuery, Drupal);

function customExternalLinks() {
  var banner = document.querySelector('.custom-external-links-banner');
  var button = document.querySelector('.custom-external-links-toggle');

  banner.style.display = 'none'; // Hide the banner initially

  button.addEventListener('click', function () {
    banner.style.display = banner.style.display === 'none' ? 'flex' : 'none'; // Toggle the visibility of the banner on button click
  });

  var customExternalLinksButton = document.querySelector('.custom-external-links-button');
  customExternalLinksButton.addEventListener('click', function () {
    var selectElement = document.querySelector('.custom-external-links-select');
    var url = selectElement.value;
    if (url) {
      window.open(url, '_blank');
    }
  });
}

document.addEventListener('DOMContentLoaded', function () {
  customExternalLinks();
});



(function ($, Drupal) {
  Drupal.behaviors.anchorLinkHighlight = {
    attach: function (context, settings) {
      // Select all anchor links in the ToC
      var anchorLinks = $('.toc-outer-wrapper a', context);

      // Select all anchor paragraphs in the content
      var anchorParagraphs = $('.anchor', context);

      // Update the active link based on the scroll position
      function updateActiveLink() {
        var activeAnchor = null;

        // Loop through each anchor paragraph and check if any part of its content is visible
        anchorParagraphs.each(function () {
          var top = $(this).offset().top;
          var bottom = top + $(this).outerHeight();
          var scroll = $(window).scrollTop();
          var windowHeight = $(window).height();

          // Check if any part of the content is visible in the viewport
          if ((top >= scroll && top <= scroll + windowHeight) || (bottom >= scroll && bottom <= scroll + windowHeight) || (top <= scroll && bottom >= scroll + windowHeight)) {
            activeAnchor = $(this).attr('id');
            return false; // Exit the loop early if an active anchor is found
          }
        });

        // Remove the active class from all links and add it to the active anchor
        anchorLinks.removeClass('active');
        if (activeAnchor) {
          anchorLinks.filter('[href="#' + activeAnchor + '"]').addClass('active');
        }
      }

      // Call the updateActiveLink function when the page loads and when the user scrolls
      $(window, context).on('load scroll', function () {
        updateActiveLink();
      });

      // Handle the click event for the ToC links
      anchorLinks.on('click', function (event) {
        event.preventDefault();
        var targetAnchor = $(this).attr('href');

        // Scroll smoothly to the target section
        $('html, body').animate({
          scrollTop: $(targetAnchor).offset().top
        }, 150);
      });
    }
  };
})(jQuery, Drupal);


document.addEventListener('DOMContentLoaded', function () {
  const showMoreButton = document.querySelector('.show-more-button');
  const hiddenLinks = document.querySelector('.hidden-links');
  const showMoreButtonContent = document.querySelector('.show-more-button-content');

  if (showMoreButton && hiddenLinks) {
    showMoreButton.addEventListener('click', function () {
      if (hiddenLinks.style.display === 'none') {
        hiddenLinks.style.display = 'flex';
        showMoreButtonContent.textContent = 'Skjul';
        showMoreButton.classList.remove('fa-plus');
        showMoreButton.classList.add('fa-minus');
      } else {
        hiddenLinks.style.display = 'none';
        showMoreButtonContent.textContent = 'Se flere muligheder';
        showMoreButton.classList.remove('fa-minus');
        showMoreButton.classList.add('fa-plus');
      }
    });
  }
});


document.addEventListener('DOMContentLoaded', function() {
  const slider = document.querySelector('.slider');
  const slidesContainer = document.querySelector('.slides-container');
  const slides = Array.from(document.querySelectorAll('.slide-item'));
  const prev = slider.querySelector('.prev');
  const next = slider.querySelector('.next');
  const dots = Array.from(document.querySelectorAll('.dot'));

  let slideInterval = setInterval(nextSlide, 5000);
  let currentIndex = 0;

  function showSlide(index) {
    const slideWidth = slides[0].clientWidth;
    slidesContainer.style.transform = `translateX(-${index * slideWidth}px)`;

    slides.forEach((slide, slideIndex) => {
      if (slideIndex !== index) {
        slide.classList.add('notActive');
      } else {
        slide.classList.remove('notActive');
      }
    });

    dots.forEach((dot, dotIndex) => {
      if (dotIndex !== index) {
        dot.classList.remove('active');
      } else {
        dot.classList.add('active');
      }
    });

    currentIndex = index;
  }

  showSlide(currentIndex);

  prev.addEventListener('click', () => {
    clearInterval(slideInterval);
    if (currentIndex === 0) {
      showSlide(slides.length - 1);
    } else {
      showSlide(currentIndex - 1);
    }
  });

  next.addEventListener('click', () => {
    clearInterval(slideInterval);
    nextSlide();
  });

  dots.forEach((dot, dotIndex) => {
    dot.addEventListener('click', () => {
      clearInterval(slideInterval);
      showSlide(dotIndex);
    });
  });

  function nextSlide() {
    if (currentIndex === slides.length - 1) {
      showSlide(0);
    } else {
      showSlide(currentIndex + 1);
    }
  }

  const sliderWidth = 100 * slides.length;
  slidesContainer.style.width = `${sliderWidth}%`;
});
