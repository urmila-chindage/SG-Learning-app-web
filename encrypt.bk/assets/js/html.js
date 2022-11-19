function saveHtmlLecture()
{
    var lecture_id                      = $('#lecture_id').val();
    var course_id                       = $('#course_id').val();
    var lecture_name                    = $('#lecture_name').val();
    var lecture_description             = $('#lecture_description').val();
    var lecture_content                 = $('#cl_lecture_content').val();
    var sent_mail_on_lecture_creation   = $('#cl_sent_mail_on_lecture_creation').prop('checked');
        sent_mail_on_lecture_creation   = (sent_mail_on_lecture_creation==true)?'1':'0';
    var lecture_image                   = $('#lecture_image_add').attr('image_name');

    var errorCount                      = 0;
    var errorMessage                    = '';
    //end

    //validation process
    if( lecture_name == '')
    {
        errorCount++;
        errorMessage += 'please enter lecture name <br />';
    }
    if( cb_has_lecture_image == 1 && lecture_image == 'default-lecture.jpg' && (($('#lecture_logo_btn')[0].files[0] == undefined)) )
    {
        errorCount++;
        errorMessage += 'Please upload lecture image.<br>';
    }
    if( lecture_description == '')
    {
        errorCount++;
        errorMessage += 'please enter lecture description<br />';
    }
    
    if( lecture_content == '')
    {
        errorCount++;
        errorMessage += 'please enter lecture content<br />';
    }
    cleanPopUpMessage();
    if(errorCount > 0 )
    {
        $('#html_form').prepend(renderPopUpMessage('error', errorMessage));
        scrollToTopOfPage();
        return false;
    }
    /*
    if(lecture_content!="")
    {
        errorCount++;
        console.log(lecture_content);
        errorMessage += 'please select a valid image<br />';
    }*/
    var formData = new FormData();
    formData.append('lecture_image', $('#lecture_logo_btn')[0].files[0]);
    formData.append('is_ajax', true);
    formData.append('lecture_content', lecture_content);
    formData.append('lecture_id', lecture_id);
    formData.append('lecture_name', lecture_name);
    formData.append('sent_mail_on_lecture_creation', sent_mail_on_lecture_creation);
    formData.append('lecture_description', lecture_description);
    formData.append('course_id', course_id);
     
    $.ajax({
        url: admin_url+'coursebuilder/save_html_detail',
        type: "POST",
        data:formData,
        processData:false,
        contentType:false,
        cache:false,
        async:false,
        success: function(response) {
            var data  = $.parseJSON(response);
            if(data['error'] != "false")
            {                
                $('#html_form').prepend(renderPopUpMessage('error', data['message']));
                scrollToTopOfPage();
            }
            else
            {
                var messageObject = { 
                    'body': data['message'],
                    'button_yes':'OK', 
                    'button_no':'CANCEL'
                    };
                    callback_success_modal(messageObject);
            }
        }
    });
}

$("#cl_limited_access").keydown(function (e) {
    // Allow: backspace, delete, tab, escape, enter and .
    if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
         // Allow: Ctrl+A, Command+A
        (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) || 
         // Allow: home, end, left, right, down, up
        (e.keyCode >= 35 && e.keyCode <= 40)) {
             // let it happen, don't do anything
             return;
    }
    // Ensure that it is a number and stop the keypress
    if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
        e.preventDefault();
    }
});