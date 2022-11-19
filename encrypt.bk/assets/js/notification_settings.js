var barType = '1'; 
var __notificationContentTimeOut = null;
$(document).ready(function(e) {
	var notification_top_offset = $(".header").height() + $(".breadcrumb").height() + $(".courses-tab").height();
    $('#n_content').redactor({
    	toolbarFixedTopOffset   : notification_top_offset,
    	minHeight               : '200px',
        maxHeight               : '200px',
        imageUpload             : admin_url+'configuration/redactore_image_upload',
		plugins                 : ['source', 'limiter'],
		buttons                 : ['bold', 'italic', 'link'],
        callbacks               : {
                                    imageUploadError: function(json, xhr){
                                        var erorFileMsg = "This file type is not allowed. upload a valid image.";
                                        $('#notification_form').prepend(renderPopUpMessage('error', erorFileMsg));
                                        scrollToTopOfPage();
                                        return false;
                                    } ,
                                    keyup: function(e){
                                        clearTimeout(__notificationContentTimeOut);
                                        __notificationContentTimeOut = setTimeout(function(){
                                            $('.n_content_html').html('');   
                                            $('.n_content_html').html($('#n_content').redactor('source.getCode'));     
                                        }, 600);
                                    }
                                  }   
    });
    barType = $('input[name="n_bar_type"]:checked').val();
    showAndHideInfoBar();
});
$(function() {
    var today = new Date();
    $("#n_expiry_date").datepicker({
        language    : 'en',
        minDate     : today,
        dateFormat  : 'dd-mm-yy',
        autoClose   : true
    });
});
function  showAndHideInfoBar(){
    $('#top_notification_slider').hide();
    $('#information-modal').hide();
    
    if(barType == '1')
    {
        $('#information-modal').show();
        
    }
    else{

        $('#top_notification_slider').show();
    }
}
$('.n_bar').click(function(){

    barType = $(this).val();
    showAndHideInfoBar(barType);
    
})