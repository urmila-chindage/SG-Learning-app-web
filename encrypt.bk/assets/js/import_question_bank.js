var __lecture_access_limit = 0;
var __single_choice        = 1;
var __removed_options      = new Array();
var __uploaded_files       = '';
var __upload_progress      = false;
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
});

$("#generate_questions_keyword").keyup(function(event) {
    if (event.keyCode === 13) {
        __offset = 1;
         getQuestions();
    }
});

$(document).on('click', '#basic-addon2', function(){
    __offset = 1;
    getQuestions();
});

//Get category list for Auto suggest
var current = '';
var timeOut = '';
$(document).on('keyup', '#q_category', function(){
    clearTimeout(timeOut);
    timeOut = setTimeout(function(){ 
        var keyword = $('#q_category').val();
        var url 	= admin_url+'generate_test/get_question_category_list';
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
        optionHtml += '    <span class="order-option">Option '+toAlpha(total_options+1)+' :</span>';
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
        if(__redactorEnabled == true)
        {
            $('#new_option_textarea_'+total_options).redactor({
                minHeight: 100,
                maxHeight: 100,
                imageUpload: admin_url+'configuration/redactore_image_upload',
                    plugins: ['table', 'alignment', 'source']
            });
        }
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
    
    $('span.order-option').text(function (i) {

        // returning the sum of i + 1 to compensate for
        // JavaScript's zero-indexing:
        return 'Option '+(i + 1)+':';
    });
});

$(document).on('change', '#question_type', function(){
    var question_type = $('#question_type').val();
        $('.question-option-input, .question-option-input-new').prop('checked', false);
	$('.option-element, #add_option_button').show();
        $('#text_editor_button_wrapper').hide();
	switch (question_type)
	{
		case '1':
			$('.question-option-input').each(function(){
					$(this).attr('type', 'radio').attr('name', "answer".substr(0, 6));
			});
			$('.question-option-input-new').each(function(){
					$(this).attr('type', 'radio').attr('name', "answer_new".substr(0, 10));
			});
                        $('#text_editor_button_wrapper').show();
		break;
		case '2':
			$('.question-option-input').each(function(){
				$(this).attr('type', 'checkbox').attr('name', "answer["+$(this).val()+"]");        
			});
			$('.question-option-input-new').each(function(){
				$(this).attr('type', 'checkbox').attr('name', "answer_new["+$(this).val()+"]");        
			});
                        $('#text_editor_button_wrapper').show();
		break;
		case '3':
		$('.option-element, #add_option_button').hide();
		break;
        case '4':
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
    var question_topic = $('#q_category').val();
    var total_options = $('#question_option_wrapper .option-element').length;
    if( total_options  < 2 && question_type != 3 && question_type != 4)
    {
        errCount++;
        errMessage += 'Please add atleast two options<br />';
    }
    if(question_topic == ''){
        errCount++;
        errMessage += 'Please add a topic<br />';
    }
	if( question_type != 3 && question_type != 4 )
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
    if( checked == 0 && question_type != 3 && question_type != 4 )
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
    __uploaded_files['extension']   = __uploaded_files['name'].split('.').pop();
    $('#upload_file_name').val(__uploaded_files['name']);
    console.log(__uploaded_files);
});

$(document).on("click", '#generate_upload_question', function(){
   $("#upload_question").val('');
   __uploaded_files = '';
});

var __filter_dropdown = '';
var __category_id     = '';
var __subject_id      = '';
var __topic_id        = '';

var __offset        = 2;
var __requestInProgress = false;
function getQuestions()
{
    if(__requestInProgress == true)
    {
        return false;
    }
    $('#load_more_question').html('Loading Question...<ripples></ripples>');
    __requestInProgress = true;
    var keyword  = $('#generate_questions_keyword').val();
    $.ajax({
        url: admin_url+'generate_test/generate_questions_json',
        type: "POST",
        data:{"is_ajax":true, "filter":__filter_dropdown, "type":__filter_type, 'offset':__offset, "category_id":__category_id, "subject_id":__subject_id, "topic_id":__topic_id, "keyword":keyword},
        success: function(response) {
            var data = $.parseJSON(response);
            var remainingQuestion = 0;
            $("#selectall").prop('checked',false);
            $('#load_more_question').hide();
            if(data['questions'].length > 0){
                $('#selectall').attr('checked', false);
                 __offset++;
                if(__offset == 2)
                {
                    remainingQuestion = (data['total_questions'] - data['questions'].length);
                    var totalQuestionsHtml = data['questions'].length+' / '+data['total_questions']+' '+((data['total_questions'] == 1)?"Question":"Questions");
                    scrollToTopOfPage();
                    $('.question-count').html(totalQuestionsHtml);
                    $('#generate_test_wrapper').html(renderQuestionsHtml(response));
                }
                else
                {
                    remainingQuestion = (data['total_questions'] - (((__offset-2)*data['limit'])+data['questions'].length));
                    var totalQuestionsHtml = (((__offset-2)*data['limit'])+data['questions'].length)+' / '+data['total_questions']+' Questions';
                    $('.question-count').html(totalQuestionsHtml);
                    $('#generate_test_wrapper').append(renderQuestionsHtml(response));                     
                }
            }else{
                $('.question-count').html("No Questions");
                $('#generate_test_wrapper').html(renderPopUpMessage('error', 'No Questions found.'));
            }
            if(data['show_load_button'] == true)
            {
                $('#load_more_question').show();
            }
            remainingQuestion = (remainingQuestion>0)?'('+remainingQuestion+')':'';
            $('#load_more_question').html('Load More Question '+remainingQuestion+'<ripples></ripples>');
            __requestInProgress = false;
        }
    });
}

function renderQuestionsHtml(response)
{
    var data        = $.parseJSON(response);
    var questionsHtml  = '';
    var j = '';
    var activeLanguage = data['active_web_language'];    
    if(data['questions'].length > 0 )
    {
        for (var i=0; i<data['questions'].length; i++)
        {
            j = i + 1;
            var question = data['questions'][i]['q_question'];
                question = $.parseJSON(question);
                question = question[activeLanguage];
            var question_html_stripped = question.replace(/(<([^>]+)>)/ig,"");
            var question_short = (question_html_stripped.length > 100)?question_html_stripped.substr(0,97)+'...':question_html_stripped;
            var q_type = {1:"Single Choice", 2:"Multiple Choice", 3:"Subjective", 4:"Fill in the blanks"};
            
            questionsHtml += '<div class="default-view-txt m0 test-folder" style="float: none; padding: 7px;" id="question_wrapper_'+data['questions'][i]['id']+'">';
            questionsHtml += '<input type="checkbox" '+((typeof __deleteQuestionIds[data['questions'][i]['id']] != 'undefined')?'checked="checked"':'')+' class="import-questions" value="'+data['questions'][i]['id']+'">';
            //questionsHtml += '  <span class="question-sl-no">'+((data['limit']*(__offset-2))+j)+' .'+'<b>#'+data['questions'][i]['id']+'</b></span>';
            questionsHtml += '  <span class="question-sl-no"><b>#'+data['questions'][i]['id']+'</b></span>';
            questionsHtml += '  <span class="question-text" onclick="previewQuestion(\''+data['questions'][i]['id']+'\')">';
            questionsHtml += '  '+question_short+'';
            questionsHtml += '  </span>';
            questionsHtml += '  <span class="question-type">'+q_type[data['questions'][i]['q_type']]+'</span>';
           
            questionsHtml += '</div>';
        }
    }
    return questionsHtml;
}

function filter_category(category_id)
{
    $('#filter_dropdown_text_subjects_ul').html('<li style="visibility:hidden;"><a href="javascript:void(0)" id="dropdown_subject_list_0" ></a></li>');
    $('#filter_dropdown_text_topics_ul').html('<li style="visibility:hidden;"><a href="javascript:void(0)" id="dropdown_topic_list_0" ></a></li>');
    $('#filter_dropdown_text_subjects').html('All Subjects <span class="caret"></span>');
    $('#filter_dropdown_text_topics').html('All Topics <span class="caret"></span>');

    $.ajax({
            url: admin_url+'generate_test/get_category_subjects',
            type: "POST",
            data:{"is_ajax":true, "category_id":category_id},
            success: function(response) {
                var data = $.parseJSON(response);
                var subjectsHtml = '';
                if(data['subjects'].length > 0 )
                {
                    for (var i=0; i<data['subjects'].length; i++)
                    {
                        if(data['subjects'][i]['qs_subject_name'] != '')
                        {
                            subjectsHtml += '<li><a href="javascript:void(0)" id="dropdown_list_subject_'+data['subjects'][i]['id']+'" onclick="filter_subjects(\''+data['subjects'][i]['id']+'\')">'+data['subjects'][i]['qs_subject_name']+'</a></li>';                        
                        }
                    }
                }
                else
                {
                    subjectsHtml = '<li style="visibility:hidden;"><a href="javascript:void(0)" id="dropdown_subject_list_0" ></a></li>';
                }
                $('#filter_dropdown_text_subjects_ul').html(subjectsHtml);
            }
    });
    __category_id   = category_id;
    __subject_id    = '';
    __topic_id      = '';
    $('#filter_dropdown_text_category').html($('#dropdown_list_'+category_id).text()+'<span class="caret"></span>');
    __offset = 1;
    getQuestions();
}

function filter_subjects(subject_id)
{
    $('#filter_dropdown_text_topics_ul').html('<li style="visibility:hidden;"><a href="javascript:void(0)" id="dropdown_topic_list_0" ></a></li>');
    $('#filter_dropdown_text_topics').html('All Topics <span class="caret"></span>');
    $.ajax({
            url: admin_url+'generate_test/get_category_topics',
            type: "POST",
            data:{"is_ajax":true, "subject_id":subject_id, "category_id":__category_id},
            success: function(response) {
                var data = $.parseJSON(response);
                var topicsHtml = '';
                if(data['topics'].length > 0 )
                {
                    for (var i=0; i<data['topics'].length; i++)
                    {
                        if(data['topics'][i]['qt_topic_name'] != '')
                        {
                            topicsHtml += '<li><a href="javascript:void(0)" id="dropdown_list_topic_'+data['topics'][i]['id']+'" onclick="filter_topics(\''+data['topics'][i]['id']+'\')">'+data['topics'][i]['qt_topic_name']+'</a></li>';                        
                        }
                    }
                }
                else
                {
                    topicsHtml = '<li style="visibility:hidden;"><a href="javascript:void(0)" id="dropdown_topic_list_0" ></a></li>';
                }
                $('#filter_dropdown_text_topics_ul').html(topicsHtml);
            }
    });
    __subject_id    = subject_id;
    __topic_id      = '';
    $('#filter_dropdown_text_subjects').html($('#dropdown_list_subject_'+subject_id).text()+'<span class="caret"></span>');
    __offset = 1;
    getQuestions();
}

function filter_topics(topic_id)
{
   __topic_id        = topic_id;
   $('#filter_dropdown_text_topics').html($('#dropdown_list_topic_'+topic_id).text()+'<span class="caret"></span>');
   __offset = 1;
   getQuestions();
}

function filter_generate_test_by(filter)
{
   __filter_dropdown        = filter;
   $('#filter_dropdown_text_difficulty').html($('#filer_dropdown_list_'+filter).text()+'<span class="caret"></span>');
   __offset = 1;
   getQuestions();
}

var __filter_type = '';
function filter_generate_test_by_type(filter)
{
   __filter_type        = filter;
   $('#filter_dropdown_text_type').html($('#filer_dropdown_list_type_'+filter).text()+'<span class="caret"></span>');
   __offset = 1;
   getQuestions();
}


function uploadQuestion()
{
    $('#popUpMessage').remove();
    var category_id = $("#q_parent_cat").val();
    if(__uploaded_files=='')
    {
        lauch_generate_message('File Missing', 'Choose any file to upload');
        return false;
    }
    
    var filename = __uploaded_files['name'];
    var valid_extensions = /(\.docx|\.doc|\.xls)$/i;   

    if(valid_extensions.test(filename))
    {
        $('#percentage_bar').show();
        var uploadURL               = admin_url+'generate_test/upload_question'
        var param                   = new Array;
        param["file"]               = __uploaded_files;
        param["category_id"]        = category_id;
        param['processing']         = 'question_upload_processing';
        if(__upload_progress == false){
            __upload_progress   = true;
            uploadFiles(uploadURL, param, uploadQuestionCompleted);
        }else{
            lauch_generate_message('Wait', 'Please wait until upload completes');     
        }
    }
    else
    {
       lauch_generate_message('Invalid File', 'Choose proper file to upload');
       return false;
    }
    
}

function lauch_generate_message(header, message)
    {
        $('#generate_message_header').html(header);
        $('#generate_message_content').html(message);
        $('#common_message_button').addClass('btn-red').removeClass('btn-green');
        $('#common_message_generate').modal('show');
    }

function uploadQuestionCompleted(response)
{
    var data = $.parseJSON(response);
    __uploaded_files = '';
    __upload_progress   = false;
    if(data['success'] == true)
    {
        $("#addquestion .modal-body").prepend(renderPopUpMessage('success', 'Review the uploaded questions'));
        //setTimeout(function(){window.location.reload();}, 500)
        var reviewHtml        = '';
        var question          = JSON.parse(response);
        reviewHtml += renderPreviewQuestions(question['question']);
        $('#question_preview_count').html(Object.keys(question['question']).length);
        $('#review-question').html(reviewHtml);
        $('#report-card').modal('show');
    }
    else
    {
        $('#percentage_bar').hide();
        $('#question_upload_processing_wrapper').html('Uploading...<b class="percentage-text" id="question_upload_processing">0%</b>');
        $("#addquestion .modal-body").prepend(renderPopUpMessage('error', 'Error occured in upload question. Fix the following issue and re-upload the tempalate again. <br />'+data['message']));        
        scrollToTopOfPage();
    }
}

function deleteQuestion(question_id)
{
    $('#delete_message').hide();
    $('#delete_header_text').html(lang('are_you_sure_delete_this_question'));
    $('#deleteSection').modal('show');
    $('#delete_generate_ok').unbind();
    $('#delete_generate_ok').click({"question_id": question_id}, deleteQuestionConfirmed);    
}

function deleteQuestionConfirmed(param)
{
    $.ajax({
        url: admin_url+'generate_test/delete_question',
        type: "POST",
        data:{ "is_ajax":true, "question_id":param.data.question_id},
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
            }
        }
    });
}

function toAlpha(number)
{
    var alphabet = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];
    //number--;
    var count = alphabet.length;
    if(number <= count)
        return alphabet[number-1];
    var modulo = 0, alpha = '';
    while(number > 0){
        modulo     = (number - 1) % count;
        alpha      = alphabet[modulo]+alpha;
        number     = Math.floor(((number - modulo) / count));
    }
    return alpha;
}



