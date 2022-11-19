// alert('s3 upload');
var s3Object              = new Array();
const __video_format      = new Array('mp4', 'flv', 'avi', 'f4v');
const __document_format   = new Array('doc', 'docx', 'pdf', 'ppt', 'pptx', 'xls', 'xlsx');
const __scorm_format      = new Array('zip');
const __audio_format      = new Array('mp3');
var __upload_path         = '';
var __service             = '';
const __fileUploadPaths   = {};
$(document).ready(function(){
    var course_id = (typeof __course_id != 'undefined' ? __course_id : 0);
     $.ajax({
        url: admin_url+'coursebuilder/s3objetcs',
        type: "POST",
        data:{"is_ajax":true, 'course_id' : course_id},
        success: function(response) {
            var data = $.parseJSON(response);
            jQuery.each(data['s3_object'], function(index, item) {
                s3Object[index] = item;
            });
            jQuery.each(data['upload_path'], function(index, item) {
                __fileUploadPaths[index] = item;
            });
            
        }
    });
});
function saveLecture()
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
        //errorCount++;
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
    
    var i                               = 0;
    var uploadURL                       = __uploads_url; 
    var fileObj                         = new processFileName(__uploading_file[i]['name']);
    var file_name                       = fileObj.uniqueFileName();        
    var extension                       = fileObj.fileExtension();
    

    __upload_path       = '';
    if(inArray(extension, __video_format))
    {
        __upload_path = __fileUploadPaths['video_upload_path']+file_name
    }
    
    if(inArray(extension, __document_format))
    {
        __upload_path = __fileUploadPaths['document_upload_path']+file_name
    }
    
    if(inArray(extension, __scorm_format))
    {
        __upload_path = __fileUploadPaths['scorm_upload_path']+file_name
    }

    if(inArray(extension, __audio_format))
    {
        __upload_path = __fileUploadPaths['audio_upload_path']+file_name
    }

    if( __upload_path == '' )
    {
        $('#upload-lecture .modal-body').prepend(renderPopUpMessage('error', 'Upload path is empty'));
        scrollToTopOfPage();
        return false;
    }

    var param                           = new Array;
        param["key"]                    = __upload_path;
        param["file"]                   = __uploading_file[i];        
        param["file"]["key"]            = param["key"];
        param["AWSAccessKeyId"]         = s3Object['access_key'];
        param["acl"]                    = s3Object['acl'];
        param["success_action_status"]  = s3Object['success_action_status'];
        param["policy"]                 = s3Object['policy'];
        param["signature"]              = s3Object['signature'];
        //uploadFileToS3(uploadURL, param);
        if( __uploading_file.length > 0 )
        {
            $('#percentage_bar').show();        
        }
        
        __service = (param["extension"] == 'mp3')?'':'aws';
        if(__service == 'aws')
        {
            uploadURL                   = admin_url+"coursebuilder/upload_to_aws_server"; 
            uploadFilesToAws(uploadURL, param, uploadFileToS3Completed); 
        }
        else
        {
            uploadFiles(uploadURL, param, uploadFileToS3Completed, 'xml');
        }            
        // console.log(param);
}
 
