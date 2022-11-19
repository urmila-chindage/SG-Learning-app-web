var lecture_access_limit = 0;
var __start_date         = '';
var myWindow = '';
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

function convertTime12to24(time12h) {
	const time12hObj 	= time12h.split(' ');
    const time 			= time12hObj[0];
    const modifier 		= time12hObj[1];
  
    const timeObj 		= time.split(':');
    let hours 			= timeObj[0];
    let minutes 		= timeObj[1];
  
    if (hours === '12') {
      hours = '00';
    }
  
    if (modifier === 'PM') {
      hours = parseInt(hours, 10) + 12;
    }
  
    return hours + ':' + minutes;
  }
  
  
function saveLectureDetail()
{
    var section_id                      = $('#section_id').val();
    var course_id                       = $('#course_id').val();
    var lecture_id                      = $('#lecture_id').val();
    var live_lecture_id                 = $('#live_lecture_id').val();
    var lecture_name                    = $('#lecture_name').val();
    var schedule_date                   = $('#schedule_date').val();
    var start_time                      = $('#start_time').val();
    var duration                        = $('#duration').val();
    var lecture_description             = $('#lecture_description').val();
    var studio_id                       = $('#studio_list').val();
    var new_time                        = $('#start_time_noon').text();
    var lecture_image                   = $('#lecture_image_add').attr('image_name');
    // lecture_access_limit            = $("input[type='radio'][name='cl_limited']:checked").val();
    // if( lecture_access_limit == 1)
    // {
    //     lecture_access_limit = $('#cl_limited_access').val();
    // }
    
    start_time = convertTime12to24(start_time +" " + new_time); 
    //alert(start_time);
    var errorCount                      = 0;
    var errorMessage                    = '';
    //end

    //validation process
    if( lecture_name == '')
    {
        errorCount++; 
        errorMessage += 'Please enter lecture name <br />';
    }
    if( (cb_has_lecture_image == 1) && lecture_image == 'default-lecture.jpg' && ($('#lecture_logo_btn')[0].files[0] == undefined)  )
    {
        errorCount++;
        errorMessage += 'Please upload lecture image.<br>';
    }
    //alert(start_time);
    if((start_time == ':undefined') || (start_time == 'NaN:undefined') || (start_time == ''))
    {
        errorCount++;
        errorMessage += 'Please select start time <br />';
    }
    if(duration == '')
    {
        errorCount++;
        errorMessage += 'Please enter duration <br />';
    }
    else{
        if(isNaN(duration) || duration < 0){
            errorCount++;
            errorMessage += 'Duration must be a valid number <br />';
        }
    }
    if(studio_id == '')
    {
        errorCount++;
        errorMessage += 'Please select studio <br />';
    }
    
    cleanPopUpMessage();
    if(errorCount > 0 )
    {
        $('#live_lecture_form').prepend(renderPopUpMessage('error', errorMessage));
        scrollToTopOfPage();
        return false;
    }
    else{
        
        var param                       = new Array;
        var uploadURL                   = admin_url+"coursebuilder/save_live_lecture"; 
        
        //setting lecture details in post array
        param["section_id"]                         = section_id;                    
        param["course_id"]                          = course_id;  
        param["lecture_id"]                         = lecture_id;  
        param["live_lecture_id"]                    = live_lecture_id;  
        param['studio_id']                          = studio_id;
        param["schedule_date"]                      = schedule_date;  
        param["start_time"]                         = start_time;  
        param["duration"]                           = duration;  
        param["live_lecture_name"]                  = lecture_name;  
        param["live_lecture_description"]           = lecture_description;          
        //end
        var formData = new FormData();
        formData.append('lecture_image', $('#lecture_logo_btn')[0].files[0]);
        formData.append('section_id', section_id);
        formData.append('course_id', course_id);
        formData.append('lecture_id', lecture_id);
        formData.append('live_lecture_id', live_lecture_id);
        formData.append('studio_id', studio_id);
        formData.append('schedule_date', schedule_date);
        formData.append('start_time', start_time);
        formData.append('duration', duration);
        formData.append('live_lecture_name', lecture_name);
        formData.append('live_lecture_description', lecture_description);

        $.ajax({
            url: admin_url+'coursebuilder/save_live_lecture',
            type: "POST",
            data:formData,
            processData:false,
            contentType:false,
            cache:false,
            async:false,
            success: function(response) {
               
            }
        });
        uploadFiles(uploadURL, param, uploadFileResponse);
    }
}

