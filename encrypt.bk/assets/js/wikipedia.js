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

function saveWikipediaLecture()
{
    var lecture_id                      = $('#lecture_id').val();
    var lecture_name                    = $('#lecture_name').val();
    var lecture_description             = $('#lecture_description').val();
    var sent_mail_on_lecture_creation   = $('#cl_sent_mail_on_lecture_creation').prop('checked');
        sent_mail_on_lecture_creation   = (sent_mail_on_lecture_creation==true)?'1':'0';
        lecture_access_limit            = $("input[type='radio'][name='cl_limited']:checked").val();
        
    var errorCount                      = 0;
    var errorMessage                    = '';
    //end

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
    
    if( lecture_access_limit > 0 )
    {
        lecture_access_limit = $('#cl_limited_access').val();
        if( lecture_access_limit == '')
        {
            errorCount++;
            errorMessage += 'please enter the access count<br />';
        }
    }
    cleanPopUpMessage();
    if(errorCount > 0 )
    {
        $('#wikipedia_form').prepend(renderPopUpMessage('error', errorMessage));
        scrollToTopOfPage();
        return false;
    }
    
    $.ajax({
        url: admin_url+'coursebuilder/save_wikipedia',
        type: "POST",
        data:{ "is_ajax":true, "lecture_id":lecture_id, 'cl_limited_access':lecture_access_limit, 'lecture_name':lecture_name, 'sent_mail_on_lecture_creation':sent_mail_on_lecture_creation, 'lecture_description':lecture_description},
        success: function(response) {
            var data  = $.parseJSON(response);
            if(data['error'] != "false")
            {                
                $('#wikipedia_form').prepend(renderPopUpMessage('error', data['message']));
                scrollToTopOfPage();
            }
        }
    });
}