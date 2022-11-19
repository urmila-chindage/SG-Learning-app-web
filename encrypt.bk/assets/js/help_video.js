// create youtube player
var player;
function onYouTubePlayerAPIReady() {
    player = new YT.Player('help_content', {
      height: '500',
      width: '100%',
      videoId: __youtubeId,
      events: {
        'onStateChange': onPlayerStateChange
      }
    });
}


// when video ends
function onPlayerStateChange(event) {        
    if(event.data === 0 && __isFirstView == 1) { 
        $.ajax({
            url: admin_url+'user/course_view_completed',
            type: "POST",
            data:{"is_ajax":true},
            success: function(response) {
                $('#help_text_close_btn').html('<button type="button" class="btn btn-blue pull-right" data-dismiss="modal">Close</button>');
            }
        });
    }
}