function uploadFileResponse(response)
{    
    var data  = $.parseJSON(response);
    if(data['error'] == "false")
    {
        var messageObject = { 
            'body': data['message'],
            'button_yes':'OK', 
            'button_no':'CANCEL'
            };
            callback_success_modal(messageObject);

        // location.reload();
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

var __uploading_file = '';
var __uploads_url = '';
$(document).on('change', '#live_file_upload_manual', function(e){
    //console.log(e.currentTarget.files[0]);
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
    $('#live_upload_progress').hide();
    __uploads_url = webConfigs('uploads_url');
    
    var i                           = 0;
    var uploadURL                   = admin_url+"coursebuilder/upload_live_files_to_home_server"; 
    var fileObj                     = new processFileName(__uploading_file[i]['name']);
    var param                       = new Array;
        param["file_name"]          = fileObj.uniqueFileName();        
        param["extension"]          = fileObj.fileExtension();
        param["file"]               = __uploading_file[i]; 
        param['live_id']            = $('#live_lecture_id').val();
              
    //uploadFileToLocalServer(uploadURL, param);
    if( __uploading_file.length > 0 )
    { 
        $('#percentage_bar, #live_upload_progress').show();
    }
    uploadFiles(uploadURL, param, saveLiveLectureConfirmed);
});

function saveLiveLectureConfirmed(response)
{
    __uploading_file = '';
    __uploads_url = '';
    var data = $.parseJSON(response);
    var fileListHtml = '';
        fileListHtml += '<div class="default-view-txt m0 mb10 test-folder" id="file_'+data['file_object']['id']+'">';
        fileListHtml += '    <a target="_blank" href="'+__livefilesPath+data['file_object']['link']+'" title="View" class="test-folder-row" data-toggle="tooltip" data-placement="top" data-original-title="View">';
        fileListHtml += '         <i class="icon icon-attach-1"></i>';
        fileListHtml += '    </a>';
        fileListHtml += '    <a target="_blank" href="'+__livefilesPath+data['file_object']['link']+'">'+data['file_object']['title']+'</a>              ';
        fileListHtml += '    <a href="javascript:void(0)" onclick="deleteLiveFiles(\''+data['file_object']['id']+'\')" title="Delete" class="test-folder-row" data-toggle="tooltip" data-placement="top" data-original-title="Delete">';
        fileListHtml += '         <i class="icon icon-trash-empty"></i>';
        fileListHtml += '    </a>';
        fileListHtml += '</div>';
    $('#live_files_list_wrapper').append(fileListHtml);
    $('#percentage_bar, #live_upload_progress').hide();
    $('.progress-bar').css('width','0%');
}



function startOrStopLive(live_id, course_id, make_online)
{
    if(make_online == '1'){
        popGoLive(live_id);
    }else if(make_online == '2'){
        popVirtualLive(live_id);
    }else if(make_online == '0'){
        closeLive();
    }
    $("#select_live_mode").modal('hide');
    $.ajax({
        url: admin_url+'coursebuilder/configure_live',
        type: "POST",
        data:{"is_ajax":true, "live_id":live_id,"course_id":course_id,"make_online":make_online},
        success: function(response) {
            var data                = $.parseJSON(response);
            if(data['already_live'] > 0 )
            {
                $('#active-lecture').modal();
                $('#header_text').html(lang('other_lecture_on_live'));
                $('#popup_message').html(lang('message_to_disable_live'));
                $('#change_status_section').unbind();
                $('#change_status_section').html(lang('start'));
                $('#change_status_section').click({"live_id": live_id, "course_id": course_id, "make_online": make_online}, startOrStopLiveInput);    
            }
            else
            {
                console.log('check_live');
                var currently_live = '';
                var current_lang = '';
                var current_color = '';
                if(make_online == '1'){
                    currently_live = (make_online==1)?0:1;
                    current_lang   = (make_online==1)?'stop':'start';
                    current_color  = (make_online==1)?'btn-red':'btn-light-green';
                }else if(make_online == '2'){
                    currently_live = (make_online==2)?0:2;
                    current_lang   = (make_online==2)?'stop':'start';
                    current_color  = (make_online==2)?'btn-red':'btn-light-green';
                }else if(make_online == '0'){
                    currently_live = '0';
                    current_lang   = 'stop';
                    current_color  = 'btn-red';
                }
                
                $('#start_or_stop_live_btn').attr('class', 'pull-right btn '+current_color);
                $('#start_or_stop_live_btn').attr('onclick','startOrStopLive('+live_id+','+course_id+', '+currently_live+')');
                if(make_online == '1' || make_online == '2'){
                    $('#start_or_stop_live_btn').remove();
                }
                $('#start_or_stop_live_btn').unbind();
                $('.start-stop-live').html()
                $('#start_or_stop_live_btn').html(lang(current_lang));
                $('#start_or_stop_live_btn').click({"live_id": live_id, "course_id": course_id, "make_online": currently_live}, startOrStopLiveInput);    
                $('#active-lecture').modal('hide');
                
                //location.reload();
            }
        }
    });    
}

function popGoLive(live_id) {
    myWindow = window.open(site_url+'/live/golive/'+live_id,'_blank');
    myWindow.focus();
}

function popVirtualLive(live_id) {
    myWindow = window.open(site_url+'/live/join/'+live_id,'_blank');
    myWindow.focus();
}

function closeLive() {
  //if(myWindow)
  console.log(myWindow);
   myWindow.close();
}
function startOrStopLiveInput(param)
{
    startOrStopLiveConfirmed(param.data.live_id, param.data.course_id, param.data.make_online);
}

function startOrStopLiveConfirmed(live_id,course_id,make_online)
{
    $.ajax({
        url: admin_url+'coursebuilder/configure_live_confirmed',
        type: "POST",
        data:{"is_ajax":true, "live_id":live_id, "course_id":course_id, "make_online":make_online},
        success: function(response) {
            var data           = $.parseJSON(response);
            var currently_live = '';
            var current_lang   = '';
            if(make_online == '1'){
                currently_live = (make_online==1)?0:1;
                current_lang   = (make_online==1)?'stop':'start';
            }else if(make_online == '2'){
                currently_live = (make_online==2)?0:2;
                current_lang   = (make_online==2)?'stop':'start';
            }else{
                currently_live = '0';
                current_lang   = 'stop';
            }
            
            
            //$('#start_or_stop_live_btn').removeAttr('onclick');
            $('#start_or_stop_live_btn').unbind();
            $('#start_or_stop_live_btn').html(lang(current_lang));
            $('#start_or_stop_live_btn').click({"live_id": live_id, "course_id": course_id, "make_online": currently_live}, startOrStopLiveInput);    
            $('#active-lecture').modal('hide');
            
        }
    });  
}

function publishRecordedVideo(lecture_id, live_id, recording_id) 
{
    $.ajax({
        url: admin_url+'coursebuilder/publish_record_live',
        type: "POST",
        data:{"is_ajax":true, "lecture_id":lecture_id, 'recording_id':recording_id},
        success: function(response) {
            var data           = $.parseJSON(response);
            
            $('#publish_recorded_video').modal();
            
            $('#publish_recorded_video #header_text_record').html(lang('publish_record_video'));
            $('#publish_recorded_video .m0-record').html(lang('are_you_sure')+" "+lang('publish')+" "+lang('recorded_videos')+" ["+data.recorded_name+"] "+" - "+data.lecture_name);
            $('#publish_recorded_video #popup_message_record').html(lang('publish_record_message_summary'));
            $('#publish_recorded_video #publish_record_video').unbind();
            $('#publish_recorded_video #publish_record_video').click({"lecture_id": lecture_id, "live_id":live_id, "recording_id":recording_id}, publishRecordedVideoConfirmed); 
            cleanPopUpMessage();
            
        }
    });
}

function publishRecordedVideoConfirmed(params)
{
    $.ajax({
        url: admin_url+'coursebuilder/publish_recorded_live',
        type: "POST",
        data:{"is_ajax":true, "lecture_id":params.data.lecture_id, "live_id":params.data.live_id, "recording_id":params.data.recording_id},
        success: function(response) {
            var data           = $.parseJSON(response);
            
            if(data['message'] == "true")
            {
                window.location = admin_url+'coursebuilder/home/'+data['course_id'];
            }
            
            
        }
    });
}
