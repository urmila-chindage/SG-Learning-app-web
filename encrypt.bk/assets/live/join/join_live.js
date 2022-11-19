
        
    

window.onbeforeunload = function() {
    saveFormData();
    return null;
}

window.onbeforeunload = function() {
    saveFormData();
    return null;
}

$(document).ready(function(){
    $(window).bind('beforeunload', function () {
        //this will work only for Chrome
        saveFormData();
    });

    $(window).bind("unload", function () {
        //this will work for other browsers
        saveFormData();
    });
});

function saveFormData() {
    console.log("test");
    $.ajax({
        async: false,
        url: webConfigs('site_url')+'/live/change_live_status_onclose',
        type: "POST",
        data:{"is_ajax":true, "live_id":__live_id, "make_online":0}, 
        success: function(response) {

        }
    });
}
    

