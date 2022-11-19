$(document).ready(function($) {
 $(".custom-input-group input").focus(function(){
   $(this).parent().addClass("active");

  }).blur(function(){
       $(this).parent().removeClass("active");
  })
});  




