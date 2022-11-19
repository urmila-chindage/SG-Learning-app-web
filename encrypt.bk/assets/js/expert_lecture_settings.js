function youtube_parser() {
    $('.message_container').remove();
	var URL 		= $('#el_url').val();
    var regExp 		= /^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#\&\?]*).*/;
    var match 		= URL.match(regExp);
    if (URL != undefined || URL != '') {
        if (match && match[7].length==11){
            var yt_main_image   = 'http://img.youtube.com/vi/' + match[7] + '/mqdefault.jpg';
            var yt_thumb_image  = 'http://img.youtube.com/vi/' + match[7] + '/2.jpg';
            $('#youtube_video_image').val(yt_main_image);
            $('#youtube_video_thumbnail').val(yt_thumb_image);
            $('#el_url').removeClass('border-error');
            cleanPopUpMessage();
            //$('#expert_form').submit();
            return true;
        }
        else{
            cleanPopUpMessage();
            $('#expert_form').prepend(renderPopUpMessage('error', 'Please enter youtube URL'));
            $('#el_url').addClass('border-error');
            scrollToTopOfPage();
            return false;
        }

    }
    
}

$(document).on('click', '#expert_lecture_submit', function(){
	if(youtube_parser() == true)
        {
            $('#expert_form').submit();
        }
});