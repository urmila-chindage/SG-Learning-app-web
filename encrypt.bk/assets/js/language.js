if(localStorage.getItem('refreshTime') == null){
    var refreshTime = {};
    localStorage.setItem('refreshTime', JSON.stringify(refreshTime));
}

var language = new Array();
function getLanguageItems()
{

    var lastRefreshTime = JSON.parse(localStorage.getItem('refreshTime'));
    if( lastRefreshTime != null && typeof lastRefreshTime[__controller] == 'undefined') {
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
    xhr.open('GET', admin_url+__controller+'/language');
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
    //console.log('test', key);
    let langItem = localStorage.getItem(key);
    if(!langItem) { 
        var lastRefreshTime = JSON.parse(localStorage.getItem('refreshTime'));
        if( lastRefreshTime != null && typeof lastRefreshTime[__controller] == 'undefined') {
            lastRefreshTime[__controller] = Date.now();
            localStorage.setItem('refreshTime', JSON.stringify(lastRefreshTime));
        }
        var xhr = new XMLHttpRequest();
        xhr.open('GET', admin_url+__controller+'/language', false);
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
        return localStorage.getItem(key);
    } else {
        return langItem;
    }
}

getConfiguredWebUrls();
getLanguageItems();
/*$(document).ready(function(){
});*/

if(localStorage.getItem('webConfiguredUrlsRefreshTime') == null){
    var webConfiguredUrlsRefreshTime = Date.now();
    localStorage.setItem('webConfiguredUrlsRefreshTime', webConfiguredUrlsRefreshTime);
}

function webConfigs(key)
{
    return localStorage.getItem(key);
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

