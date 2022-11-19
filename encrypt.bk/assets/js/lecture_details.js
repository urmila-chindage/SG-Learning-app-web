
function changeLectureStatus(current_status, lecture_name, lecture_id)
{
    
    var header_status = ((current_status==1)?'deactivate':'activate');
    // $('#header_text').html(lang(header_status)+' '+lang('lecture')+' '+atob(lecture_name));
    
    var messageObject = {
        'body': 'Are you sure to '+header_status+' <b>"'+atob(lecture_name)+'"</b>',
        'button_yes': header_status.toUpperCase(),
        'button_no':'CANCEL',
        'continue_params':{
            "lecture_id": lecture_id,
            "lecture_name": atob(lecture_name),
            'is_ajax':'true',
            "status":((current_status==1)?0:1)
        },
    };
    callback_warning_modal(messageObject, changeLectureStatusConfirmed); 
}

function changeLectureStatusConfirmed(param)
{
    var lecture_id      = param.data.lecture_id;
    var status          = param.data.status;
    var lecture_name    = param.data.lecture_name;
    $.ajax({
        url: admin_url+'coursebuilder/change_lecture_status',
        type: "POST",
        data:{ "is_ajax":true, 'lecture_id':lecture_id, 'status':status},
        success: function(response) {
            var data  = $.parseJSON(response);
            if(data['error'] == false)
            {
                if(status == 1 )
                {
                    $('#lecture_status_text_'+lecture_id).html(lang('active'));
                    $('#lecture_status_wraper_'+lecture_id).removeClass('active-section').removeClass('Inactive-section').addClass('active-section');
                    $('#lecture_action_status_'+lecture_id).attr('onclick', 'changeLectureStatus(\'1\', \''+btoa(lecture_name)+'\', \''+lecture_id+'\')').html(lang('deactivate').toUpperCase()).removeClass('btn-green').removeClass('btn-orange').addClass('btn-orange');
                }
                else
                {
                    $('#lecture_status_text_'+lecture_id).html(lang('inactive'));
                    $('#lecture_status_wraper_'+lecture_id).removeClass('active-section').removeClass('Inactive-section').addClass('Inactive-section');
                    $('#lecture_action_status_'+lecture_id).attr('onclick', 'changeLectureStatus(\'0\', \''+btoa(lecture_name)+'\', \''+lecture_id+'\')').html(lang('activate').toUpperCase()).removeClass('btn-green').removeClass('btn-orange').addClass('btn-green');
                }
                var messageObject = {
                    'body': data['message'],
                    'button_yes': 'OK',
                };
                callback_success_modal(messageObject);
            }
            else
            {
                var messageObject = {
                    'body': data['message']+lecture_name,
                    'button_yes': 'OK',
                };
                callback_danger_modal(messageObject);
                return false;
            }
        }
    });
}

function deleteLectureConfirmed(param)
{
    $.ajax({
        url: admin_url+'coursebuilder/delete_lecture',
        type: "POST",
        data:{ "is_ajax":true, 'lecture_id':param.data.lecture_id},
        success: function(response) {
            $('#back_button').trigger('click');
        }
    });
}

function reinitializeVideoLecture(lecture_id, lecture_name) {
    var messageObject = {
                            'body': 'Are you sure to Re-initialize <b>"'+atob(lecture_name)+'"</b>',
                              'button_yes': 'REINITIALIZE',
                              'button_no':'CANCEL',
                              'continue_params':{
                              "lecture_id": lecture_id,
                              'is_ajax':'true'
                        },
    };
    callback_warning_modal(messageObject, reinitializeVideoLectureConfirmed);
    }
    
    function reinitializeVideoLectureConfirmed(param) {
        var lecture_id = param.data.lecture_id;
            $.ajax({
                url: admin_url+'coursebuilder/reinitialize_video_lecture',
                type: "POST",
                data:{ "is_ajax":true, 'lecture_id':lecture_id},
                success: function(response) {
                var data = $.parseJSON(response);
                    if(data['error'] == false)
                    {
                        $('#reinitialize_button').hide();
                        var messageObject = {
                        'body': data['message'],
                        'button_yes': 'OK',
                        };
                        callback_success_modal(messageObject);
                    }
                    else
                    {
                        var messageObject = {
                        'body': data['message'],
                        'button_yes': 'OK',
                        };
                        callback_danger_modal(messageObject);
                        return false;
                    }
            }
        });
    }