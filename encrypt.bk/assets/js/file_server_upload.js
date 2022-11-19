
function saveLecture()
{
   // alert('test 1');
    //getting file details
    var section_id                      = $('#section_id').val();
    var section_name                    = $('#section_name').val();
    var lecture_name                    = $('#lecture_name').val();
    var lecture_description             = $('#lecture_description').val();
    var sent_mail_on_lecture_creation   = $('#sent_mail_on_lecture_creation').prop('checked');
        sent_mail_on_lecture_creation   = (sent_mail_on_lecture_creation==true)?'1':'0';
    var errorCount                      = 0;
    var errorMessage                    = '';
    //end

    //validation process
    if( lecture_name == '')
    {
        errorCount++;
        errorMessage += 'please enter lecture name <br />';
    }

    if(__create_section_as_new == true && section_name == '')
    {
        errorCount++;
        errorMessage += 'Section required <br />';
    }

    if(__create_section_as_new == false && section_id == '')
    {
        errorCount++;
        errorMessage += 'please choose section <br />';
    }

    if( lecture_description == '')
    {
       // errorCount++;
        //errorMessage += 'please enter lecture description<br />';
    }
    cleanPopUpMessage();
    if(errorCount > 0 )
    {
        $('#upload-lecture .modal-body').prepend(renderPopUpMessage('error', errorMessage));
        scrollToTopOfPage();
        return false;
    }
    //End of validation
    if(__uploading_file.length!=0){
        __uploadFileName = __uploading_file;
    }
    var i                           = 0;
    var fileObj                     = new processFileName(__uploadFileName[i].name);
    var param                       = new Array;
        param["file_name"]          = fileObj.uniqueFileName();        
        param["extension"]          = fileObj.fileExtension();
        param["course_id"]          = __course_id;
        param["file"]               = __uploadFileName[i]; 
    var uploadURL                   = admin_url+"coursebuilder/upload_to_home_server"; 
                 
    //uploadFileToLocalServer(uploadURL, param);
    if( __uploadFileName.length > 0 )
    { 
        $('#percentage_bar').show();
    }
    uploadFiles(uploadURL, param, saveLectureConfirm);
}

