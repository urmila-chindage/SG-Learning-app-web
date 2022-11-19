// JavaScript Document
var __slideWidth = '70%';
$(document).ready(function(e) {
  if($(window).width() <= 800){
	  __slideWidth = '0%';
  }
});
$(window).on("resize", function(event){
  __slideWidth = '70%';
  if($(this).width() <= 800){
	  __slideWidth = '0%';
  }
});

$(document).on('click', '.browse-questions', function(){
	$('.left').animate({width:__slideWidth}, '3000');
	if($('.left').width() <= 800){
	  $('.static-foot').hide();
  	}
	$('.curiculums').hide();
	$('.discussions').show();
});

$(document).on('click', '.browse-curiculum', function(){
	$('.left').animate({width:__slideWidth}, '3000');
	if($('.left').width() <= 800){
	  $('.static-foot').hide();
  	}
	$('.discussions').hide();
	$('.curiculums').show();
});

$(document).on('click', '.close-questions', function(){
	$('.left').animate({width:'100%'}, '3000');
	$('.static-foot').show();
});

$(document).on('click', '.close-curiculum', function(){
	$('.left').animate({width:'100%'}, '3000');
	$('.static-foot').show();
});

$(document).on('click','.individual-question',function(){
	$('.discussions').hide();
	$('.add-new-questions').hide();
	$('.question-detail').fadeIn('slow')
});

$(document).on('click','.back-question',function(){
	$('.question-detail').hide();
	$('.discussions').fadeIn('slow')
});

$(document).on('click','.back-main',function(){
	$('.add-new-questions').hide();
	$('.discussions').fadeIn('slow')
});

$(document).on('click','.ask-question',function(){
	$('.discussions').hide();
	$('.add-new-questions').fadeIn('slow')
});

$(document).on('click', '.close-detail', function(){
	$('.question-detail').hide();
	$('.left').animate({width:'100%'}, '3000');
	$('.static-foot').show();
});

$(document).on('click', '.add-answer', function(){
 //$('.add-answer').animate({height:'80px'},200);
	 $('#comment_textarea').redactor({
	    imageUpload: admin_url+'configuration/redactore_image_upload',
	    placeholder: 'Write your response',
	    focus: true,
	    callbacks: {
	        imageUploadError: function(json, xhr)
	        {
	             // var erorFileMsg = "This file type is not allowed. upload a valid image.";
	             // $('#course_form').prepend(renderPopUpMessage('error', erorFileMsg));
	             // scrollToTopOfPage();
	             // return false;
	        }
	    }  
	});
 	$('.add-btn').css('display','block');
});

$(document).on('click', '#add_discussion', function(){
 	$('.add-new-questions').hide();
	$('.discussions').fadeIn('slow')
});