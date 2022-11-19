var __lecture_access_limit = 0;
var __single_choice        = 1;
var __removed_options      = new Array();
var __uploaded_files       = '';
$(document).ready(function(){
    __lecture_access_limit = $("input[type='radio'][name='cl_limited']:checked").val();
    $("input[type='radio'][name='cl_limited']").click(function(){
        __lecture_access_limit = $("input[type='radio'][name='cl_limited']:checked").val();
        if( __lecture_access_limit == 0 )
        {
            $('#cl_limited_access_wrapper').hide();
        }
        else
        {
            $('#cl_limited_access_wrapper').show();
        }
    });

    $("#cl_limited_access,#assesment_duration").keydown(function (e) {
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

});

//Get category list for Auto suggest
var current = '';
var timeOut = '';
$(document).on('keyup', '#q_category', function(){
    clearTimeout(timeOut);
    timeOut = setTimeout(function(){ 
        var keyword = $('#q_category').val();
        var url 	= admin_url+'coursebuilder/get_question_category_list';
        var tagHTML	= '';
        current 	= this;
        if( keyword ){
            $.ajax({
                    url: url,
                    type: "POST",
                    data:{ 'q_category':keyword, 'is_ajax':true},
                    success: function (response){
                        var data    = $.parseJSON(response);
                        if( data['tags'].length > 0 ){
                            for( var i = 0; i < data['tags'].length; i++){
                                tagHTML += '<li id="'+data['tags'][i]['id']+'">'+data['tags'][i]['qc_category_name']+'</li>';
                            }
                        }
                        $("#listing_question_category").html(tagHTML).show();       
                    },
            });
        }
    }, 600);
});

var current_li = '';
$(document).on('click', '.auto-search-lister li', function(){
    $('#q_category').val($(this).text());
    $(this).parent().html('').hide();
});


function saveAssesmentLecture()
{
    var lecture_id                      = $('#lecture_id').val();
    var course_id                       = $('#course_id').val();
    var lecture_name                    = $('#lecture_name').val();
    var assessment_access               = $('#cl_limited_radio').val();
    var lecture_description             = $('#lecture_description').val();
    var lecture_instruction             = $('#lecture_instruction').val();
    var assesment_duration              = $('#assesment_duration').val();
    var pass_percentage                 = $('#pass_percentage').val();

    var show_categories   = $('#a_show_categories').prop('checked');
        show_categories   = (show_categories==true)?'1':'0';
    var sent_mail_on_lecture_creation   = $('#cl_sent_mail_on_lecture_creation').prop('checked');
        sent_mail_on_lecture_creation   = (sent_mail_on_lecture_creation==true)?'1':'0';
        __lecture_access_limit          = $("input[type='radio'][name='cl_limited']:checked").val();
        
    var errorCount                      = 0;
    var errorMessage                    = '';
    //end

    //validation process
    if( lecture_name == '')
    {
        errorCount++;
        errorMessage += 'Please enter assesment name <br />';
    }

    if( assesment_duration == ''|| assesment_duration<1 || assesment_duration%1 != 0)
    {
        errorCount++;
        errorMessage += 'Please enter a valid assesment duration <br />';
    }
    
    if( lecture_instruction == '')
    {
        errorCount++;
        errorMessage += 'Please enter lecture instructions<br />';
    }
    
    if( pass_percentage == '' || isNaN(pass_percentage))
    {
        errorCount++;
        errorMessage += 'Enter a valid percentage<br />';
    }
    else
    {
        if( pass_percentage < 25)
        {
            errorCount++;
            errorMessage += 'Pass password must be greater that 25<br />';
        }
    }
    
    if( __lecture_access_limit > 0 )
    {
        __lecture_access_limit = $('#cl_limited_access').val();
        if( __lecture_access_limit == ''||__lecture_access_limit<1 || __lecture_access_limit%1 != 0)
        {
            errorCount++;
            errorMessage += 'Please enter a valid access count<br />';
        }
    }
    cleanPopUpMessage();
    if(errorCount > 0 )
    {
        $('#assesment_form').prepend(renderPopUpMessage('error', errorMessage));
        scrollToTopOfPage();
        return false;
    }
    
    $.ajax({
        url: admin_url+'coursebuilder/save_assesment_detail',
        type: "POST",
        data:{ "is_ajax":true, "pass_percentage":pass_percentage, "lecture_id":lecture_id, "course_id":course_id, "lecture_name":lecture_name, 'cl_limited_access':__lecture_access_limit, 'lecture_name':lecture_name, 'assesment_duration':assesment_duration, 'sent_mail_on_lecture_creation':sent_mail_on_lecture_creation, 'lecture_description':lecture_description, 'lecture_instruction':lecture_instruction, 'show_categories':show_categories},
        success: function(response) {
            var data  = $.parseJSON(response);
            if(data['error'] != "false")
            {                
                $('#assesment_form').prepend(renderPopUpMessage('error', data['message']));
                scrollToTopOfPage();
            }
            else
            {
                $('#assesment_form').prepend(renderPopUpMessage('success', data['message']));
                scrollToTopOfPage();
                //location.reload();
            }
        }
    });
}
$(document).ready(function(e) {
    $('.redactor').redactor({
        minHeight: 250,
        maxHeight: 250,
        imageUpload: admin_url + 'configuration/redactore_image_upload',
        plugins: ['table', 'alignment'],
        callbacks: {
            imageUploadError: function(json, xhr) {
                var erorFileMsg = "This file type is not allowed. upload a valid image.";
                $('#assesment_form').prepend(renderPopUpMessage('error', erorFileMsg));
                scrollToTopOfPage();
                return false;
            }
        }
    });
});

$(document).on('click', '#add-more-option', function(){
    var total_options = $('#question_option_wrapper .option-element').length;
        total_options++;
    var question_type = $('#question_type').val(); 
    if( question_type == '' )
    {
        $('#question_form').prepend(renderPopUpMessage('error', 'choose question type'));
        scrollToTopOfPage();
        return false;
    }
    var optionHtml  = '';
        optionHtml += '<div id="option_wrapper_'+total_options+'" class="col-sm-12 option-element">';
        optionHtml += '    <span class="order-option">Option '+total_options+' :</span>';
        optionHtml += '    <div class="input-group">';
    var input_type = 'checkbox';
    var answer     = '['+total_options+']';                                     
        if( question_type == __single_choice )
        {
            input_type = 'radio';
            answer     = '';
        }
        optionHtml += '        <span class="input-group-addon option-type">';
        optionHtml += '            <input class="question-option-input-new" type="'+input_type+'" name="answer_new'+answer+'" value="'+total_options+'">';
        optionHtml += '        </span>';
         optionHtml += '       <textarea id="new_option_textarea_'+total_options+'" name="option_new['+total_options+']" class="form-control question-text-input" rows="4" ></textarea>';
        optionHtml += '        <span class="remove-cross remove-new-option"> X </span>';
        optionHtml += '    </div>';
        optionHtml += '</div>';
        $( optionHtml ).insertBefore( "#add_option_button" );
		$('#new_option_textarea_'+total_options).redactor({
            minHeight: 250,
            maxHeight: 250,
            imageUpload: admin_url+'configuration/redactore_image_upload',
       		plugins: ['table', 'alignment', 'source']
    	});
});

$(document).on('click', '.remove-existing-option', function(){
    var question_id = $(this).parents('.option-element').attr('id');
    __removed_options.push($('#'+question_id).attr('data-id'));
    $('#'+question_id).remove();
    
    $('span.order-option').text(function (i) {

        // returning the sum of i + 1 to compensate for
        // JavaScript's zero-indexing:
        return 'Option '+(i + 1)+':';
    });
});

$(document).on('click', '.remove-new-option', function(){
    $(this).parents('.option-element').remove();
    
    $('td.order').text(function (i) {
        // returning the sum of i + 1 to compensate for
        // JavaScript's zero-indexing:
        return i + 1;
    });
});

$(document).on('change', '#question_type', function(){
    var question_type = $('#question_type').val();
    $('.question-option-input, .question-option-input-new').prop('checked', false);
	$('.option-element, #add_option_button').show();
	switch (question_type)
	{
		case '1':
			$('.question-option-input').each(function(){
					$(this).attr('type', 'radio').attr('name', "answer".substr(0, 6));
			});
			$('.question-option-input-new').each(function(){
					$(this).attr('type', 'radio').attr('name', "answer_new".substr(0, 10));
			});
		break;
		case '2':
			$('.question-option-input').each(function(){
				$(this).attr('type', 'checkbox').attr('name', "answer["+$(this).val()+"]");        
			});
			$('.question-option-input-new').each(function(){
				$(this).attr('type', 'checkbox').attr('name', "answer_new["+$(this).val()+"]");        
			});
		break;
		case '3':
		$('.option-element, #add_option_button').hide();
		break;
	}
});

$(document).on('click', '.question-option-input, .question-option-input-new', function(){
    var question_type = $('#question_type').val();
    if( question_type == __single_choice )
    {
        $('.question-option-input, .question-option-input-new').prop('checked', false);
        $(this).prop('checked', true);
    }
});




function saveQuestion()
{
    var errCount      = 0;
    var errMessage    = '';
    var question      = $('#question').val();
    var question_type = $('#question_type').val();
    var total_options = $('#question_option_wrapper .option-element').length;
    if( total_options  < 2 && question_type != 3 )
    {
        errCount++;
        errMessage += 'Please add atleast two options<br />';
    }
	if( question_type != 3 )
	{
		$('.question-text-input').each(function(){
			if($(this).val()=='')
			{
				errMessage += 'Options cannot be empty<br />';   
				errCount++;
				return false;
			}
		});
	}
    var checked = 0;
    $('.question-option-input, .question-option-input-new').each(function(){
        if( $(this).prop('checked') == true )
        {
            checked++;
        }
    });
    
    if( question == '' )
    {
        errMessage += 'Question cannot be empty<br />';   
        errCount++;        
    }
    if( checked == 0 && question_type != 3 )
    {
        errMessage += 'Choose the answer<br />';   
        errCount++;        
    }
    cleanPopUpMessage();
    if( errCount > 0 )
    {
        $('#question_form').prepend(renderPopUpMessage('error', errMessage));
        scrollToTopOfPage();
        return false;        
    }
    
    //$('#removed_options').val(JSON.stringify(__removed_options));
    $('#question_form').submit();
}
$(document).on('change', '#upload_question', function(e){
    //console.log(e.currentTarget.files[0]);
    $('#percentage_bar').hide();
    var i                           = 0;
    __uploaded_files                = e.currentTarget.files[i];
    $('#upload_assessment_file').val(__uploaded_files['name']);
});

function uploadQuestion()
{
    if(__uploaded_files=='')
    {
        lauch_common_message('File Missing', 'Choose any file to upload');
        return false;
    }
    $('#percentage_bar').show();
    var uploadURL                   = admin_url+'coursebuilder/upload_question'
    var param                       = new Array;
        param["file"]               = __uploaded_files;
        param["lecture_id"]         = __lecture_id;
    uploadFiles(uploadURL, param, uploadQuestionCompleted);    
}

function uploadQuestionCompleted(response)
{
    __uploading_file = '';
    setTimeout(function(){window.location.reload();}, 500)
}

function deleteQuestion(question_id, assesment_id)
{
    $('#delete_message').hide();
    $('#delete_header_text').html(lang('are_you_sure_delete_this_question'));
    $('#deleteSection').modal('show');
    $('#delete_lecture_ok').unbind();
    $('#delete_lecture_ok').click({"question_id": question_id, "assesment_id": assesment_id}, deleteQuestionConfirmed);    
}

function deleteQuestionConfirmed(param)
{
    $.ajax({
        url: admin_url+'coursebuilder/delete_assesment_question',
        type: "POST",
        data:{ "is_ajax":true, "question_id":param.data.question_id, "assesment_id": param.data.assesment_id},
        success: function(response) {
            var data  = $.parseJSON(response);
            if(data['error'] != "false")
            {                
                $('#delete_header_text').html(data['message']);
                
            }
            else
            {
               $('#question_wrapper_'+param.data.question_id).remove();
               $('#deleteSection').modal('hide');
               var question = (data['question_count']>1)?' Questions':' Question';
               $('#questions_count_assessment').html(data['question_count']+question);
            }
        }
    });
}