function saveLectureConfirm(response)
{
    //getting file details
    var section_id                      = $('#section_id').val();
    var section_name                    = $('#section_name').val();
    var lecture_name                    = $('#lecture_name').val();
    var lecture_description             = $('#lecture_description').val();
    var sent_mail_on_lecture_creation   = $('#sent_mail_on_lecture_creation').prop('checked');
        sent_mail_on_lecture_creation   = (sent_mail_on_lecture_creation==true)?'1':'0';
    var errorCount                      = 0;
    var errorMessage                    = '';
    //end

    //validation process
    if( lecture_name == '')
    {
        errorCount++;
        errorMessage += 'Please enter lecture name <br />';
    }

    if(__create_section_as_new == true && section_name == '')
    {
        errorCount++;
        errorMessage += 'Section required <br />';
    }

    if(__create_section_as_new == false && section_id == '')
    {
        errorCount++;
        errorMessage += 'please choose section <br />';
    }

    if( lecture_description == '')
    {
       // errorCount++;
        //errorMessage += 'please enter lecture description<br />';
    }
    cleanPopUpMessage();
    if(errorCount > 0 )
    {
        $('#upload-lecture .modal-body').prepend(renderPopUpMessage('error', errorMessage));
        scrollToTopOfPage();
        return false;
    }
    //End of validation
    console.log(response);
    var data   = $.parseJSON(response);
    if(data.error == true)
    {
        $('#percentage_bar').hide();
        $('#percentage_bar .progress-bar').css('width', '0px');
        $('#upload-lecture .modal-body').prepend(renderPopUpMessage('error', data.message));
        scrollToTopOfPage();
        return false;
    }
    else
    {
        var i                           = 0;
        var fileObj                     = new processFileName(__uploadFileName[i].name);
        var uploadURL                   = admin_url+"coursebuilder/save_lecture"; 
        var param                       = new Array;
            if(typeof data.cl_filename != 'undefined') {
                param["file_name"]          = data.cl_filename;        
                param["extension"]          = 'zip';             
            } else {
                param["file_name"]          = fileObj.uniqueFileName();        
                param["extension"]          = fileObj.fileExtension();
            } 
            //setting lecture details in post array

            if(__create_section_as_new == true)
            {
                param["section_name"]               = section_name;                    
                param["section_id"]                 = '';
            }
            else
            {
                param["section_name"]               = '';
                param["section_id"]                 = section_id;                    
            }
            param["lecture_name"]                   = lecture_name;  
            param["course_id"]                      = __course_id;  
            param["lecture_description"]            = lecture_description;        
            param["sent_mail_on_lecture_creation"]  = sent_mail_on_lecture_creation;  
            param["upload_data"]                    = response;    
            param.course_name                       = __course_name;    
            //end

            if(['mp4','mp3','f4v'].indexOf(param["extension"]) != -1){
                //window.URL = window.URL || window.webkitURL;
                var video = document.createElement('video');
                video.preload = 'metadata';
                video.src = URL.createObjectURL(__uploadFileName[0]);
                video.onloadedmetadata = function() {
                    param['duration']       = video.duration;
                    uploadFiles(uploadURL, param, uploadFileToLocalServerCompleted);
                }
            } else {
                //uploadFileToLocalServer(uploadURL, param);
                uploadFiles(uploadURL, param, uploadFileToLocalServerCompleted);
            }
    }
}
$(document).on('click', '#upload-lecture .close, #upload-lecture .btn-red', function(){
    if($(this).parent().attr('id') != 'popUpMessage' ) {
        $("#lecture_file_upload_manual").val('');
        __create_section_as_new = false;
        __create_description_as_new = false;
        $('#create_new_section_cancel, #section_name, #create_new_section_cancel_certificate, #create_new_desciptive_test_cancel, #create_new_section_scorm_cancel, #create_new_section_cancel_assesment, #create_new_section_cancel_survey, #create_new_section_cancel_live_lecture, #create_new_section_cancel_html, #section_name_assesment, #section_name_survey, #section_name_certificate, #section_name_live_lecture, #section_name_html, #create_new_section_cancel_youtube, #section_name_youtube, #create_new_section_cancel_import_content, #section_name_import_content, #section_name_descriptive, #section_name_scorm').hide();
        $('#create_new_section, #section_id, #create_new_desciptive_test, #create_new_section_scorm, #create_new_section_assesment, #create_new_section_certificate, #create_new_section_survey, #create_new_section_live_lecture, #create_new_section_html, #section_id_assesment, #section_id_certificate,  #section_id_survey, #section_id_live_lecture, #section_id_html, #create_new_section_youtube, #section_id_youtube, #create_new_section_import_content, #section_id_import_content, #section_id_descriptive, #section_id_scorm').show();
        $('#section_name, #section_name_assesment, #section_name_survey, #section_name_certificate, #section_name_live_lecture, #section_name_html, #section_name_youtube, #section_name_import_content, #section_name_descriptive').val("");

    }
    
    
});
function uploadFileToLocalServerCompleted(response)
{
    console.log(response);
    $('#percentage_text').html(lang('complete'));
    $('#percentage_count').html(lang('complete'));
    // __uploading_file = null;
    $(" #section_id, #section_name").val("");//#lecture_name,, #lecture_description
    $("#sent_mail_on_section_creation").prop('checked', false);
    // $("#upload-lecture").modal('hide');
    var data = $.parseJSON(response);
    console.log('uploadFileToLocalServerCompleted', 'file_server_upload');
    if(data.status == false)
    {  
        window.location = admin_url+'coursebuilder/lecture/'+data.lecture_id;
    }else{
        $('#upload-lecture .modal-body').prepend(renderPopUpMessage('error', data.message));
        scrollToTopOfPage();
        $('#percentage_bar').hide();
        $('#percentage_bar .progress-bar').css('width', '0px');
        $("#section_id, #section_name").val("");//
        return false;
    }
}

