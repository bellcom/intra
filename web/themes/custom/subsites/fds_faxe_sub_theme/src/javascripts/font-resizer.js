(function() {
  const html = jQuery("html");
  jQuery(document).ready(function () {
    jQuery(".js-increase-font-size").click(function () {
      modifyFontSize("increase");
    });
    jQuery(".js-decrease-font-size").click(function () {
      modifyFontSize("decrease");
    });


    function modifyFontSize(flag) {
      let currentFontSize = parseInt(html.css("font-size"));
      if (flag == "increase") {
        switch (html.css("font-size")) {
          case "10px":
            currentFontSize = 12;
            break;
          case "12px":
            currentFontSize = 13;
            break;
          case "13px":
            currentFontSize = 15;
            break;
          case "15px":
            currentFontSize = 17;
            break;
          case "17px":
            currentFontSize = 20;
            break;
        }
      } else if (flag == "decrease") {
        switch (html.css("font-size")) {
          case "20px":
            currentFontSize = 17;
            break;
          case "17px":
            currentFontSize = 15;
            break;
          case "15px":
            currentFontSize = 13;
            break;
          case "13px":
            currentFontSize = 12;
            break;
          case "12px":
            currentFontSize = 10;
            break;
        }
      }
      localStorage.setItem("fontSize", currentFontSize);
      html.css("font-size", parseInt(currentFontSize));
    }
    html.css("font-size", parseInt(localStorage.getItem("fontSize")));
  });
})();