function renderQtype(type){
    var rreturn = '';
    type = parseInt(type);
    switch(type){
        case 1:
            rreturn = 'Single Choice';
        break;
        case 2:
            rreturn = 'Multiple Choice';
        break
        case 3:
            rreturn = 'Subjective Type';
        break;
        case 4:
            rreturn = 'Fill in the blanks';
        break;
    }

    return rreturn;
}


function deleteQuestionBulk()
{
    if(Object.keys(__deleteQuestionIds).length  > 0 )
    {
        $('#deleteSection').modal('show');
        $('#delete_header_text').html('Confirm delete Questions');
        $('#delete_generate_ok').unbind();
        $('#delete_generate_ok').click({}, deleteQuestionBulkConfirmed);  
    }
}



var __deletingQuestionInProgress = false;
function deleteQuestionBulkConfirmed()
{
    if(__deletingQuestionInProgress == true)
    {
        return false;
    }
    __deletingQuestionInProgress = true;
    $.ajax({
        url: admin_url+'generate_test/delete_question_bulk',
        type: "POST",
        data:{"is_ajax":true, "question_ids":JSON.stringify(__deleteQuestionIds)},
        success: function(response) {
            var data = $.parseJSON(response);
            $('#delete_generate_ok').unbind();
            if(data['error'] == false)
            {
                location.href = admin_url+'generate_test';
            }
            else
            {
                lauch_common_message('Something went Wrong' , 'Please try to delete question again!!');
            }
            __deletingQuestionInProgress = false;
        }
    });
}