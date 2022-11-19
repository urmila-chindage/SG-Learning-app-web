function uploadFiles(uploadURL, param, callback, __dataType)
{
    var formDataType = (typeof __dataType == 'undefined')?'json':__dataType;
    var formData  = new FormData();
        for(key in param)
        {
            formData.append(key, param[key]);

        }
    var processingId = (typeof param['processing'] != 'undefined')?param['processing']:'percentage_count';
    var jqXHR = $.ajax({
            xhr: function() {
            var xhrobj = $.ajaxSettings.xhr();
            if (xhrobj.upload) {
                    xhrobj.upload.addEventListener('progress', function(event) {
                        var percent = 0;
                        var position = event.loaded || event.position;
                        var total = event.total;
                        if (event.lengthComputable) {
                            percent = Math.ceil(position / total * 100);
                        }
                        $('.progress-bar').css('width', percent+'%');
                        $('.progress-bar .sr-only').html(percent+'%'+lang('complete'));
                        $('.percentage-text').html(percent+'%');
                        //Set progress
                        if( percent == 100){
                            $('#'+processingId).parent().html(lang('processing_please_wait'));
                        }
                        console.log(percent);
                    }, false);
                }
            return xhrobj;
        },
    url: uploadURL,
    type: "POST",
    datatype: formDataType,
    contentType:false,
    processData: false,
        cache: false,
        data: formData,
        success: function(data){
           callback(data);
        }
    }); 
}

function validateMaxLength(selector)
{
    var maxlength       = $('#'+selector).attr('maxlength');
    var current_length  = $('#'+selector).val().length;
    var remaining       = parseInt(maxlength - current_length);
    var left_character  = (remaining == 1)?lang('character_left'):lang('characters_left');
    $('#'+selector+'_char_left').html(remaining+' '+left_character);
}
function isValidYoutubeURL(str){
    var URL         = str;
    var regExp      = /^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#\&\?]*).*/;
    var match       = URL.match(regExp);
    if (URL != undefined || URL != '') {
        if (match && match[7].length==11){
            return true;
        }
    }
}
function isValidURL(str) {
  var pattern = new RegExp('^(https?:\\/\\/)?'+ // protocol
  '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.?)+[a-z]{2,}|'+ // domain name
  '((\\d{1,3}\\.){3}\\d{1,3}))'+ // OR ip (v4) address
  '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*'+ // port and path
  '(\\?[;&a-z\\d%_.~+=-]*)?'+ // query string
  '(\\#[-a-z\\d_]*)?$','i'); // fragment locator
  return pattern.test(str);
}

function removeArrayIndex(array, index) {
      for(var i = array.length; i--;) {
          if(array[i] === index) {
              array.splice(i, 1);
          }
      }
}

function inArray(needle, haystack) {
    var length = haystack.length;
    for(var i = 0; i < length; i++) {
        if(haystack[i] == needle) return true;
    }
    return false;
}

function trimFileName(file_name){
    var trimed_filename = file_name.split(' ').join('-');
            trimed_filename = trimed_filename.split('&').join('-');
            trimed_filename = trimed_filename.split(';').join('-');
            trimed_filename = trimed_filename.split(':').join('-');
            trimed_filename = trimed_filename.split('/').join('-');
            trimed_filename = trimed_filename.split('{').join('-');
            trimed_filename = trimed_filename.split('}').join('-');
            trimed_filename = trimed_filename.split('(').join('-');
            trimed_filename = trimed_filename.split(')').join('-');
            trimed_filename = trimed_filename.split('\'').join('-');
            trimed_filename = trimed_filename.split('"').join('-');  
    return trimed_filename;
}

function processFileName(fileName)
{
    var __fileNameTemp     = fileName;
    var __explodedFileName = '';
    this.trimFileName = function()
    {
       __fileNameTemp = trimFileName(__fileNameTemp);
    }
    
    this.explodeFileName = function()
    {
        return __explodedFileName = __fileNameTemp.split('.');
    }
    
    this.fileExtension = function()
    {
        return __explodedFileName[(__explodedFileName.length)-1].toLowerCase();
    }
    
    this.uniqueFileName = function()
    {
        this.trimFileName();
        this.explodeFileName();
        var currentdate    = new Date(); 
        var datetime 	   = currentdate.getDate()+'-'+(currentdate.getMonth()+1)+'-'+currentdate.getFullYear()+'-'+currentdate.getHours()+'-'+currentdate.getMinutes()+'-'+currentdate.getSeconds();
        var uniqueFileName = __explodedFileName[0].slice(0,30) + datetime + "." + this.fileExtension();
        return uniqueFileName.replace(/\\/g, "");
    }
}

var month    = new Array();
    month[0] = "Jan";
    month[1] = "Feb";
    month[2] = "Mar";
    month[3] = "Apr";
    month[4] = "May";
    month[5] = "Jun";
    month[6] = "Jul";
    month[7] = "Aug";
    month[8] = "Sep";
    month[9] = "Oct";
    month[10] = "Nov";
    month[11] = "Dec";
    
    
    function renderPopUpMessage(template, message){
        $('#popUpMessage').remove();
        var errorClass   = (template=='error')?'danger':'success';
        var messageHtml  = '';
            messageHtml += '<div id="popUpMessage" class="alert alert-'+errorClass+'">';
            messageHtml += '    <a data-dismiss="alert" class="close">×</a>';
            messageHtml += '    '+message;
            messageHtml += '</div>';
        return messageHtml;
    }
    
    function cleanPopUpMessage(){
        $('#popUpMessage').remove();
    }
    
    function scrollToTopOfPage(){
        $("html, body").animate({ scrollTop: 0 }, "slow");
    }
    
    function lauch_common_message(header, message)
    {
        $('#common_message_header').html(header);
        $('#common_message_content').html(message);
        $('#common_message_button').addClass('btn-red').removeClass('btn-green');
        $('#common_message').modal('show');
    }
    
    function lauch_common_success_message(header, message)
    {
        $('#common_message_header').html(header);
        $('#common_message_content').html(message);
        $('#common_message_button').addClass('btn-green').removeClass('btn-red');
        $('#common_message').modal('show');
    }
    
    function preventSpecialCharector(e)
    {
        var k;
        document.all ? k = e.keyCode : k = e.which;
        return ((k > 64 && k < 91) || (k > 96 && k < 123) || k == 8 || k == 32 || (k >= 48 && k <= 57));
    }
    
    function preventAlphabets(evt) {
        var charCode = (evt.which) ? evt.which : event.keyCode;
        console.log(charCode);
        if ((charCode != 46 && charCode > 31
        && (charCode < 48 || charCode > 57)) || charCode == 46 )
            return false;

        return true;
    }
    
    function validateEmail(email)
    {
        var filter = /^[\w\-\.\+]+\@[a-zA-Z0-9\.\-]+\.[a-zA-z0-9]{2,4}$/;
        if (filter.test(email)) {
            return true;
        }
        else {
            return false;
        }
    }
    
    function preventNumbers(e) {
        var keyCode = (e.keyCode ? e.keyCode : e.which);
        if (keyCode > 47 && keyCode < 58) {
            e.preventDefault();
        }
    }


function changeLanguage(language_id)
{
    $.ajax({
        url: webConfigs('admin_url')+'coursebuilder/change_language/'+language_id,
        type: "POST",
        data:{ "is_ajax":true},
        success: function(response) {
            var data  = $.parseJSON(response);
            if(data['error'] != false)
            {   
                lauch_common_message('Error in switching language', data['message']);
            }
            else
            {
                location.reload();
            }
        }
    });
}