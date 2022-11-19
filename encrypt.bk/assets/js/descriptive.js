var __uploaded_files = '';
function saveDescriptiveTest(){
	var lecture_id                      = $('#lecture_id').val();
	var course_id                       = $('#course_id').val();
	var test_title                      = $('#test_title').val();
    var test_description                = $('#test_description').val();
    var submission_date                 = $("#descriptive_submission_date").val();
    var descriptive_words_limit         = $("#descriptive_words_limit").val();
    var total_mark                      = $("#total_mark").val();
    var lecture_image                   = $('#lecture_image_add').attr('image_name');
    var today                           = new Date();
    today.setHours(0,0,0,0);
    var last_date                       = toDate(submission_date);
    last_date.setHours(0,0,0,0);
    var errorCount                      = 0;
    var errorMessage                    = '';
    
    var formData = new FormData();
    formData.append('lecture_image', $('#lecture_logo_btn')[0].files[0]);
    formData.append('is_ajax', true);
    formData.append('lecture_id', lecture_id);
    formData.append('course_id', course_id);
    formData.append('test_title', test_title);
    formData.append('test_description', test_description);
    formData.append('submission_date', submission_date);
    formData.append('descriptive_words_limit', descriptive_words_limit);
    formData.append('total_mark', total_mark);
    formData.append('uploaded_files', JSON.stringify(__uploadedFiles));
    if( test_title == '')
    {
        errorCount++;
        errorMessage += 'Please enter assignment title.<br>';
    }
    console.log($('#lecture_logo_btn')[0].files[0]);
    if( cb_has_lecture_image == 1 && lecture_image == 'default-lecture.jpg' && (($('#lecture_logo_btn')[0].files[0] == undefined)) )
    {
        errorCount++;
        errorMessage += 'Please upload lecture image.<br>';
    }

    if( test_description == '')
    {
        errorCount++;
        errorMessage += 'Please enter assignment description.<br>';
    }
    
    if( total_mark == ''){
        errorCount++;
        errorMessage += 'Please enter total mark. <br />';
    }

    if( submission_date == ''){
        errorCount++;
        errorMessage += 'Please enter the submission date. <br />';
    }

    if(last_date < today){
        errorCount++;
        errorMessage += 'Please enter the valid submission date. <br />';
    }

    if(errorCount > 0 )
    {
        $('#descriptive_wrapper_form').prepend(renderPopUpMessage('error', errorMessage));
        return false;
    }

    $.ajax({
        url: admin_url+'coursebuilder/save_descriptive_detail',
        type: "POST",
        data:formData,
        processData:false,
        contentType:false,
        cache:false,
        async:false,
        success: function(response) {
            $("#popUpMessage").remove();
            $("#header-title").html(test_title);
            var data  = $.parseJSON(response);
            if(data['error'] != false)
            {   
                 var messageObject = {
                'body': data['message'],
                'button_yes':'OK', 
                'button_no':'CANCEL'
                 };
            }
            else
            {
                var messageObject = {
                    'body': data['message'],
                    'button_yes':'OK', 
                    'button_no':'CANCEL'
                     };
                
            }
            callback_success_modal(messageObject);
        }
    });
}

function toDate(dateStr) {
    var parts = dateStr.split("-")
    return new Date(parts[2], parts[1] - 1, parts[0])
}

function isValidDate(s) {
    var bits = s.split('-');
    var d = new Date(bits[2] + '-' + bits[1] + '-' + bits[0]);
    return !!(d && (d.getMonth() + 1) == bits[1] && d.getDate() == Number(bits[0]));
  }
  
function isNumber(evt) {
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        return false;
    }
    return true;
}

// var descriptive_allowed = ['pdf'];
// $(document).on('change', '#upload_question', function(e){
//     $('#percentage_bar').hide();
//     var i                           = 0;
//     __uploaded_files                = e.currentTarget.files[i];
    
//     var fileObj                     = new processFileName(__uploaded_files['name']);
//         fileObj.uniqueFileName();        
//     if(inArray(fileObj.fileExtension(), descriptive_allowed) == false)
//     {
//         lauch_common_message('Invalid File', 'This type of file is not allowed.');    
//         return false;        
//     }
// });

// function uploadQuestion()
// {
//     //console.log('bbbbb');
//     if(__uploaded_files=='')
//     {
//         lauch_common_message('Choose file', 'No file chosen');
//         return false;
//     }
//     var fileObj                     = new processFileName(__uploaded_files['name']);
//         fileObj.uniqueFileName();        
//     if(inArray(fileObj.fileExtension(), descriptive_allowed) == false)
//     {
//         lauch_common_message('Invalid File', 'This type of file is not allowed.');    
//         return false;        
//     }
//     $('#uploaddescriptive .progress-custom').show();
//     var uploadURL                   = admin_url+'coursebuilder/descriptive_question_upload'
//     var param                       = new Array;
//         param["file"]               = __uploaded_files;
//         param["lecture_id"]         = $('#lecture_id').val();
//     uploadFiles(uploadURL, param, uploadQuestionCompleted);    
// }

// function uploadQuestionCompleted(response)
// {
//     __uploading_file = '';
//     $("#uploaddescriptive").modal('hide');
//     setTimeout(function(){window.location.reload();}, 100)
// }

// $(function(){
//     $("#last_date").datepicker({
//         language: 'en',
//         dateFormat: "yyyy-mm-dd",
//         minDate: new Date() // Now can select only dates, which goes after today
//     });
// })