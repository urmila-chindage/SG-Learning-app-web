var __lecture_access_limit = 0;
var __single_choice        = 1;
var __removed_options      = new Array();
var __uploaded_files       = '';
var __upload_progress      = false;
var __uploaded_questions   = '';

var __filter_dropdown = 'all';
var __filter_type     = 'all';
var __category_id     = 'all';
var __subject_id      = '';
var __topic_id        = '';
var __offset          = 2;
$(document).ready(function(){

    var filter      = getQueryStringValue('filter');
    var keyword     = getQueryStringValue('keyword');
    var ques_type   = getQueryStringValue('type');
    var ques_cat    = getQueryStringValue('category');
    var ques_sub    = getQueryStringValue('subject');
    var ques_top    = getQueryStringValue('topic');
    if (filter != '') {
        __filter_dropdown = filter;
        $('#filter_dropdown_text_difficulty').html($('#filer_dropdown_list_' + filter).text() + '<span class="caret"></span>');
    }
    if (keyword != '') {
        keyword = keyword.split('-').join(' ');
        $('#generate_questions_keyword').val(keyword);
    }
    if (ques_type != '') {
        __filter_type = ques_type;
        $('#filter_dropdown_text_type').html($('#filer_dropdown_list_type_' + ques_type).text() + '<span class="caret"></span>');
    }
    if (ques_cat != '') {
        __category_id = ques_cat;
        $('#filter_dropdown_text_category').html($('#dropdown_list_' + ques_cat).text() + '<span class="caret"></span>');
    }
    if (ques_sub != '') {
        __subject_id = (ques_sub !='all')? ques_sub : 0 ;
        $('#filter_dropdown_text_subjects').html($('#dropdown_subject_list_' + __subject_id).text() + '<span class="caret"></span>');
    }

    if (ques_top != '') {
        __topic_id = (ques_top !='all')? ques_top : 0 ;
        $('#filter_dropdown_text_topics').html($('#dropdown_topic_list_' + __topic_id).text() + '<span class="caret"></span>');
    }

   // getQuestions();
    if($('#check_redactor').prop("checked") == true){
        __redactorEnabled == true;
    } 
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
function getQueryStringValue(key) {
    return decodeURIComponent(window.location.search.replace(new RegExp("^(?:.*[&\\?]" + encodeURIComponent(key).replace(/[\.\+\*]/g, "\\$&") + "(?:\\=([^&]*))?)?.*$", "i"), "$1"));
}
$("#generate_questions_keyword").keyup(function(event) {
    if (event.keyCode === 13) {
        __offset = 1;
         getQuestions();
    }
});

$(document).on('click', '#searchclear', function(){
    __offset = 1;
    getQuestions();
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
        if($('#check_redactor').prop("checked") == true){
            __redactorEnabled = true;
        }
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
    console.log(e.currentTarget.files[0]);
    $('#percentage_bar').hide();
    $("#upload_question").html('UPLOAD');
    var i                           = 0;
    __uploaded_files                = e.currentTarget.files[i];
    __uploaded_files['extension']   = __uploaded_files['name'].split('.').pop();
    $('#upload_file_name').val(__uploaded_files['name']);
    console.log(__uploaded_files);
});

$(document).on("click", '#generate_upload_question', function(){
    cleanPopUpMessage(); 
   $("#q_parent_cat").val('0');
   $("#upload_question").val('');
   $("#upload_file_name").val('');
   __uploaded_files = '';
});




var __requestInProgress = false;
function getQuestions()
{
    if(__subject_id ==0){
        __subject_id ='all';
    }
    if(__topic_id ==0){
        __topic_id ='all';
    }
    if(__requestInProgress == true)
    {
        return false;
    }
    

    $('#load_more_question').html('Loading Question...<ripples></ripples>');
    __requestInProgress = true;
    var keyword           = $('#generate_questions_keyword').val();
    if (history.pushState) {
        var link = window.location.protocol + "//" + window.location.host + window.location.pathname;
        if (__filter_dropdown != '' || __filter_type != '' || __category_id != '' || __subject_id != '' || __topic_id != '' || keyword != '') {
            link += '?';
        }
        if (__filter_dropdown != '') {
            link += '&filter=' + __filter_dropdown;
        }
        if (keyword != '') {
            var uSearch = keyword.split(' ').join('-');
            link += '&keyword=' + uSearch;
        }
        if (__filter_type != '') {
            link += '&type=' + __filter_type;
        }
        if (__category_id != '') {
            link += '&category=' + __category_id;
        }
        if (__subject_id != '') {
            link += '&subject=' + __subject_id;
        }
        if (__topic_id != '') {
            link += '&topic=' + __topic_id;
        }
        __offset = (typeof __offset == 'undefined') ? 1 : __offset;
        link += '&offset=' + __offset;
        window.history.pushState({
            path: link
        }, '', link);
    }
    $.ajax({
        url: admin_url+'generate_test/generate_questions_json',
        type: "POST",
        data:{"is_ajax":true, "filter":__filter_dropdown, "type":__filter_type, 'offset':__offset, "category_id":__category_id, "subject_id":__subject_id, "topic_id":__topic_id, "keyword":keyword},
        success: function(response) {
            var data = $.parseJSON(response);
            var remainingQuestion = 0;
            $('#load_more_question').hide();
            $("#selectall").prop('checked', false); 
            if(data['questions'].length > 0){
                
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
            } else {
                $('#load_more_question').hide();
            }
            var remainingQuestions = (remainingQuestion>0)?'('+remainingQuestion+')':'';
            $('#load_more_question').html('Load More Question '+remainingQuestions+'<ripples></ripples>');
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
            var question_short = (question_html_stripped.length > 80)?question_html_stripped.substr(0,77)+'...':question_html_stripped;
            var q_type = {1:"Single Choice", 2:"Multiple Choice", 3:"Subjective", 4:"Fill in the blanks"};
            
            questionsHtml += '<div class="default-view-txt m0 test-folder" style="float: none; padding: 7px;" id="question_wrapper_'+data['questions'][i]['id']+'">';
            if(__delete_permission == 1){
            questionsHtml += '<input type="checkbox" '+((typeof __deleteQuestionIds[data['questions'][i]['id']] != 'undefined')?'checked="checked"':'')+' class="import-questions" value="'+data['questions'][i]['id']+'">';
            }
            console.log(data['questions'][i]['q_pending_status']);
            if( data['questions'][i]['q_pending_status'] == '2' ) 
            {
                var question_pending_status_color = "style='background-color:green'";
            }
            else if( data['questions'][i]['q_pending_status'] == '1' )
            {
                var question_pending_status_color = "style='background-color:#4b95db'";
            }
            else{
                var question_pending_status_color = "style='background-color:red'";
            }
            questionsHtml += ' <span '+ question_pending_status_color +'class="dot"></span>';
           

            //questionsHtml += '  <span class="question-sl-no">'+((data['limit']*(__offset-2))+j)+' .'+'<b>#'+data['questions'][i]['id']+'</b></span>';
            questionsHtml += '  <span class="question-sl-no"><b>#'+data['questions'][i]['q_code']+'</b></span>';
            questionsHtml += '  <span class="question-text"><a href="#" onclick="previewQuestion(\''+data['questions'][i]['id']+'\')">';
            questionsHtml += '  '+question_short+'';
            questionsHtml += '  </a></span>';
            questionsHtml += '  <span class="question-type">'+q_type[data['questions'][i]['q_type']]+'</span>';
            if(__delete_permission == 1){
                questionsHtml += '  <a href="javascript:void(0)" title="'+lang('delete')+'" onclick="deleteQuestion('+data['questions'][i]['id']+')" data-toggle="tooltip" class="test-folder-delte" data-placement="top" data-original-title="'+lang('delete')+'">';
                questionsHtml += '      <i class="icon icon-trash-empty"></i>';
                questionsHtml += '  </a>';
            }
            if(__edit_permission == 1){
                questionsHtml += '  <a href="'+webConfigs('admin_url')+'generate_test/question/'+data['questions'][i]['id']+'" title="'+lang('edit')+'" class="test-folder-delte" data-toggle="tooltip" class="test-folder-delte" data-placement="top" data-original-title="'+lang('edit')+'">';
                questionsHtml += '      <i class="icon icon-pencil"></i>';
                questionsHtml += '  </a>'; 
            }
            
            questionsHtml += '</div>';
        }
    }
    return questionsHtml;
}

function filter_category(category_id)
{

    $('#filter_dropdown_text_subjects_ul').html('<li style="visibility:hidden;"><a href="javascript:void(0)" id="dropdown_subject_list_0" ></a></li>');
    $('#filter_dropdown_text_topics_ul').html('<li><a href="javascript:void(0)"id="dropdown_topic_list_0" onclick="filter_topics(0)">All Topics</a></li>');
    $('#filter_dropdown_text_subjects').html('All Subjects <span class="caret"></span>');
    $('#filter_dropdown_text_topics').html('All Topics <span class="caret"></span>');

    $.ajax({
            url: admin_url+'generate_test/get_category_subjects',
            type: "POST",
            data:{"is_ajax":true, "category_id":category_id},
            success: function(response) {
                var data = $.parseJSON(response);
                var subjectsHtml = '<li><a href="javascript:void(0)" id="dropdown_subject_list_0" onclick="filter_subjects(0)">All Subjects</a></li>';
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
                    subjectsHtml = '<li><a href="javascript:void(0)" id="dropdown_subject_list_0" onclick="filter_subjects(0)">All Subjects</a></li>';
                }
                $('#filter_dropdown_text_subjects_ul').html(subjectsHtml);
            }
    });
    __category_id   = category_id;
    __subject_id    = 'all';
    __topic_id      = 'all';
    $('#filter_dropdown_text_category').html($('#dropdown_list_'+category_id).text()+'<span class="caret"></span>');
    __offset = 1;
    getQuestions();
}

function filter_subjects(subject_id)
{
    if(subject_id==0){
        subject_id = 'all';
    }
    //$('#filter_dropdown_text_topics_ul').html('<li style="visibility:hidden;"><a href="javascript:void(0)" id="dropdown_topic_list_0" ></a></li>');
    //$('#filter_dropdown_text_topics').html('All Topics <span class="caret"></span>');
    $.ajax({
            url: admin_url+'generate_test/get_category_topics',
            type: "POST",
            data:{"is_ajax":true, "subject_id":subject_id, "category_id":__category_id},
            success: function(response) {
                var data = $.parseJSON(response);
                var topicsHtml = '<li><a href="javascript:void(0)" id="dropdown_topic_list_0" onclick="filter_topics(0)">All Topics</a></li>';
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
                    topicsHtml = '<li><a href="javascript:void(0)" id="dropdown_topic_list_0" onclick="filter_topics(0)">All Topics</a></li>';
                }
                $('#filter_dropdown_text_topics_ul').html(topicsHtml);
            }
    });
    __subject_id    = subject_id;
    __topic_id      = 'all';
    if(__subject_id=='all'){
        $('#filter_dropdown_text_subjects').html('All Subject<span class="caret"></span>');
    } else {
        $('#filter_dropdown_text_subjects').html($('#dropdown_list_subject_'+subject_id).text()+'<span class="caret"></span>');
    }
    __offset = 1;
    getQuestions();
}

function filter_topics(topic_id)
{
   if(topic_id==0){
    topic_id = 'all';  
   }
   __topic_id        = topic_id;
   if(topic_id=='all'){
   $('#filter_dropdown_text_topics').html('All Topics<span class="caret"></span>');
   } else {
    $('#filter_dropdown_text_topics').html($('#dropdown_list_topic_'+topic_id).text()+'<span class="caret"></span>');   
   }
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
    if(category_id==0)
    {
        var messageObject = {
            'body':'Category Cannot be empty!',
            'button_yes':'OK'
        };
        callback_warning_modal(messageObject);
        return false;
    }
    if(__uploaded_files=='')
    {
        //lauch_generate_message('File Missing', 'Choose any file to upload');
        var messageObject = {
            'body':'File Missing!</br>Choose any file to upload',
            'button_yes':'OK'
        };
        callback_warning_modal(messageObject);
        return false;
    }
    
    var filename = __uploaded_files['name'];
    //var valid_extensions = /(\.xls)$/i;  
    var valid_extensions = /(\.xls|\.docx|\.doc)$/i;   

    if(valid_extensions.test(filename))
    {
        $('#percentage_bar').show();
        //$("#save_upload_question").html('Uploading<img src="'+asset_url+'images/loader.svg" width="25">');
        var uploadURL               = admin_url+'generate_test/upload_question';
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
        if(typeof data['html_file'] != 'undefined') {
            $('#percentage_bar span').html('Parsing the template...');

            var xhr= new XMLHttpRequest();
            xhr.open('GET', data['html_file'], true);
            xhr.onreadystatechange= function() {
                if (this.readyState!==4) return;
                if (this.status!==200) return; // or whatever error handling you want
                document.getElementById('question_template_preview').innerHTML= this.responseText;
                var dom = new DocumentReader('#question_template_preview > table', data['uploaded_object']);
                var category_id = $('#q_parent_cat').val();
                var documentParsed = dom.parseDocument();
                if(documentParsed['error'] == false) {
                    $.ajax({
                            url: admin_url+"generate_test/load_parsed_document",
                            type: "POST",
                            data:{ 'category_id':category_id, 'upload_data':data['uploaded_object'], 'doc_details':dom.getDocumentProperties(), 'doc_objects':JSON.stringify(documentParsed['question'])},                            
                            success: function (response){
                                var data = $.parseJSON(response);
                                if(data['success'] == true) {
                                    $("#addquestion .modal-body").prepend(renderPopUpMessage('success', 'Review the uploaded questions'));
                                    window.location = admin_url+'generate_test/upload_question_preview';    
                                } else {
                                    $("#upload_question").val('');
                                    $("#upload_file_name").val('');
                                    __uploaded_files = '';
                                    $('#percentage_bar').hide();
                                    $("#save_upload_question").html('UPLOAD');
                                    $('#question_upload_processing_wrapper').html('Uploading...<b class="percentage-text" id="question_upload_processing">0%</b>');
                                    $("#addquestion .modal-body").prepend(renderPopUpMessage('error', '<div>Error occured in upload question. Fix the following issue and re-upload the template again. <br />'+data['message']+'</div>'));        
                                    scrollToTopOfPage();                        
                                }
                            },
                    });
                } else {
                    $("#upload_question").val('');
                    $("#upload_file_name").val('');
                    __uploaded_files = '';
                    $('#percentage_bar').hide();
                    $("#save_upload_question").html('UPLOAD');
                    $('#question_upload_processing_wrapper').html('Uploading...<b class="percentage-text" id="question_upload_processing">0%</b>');
                    $("#addquestion .modal-body").prepend(renderPopUpMessage('error', '<div>Error occured in upload question. Fix the following issue and re-upload the template again. <br />'+documentParsed['message']+'</div>'));        
                    scrollToTopOfPage();                        
                }
    
            };
            xhr.send();


            // $.ajax({
            //     url: admin_url+'generate_test/delete_question',
            //     type: "POST",
            //     data:{ "is_ajax":true, "question_id":param.data.question_id},
            //     success: function(response) {
            //         var data  = $.parseJSON(response);
            //         if(data['error'] != "false")
            //         {
                        
            //         }                    
            //     }
            // });


        } else {
            $("#addquestion .modal-body").prepend(renderPopUpMessage('success', 'Review the uploaded questions'));
            window.location = admin_url+'generate_test/upload_question_preview';    
        }
        //setTimeout(function(){window.location.reload();}, 500)
        // var reviewHtml        = '';
        // var question          = JSON.parse(response);
        // reviewHtml += renderPreviewQuestions(question['question']);
        // $('#question_preview_count').html(Object.keys(question['question']).length);
        // $('#review-question').html(reviewHtml);
        // $('#report-card').modal('show');
    }
    else
    {
        $("#upload_question").val('');
        $("#upload_file_name").val('');
        __uploaded_files = '';
        $('#percentage_bar').hide();
        $("#upload_question").html('UPLOAD');
        $('#question_upload_processing_wrapper').html('Uploading...<b class="percentage-text" id="question_upload_processing">0%</b>');
        $("#addquestion .modal-body").prepend(renderPopUpMessage('error', '<div>Error occured in upload question. Fix the following issue and re-upload the template again. <br />'+data['message']+'</div>'));        
        scrollToTopOfPage();
    }
}


function deleteQuestion(question_id)
{
    var messageObject = {
        'body':'Are you sure delete this question ?',
        'button_yes':'CONTINUE', 
        'button_no':'CANCEL',
        'continue_params':{'question_id':question_id}
    };  
    callback_danger_modal(messageObject, deleteQuestionConfirmed);
    // $('#delete_message').hide();
    // $('#delete_header_text').html(lang('are_you_sure_delete_this_question'));
    // $('#deleteSection').modal('show');
    // $('#delete_generate_ok').unbind();
    // $('#delete_generate_ok').click({"question_id": question_id}, deleteQuestionConfirmed);    
}

function previewQuestion(question_id)
{
   window.location=admin_url+'generate_test/preview/'+question_id;
}

function deleteQuestionConfirmed(param)
{
    var question_id = param.data.question_id;
    $.ajax({
        url: admin_url+'generate_test/delete_question',
        type: "POST",
        data:{ "is_ajax":true, "question_id":param.data.question_id},
        success: function(response) {
            var data  = $.parseJSON(response);
            if(data['error'] != "false")
            {      
                var messageObject = {
                    'body':data['message'],
                    'button_yes':'CONTINUE', 
                    'button_no':'CANCEL'
                };  
                callback_warning_modal(messageObject);          
               // $('#delete_header_text').html(data['message']);
            }
            else
            {
                __totalQuestions  = __totalQuestions-1;
                __questionsCount  = __questionsCount-1;
               $("#total_questions").html(__totalQuestions);
               $("#questions_count").html(__questionsCount);
               $('#question_wrapper_'+question_id).remove();
               $('#deleteSection').modal('hide');
                var messageObject = {
                    'body':'Question successfully deleted',
                    'button_yes':'OK', 
                    'button_no':'CANCEL'
                };  
               callback_success_modal(messageObject); 
               
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

function renderPreviewQuestions(questions){
        //console.log(questions);
        __uploaded_questions = questions;
        var html        = '';
         $.each(__uploaded_questions,function(id,question){
        html           += renderPreviewQuestion(question,id);
         });

        return html;
    }
function renderPreviewQuestion(question,key){
        //alert(question['q_options'][1].length);
        var quesString  = question['q_question'];
        quesString      = quesString[1];
        var regex = /(<([^>]+)>)/ig
        quesString      = quesString.replace(regex,"");
        var explanationString  = question['q_explanation'];
        if(explanationString!=null){
           explanationString   = explanationString[1];
           explanationString   = explanationString.replace(regex,""); 
        } else {
           explanationString   = '';
        }
        var correctAnswer ='';
        type              = parseInt(question['q_type']);
        switch(type){
            case 1:
                //correctAnswer = toAlpha(question['q_answer']);
                correctAnswer = question['q_answer'];
            break;
            case 2:
                correctAnswer = question['q_answer'];
                // var answerArr = question['q_answer'].split(",");
                // var answers   = new Array();
                // $.each(answerArr,function(index,item){
                //     answers.push(toAlpha(parseInt(item))); 
                // });
                // correctAnswer = answers.join();
            break
            case 3:
                correctAnswer = (question['q_answer']!=null)?question['q_answer']:'';
            break;
            case 4:
                correctAnswer = (question['q_answer']!=null)?question['q_answer']:'';
            break;
        }

        var html        = '';
        html            +=' <div class="single-choice-wraper right" ><div class="single-choice-header">';
        html            += '<div class="row"><div class="col-md-6"><span class="no-in-round ">'+ key +'</span><b><span class="single-choice-label">'+renderQtype(question['q_type'])+'</span></b></div> <div class="col-md-6 text-right"><button type="button" class="btn btn-danger"  onclick="deletePreviewQuestion(\''+btoa(key)+'\');" >Delete</button></div></div>';
        html            += '<div class="row"><div class="question-master-parent"><div class="col-md-6 no-padding"><div class="margin-top-bottom"><span class="text-blue">Subject : </span><span class="preview-select">'+ question['q_subject'] +'</span><select class="form-control question-master-select" id="question_sub_'+key+'" onchange="topicGeneration('+key+','+question['q_category']+')">';
        html            += '<option value="0" >Choose Subject</option>';
        for(var s=0;s<Object.keys(question['q_subjects']).length;s++)
        {

        html            += '<option value="'+question['q_subjects'][s].id+'"';
        if(question['q_subjects'][s].qs_subject_name==question['q_subject'].toLowerCase()){
        html            += 'selected';
        }
        html            += '>'+question['q_subjects'][s].qs_subject_name+'</option>';
        }
        html            += '</select></div></div><div class="col-md-6"><div class="single-choice-label  margin-top-bottom"><span class="text-blue">Topic : </span><span class="preview-select">'+ question['q_topic'] +'</span><select class="form-control question-master-select" id="question_topic_'+key+'" >';
        html            += '<option value="0">Choose Topic</option>';
        for(var t=0;t<Object.keys(question['q_topics']).length;t++)
        {
        html            += '<option value="'+question['q_topic'][t].id+'"';
         if(question['q_topics'][t].qt_topic_name==question['q_topic'].toLowerCase()){
        html            += 'selected';
        }
        html            += '>'+question['q_topics'][t].qt_topic_name+'</option>';
        }
        html            += '</select></div></div></div></div>';
        html            += '<div class="question-master-parent"><span class="what-are-some-para"><p>'+ quesString +'</p></span><span class="question-wrap">';
        if(Object.keys(question['q_options']).length > 0 )
        {
           for(var i=1;i<=Object.keys(question['q_options']).length;i++)
           {
               if(i % 2 == 0){
                html            += '<span class="series-of-question text-qus-padding-left">';
               } else {
                html            += '<span class="series-of-question text-qus-padding-right">';
               }
                var option    = (typeof question['q_options'][i]!= 'undefined')?question['q_options'][i]:'';
                 html            += '<span class="a-b-c">'+ toAlpha(i) +')</span><p class="text-qus">'+ option[1] +'</p></span>';
           }
        }

        html            += '</span></div>';

        html            += '<hr class="hr-alter"><div class="choice-footer-wrap clearfix"><div class="choice-footer-wrap clearfix"><span class="your-answer-wrap-left"><span class="your-answer-wrap-left-inside-text">Correct answer : &nbsp; <span class="text-blue">'+ correctAnswer +'</span></span></span><span class="your-answer-wrap-right"> <span class="small-device-border"><strong>+ve marks : <span class="green-status">'+ question['q_positive_mark'] +'</span></strong> &nbsp;&nbsp;&nbsp;<strong>-ve marks : <span class="red-status">'+ Math.abs(question['q_negative_mark']) +'</span></strong>&nbsp;&nbsp;&nbsp;</span></div></div>';

        html            += '<div class="choice-footer-wrap margin-top clearfix"><span class="answer-exp">Answer Explanation</span><p>'+ explanationString +'</p></div>';

        html            +=' </div></div>';
        return html;
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

function renderSubjects(type){
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

function deletePreviewQuestion(question_id){
    question_id = typeof question_id != 'undefined' ? question_id : '';
    $('#test_pop_title').html('Delete Question');
    $('#test_pop_desc').html('Question will be removed from quiz.<br/>');
    $('#test_pop_continue').attr('onclick','deletePreviewQuestionConfirmed(\''+question_id+'\')');
    $('#test_basic_modal').modal('show');
}
function deletePreviewQuestionConfirmed(question_id){
        question_id = typeof question_id != 'undefined' ? question_id : '';
        __questionsToDelete = [];
        __questionsToDelete.push(atob(question_id));
        deletePreviewQuestions(__questionsToDelete);
}
function deletePreviewQuestions(questions){
        $('#test_pop_continue').html('Deleting...');
        $.ajax({
            url: webConfigs('admin_url')+'test_manager/delete_preview_questions',
            type: "POST",
            data:{ "is_ajax":true,"questions":JSON.stringify(questions)},
            success: function(response){
                $('#test_pop_cancel').click();
                var reviewHtml        = '';
                $('#test_pop_continue').html('Continue');
                var question          = JSON.parse(response);
                $('#question_preview_count').html(Object.keys(question['question']).length);
                reviewHtml            += renderPreviewQuestions(question['question']);
                $('#review-question').html(reviewHtml);
                $('#report-card').modal('show');
           
            }
        });
}

function savePreviewQuestions(){
    $('#test_preview_continue').html('Saving...');
    var selectedValues              = new Array();
    var data = {};
    for (var i = 1; i <= Object.keys(__uploaded_questions).length; i++) {
        data.i         = i;
        data.subject_id = $("#question_sub_"+i+" option:selected").val();
        data.topic_id   = $("#question_topic_"+i+" option:selected").val();
        selectedValues[i] = data;
    }
    var selectedString = JSON.stringify(selectedValues);
    $.ajax({
        url: webConfigs('admin_url')+'generate_test/confirm_import_questions',
        type: "POST",
        data:{ "is_ajax":true, "selectedValues": selectedString},
        success: function(response){
            var data  = $.parseJSON(response);
            $('#test_preview_continue').html('save');
            if(data['success'] == true){
               setTimeout(function(){window.location.reload();}, 500)
            }
        }
    });
}

function clearPreviewQuestions(){
    $("#q_parent_cat").val('');
    $("#upload_file_name").val('');
    $("#percentage_bar").html('');
    $("#upload_question").html('UPLOAD');
    $(".close").trigger("click");
};

function deleteQuestionBulk()
{
    if(Object.keys(__deleteQuestionIds).length  > 0 )
    {
        var messageObject = {
            'body':'Are you sure to delete questions',
            'button_yes':'CONTINUE', 
            'button_no':'CANCEL'
        };
        callback_warning_modal(messageObject, deleteQuestionBulkConfirmed); 
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

function topicGeneration(key,category){
    var subject_id = $("#question_sub_"+key+" option:selected").val();
    $.ajax({
            url: admin_url + 'question_manager/get_topics',
            type: "POST",
            data: {"is_ajax": true, 'course_category': category, 'question_subject': subject_id},
            success: function (response) {
                var data = $.parseJSON(response);
                //console.log(data);
                if(data['error']==false)
                {
                    $("#question_topic_"+key).html(" ");
                    var renderTopicHtml = '';
                    renderTopicHtml = '<option value="">Choose Topic</option>';

                    if(data['topic'].length!=0){
                        for (i = 0; i < data['topic'].length; i++) { 
                        var topic_data    = data['topic'][i];
                           renderTopicHtml            += '<option value="'+topic_data['id']+'" >'+topic_data['qt_topic_name']+'</option>';
                        }  
                    }
                    $("#question_topic_"+key).prepend(renderTopicHtml);
                   

                }
            }
        });
}


