// JavaScript code for sidebar tabs starts here
$(document).ready(function(){
$(".link").click(function(){
    $(".innercontent").hide();
    theDiv = $(this).attr('data-ID');
    $(theDiv).show();
});	


$(".pdfloader").hover(
	function(){$(".quicktools").css('display','block');},
	function(){	$(".quicktools").css('display','none');}
);

var selector = 'li.link';

$(selector).on('click', function(){
	//alert(selector);
    $(selector).removeClass('activelist');
    $(this).addClass('activelist');
});
 
 
 $(".myButton").click(function () {
	//alert('we are in function');
    var effect = 'slide';
    var options = { direction:'right' };
    var duration = 500;
    $('#slideDiv').toggle(effect, options, duration);
});
 
  
  	
});