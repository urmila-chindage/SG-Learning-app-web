function uploadFiles(uploadURL, param, callback, __dataType) {
    console.log('uploadFiles system');
    var formDataType = (typeof __dataType == 'undefined') ? 'json' : __dataType;
    var formData = new FormData();
    for (key in param) {
        formData.append(key, param[key]);

    }
    var processingId = (typeof param['processing'] != 'undefined') ? param['processing'] : 'percentage_count';
    var jqXHR = $.ajax({
        xhr: function() {
            var xhrobj = $.ajaxSettings.xhr();
            if (xhrobj.upload) {
                xhrobj.upload.addEventListener('progress', function(uploadEvent) {
                    var event = uploadEvent;
                    var percent = 0;
                    var position = event.loaded || event.position;
                    var total = event.total;
                    if (event.lengthComputable) {
                        percent = Math.ceil(position / total * 100);
                    }
                    $('.progress-bar').css('width', percent + '%');
                    $('.progress-bar .sr-only').html(percent + '%' + lang('complete'));
                    $('.percentage-text').html(percent + '%');
                    //Set progress
                    if (percent == 100) {
                        $('#' + processingId).parent().html(lang('processing_please_wait'));
                    }
                    //console.log(percent);
                }, false);
            }
            return xhrobj;
        },
        url: uploadURL,
        type: "POST",
        datatype: formDataType,
        contentType: false,
        processData: false,
        cache: false,
        data: formData,
        async: true,
        success: function(responseData) {
            var data = responseData;
            
            callback(data);
        }
    });
}

function validateMaxLength(selector) {
    var maxlength = $('#' + selector).attr('maxlength');
    var current_length = $('#' + selector).val().length;
    var remaining = parseInt(maxlength - current_length);
    var left_character = (remaining == 1) ? lang('character_left') : lang('characters_left');
    $('#' + selector + '_char_left').html(remaining + ' ' + left_character);
}

