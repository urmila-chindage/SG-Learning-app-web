if(localStorage.getItem('refreshTime') == null){
    var refreshTime = {};
    localStorage.setItem('refreshTime', JSON.stringify(refreshTime));
}

var language = new Array();
function getLanguageItems()
{
    var lastRefreshTime = JSON.parse(localStorage.getItem('refreshTime'));
    if( typeof lastRefreshTime[__controller] == 'undefined'){
        lastRefreshTime[__controller] = Date.now();
        console.log(lastRefreshTime[__controller]);
        localStorage.setItem('refreshTime', JSON.stringify(lastRefreshTime));
    }else{
        var start = lastRefreshTime[__controller];
        var end = Date.now();
        var elapsed = (end - start) / 1000;
        elapsed = parseInt(elapsed);
        if(elapsed < 300){
           //return false;
        }   
    }
    $.ajax({
        url: site_url+'/'+__controller+'/language',
        type: "POST",
        data:{ "is_ajax":true},
        success: function(response) {
            language  = $.parseJSON(response);
            language  = language.language;
            $.each(language, function( index, value ) {
              localStorage.setItem(index, value);
            });
            lastRefreshTime[__controller] = Date.now();
            localStorage.setItem('refreshTime', JSON.stringify(lastRefreshTime));
        }
    });    
}

function lang(key)
{
   // console.log(language);
    return localStorage.getItem(key);
}

$(document).ready(function(){
    getLanguageItems();
    getConfiguredWebUrls();
});

if(localStorage.getItem('webConfiguredUrlsRefreshTime') == null){
    var webConfiguredUrlsRefreshTime = Date.now();
    localStorage.setItem('webConfiguredUrlsRefreshTime', webConfiguredUrlsRefreshTime);
}

var webUrl = new Array();
function getConfiguredWebUrls()
{
    var start   = localStorage.getItem('webConfiguredUrlsRefreshTime');
    var end     = Date.now();
    var elapsed = (end - start) / 1000;
        elapsed = parseInt(elapsed);
    if(elapsed < 300){
       //return false;
    }   
    
    $.ajax({
        url: __admin_url+'configuration',
        type: "POST",
        data:{ "is_ajax":true},
        success: function(response) {
            webUrl  = $.parseJSON(response);
            $.each(webUrl, function( index, value ) {
              localStorage.setItem(index, value);
            });
            webConfiguredUrlsRefreshTime = Date.now();
            localStorage.setItem('webConfiguredUrlsRefreshTime', webConfiguredUrlsRefreshTime);
        }
    });    
}

function webConfigs(key)
{
    return localStorage.getItem(key);
}
