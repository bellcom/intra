// Search.
(function() {
  var searchInputs = document.querySelectorAll('.search-input__input');
  var searchSubmits = document.querySelectorAll('.search-input__button');

  for (var i = 0; i < searchInputs.length; i++) {
    var input = searchInputs[i];

    input.setAttribute('aria-label', 'Indtast søgeord');
  }

  for (var j = 0; j < searchSubmits.length; j++) {
    var submit = searchSubmits[j];

    submit.setAttribute('aria-label', 'Søg');
  }
})();