function saveLectureDetail()
{
    var lecture_name                    = $('#lecture_name').val();
    
    var errorCount                      = 0;
    var errorMessage                    = '';
    var lecture_image = $('#lecture_image_add').attr('image_name');

    //end
    if ((cb_has_lecture_image == 1) && lecture_image == 'default-lecture.jpg' && ($('#lecture_logo_btn')[0].files[0] == undefined)) {
        errorCount++;
        errorMessage += 'Please upload lecture image.<br>';
    }
    //validation process
    if( lecture_name == '')
    {
        errorCount++;
        errorMessage += 'Please enter lecture name <br />';
    }
        
    cleanPopUpMessage();
    if(errorCount > 0 )
    {
        $('#video_form').prepend(renderPopUpMessage('error', errorMessage));
        scrollToTopOfPage();
        return false;
    }
    
    if( __uploading_file.length > 0 )
    {
       
        var param                       = new Array;
        var i                           = 0;
        var fileObj                     = new processFileName(__uploading_file[i].name);

        
            param["file_name"]          = fileObj.uniqueFileName();        
            param["extension"]          = fileObj.fileExtension();
            param["file"]               = __uploading_file[i]; 
            param["course_id"]          = __course_id;
            //console.log(param); return;
            var validImageTypes = ['gif', 'jpeg', 'png', 'jpg', 'bmp'];
            if(validImageTypes.includes(param["extension"])){
                $('#video_form').prepend(renderPopUpMessage('error', 'Image files could not be uploaded as lecture content.'));
                scrollToTopOfPage();
                return false;
            }
            $('#lecture_upload_progress').show();
            var uploadURL               = admin_url+"coursebuilder/upload_to_home_server"; 
            uploadFiles(uploadURL, param, saveLectureDetailsAlone);
    }
    else
    {
        var request                 = JSON.stringify(new Array());
        saveLectureDetailsAlone(request);
    }
}

function save_support_files()
{
    var lecture_id                   = $('#support_file_lecture_id').val();
    
    var errorCount                      = 0;
    var errorMessage                    = '';
    //end

        
    cleanPopUpMessage();
    if(errorCount > 0 )
    {
        $('#video_form').prepend(renderPopUpMessage('error', errorMessage));
        scrollToTopOfPage();
        return false;
    }
    
    if( __support_file.length > 0 )
    {
        var param                       = new Array;
        var i                           = 0;
        var fileObj                     = new processFileName(__support_file[i].name);
            param["file_name"]          = fileObj.uniqueFileName();        
            param["extension"]          = fileObj.fileExtension();
            param["file"]               = __support_file[i]; 
            param["course_id"]          = __course_id;
            //param["lecture_id"]         = lecture_id;
            $('#lecture_upload_progress').show();
            $("#save_support_file").html('Saving<img src="'+asset_url+'images/loader.svg" width="25">');
            var uploadURL                   = admin_url+"coursebuilder/upload_supportfile_to_home_server"; 
            uploadFiles(uploadURL, param, supportfileTouploadCompleted);
    }
}

function saveLectureDetailsAlone(response)
{
    
    var param                           = new Array;
    var uploadURL                       = admin_url+"coursebuilder/save_lecture"; 
    var section_id                      = $('#section_id').val();
    var course_id                       = $('#course_id').val();
    var lecture_id                      = $('#lecture_id').val();
    var lecture_name                    = $('#lecture_name').val();
    var lecture_description             = $('#lecture_description').val();
    var lecturePreview                  = $("input[name='lecture_preview']:checked").val();
    var sent_mail_on_lecture_creation   = $('#cl_sent_mail_on_lecture_creation').prop('checked');
        sent_mail_on_lecture_creation   = (sent_mail_on_lecture_creation==true)?'1':'0';
    var lecture_access_limit            = $("input[type='radio'][name='cl_limited']:checked").val();
        if( lecture_access_limit == 1)
        {
            lecture_access_limit = $('#cl_limited_access').val();
        }
        var formData = new FormData();
        console.log($('#lecture_logo_btn')[0].files[0]);
        formData.append('lecture_image', $('#lecture_logo_btn')[0].files[0]);
        formData.append('section_id', section_id);
        formData.append('course_id', course_id);
        formData.append('lecture_id', lecture_id);
        formData.append('lecture_name', lecture_name);
        formData.append('preview_lecture', lecturePreview);
        formData.append('lecture_description', lecture_description);
        formData.append('sent_mail_on_lecture_creation', sent_mail_on_lecture_creation);
        formData.append('lecture_access_limit', lecture_access_limit);
        $.ajax({
            url: admin_url + 'coursebuilder/save_lecture',
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            cache: false,
            async: false,
            success: function (response) {
            }
        });
    if( __uploading_file.length > 0 )
    {
        var i                           = 0;
        var fileObj                     = new processFileName(__uploading_file[i].name);
            param["file_name"]          = fileObj.uniqueFileName();        
            param["extension"]          = fileObj.fileExtension();
    }
    //setting lecture details in post array
    param["section_id"]                     = section_id;                    
    param["course_id"]                      = course_id;  
    param["lecture_id"]                     = lecture_id;  
    param["lecture_name"]                   = lecture_name;  
    param["preview_lecture"]                = lecturePreview;
    param["lecture_description"]            = lecture_description;        
    param["sent_mail_on_lecture_creation"]  = sent_mail_on_lecture_creation;        
    param["lecture_access_limit"]           = lecture_access_limit;        
    //end
    var data                = $.parseJSON(response);
    param["upload_data"]    = response;
    
    if(['mp4','mp3','f4v'].indexOf(param["extension"]) != -1){
        //window.URL = window.URL || window.webkitURL;
        var video = document.createElement('video');
        video.preload = 'metadata';
        video.src = URL.createObjectURL(__uploading_file[0]);
        video.onloadedmetadata = function() {
            param['duration']       = video.duration;
            uploadFiles(uploadURL, param, uploadFileResponse);
        }
    } else {
        //uploadFileToLocalServer(uploadURL, param);
        uploadFiles(uploadURL, param, uploadFileResponse);
    }
    //end
    // uploadFiles(uploadURL, param, uploadFileResponse);
}

