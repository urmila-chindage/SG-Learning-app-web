var lecture_access_limit = 0;

$(document).ready(function(){
    lecture_access_limit = $("input[type='radio'][name='cl_limited']:checked").val();
    $("input[type='radio'][name='cl_limited']").click(function(){
        lecture_access_limit = $("input[type='radio'][name='cl_limited']:checked").val();
        if( lecture_access_limit == 0 )
        {
            $('#cl_limited_access_wrapper').hide();
        }
        else
        {
            $('#cl_limited_access_wrapper').show();
        }
    });
});

function lectureDownloadable(current, lecture_id)
{
    var downloadable = $(current).prop('checked');
        downloadable = (downloadable==true)?'1':'0';
    $.ajax({
        url: admin_url+'coursebuilder/lecture_downloadable',
        type: "POST",
        data:{"is_ajax":true, "downloadable":downloadable, "lecture_id":lecture_id},
        success: function(response) {
        }
    });
}
var __uploading_file = '';
var __uploads_url = '';
$(document).on('change', '#lecture_file_upload_manual', function(e){
    __uploading_file = e.currentTarget.files;
    if( __uploading_file.length > 1 )
    {
        lauch_common_message('Multiple file not allowed', 'more than one file not allowed.');    
        __uploading_file = '';
        return false;
    }
    var fileObj                     = new processFileName(__uploading_file[0]['name']);
        fileObj.uniqueFileName();        
    if(inArray(fileObj.fileExtension(), __allowed_files) == false)
    {
        lauch_common_message('Invalid File', 'This type of file is not allowed.');
        __uploading_file = '';
        return false;        
    }
    $('#upload_file_name').val(__uploading_file[0]['name']);
    var name    = e.currentTarget.value;
    name        = name.split("\\");
    name        = name[name.length - 1];
    // $('.fle-upload').prepend(name);
    $('#video_upload_progress').hide();
    __uploads_url = webConfigs('uploads_url');
});


function uploadFileResponse(response)
{
    __uploading_file = '';
    //console.log(response);
    location.reload();
}