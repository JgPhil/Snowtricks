/* -------------------------------------------------------------------------
    begin Scroll Down Button
  * ------------------------------------------------------------------------- */
(function () {
  var btnScrollDown = document.querySelector("body > div.toto > div > div.jumbotron > div > a");
  btnScrollDown.addEventListener('click', scrollDown);
})();

/* -------------------------------------------------------------------------
   end Scroll Down Button
 * ------------------------------------------------------------------------- */


 /* -------------------------------------------------------------------------
    begin Scroll Up Button
  * ------------------------------------------------------------------------- */
(function () {
  var btnScrollUp = document.querySelector("#js-btn-scroll-up");
  btnScrollUp.addEventListener('click', scrollUp);
})();

/* -------------------------------------------------------------------------
   end Scroll Up Button
 * ------------------------------------------------------------------------- */

function scrollDown() {
  var windowCoords = document.documentElement.clientHeight;
  (function scroll() {
    if (window.pageYOffset < windowCoords) {
      window.scrollBy(0, 10);
      setTimeout(scroll, 0);
    }
    if (window.pageYOffset > windowCoords) {
      window.scrollTo(0, windowCoords);
    }
  })();
}

function scrollUp() {
  var windowCoords = document.documentElement.clientHeight;
  (function scroll() {
    if (window.pageYOffset < windowCoords) {
      window.scrollBy(0, 10);
      setTimeout(scroll, 0);
    }
    if (window.pageYOffset > windowCoords) {
      window.scrollTo(0, windowCoords);
    }
  })();
}

scrollUpBtn.addEventListener('click', function(){
  scrollUpBtn.style.display = 'none';
})