function save_support_files()
{
    //getting file details

    var errorCount                      = 0;
    var errorMessage                    = '';
    var extensions                      = ['mp4','avi','flv'];
    
    var i                               = 0;
    //var uploadURL                       = __uploads_url; 
    var uploadURL                       = admin_url+"coursebuilder/upload_to_home_server?supportfile=1";
    var fileObj                         = new processFileName(__support_file[i]['name']);
    var file_name                       = fileObj.uniqueFileName();        
    var extension                       = fileObj.fileExtension();
    

    __upload_path       = '';
    __upload_path       = __fileUploadPaths['supportfile_upload_path']+file_name
    if( __upload_path == '' )
    {
        $('#upload-lecture .modal-body').prepend(renderPopUpMessage('error', 'Upload path is empty'));
        scrollToTopOfPage();
        return false;
    }
    
    var param                           = new Array;
        param["key"]                    = __upload_path;
        param["file"]                   = __support_file[i];        
        param["file"]["key"]            = param["key"];
        param["AWSAccessKeyId"]         = s3Object['access_key'];
        param["acl"]                    = s3Object['acl'];
        param["success_action_status"]  = s3Object['success_action_status'];
        param["policy"]                 = s3Object['policy'];
        param["signature"]              = s3Object['signature'];
        param["extension"]              = extension;
        param['course_id']              = __course_id;
        //uploadFileToS3(uploadURL, param);
        if( __support_file.length > 0 )
        {
            $('#percentage_bar').show(); 
            $("#save_support_file").html('Saving...');
        }
        
        __service = (has_s3_enabled == 1)?'aws':((!inArray(param["extension"], extensions))?'':'aws');
        if(__service == 'aws' )
        {
            uploadURL                   = admin_url+"coursebuilder/upload_to_aws_server"; 
            uploadFilesToAws(uploadURL, param, supportfileTouploadCompletedS3); 
        }
        else
        {
            uploadFiles(uploadURL, param, supportfileTouploadCompleted, 'xml');
        }            
        // console.log(param);
}


function uploadFileToS3Completed(response)
{
    var uploadURL                       = admin_url+"coursebuilder/save_lecture"; 
    var section_id                      = $('#section_id').val();
    var section_name                    = $('#section_name').val();
    var lecture_name                    = $('#lecture_name').val();
    var lecture_description             = $('#lecture_description').val();
    var sent_mail_on_lecture_creation   = $('#sent_mail_on_lecture_creation').prop('checked');
        sent_mail_on_lecture_creation   = (sent_mail_on_lecture_creation==true)?'1':'0';

    var file_path 	= __uploads_url+__upload_path;
    /*var xmlDoc		= response.jqXHR.responseXML;
    var xml_node	= xmlDoc.getElementsByTagName("Location");
        file_path       = xml_node[0].childNodes[0].nodeValue;*/
        
        var param       = new Array;
        //setting lecture details in post array
        if(__create_section_as_new==true)
        {
            param["section_name"]               = section_name;                    
            param["section_id"]                 = '';
        }
        else
        {
            param["section_name"]               = '';
            param["section_id"]                 = section_id;                    
        }
        param["file_name"]                      = file_path;  
        param["lecture_name"]                   = lecture_name;  
        param["course_id"]                      = __course_id;  
        param["lecture_description"]            = lecture_description;        
        param["sent_mail_on_lecture_creation"]  = sent_mail_on_lecture_creation;  
        param["from_s3"]                        = true;
        
        let fileObject                          = new processFileName(response.file_object.full_path);
        param["unique_file_name"]               = fileObject.uniqueFileName();
        param["extension"]                      = fileObject.fileExtension();
        response.file_object.file_ext           = '.'+param["extension"];
        console.log(response);
        param["upload_data"]                    = JSON.stringify(response);   

        uploadFiles(uploadURL, param, saveLectureCompleted);
}

function saveLectureCompleted(response)
{
    var data = $.parseJSON(response);
    if(data.status == false)
    {
        window.location = admin_url+'coursebuilder/lecture/'+data['lecture_id'];
    }else{
        lauch_common_message('Error occured', data['message']);
    }

}

