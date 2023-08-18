'use strict';

document.addEventListener('DOMContentLoaded', () => {
  AOS.init();

});


const hamburgerBtn = document.querySelector('.hamburger');
const menuElement = document.querySelector('.region.region--header');
const bodyElement = document.querySelector('body');

hamburgerBtn.addEventListener('click', () => {
  menuElement.classList.toggle('is-open');
  bodyElement.classList.toggle('is-fixed');
  hamburgerBtn.classList.toggle('active');
});

window.onresize = function () {
  var w = window.outerWidth;
  if (w > 768) {
    menuElement.classList.remove('is-open');
    bodyElement.classList.remove('is-fixed');
    hamburgerBtn.classList.remove('active');
  }
};

const subscribeBtn = document.querySelector('#subscribe-btn');

function removeSuccess() {
  subscribeBtn.classList.remove('success');
  subscribeBtnTextImage.classList.remove('success');
}

subscribeBtn.addEventListener('click', (e) => {
  subscribeBtn.classList.add('success');
  e.preventDefault();
  setTimeout(() => {
    removeSuccess();
  }, 3000);
});


const subscribeBtnTextImage = document.querySelector('#subscribe-btn-text-image');

subscribeBtnTextImage.addEventListener('click', (e) => {
  subscribeBtnTextImage.classList.add('success');
  e.preventDefault();
  setTimeout(() => {
    removeSuccess();
  }, 3000);
});



