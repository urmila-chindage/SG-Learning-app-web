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
        localStorage.setItem('refreshTime', JSON.stringify(lastRefreshTime));
    }else{
        var start = lastRefreshTime[__controller];
        var end = Date.now();
        var elapsed = (end - start) / 1000;
        elapsed = parseInt(elapsed);
        if(elapsed < 300){
           return false;
        }   
    }

    var xhr = new XMLHttpRequest();
    xhr.open('GET', __siteUrl+__controller+'/language');
    xhr.onload = function() {
        if (xhr.status === 200) {
            language = JSON.parse(xhr.responseText);
            language  = language.language;
            for (var key in language) {
                if (language.hasOwnProperty(key)) {
                    localStorage.setItem(key, language[key]);
                }
            }
            lastRefreshTime[__controller] = Date.now();
            localStorage.setItem('refreshTime', JSON.stringify(lastRefreshTime));

        }
    };
    xhr.send();
}

function lang(key)
{
    return localStorage.getItem(key);
}

getConfiguredWebUrls();
getLanguageItems();
/*$(document).ready(function(){
});*/

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
       return false;
    }   

    var xhr = new XMLHttpRequest();
    xhr.open('GET', admin_url+'configuration');
    xhr.onload = function() {
        if (xhr.status === 200) {
            webUrl = JSON.parse(xhr.responseText);
            for (var key in webUrl) {
                if (webUrl.hasOwnProperty(key)) {
                    localStorage.setItem(key, webUrl[key]);
                }
            }
            webConfiguredUrlsRefreshTime = Date.now();
            localStorage.setItem('webConfiguredUrlsRefreshTime', webConfiguredUrlsRefreshTime);

        }
    };
    xhr.send();
}

function webConfigs(key)
{
    return localStorage.getItem(key);
}