/*used in lecture details page*/
function saveLectureDetail()
{
    var section_id                      = $('#section_id').val();
    var course_id                       = $('#course_id').val();
    var lecture_id                      = $('#lecture_id').val();
    var lecture_name                    = $('#lecture_name').val();
    var lecture_description             = $('#lecture_description').val();
    var errorCount                      = 0;
    var errorMessage                    = '';
    var sent_mail_on_lecture_creation   = $('#cl_sent_mail_on_lecture_creation').prop('checked');
        sent_mail_on_lecture_creation   = (sent_mail_on_lecture_creation==true)?'1':'0';
    var lecture_access_limit            = $("input[type='radio'][name='cl_limited']:checked").val();
    if( lecture_access_limit == 1)
    {
        lecture_access_limit = $('#cl_limited_access').val();
    }
    var lecture_image                   = $('#lecture_image_add').attr('image_name');
    
    var i                               = 0;
    var uploadURL                       = __uploads_url; 
    if(__uploading_file.length > 0){
        var fileObj                     = new processFileName(__uploading_file[i]['name']);
        var file_name                   = fileObj.uniqueFileName();        
        var extension                   = fileObj.fileExtension();
        __upload_path       = '';
        if(inArray(extension, __video_format))
        {
            __upload_path   = __fileUploadPaths['video_upload_path']+file_name
        }

        if(inArray(extension, __document_format))
        {
            __upload_path   = __fileUploadPaths['document_upload_path']+file_name
        }
        if(inArray(extension, __scorm_format))
        {
            __upload_path = __fileUploadPaths['scorm_upload_path']+file_name
        }
    
        if(inArray(extension, __audio_format))
        {
            __upload_path = __fileUploadPaths['audio_upload_path']+file_name
        }
    }
    //validation process
    if( lecture_name == '')
    {
        errorCount++;
        errorMessage += 'please enter lecture name <br />';
    }


    if( lecture_description == '')
    {
        errorCount++;
        errorMessage += 'please enter lecture description<br />';
    }
    if( (cb_has_lecture_image == 1) && lecture_image == 'default-lecture.jpg' && ($('#lecture_logo_btn')[0].files[0] == undefined)  )
    {
        errorCount++;
        errorMessage += 'Please upload lecture image.<br>';
    }
    if(__upload_path == '' && __uploading_file.length > 0 ){
        errorCount++;
        errorMessage += 'Invalid file<br />';
    }
    cleanPopUpMessage();
    if(errorCount > 0 )
    {
        $('#video_form').prepend(renderPopUpMessage('error', errorMessage));
        scrollToTopOfPage();
        return false;
    }

    //image file upload
    
    //End of validation    
     if( __uploading_file.length > 0 )
    {
        var param                           = new Array;
            param["key"]                    = __upload_path;
            param["file"]                   = __uploading_file[i];        
            param["file"]["key"]            = param["key"];
            param["AWSAccessKeyId"]         = s3Object['access_key'];
            param["acl"]                    = s3Object['acl'];
            param["success_action_status"]  = s3Object['success_action_status'];
            param["policy"]                 = s3Object['policy'];
            param["signature"]              = s3Object['signature'];
            if( __uploading_file.length > 0 )
            {
                $('#percentage_bar, #lecture_upload_progress').show();
            }
            __service = (param["extension"] == 'mp3')?'':'aws';
            if(__service == 'aws')
            {
                uploadURL                   = admin_url+"coursebuilder/upload_to_aws_server"; 
                uploadFilesToAws(uploadURL, param, uploadFileToS3FromDetailsPageCompleted); 
            }
            else
            {
                uploadFiles(uploadURL, param, uploadFileToS3FromDetailsPageCompleted, 'xml');
            }            
    }else{
        uploadFileToS3FromDetailsPageCompleted(false);
    }
}


function uploadFileToS3FromDetailsPageCompleted(response)
{
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
    var param                       = new Array;
    var uploadURL                   = admin_url+"coursebuilder/save_lecture"; 
    if( __uploading_file.length > 0 )
    {
        var file_path                   = __uploads_url+__upload_path;
        param["file_name"]              = file_path;  
        param["from_s3"]                = true;
        $('#lecture_upload_progress').show();
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
       if(response){
        let fileObject                          = new processFileName(response.file_object.full_path);
        param["unique_file_name"]               = fileObject.uniqueFileName();
        param["extension"]                      = fileObject.fileExtension();
        response.file_object.file_ext           = '.'+param["extension"];
        console.log(response);
        param["upload_data"]                    = JSON.stringify(response);   

        //end
        uploadFiles(uploadURL, param, uploadFileResponse);
       }else{
        uploadFiles(uploadURL, param, uploadFileResponse);
       }
       console.log('uploadFiles 396');
}