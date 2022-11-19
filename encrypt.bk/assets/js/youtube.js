var lecture_access_limit = 0;
$(document).ready(function(){
    lecture_access_limit = $("input[type='radio'][name='cl_limited']:checked").val();
    $("input[type='radio'][name='cl_limited']").click(function(){
        lecture_access_limit = $("input[type='radio'][name='cl_limited']:checked").val();
        if( lecture_access_limit == 0 )
        {
            $("#cl_limited_access").val('');
            $('#cl_limited_access_wrapper').hide();
            $("#cl_limited_access").removeAttr('required');
        }
        else
        {
            $("#cl_limited_access").attr('required','required');
            $('#cl_limited_access_wrapper').show();
        }
    });
});

function saveYoutubeLecture()
{
    var lecture_name                    = $('#lecture_name').val();
    var lecture_id                      = $('#lecture_id').val();
    var section_id                      = $('#section_id').val();
    var course_id                       = $('#course_id').val();
    var lecture_description             = $('#lecture_description').val();
    var youtube_url                     = $('#youtube_url').val();
    // var sent_mail_on_youtube_creation   = $('#cl_sent_mail_on_lecture_creation').prop('checked');
    //     sent_mail_on_youtube_creation   = (sent_mail_on_youtube_creation==true)?'1':'0';
    // lecture_access_limit = $("input[type='radio'][name='cl_limited']:checked").val();
    var lecture_access_limit='';  
    var errorCount                      = 0;
    var errorMessage                    = '';
    var lecture_image                   = $('#lecture_image_add').attr('image_name');
    //end

    //validation process
    if( lecture_name == '')
    {
        errorCount++;
        errorMessage += 'please enter Lecture title <br />';
    }
    if( cb_has_lecture_image == 1 && lecture_image == 'default-lecture.jpg' && (($('#lecture_logo_btn')[0].files[0] == undefined)) )
    {
        errorCount++;
        errorMessage += 'Please upload lecture image.<br>';
    }
    // if( lecture_access_limit > 0 )
    // {
    //     lecture_access_limit = $('#cl_limited_access').val();
    //     if( lecture_access_limit == '')
    //     {
    //         errorCount++;
    //         errorMessage += 'please enter the access count<br />';
    //     }
    // }


    if( youtube_url == "")
    {
        errorCount++;
        errorMessage += 'please enter url<br />';        
    }
    else
    {
        if( isValidURL(youtube_url) == false)
        {
            errorCount++;
            errorMessage += 'Invalid url<br />';   
                
        }
    }
    cleanPopUpMessage();
    if(errorCount > 0 )
    {
        
        $('#youtube_form').prepend(renderPopUpMessage('error', errorMessage));
        scrollToTopOfPage();
        return false;
    }else{
        var formData = new FormData();
        formData.append('lecture_image', $('#lecture_logo_btn')[0].files[0]);
        formData.append('is_ajax', true);
        formData.append('course_id', course_id);
        formData.append('cl_limited_access', lecture_access_limit);
        formData.append('youtube_url', youtube_url);
        formData.append('youtube_name', lecture_name);
        formData.append('youtube_description', lecture_description);
        formData.append('section_id', section_id);
        formData.append('lecture_id', lecture_id);
        formData.append('section_name', '');
        $.ajax({
            url: admin_url+'coursebuilder/save_youtube',
            type: "POST",
            data:formData,
            processData:false,
            contentType:false,
            cache:false,
            async:false,
            beforeSend: function() {
               $("#save_btn").text('SAVING...');
               $("#save_btn").attr('disabled','disabled');
            },
            success: function(response) {
                var data  = $.parseJSON(response);
                $("#save_btn").text('SAVE');
                $("#save_btn").removeAttr("disabled");
                if(data['error'] != false)
                {             
                    $('#youtube_form').prepend(renderPopUpMessage('error', data['message']));
                    scrollToTopOfPage();
                }else
                {
                    scrollToTopOfPage();
                    var messageObject = {
                        'body': data['message'],
                        'button_yes': 'OK',
                    };
                    callback_success_modal(messageObject,pageReload);
                    
                    $('#common_message_advanced').on('hidden.bs.modal', () =>  {
                        pageReload();
                    });
                    // $('#youtube_form').prepend(renderPopUpMessage('success', data['message']));
                     
                }
                
            }
        });
    }
    
    
}
function pageReload(){

    location.reload();
}
