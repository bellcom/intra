(function ($) {
  const html = $("html");
  $(document).ready(function () {
    $("a[data-increase-font-size]").click(function () {
      modifyFontSize("increase");
    });
    $("a[data-decrease-font-size]").click(function () {
      modifyFontSize("decrease");
    });


    function modifyFontSize(flag) {
      let currentFontSize = parseInt(html.css("font-size"));
      if (flag == "increase") {
        switch (html.css("font-size")) {
          case "10px":
            currentFontSize = 15;
            break;
          case "15px":
            currentFontSize = 23;
            break;
        }
      } else if (flag == "decrease") {
        switch (html.css("font-size")) {
          case "23px":
            currentFontSize = 15;
            break;
          case "15px":
            currentFontSize = 10;
            break;
        }
      }
      localStorage.setItem("fontSize", currentFontSize);
      html.css("font-size", parseInt(currentFontSize));
    }
    html.css("font-size", parseInt(localStorage.getItem("fontSize")));

  });
})(jQuery);
