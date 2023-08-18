(function() {

  function getCookie(name) {
    var v = document.cookie.match('(^|;) ?' + name + '=([^;]*)(;|$)');
    return v ? v[2] : null;
  }
  function setCookie(name, value, days) {
    var d = new Date;

    d.setTime(d.getTime() + 24*60*60*1000*days);

    document.cookie = name + "=" + value + ";path=/;expires=" + d.toGMTString();
  }
  function deleteCookie(name) {
    setCookie(name, '', -1);
  }

  function initialBoot() {
    var wrapper = document.getElementById('font-resize-wrapper');
    var storedFontSize = getCookie('fontResizer');

    if (storedFontSize === null) return;

    wrapper.style.fontSize = parseFloat(storedFontSize) + 'px';
  }

  function handleDecreaseFontSize(event) {
    event.preventDefault();

    decreaseFontSize();
  }
  function decreaseFontSize() {
    var wrapper = document.getElementById('font-resize-wrapper');
    var styles = getComputedStyle(wrapper);
    var currentFontSize = parseFloat(styles.fontSize, 10);
    var newFontSize = currentFontSize / 1.2;

    setCookie('fontResizer', newFontSize, 100);
    wrapper.style.fontSize = newFontSize + 'px';
  }

  function handleIncreaseFontSize(event) {
    event.preventDefault();

    increaseFontSize();
  }
  function increaseFontSize() {
    var wrapper = document.getElementById('font-resize-wrapper');
    var styles = getComputedStyle(wrapper);
    var currentFontSize = parseFloat(styles.fontSize, 10);
    var newFontSize = currentFontSize * 1.2;

    setCookie('fontResizer', newFontSize, 100);
    wrapper.style.fontSize = newFontSize + 'px';
  }

  // Add event listeners.
  var decreaseButtons = document.querySelectorAll('.js-decrease-font-size');
  for (var i = 0; i < decreaseButtons.length; i++) {
    var decreaseButton = decreaseButtons[i];

    decreaseButton.addEventListener('click', handleDecreaseFontSize);
  }

  var increaseButtons = document.querySelectorAll('.js-increase-font-size');
  for (var i = 0; i < increaseButtons.length; i++) {
    var increaseButton = increaseButtons[i];

    increaseButton.addEventListener('click', handleIncreaseFontSize);
  }

  initialBoot();
})();
