// Search.
(function() {
  var searchInputs = document.querySelectorAll('.search-input__input');

  for (var i = 0; i < searchInputs.length; i++) {
    var input = searchInputs[i];

    input.setAttribute('aria-label', 'Indtast søgeord');
  }
})();
