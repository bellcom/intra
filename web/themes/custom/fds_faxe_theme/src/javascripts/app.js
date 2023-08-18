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

window.addEventListener('load', () => {
  window.scrollTo(0, 0);
});

// Set focus on the first navigational element on page load
window.addEventListener('load', () => {
  const firstTabElement = document.querySelector('#skip-to-main');
  firstTabElement.focus();
});