function isValidYoutubeURL(str) {
    var URL = str;
    var regExp = /^.*(vimeo\.com|youtu\.be|www\.youtube\.com)\/([\w\/-]+)([^#\&\?]*).*/;
    // var regExp = /^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#\&\?]*).*/;
    var match = URL.match(regExp);
    if (URL != undefined || URL != '') {
        if (match && match[2].length == 11) {
            return true;
        }else{
            return false;
        }
    }else{
        return false;
    }
}
function isValidYoutubeOrVimeoURL(str) {
    var URL     = str;
    var regExp = /^.*(vimeo\.com|youtu\.be|www\.youtube\.com)\/([\w\/-]+)([^#\&\?]*).*/;
    // var regExp=/^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=|\?v=)([^#\&\?]*).*/;
    var match   = URL.match(regExp);
    console.log(match);
    if (URL != undefined || URL != '') {
        if (match) {
            return true;
        }else{
            return false;
        }
    }else{
        return false;
    }
}

function isValidURL(str) {
    var pattern = new RegExp('^(https?:\\/\\/)?' + // protocol
        '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.?)+[a-z]{2,}|' + // domain name
        '((\\d{1,3}\\.){3}\\d{1,3}))' + // OR ip (v4) address
        '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*' + // port and path
        '(\\?[;&a-z\\d%_.~+=-]*)?' + // query string
        '(\\#[-a-z\\d_]*)?$', 'i'); // fragment locator
    return pattern.test(str);
}

function removeArrayIndex(array, index) {
    for (var i = array.length; i--;) {
        if (array[i] === index) {
            array.splice(i, 1);
        }
    }
}

function inArray(needle, haystack) {
    var length = haystack.length;
    for (var i = 0; i < length; i++) {
        if (haystack[i] == needle) return true;
    }
    return false;
}

function trimFileName(file_name) {
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

function processFileName(fileName) {
    var __fileNameTemp = fileName;
    var __explodedFileName = '';
    this.trimFileName = function() {
        __fileNameTemp = trimFileName(__fileNameTemp);
    }

    this.explodeFileName = function() {
        return __explodedFileName = __fileNameTemp.split('.');
    }

    this.fileExtension = function() {
        return __explodedFileName[(__explodedFileName.length) - 1].toLowerCase();
    }

    this.uniqueFileName = function() {
        this.trimFileName();
        this.explodeFileName();
        var currentdate = new Date();
        var datetime = currentdate.getDate() + '-' + (currentdate.getMonth() + 1) + '-' + currentdate.getFullYear() + '-' + currentdate.getHours() + '-' + currentdate.getMinutes() + '-' + currentdate.getSeconds();
        var uniqueFileName = __explodedFileName[0].slice(0, 30) + datetime + "." + this.fileExtension();
        return uniqueFileName.replace(/\\/g, "");
    }
}

var month = new Array();
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


function renderPopUpMessage(template, message) {
    $('#popUpMessage').remove();
    //console.log(template);
    var errorClass = '';
    switch(template) {
        case "error":
            errorClass = 'danger';
        break;
        case "warning":
            errorClass = 'warning';
        break;
        default:
            errorClass = 'success';
        break;
    }
    var messageHtml = '';
    messageHtml += '<div id="popUpMessage" class="alert alert-' + errorClass + '">';
    messageHtml += '    <a data-dismiss="alert" class="close">×</a>';
    messageHtml += '    ' + message;
    messageHtml += '</div>';
    return messageHtml;
}

function cleanPopUpMessage() {
    $('#popUpMessage').remove();
}

function renderPopUpMessagePage(template, message) {
    $('#popUpMessagePage').remove();
    var errorClass = (template == 'error') ? 'danger' : 'success';
    var messageHtml = '';
    messageHtml += '<div id="popUpMessagePage" class="alert alert-' + errorClass + '">';
    messageHtml += '    <a data-dismiss="alert" class="close">×</a>';
    messageHtml += '    ' + message;
    messageHtml += '</div>';
    return messageHtml;
}

function cleanPopUpMessagePage() {
    $('#popUpMessagePage').remove();
}

function scrollToTopOfPage() {
    $("html, body").animate({
        scrollTop: 0
    }, "slow");
}

function lauch_common_message(header, message) {
    /*$('#common_message_header').html(header);
    $('#common_message_content').html(message);
    $('#common_message_button').addClass('btn-red').removeClass('btn-green');
    $('#common_message').modal('show');*/
    var messageObject = {
        'body': message,
        'button_yes': 'OK',
        'prevent_button_no': true,
    };
    callback_danger_modal(messageObject);
}

function lauch_common_success_message(header, message) {
    var messageObject = {
        'body': message,
        'button_yes': 'OK',
    };
    callback_success_modal(messageObject);
    /*$('#common_message_header').html(header);
    $('#common_message_content').html(message);
    $('#common_message_button').addClass('btn-green').removeClass('btn-red');
    $('#common_message').modal('show');*/
}

function preventSpecialCharector(e) {
    var k;
    document.all ? k = e.keyCode : k = e.which;
    return ((k > 64 && k < 91) || (k > 96 && k < 123) || k == 8 || k == 32 || (k >= 48 && k <= 57));
}

function preventAlphabets(evt) {
    var charCode = (evt.which) ? evt.which : event.keyCode;
    //console.log(charCode);
    if ((charCode != 46 && charCode > 31 &&
            (charCode < 48 || charCode > 57)) || charCode == 46)
        return false;

    return true;
}

function validateEmail(email) {
    var filter  = /^([A-Za-z0-9_\-\.\+])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
    if (filter.test(email)) {
        return true;
    } else {
        return false;
    }
}

function IsmobileNumber(Numbers) {
    var IndNum = /^([0|\+[0-9]{1,5})?([7-9][0-9]{9})$/;
    if (IndNum.test(Numbers)) {
        return true;
    } else {
        return false;
    }
}

function isPhoneNumber(number){
  var phoneno = /^\d{10}$/;
  if(number.match(phoneno) || number.match(/^\d{11}$/)){
    return true;
  }
  return false;
}


function preventNumbers(e){ 
    var keyCode = (e.keyCode ? e.keyCode : e.which); 
    if (keyCode > 47 && keyCode < 58){
        e.preventDefault();
    }
}

function preventCharector(e){
    var k; 
    document.all ? k = e.keyCode : k = e.which; 
    return (k >= 48 && k <= 57);
}
// action dropdown btn-red

$(document).ready(function() {
    $("#table").on("click", "tr", function(e) {
        if (e.target.tagName !== "BUTTON" && e.target.parentElement.tagName !== "BUTTON") {
            $('#tr-modal').modal({
                backdrop: 'static',
                keyboard: false
            });
        }
    });
});

function opener() {
    $('#dd-modal').modal('show');
}

function reloadLocation(){
    location.reload();
}

function callback_success_modal(messageObject, callbackContinue, callbackHalt) {
    callbackContinue = typeof callbackContinue != 'undefined' ? callbackContinue : false;
    callbackHalt = typeof callbackHalt != 'undefined' ? callbackHalt : false;
    var commonMessageAdvanced = $('#common_message_advanced');
    var messageBody = typeof messageObject['body'] != 'undefined' ? messageObject['body'] : 'Unknown error occured';
    var messageButtonYes = (typeof messageObject['button_yes'] != 'undefined' && messageObject['button_yes'] != '') ? messageObject['button_yes'] : 'CONTINUE';
    var messageButtonNo = (typeof messageObject['button_no'] != 'undefined' && messageObject['button_no'] != '') ? messageObject['button_no'] : 'CANCEL';
    var continueParams = typeof messageObject['continue_params'] != 'undefined' ? messageObject['continue_params'] : {};
    var haltParams = typeof messageObject['halt_params'] != 'undefined' ? messageObject['halt_params'] : {};
    var prevent_button_no = typeof messageObject['prevent_button_no'] != 'undefined' ? messageObject['prevent_button_no'] : false;

    $('#advanced_confirm_box_ok').html(messageButtonYes).unbind();
    $('#advanced_confirm_box_cancel').html(messageButtonNo).unbind();
    //if(prevent_button_no === true)
    {
        $('#advanced_confirm_box_cancel').hide();
    }
    if (callbackContinue != false && callbackContinue != '') {
        $('#advanced_confirm_box_ok').click(continueParams, callbackContinue);
        $('#advanced_confirm_box_ok').click(function() {
            $(this).html('WORKING...');
        });
    } else {
        $('#advanced_confirm_box_ok').click(function() {
            //$('.close').trigger('click');
            commonMessageAdvanced.modal('hide');
        });
    }
    if (callbackHalt != false && callbackHalt != '') {
        $('#advanced_confirm_box_cancel').click(haltParams, callbackHalt);
        $('#advanced_confirm_box_cancel').click(function() {
            $(this).html('WORKING...');
        });
    } else {
        $('#advanced_confirm_box_cancel').click(function() {
            //$('.close').trigger('click');
            commonMessageAdvanced.modal('hide');
        });
    }
    commonMessageAdvanced.removeClass('success-alert warning-alert danger-alert').addClass('success-alert');
    $('.success-alert .message-body').html(messageBody);
    commonMessageAdvanced.modal();
}

function callback_warning_modal(messageObject, callbackContinue, callbackHalt) {

    callbackContinue = typeof callbackContinue != 'undefined' ? callbackContinue : false;
    callbackHalt = typeof callbackHalt != 'undefined' ? callbackHalt : false;
    var commonMessageAdvanced = $('#common_message_advanced');
    var messageBody = typeof messageObject['body'] != 'undefined' ? messageObject['body'] : 'Unknown error occured';
    var messageButtonYes = (typeof messageObject['button_yes'] != 'undefined' && messageObject['button_yes'] != '') ? messageObject['button_yes'] : 'CONTINUE';
    var messageButtonNo = (typeof messageObject['button_no'] != 'undefined' && messageObject['button_no'] != '') ? messageObject['button_no'] : 'CANCEL';
    var continueParams = typeof messageObject['continue_params'] != 'undefined' ? messageObject['continue_params'] : {};
    var haltParams = typeof messageObject['halt_params'] != 'undefined' ? messageObject['halt_params'] : {};
    var prevent_button_no = typeof messageObject['prevent_button_no'] != 'undefined' ? messageObject['prevent_button_no'] : false;

    $('#advanced_confirm_box_ok').html(messageButtonYes).unbind();
    $('#advanced_confirm_box_cancel').html(messageButtonNo).unbind();
    $('#advanced_confirm_box_cancel').show();
    if (prevent_button_no === true) {
        $('#advanced_confirm_box_cancel').hide();
    }
    if (callbackContinue != false && callbackContinue != '') {
        $('#advanced_confirm_box_ok').click(continueParams, callbackContinue);
        $('#advanced_confirm_box_ok').click(function() {
            $(this).html('WORKING...');
        });
    } else {
        $('#advanced_confirm_box_ok').click(function() {
            //$('.close').trigger('click');
            commonMessageAdvanced.modal('hide');
        });
    }
    if (callbackHalt != false && callbackHalt != '') {
        $('#advanced_confirm_box_cancel').click(haltParams, callbackHalt);
        $('#advanced_confirm_box_cancel').click(function() {
            $(this).html('WORKING...');
        });
    } else {
        $('#advanced_confirm_box_cancel').click(function() {
            //$('.close').trigger('click');
            commonMessageAdvanced.modal('hide');
        });
    }
    commonMessageAdvanced.removeClass('success-alert warning-alert danger-alert').addClass('warning-alert');
    $('.warning-alert .message-body').html(messageBody);
    commonMessageAdvanced.modal();
}

function callback_danger_modal(messageObject, callbackContinue, callbackHalt) {
    callbackContinue = typeof callbackContinue != 'undefined' ? callbackContinue : false;
    callbackHalt = typeof callbackHalt != 'undefined' ? callbackHalt : false;
    var commonMessageAdvanced = $('#common_message_advanced');
    var messageBody = typeof messageObject['body'] != 'undefined' ? messageObject['body'] : 'Unknown error occured';
    var messageButtonYes = (typeof messageObject['button_yes'] != 'undefined' && messageObject['button_yes'] != '') ? messageObject['button_yes'] : 'CONTINUE';
    var messageButtonNo = (typeof messageObject['button_no'] != 'undefined' && messageObject['button_no'] != '') ? messageObject['button_no'] : 'CANCEL';
    var continueParams = typeof messageObject['continue_params'] != 'undefined' ? messageObject['continue_params'] : {};
    var haltParams = typeof messageObject['halt_params'] != 'undefined' ? messageObject['halt_params'] : {};
    var prevent_button_no = typeof messageObject['prevent_button_no'] != 'undefined' ? messageObject['prevent_button_no'] : false;

    $('#advanced_confirm_box_ok').html(messageButtonYes).unbind();
    $('#advanced_confirm_box_cancel').html(messageButtonNo).unbind();
    $('#advanced_confirm_box_cancel').show();
    if (prevent_button_no === true) {
        $('#advanced_confirm_box_cancel').hide();
        $('#advanced_confirm_box_ok').parent().css('text-align','center');
    }
    if (callbackContinue != false && callbackContinue != '') {
        $('#advanced_confirm_box_ok').click(continueParams, callbackContinue);
        $('#advanced_confirm_box_ok').click(function() {
            $(this).html('WORKING...');
        });
    } else {
        $('#advanced_confirm_box_ok').click(function() {
            //$('.close').trigger('click');
            commonMessageAdvanced.modal('hide');
        });
    }
    if (callbackHalt != false && callbackHalt != '') {
        $('#advanced_confirm_box_cancel').click(haltParams, callbackHalt);
        $('#advanced_confirm_box_cancel').click(function() {
            $(this).html('WORKING...');
        });
    } else {
        $('#advanced_confirm_box_cancel').click(function() {
            //$('.close').trigger('click');
            commonMessageAdvanced.modal('hide');
        });
    }
    commonMessageAdvanced.removeClass('success-alert warning-alert danger-alert').addClass('danger-alert');
    $('.danger-alert .message-body').html(messageBody);
    commonMessageAdvanced.modal();
}

function generatePagination(currentPage, totalPages) {
    var pageHtml = '';
    if (totalPages >= currentPage && totalPages > 1) {
        var pageNumber = currentPage;
        var pageGap = 3;

        //rendering button "First Page"
        if ((currentPage - 1) > pageGap) {
            pageHtml += '<li><a href="javascript:void(0);" data-page="1" class="locate-page">First Page</a></li>';
        } else {
            pageHtml += '<li class="disabled"><a href="javascript:void(0);">First Page</a></li>';
        }
        //End of rendering button "First Page"

        //rendering button "Previous"
        var previousPage = (currentPage - 1);
        if (previousPage > 0) {
            pageHtml += '<li><a href="javascript:void(0);" data-page="' + previousPage + '" class="locate-page">&laquo</a></li>';
        } else {
            pageHtml += '<li class="disabled"><a href="javascript:void(0);" >&laquo</a></li>';
        }
        //End of rendering button "Previous"

        //rendering pages that comes before current page
        var beforeLoopLength = currentPage - pageGap;
        while (beforeLoopLength > 0 && beforeLoopLength <= (currentPage - 1)) {
            pageHtml += '<li><a href="javascript:void(0);" data-page="' + beforeLoopLength + '" class="locate-page">' + beforeLoopLength + '</a></li>';
            beforeLoopLength++;
        }
        if (currentPage <= pageGap) {
            beforeLoopLength = 1;
            while (currentPage > beforeLoopLength) {
                pageHtml += '<li><a href="javascript:void(0);" data-page="' + beforeLoopLength + '" class="locate-page">' + beforeLoopLength + '</a></li>';
                beforeLoopLength++;
            }
        }
        //end of rendering pages that comes before current page

        //rendering current page
        var lastPageClass = '';
        if (currentPage == totalPages) {
            lastPageClass = 'pagination-last-page';
        }
        pageHtml += '<li class="active"><a href="javascript:void(0);" data-page="' + pageNumber + '" class="' + lastPageClass + '">' + pageNumber + '</a></li>';
        pageNumber++;
        lastPageClass = '';
        //end of rendering current page


        //rendering pages that comes after current page
        var afterLoopLength = pageGap;
        while (afterLoopLength > 0 && pageNumber <= totalPages) {
            if (pageNumber == totalPages) {
                lastPageClass = 'pagination-last-page';
            }
            pageHtml += '<li><a href="javascript:void(0);" data-page="' + pageNumber + '" class="locate-page ' + lastPageClass + '">' + pageNumber + '</a></li>';
            afterLoopLength--;
            pageNumber++;
        }
        //end of rendering pages that comes after current page

        //rendering button "Next"
        if (totalPages > currentPage) {
            var nextPage = currentPage + 1;
            pageHtml += '<li><a href="javascript:void(0);" data-page="' + nextPage + '" class="locate-page">&raquo</a></li>';
        } else {
            pageHtml += '<li class="disabled"><a href="javascript:void(0);">&raquo</a></li>';
        }
        //End of rendering button "Next"

        //rendering button "Last Page"
        if ((totalPages - pageGap) > currentPage) {
            pageHtml += '<li><a href="javascript:void(0);" data-page="' + totalPages + '" class="locate-page">Last Page</a></li>';
        } else {
            pageHtml += '<li class="disabled"><a href="javascript:void(0);" >Last Page</a></li>';
        }
        //End of rendering button "Last Page"
    }
    scrollToTopOfPage();
    return pageHtml;
}

function User(permissions) {

    this.permissions = permissions;
    this.permissionOption = {
        "view": 1,
        "add": 2,
        "edit": 3,
        "delete": 4
    };
}

User.prototype.view = function() {
    if(this.permissions.indexOf(this.permissionOption.view) >= 0) {
        return true;
    } else {
        return false;
    }
}
User.prototype.add = function() {
    if(this.permissions.indexOf(this.permissionOption.add) >= 0) {
        return true;
    } else {
        return false;
    }
}
User.prototype.edit = function() {
    if(this.permissions.indexOf(this.permissionOption.edit) >= 0) {
        return true;
    } else {
        return false;
    }
}
User.prototype.pdelete = function() {
    if(this.permissions.indexOf(this.permissionOption.delete) >= 0) {
        return true;
    } else {
        return false;
    }
}



function abortPreviousAjaxRequest(requestObject) {
    if(typeof requestObject != 'undefined' && requestObject.length > 0 )
    {
        for(var i=0; i<requestObject.length; i++) {
            requestObject[i].abort();
        }
    }
}
function parse_query_string(query) {
    var vars = query.split("&");
    var query_string = {};
    for (var i = 0; i < vars.length; i++) {
    var pair = vars[i].split("=");
    var key = decodeURIComponent(pair[0]);
    var value = decodeURIComponent(pair[1]);
    // If first entry with this name
    if (typeof query_string[key] === "undefined") {
        query_string[key] = decodeURIComponent(value);
        // If second entry with this name
    } else if (typeof query_string[key] === "string") {
        var arr = [query_string[key], decodeURIComponent(value)];
        query_string[key] = arr;
        // If third or later entry with this name
    } else {
        query_string[key].push(decodeURIComponent(value));
    }
    }
    return query_string;
}


var Access = function () {
    var _previlages = $.parseJSON(__previlages__);
    this.getPrivilage = function () {
        return _previlages;
    };
    this.hasView = function() {
        return (Object.values(this.getPrivilage()).indexOf('1') > -1);     
    };
    this.hasAdd = function() {
        return (Object.values(this.getPrivilage()).indexOf('2') > -1);     
    };
    this.hasEdit = function() {
        return (Object.values(this.getPrivilage()).indexOf('3') > -1);     
    };
    this.hasDelete = function() {
        return (Object.values(this.getPrivilage()).indexOf('4') > -1);     
    };
};


function uploadFilesToAws(uploadURL, param, callback)
{
    var processingId = (typeof param['processing'] != 'undefined')?param['processing']:'percentage_count';
    var upload = function() {
            var updateProgress = function(data) {
                var percent = data*100;
                    percent = percent.toFixed(2);
                $('.progress-bar').css('width', percent+'%');
                $('.progress-bar .sr-only').html(percent+'%'+lang('complete'));
                $('.percentage-text').html(percent+'%');
                //Set progress
                if( Math.round(percent) == 100){
                    $('#'+processingId).parent().html(lang('processing_please_wait'));
                }
            }

            var uploader = new S3BlobUploader({server_url:uploadURL, partSize : (5 * 1024 * 1024), param:JSON.stringify(param)});
            
            uploader.on('beforeUpload', function() {
                updateProgress(0);
                //console.log('preparing upload');
            });
            uploader.on('startUpload', function() {
                //console.log('upload starting');
            });
            uploader.on('progress', function(data) {
                updateProgress(data);
            });
            uploader.on('progressStats', function(data) {
                //console.log(data);
            });
            uploader.on('finishing', function() {
                //console.log('Upload complete, preparing final file');
            });
            uploader.on('complete', function(data) {
                //console.log('We did it!', data);
                data['file_object'] = new Object();
                data['file_object'] = data['result'];
                data['error'] = 'false';
                callback(data);
            });
            uploader.on('error', function(data) {
                //console.log('failed', data);
                alert('it failed :(');
            });
            uploader.on('cancel', function(data) {
                //console.log('cancelled', data);
            });
            $('#'+param['cancelElement']).on('click', function() {
                updateProgress(0);
                uploader.abort();
            }).show();
            uploader.start(param['file']);
        }
        upload();
}


function webConfigs(key)
{
    return localStorage.getItem(key);
}


var DocumentReader = function (element, uploadedObject) {
    const _this_                = this;
    const questionType          = { "single_choice": 1, "multiple_choice": 2, "subjective": 3, "fillups": 4 };
    const questionDifficulty    = { "easy": 1, "medium": 2, "hard": 3 };
    var questionObject          = {};
    var questionObjectCount     = 1;
    var documemtProperties      = {};
    var columnCount             = 1;
    var evenColumn              = false;
    var serviceName             = "";
    var questionCaptured        = false;
    var nextService             = '';
    var imageCount              = 0;
    var invalidTableInputFound  = false;
    var response                = {};
        response['error']       = false;
        response['message']     = 'Template parsed successfully';
    this.parseDocumentProperties = function(table) {
        var identify = 'key';
        $(table).children('tbody').children('tr').children('td').each(function(tableRowIndex, tableRow) {
            switch(identify) {
                case "key":
                    documemtProperties['document_id'] = "";
                    identify = 'value';
                break;
                case "value":
                    documemtProperties['document_id'] = _this_.trimText($(tableRow).text());
                    identify = 'exit';
                break;
            }
        });
    }

    this.parseDocument = function() {
        questionObject          = {};
        invalidTableInputFound  = false;
        $(element).each(function(tableIndex, table) {
            if( Object.keys(documemtProperties).length == 0) {
                _this_.parseDocumentProperties(table);
                return;
            }
            columnCount = 1;
            // console.log(questionObject);
            questionObject[questionObjectCount] = {};
            optionCount                         = 0;
            nextService                         = 'sl_no';
            invalidTableInputFound              = false;
            $(table).children('tbody').children('tr').children('td').each(function(tableRowIndex, tableRow) {
                if(invalidTableInputFound == true) {
                    return false;
                }
                if(_this_.startToCaptureOption == true) {
                    if(_this_.getService() != "" && _this_.getService() != "answer") {
                        if(((columnCount%2) == 0) == true) {
                            if(questionObject[questionObjectCount]['q_type'] != questionType['subjective'] && questionObject[questionObjectCount]['q_type'] != questionType['fillups']) {
                                nextService = "option";
                            } else {
                                nextService = "positive_mark";
                            }
                            _this_.setService(nextService);
                        }
                    } else {
                        nextService = "answer";
                    }
                }
            //if odd column
                // console.log(_this_.serviceName+'=='+questionObjectCount+'--'+nextService);
                if( typeof _this_.serviceName != 'undefined' && _this_.serviceName != "" && nextService != _this_.serviceName) {
                    // console.log(_this_.serviceName+'=='+questionObjectCount+'=='+nextService);
                    if(response['error'] == false) {
                        response['error'] = true;
                        response['message'] = 'Invalid table found. Please check near table no <b>'+questionObjectCount+'</b>';
                        if(typeof questionObject[questionObjectCount]['sl_no'] != 'undefined')
                        {
                            response['message'] += ' OR table with SL_NO <b>'+questionObject[questionObjectCount]['sl_no']+'</b>'; 
                        }
                    }
                    invalidTableInputFound = true;
                    return false;
                }

                switch(_this_.serviceName) {
                    case "sl_no":
                        nextService = "question_type";
                        questionObject[questionObjectCount]['sl_no'] = _this_.trimText($(tableRow).text());
                        _this_.setService("");
                    break;
                    case "question_type":
                        nextService = "difficulty";
                        questionObject[questionObjectCount]['q_type'] = _this_.trimText($(tableRow).text());
                        questionObject[questionObjectCount]['q_type'] = _this_.getQuestionType(questionObject[questionObjectCount]['q_type']);
                        _this_.setService("");
                        if(questionObject[questionObjectCount]['q_type'] == false) {
                            response['error']       = true;
                            response['message']     = 'Seems value for <b>question type</b> is misspelled. Please check near table no <b>'+questionObjectCount+'</b>';
                            invalidTableInputFound  = true;
                        }
                    break;
                    case "difficulty":
                        nextService = "question";
                        questionObject[questionObjectCount]['q_difficulty'] = _this_.trimText($(tableRow).text());
                        questionObject[questionObjectCount]['q_difficulty'] = _this_.getQuestionDifficulty(questionObject[questionObjectCount]['q_difficulty']);
                        _this_.setService("");
                        if(questionObject[questionObjectCount]['q_difficulty'] == false) {
                            response['error']       = true;
                            response['message']     = 'Seems value for <b>difficulty</b> is misspelled. Please check near table no <b>'+questionObjectCount+'</b>';
                            invalidTableInputFound  = true;
                        }
                    break;
                    case "question":
                        _this_.parseImagePath(tableRow);
                        if(questionObject[questionObjectCount]['q_type'] != questionType['subjective'] && questionObject[questionObjectCount]['q_type'] != questionType['fillups']) {
                            nextService = "option";
                        } else {
                            nextService = "positive_mark";
                        }
                        questionObject[questionObjectCount]['q_question'] = $(tableRow).html();
                        if(questionObject[questionObjectCount]['q_type'] != 3 && questionObject[questionObjectCount]['q_type'] != 4) {
                            _this_.startToCaptureOption = true;
                        }
                        _this_.setService("");
                        
                        var questionTrimmed = _this_.trimText($(tableRow).text());
                        if(questionTrimmed == ""  && $(tableRow).find('img').length <= 0){
                            response['error']       = true;
                            response['message']     = '<b>Question</b> is mandatory. Please check near table no <b>'+questionObjectCount+'</b>';
                            invalidTableInputFound  = true;
                        }
                    break;
                    case "option":
                        _this_.parseImagePath(tableRow);
                        if(_this_.trimText($(tableRow).text()) != "" || $(tableRow).find('img').length > 0) {
                            if(typeof questionObject[questionObjectCount]['q_option'] == "undefined") {
                                questionObject[questionObjectCount]['q_option'] = {};
                            }
                            questionObject[questionObjectCount]['q_option'][optionCount] = $(tableRow).html();
                            optionCount++;
                        }
                        _this_.setService("");
                    break;
                    case "answer":
                        _this_.parseImagePath(tableRow);
                        nextService = "positive_mark";
                        questionObject[questionObjectCount]['q_answer'] = _this_.trimText($(tableRow).text());
                        _this_.startToCaptureOption = false;
                        _this_.setService("");
                    break;
                    case "positive_mark":
                        nextService = "negative_mark";
                        questionObject[questionObjectCount]['q_positive_mark'] = _this_.trimText($(tableRow).text());
                        _this_.setService("");
                        if(questionObject[questionObjectCount]['q_positive_mark'] == "" || isNaN(questionObject[questionObjectCount]['q_positive_mark']) == true) {
                            response['error']       = true;
                            response['message']     = 'Seems value for <b>positive mark</b> is invalid. Please check near table no <b>'+questionObjectCount+'</b>';
                            invalidTableInputFound  = true;
                        }
                    break;
                    case "negative_mark":
                        nextService = "subject";
                        questionObject[questionObjectCount]['q_negative_mark'] = _this_.trimText($(tableRow).text());
                        _this_.setService("");
                        if(questionObject[questionObjectCount]['q_negative_mark'] == "" || isNaN(questionObject[questionObjectCount]['q_negative_mark']) == true) {
                            response['error']       = true;
                            response['message']     = 'Seems value for <b>negative mark</b> is invalid. Please check near table no <b>'+questionObjectCount+'</b>';
                            invalidTableInputFound  = true;
                        }
                    break;
                    case "subject":
                        nextService = "topic";
                        questionObject[questionObjectCount]['q_subject'] = _this_.trimText($(tableRow).text());
                        _this_.setService("");
                        if(questionObject[questionObjectCount]['q_subject'] == "" ) {
                            response['error']       = true;
                            response['message']     = 'Seems value for <b>subject</b> is invalid. Please check near table no <b>'+questionObjectCount+'</b>';
                            invalidTableInputFound  = true;
                        }
                    break;
                    case "topic":
                        nextService = "explanation";
                        questionObject[questionObjectCount]['q_topic'] = _this_.trimText($(tableRow).text());
                        _this_.setService("");
                        if(questionObject[questionObjectCount]['q_topic'] == "") {
                            response['error']       = true;
                            response['message']     = 'Seems value for <b>topic</b> is invalid. Please check near table no <b>'+questionObjectCount+'</b>';
                            invalidTableInputFound  = true;
                        }
                    break;
                    case "explanation":
                        _this_.parseImagePath(tableRow);
                        nextService = "tags";
                        questionObject[questionObjectCount]['q_explanation'] = $(tableRow).html();
                        _this_.setService("");
                    break;
                    case "tags":
                        nextService = "sl_no";
                        questionObject[questionObjectCount]['q_tags'] = _this_.trimText($(tableRow).text());
                        _this_.setService("");
                    break;
                    default:
                        // console.log('recording service');
                        _this_.setService("");
                    break;
                }

                evenColumn = ((columnCount%2) == 0)?true:false
                if( evenColumn == false ) {
                    _this_.setService($(tableRow).text());
                }
                columnCount++;
            });
            if(Object.keys(questionObject[questionObjectCount]).length == 0) {
                delete questionObject[questionObjectCount];
            }
            questionObjectCount++;
        });
        response['question'] = questionObject;
        return response;
    }

    this.parseImagePath = function(tableRow) {
        var imageSource = '', imagePath;
        //console.log('parseImagePath');
        $(tableRow).find('img').each(function(){
            imageSource = $(this).attr('src');
            if(imageSource.indexOf('base64') == -1) {
                imageSource = imageSource.replace("../", "");
                imagePath   = uploadedObject['question_path']+uploadedObject['raw_name']+'/'+imageSource;
                $(this).attr('src', imagePath)
                imageCount++;
            }
            //console.log($(this).attr('src').indexOf('base64'));
        });
    }
    
    this.getQuestionType = function(type) {
        type = type.toLowerCase();
        return typeof questionType[type] != "undefined" ? questionType[type] : false;
    }
    
    this.getQuestionDifficulty = function(type) {
        type = type.toLowerCase();
        return typeof questionDifficulty[type] != "undefined" ? questionDifficulty[type] : false;
    }

    this.trimText = function (text) {
        return $.trim(text);
    }

    this.getService = function() {
        return _this_.serviceName;
    }

    this.setService = function(service) {
        _this_.serviceName = _this_.trimText(service.toLowerCase());
    }

    this.getDocumentProperties = function() {
        return documemtProperties;
    }
};

function stripHtmlTags(content){
    content    = content.trim();
    content    = content.replace(/<img[^>]+>/i, 'img').replace(/<\/?[^>]+(>|$)/g, '').replace(/&.*;/g, '');
    //content    = content.replace(/[^a-zA-Z0-9]/g, '').replace(/\s+/g, '').replace(/_/g, '').replace(/-/g, '').replace(/[\[\]']/g,'');
    var regex  = /(&nbsp;|<([^>]+)>)/ig;
    content    = content.replace(regex, "");
    return content;
}
