function navigationArrow(path){

  $("#nav ul li ul").addClass("push"); // This is line 3
  $("#nav ul li.page_item").addClass("enhanced");
  $("#nav ul li.page_item").removeClass("page_item");
  $("#nav ul li.enhanced ul").after(' <img src="' + path + '" />');
  $("#nav ul li.enhanced img").wrap('<a class="arrow rest"></a>');

  $("#nav ul li a.arrow").hover(function(){
    $(this).removeClass("rest").addClass("hover");
  }, function(){
    $(this).removeClass("hover").addClass("rest");
  });
  
  $("#nav ul li a.arrow").click(function(){
    if ($(this).hasClass("hover") == true) {
      $("#nav ul li a.open").removeClass("open").addClass("rest");
      $("#nav ul li ul").hide();
      $(this).removeClass("hover").addClass("open");
      $(this).parent().find("ul").fadeIn();
    } else {
      if ($(this).hasClass("open") == true) {
        $(this).removeClass("open").addClass("hover");
        $(this).parent().find("ul").hide();
      }
    }
  });

  $(document).click(function(event){
    var target = $(event.target);
    if (target.parents("#nav").length == 0) {
      $("#nav ul li a.arrow").removeClass("open").addClass("rest");
      $("#nav ul li ul").hide();
    }
	});

}