function uploadFileResponse(response)
{    
    var data  = $.parseJSON(response);
    if(data['error'] == false)
    {
        console.log(data);
        var messageObject = { 
            'body': data['message'],
            'button_yes':'OK', 
            'button_no':'CANCEL'
            };
            callback_success_modal(messageObject);

         //location.reload();
        // window.location = admin_url+'coursebuilder/lecture/'+data['id'];
    }
    else
    {
        var messageObject = {
            'body': data['message'],
            'button_yes': 'OK',
        };
        callback_danger_modal(messageObject);        
    }
}
function saveLectureDetailScorm()
{
    var lecture_name                    = $('#lecture_name').val();
    var lecture_description             = $('#lecture_description').val();
    var course_id                       = $('#course_id').val();
    var lecture_id                      = $('#lecture_id').val();
    var section_id                      = $('#section_id').val();
    var errorCount                      = 0;
    var errorMessage                    = '';
    //end
    var lecture_image                   = $('#lecture_image_add').attr('image_name');
    if ((cb_has_lecture_image == 1) && lecture_image == 'default-lecture.jpg' && ($('#lecture_logo_btn')[0].files[0] == undefined)) {
        errorCount++;
        errorMessage += 'Please upload lecture image.<br>';
    }
    //validation process
    if( lecture_name == '')
    {
        errorCount++;
        errorMessage += 'Please enter lecture name <br />';
    }
    
    cleanPopUpMessage();
    if(errorCount > 0 )
    {
        $('#video_form').prepend(renderPopUpMessage('error', errorMessage));
        scrollToTopOfPage();
        return false;
    }
    
    if( __uploading_file.length > 0 )
    {
        var param                       = new Array;
        var uploadURL                   = admin_url+"coursebuilder/upload_to_home_server_scorm"; 
        var i                           = 0;
        var fileObj                     = new processFileName(__uploading_file[i].name);
            param["file_name"]          = fileObj.uniqueFileName();        
            param["extension"]          = fileObj.fileExtension();
            param["file"]               = __uploading_file[i]; 
            param['lecture_name']       = lecture_name;
            param['lecture_description']  = lecture_description;
            param['course_id']          = course_id;
            param['lecture_id']         = lecture_id;
            param['section_id']         = section_id;
            $('#lecture_upload_progress').show();
            var formData = new FormData();
            console.log($('#lecture_logo_btn')[0].files[0]);
            formData.append('lecture_image', $('#lecture_logo_btn')[0].files[0]);
            formData.append('lecture_name', section_id);
            formData.append('course_id', course_id);
            formData.append('lecture_id', lecture_id);
            formData.append('lecture_name', lecture_name);
            formData.append('section_id', section_id);
            $.ajax({
                url: admin_url + 'coursebuilder/upload_to_home_server_scorm',
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                cache: false,
                async: false,
                success: function (response) {
                }
            });
            uploadFiles(uploadURL, param, saveLectureDetailScormFinished);
    }
    else
    {
        var request                 = JSON.stringify(new Array());
        saveLectureDetailsAlone(request);
    }
}

function saveLectureDetailScormFinished(response) {
    var data  = $.parseJSON(response);
    if(data.error == "false")
    {
        window.location = admin_url+'coursebuilder/lecture/'+data.id;
    }
    else
    {
        $('#lecture_upload_progress').hide();
        var messageObject = {
            'body': data.message,
            'button_yes': 'OK',
        };
        callback_danger_modal(messageObject);
    }
}