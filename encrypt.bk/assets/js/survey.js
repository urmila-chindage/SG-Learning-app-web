var __lecture_access_limit = 0;
var __single_choice        = 1;


var defaultQuestionTypes = {'1':'', '2':'', '3':'', '4':'', '5':'' };
function renderQuestionHtml(question)
{
    var question_row ='';

    question_row += '    <div class="survey-form-flex">';
    question_row += '        <div class="form-group col-md-1 col-sm-1 ques-no no-padding text-right clearfix question-counter"></div>';
    question_row += '        <div class="form-group col-md-8 col-sm-8 clearfix">';
    question_row += '               <textarea class="form-control" placeholder="Enter your Question here?" id="question_'+ question['id'] +'" name="lecture_name">'+ question['sq_question'] +'</textarea>';
    question_row += '               <div class="question-preview">'+ question['sq_question'] +'</div>';
    //question_row += '            <input type="text" placeholder="Enter your Question here?" id="question_'+ question['id'] +'" name="lecture_name" value="'+ question['sq_question'] +'" class="form-control">';
    question_row += '        </div>';
    question_row += '        <div class="form-group col-md-3 col-sm-3 clearfix">';
    question_row += '            <select class="form-control ques-type-selector" id="input_type_'+question['id']+'" data-id="'+ question['id'] +'">';
    defaultQuestionTypes[question['sq_type']] = 'selected="selected"';
    question_row += '                <option value="1" '+ defaultQuestionTypes['1'] +'>Single Choice</option>';
    question_row += '                <option value="2" '+ defaultQuestionTypes['2'] +'>Multiple Choice</option>';
    question_row += '                <option value="3" '+ defaultQuestionTypes['3'] +'>Drop Down</option>';
    question_row += '                <option value="4" '+ defaultQuestionTypes['4'] +'>Paragraph</option>';
    question_row += '                <option value="5" '+ defaultQuestionTypes['5'] +'>Linear Choice</option>';
    question_row += '            </select>';
    defaultQuestionTypes[question['sq_type']] = '';
    question_row += '        </div>';
    question_row += '    </div>';
    question_row += '     <div class="survey-option-wrapper">';
    if(typeof question['sq_options'] == 'string') {
        question['sq_options'] = $.parseJSON(question['sq_options']);
    }
    switch (question['sq_type']) {
        case '1':                     // Single Choice
            
            var options     = question['sq_options'];      
            question_row    += '<div class="option-holder">';     
            for(var i in options) {
                question_row += '<div class="survey-option-row">';
                question_row += '    <div class="form-group col-md-1 col-sm-1 clearfix">';
                question_row += '        <div class="option-type-dev single-choice-option"></div>';
                question_row += '    </div>';
                question_row += '    <div class="form-group col-md-10 col-sm-10 no-padding clearfix">';
                question_row += '        <input type="text" maxlength="80" placeholder="eg:Option 1" name="lecture_name" value="' + options[i] + '" class="form-control question_options">';
                question_row += '    </div>';
                question_row += '    <div class="form-group col-md-1 col-sm-1 clearfix">';
                question_row += '        <div class="remove-survey-option">&times;</div>';
                question_row += '    </div>';
                question_row += '</div>';
            }
            question_row += '</div>';
            question_row += '<div class="add-btn-wrapper">';
            question_row += '        <div class="form-group col-md-10 col-sm-10 col-md-push-1 col-sm-push-1 no-padding clearfix">';
            question_row += '            <button type="" id="add_option_btn_'+question['id']+'" onclick="addOption('+question['id']+')" class="add-survey-btn btn btn-default add-option">ADD MORE OPTION</button>';
            question_row += '        </div>';
            question_row += '    </div>';
            break;

        case '2':                     // Multiple Choice
            
            var options     = question['sq_options'];   
            question_row    += '<div class="option-holder">';          
            for(var i in options) {
                question_row += '<div class="survey-option-row">';
                question_row += '    <div class="form-group col-md-1 col-sm-1 clearfix">';
                question_row += '        <div class="option-type-dev multiple-choice-option"></div>';
                question_row += '    </div>';
                question_row += '    <div class="form-group col-md-10 col-sm-10 no-padding clearfix">';
                question_row += '        <input type="text" maxlength="80" placeholder="eg:Option 1" name="lecture_name" value="' + options[i] + '" class="form-control question_options">';
                question_row += '    </div>';
                question_row += '    <div class="form-group col-md-1 col-sm-1 clearfix">';
                question_row += '        <div class="remove-survey-option">&times;</div>';
                question_row += '    </div>';
                question_row += '</div>';
            }      
            question_row += '</div>';
            question_row += '<div class="add-btn-wrapper">';
            question_row += '        <div class="form-group col-md-10 col-sm-10 col-md-push-1 col-sm-push-1 no-padding clearfix">';
            question_row += '            <button type="" id="add_option_btn_'+question['id']+'" onclick="addOption('+question['id']+')" class="add-survey-btn btn btn-default add-option">ADD MORE OPTION</button>';
            question_row += '        </div>';
            question_row += '    </div>';
            break;

        case '3':                     // Dropdown
            
            var options     = question['sq_options'];     
            question_row    += '<div class="option-holder">';        
            for(var i in options) {
                question_row += '<div class="survey-option-row">';
                question_row += '    <div class="form-group col-md-1 col-sm-1 clearfix">';
                question_row += '        <div class="option-type-dev dropdown-option"></div>';
                question_row += '    </div>';
                question_row += '    <div class="form-group col-md-10 col-sm-10 no-padding clearfix">';
                question_row += '        <input type="text" maxlength="80" placeholder="eg:Option 1" name="lecture_name" value="' + options[i] + '" class="form-control question_options">';
                question_row += '    </div>';
                question_row += '    <div class="form-group col-md-1 col-sm-1 clearfix">';
                question_row += '        <div class="remove-survey-option">&times;</div>';
                question_row += '    </div>';
                question_row += '</div>';
            }      
            question_row += '</div>';
            question_row += '<div class="add-btn-wrapper" style="display: none;">';
            question_row += '        <div class="form-group col-md-10 col-sm-10 col-md-push-1 col-sm-push-1 no-padding clearfix">';
            question_row += '            <button type="" id="add_option_btn_'+question['id']+'" onclick="addOption('+question['id']+')" class="add-survey-btn btn btn-default add-option">ADD MORE OPTION</button>';
            question_row += '        </div>';
            question_row += '    </div>';
            break;

        case '4':                     // Text
            question_row    += '<div class="option-holder">';
            question_row    += '            <div class="survey-option-row">';
            question_row    += '            </div>'; 
            question_row    += '</div>';
            

            question_row += '<div class="add-btn-wrapper" style="display: none;">';
            question_row += '        <div class="form-group col-md-10 col-sm-10 col-md-push-1 col-sm-push-1 no-padding clearfix">';
            question_row += '            <button type="" id="add_option_btn_'+question['id']+'" onclick="addOption('+question['id']+')" class="add-survey-btn btn btn-default add-option">ADD MORE OPTION</button>';
            question_row += '        </div>';
            question_row += '    </div>';
            break;

        case '5':                     // Range
            question_row    += '<div class="option-holder">'; 
            question_row    += '</div>';
            question_row += '            <div class="survey-option-row">';
            question_row += '                <table class="linear-choice-table">';
            question_row += '                    <thead>';
            question_row += '                        <tr>';
            for (var val = question['sq_low_limit']; val <= question['sq_high_limit']; val++)
            {
                question_row += '       <th>'+ val +'</th>';
            }
            // question_row += '                            <th>1</th>';
           
            question_row += '                        </tr>';
            question_row += '                    </thead>';
            question_row += '                    <tbody>';
            question_row += '                        <tr>';
            for (var val = question['sq_low_limit']; val <= question['sq_high_limit']; val++)
            {
                question_row += '     <td><div class="single-choice-option"></div></td>';
            }
            question_row += '                        </tr>';
            question_row += '                    </tbody>';
            question_row += '                </table>';
            question_row += '            </div>';
            question_row += renderLinearRangeSelectorHtml(question);

            question_row += '<div class="add-btn-wrapper" style="display: none;">';
            question_row += '        <div class="form-group col-md-10 col-sm-10 col-md-push-1 col-sm-push-1 no-padding clearfix">';
            question_row += '            <button type="" id="add_option_btn_'+question['id']+'" onclick="addOption('+question['id']+')" class="add-survey-btn btn btn-default add-option">ADD MORE OPTION</button>';
            question_row += '        </div>';
            question_row += '    </div>';
            break;
    }
    question_row += '         <div class="survey-actions">';
    question_row += '             <div class="col-md-6 col-sm-6 no-padding survey-action-btn survey-save-wrapper">';
    question_row += '           <button class="btn btn-danger" onclick="cancelSurveyQuestion('+ question['id'] +')">CANCEL</button>';
    question_row += '           <button class="btn btn-success" onclick="saveSurveyQuestion('+ question['id'] +')">SAVE</button>'
    question_row += '               </div>';
    if(question['id'] != '0'){
        question_row += '             <div class="col-md-3 col-sm-3 text-center action-col-border survey-action-btn">';
        question_row += '                 <span class="edit-btn">EDIT</span>';
        question_row += '                 <span class="copy-btn" onclick="saveSurveyQuestion('+ question['id'] +', true)" data-toggle="tooltip" title="Copy"></span>';
        question_row += '                 <span class="delete-btn" onclick="deleteSurveyQuestion('+ question['id'] +')" data-toggle="tooltip" title="Delete"></span>';
        question_row += '             </div>';
    }

    var required = (question['sq_required'] == '1') ? 'checked="checked"': '';
    question_row += '             <div class="col-md-3 col-sm-3 required-switch-holder">';
    question_row += '                 <span class="required-label">Required</span>';
    question_row += '                 <label class="required-switch">';
    question_row += '                 <input type="checkbox" '+required+' id="is_required_'+ question['id'] +'">';
    question_row += '                 <span class="toggle-slider toggle-slider-round"></span>';
    question_row += '                 </label>';
    question_row += '             </div>';
    question_row += '         </div>';
    question_row += '     </div>';

    return question_row;
}

function renderLinearRangeSelectorHtml(question) {
    var linearRange = {'0':'', '1':'', '2':'', '3':'', '4':'', '5':'', '6':'', '7':'', '8':'', '9':'', '10':'' };
    var question_row = '';
        question_row += '<div class="survey-option-row linear-range-selector-holder">';
        question_row += '<div class="form-group col-md-1 col-sm-1 no-padding clearfix"></div>';
        question_row += '    <div class="form-group col-md-10 col-sm-10 no-padding clearfix">';
        question_row += '        <div class="linear-range-selector">';
        question_row += '            <span style="padding-left:0;">From</span>';
        question_row += '            <select name="" id="low_range_'+ question['id'] +'">';
        linearRange[question['sq_low_limit']] = 'selected="selected"';
        question_row += '                <option value="0" '+ linearRange[0] +'>0</option>';
        question_row += '                <option value="1" '+ linearRange[1] +'>1</option>';
        question_row += '            </select>';
        linearRange[question['sq_low_limit']] = '';
        question_row += '        </div>';
        question_row += '        <div class="linear-range-selector">';
        question_row += '            <span>To</span>';
        linearRange[question['sq_high_limit']] = 'selected="selected"';
        question_row += '            <select name="" id="high_range_'+ question['id'] +'">';
        question_row += '                <option value="2" '+ linearRange[2] +'>2</option>';
        question_row += '                <option value="3" '+ linearRange[3] +'>3</option>';
        question_row += '                <option value="4" '+ linearRange[4] +'>4</option>';
        question_row += '                <option value="5" '+ linearRange[5] +'>5</option>';
        question_row += '                <option value="6" '+ linearRange[6] +'>6</option>';
        question_row += '                <option value="7" '+ linearRange[7] +'>7</option>';
        question_row += '                <option value="8" '+ linearRange[8] +'>8</option>';
        question_row += '                <option value="9" '+ linearRange[9] +'>9</option>';
        question_row += '                <option value="10" '+ linearRange[10] +'>10</option>';
        question_row += '            </select>';
        linearRange[question['sq_high_limit']] = '';
        question_row += '        </div>';
        question_row += '    </div>';
        question_row += ' </div>';
    return question_row;
}

function renderSurveyQuestions(questions) {
    // console.log(question);

    var question_row        = '';
    if(Object.keys(questions).length > 0 )
    {
        $.each(questions, function(questionKey, question )
        {
            if(inArray(question['sq_type'], ['1','2','3']) == true) {
                question['sq_options'] = $.parseJSON(question['sq_options']);
            }
            question_row += '<div class="survey-row no-edit" id="survey_question_'+ question['id'] +'" data-question="'+question['id']+'">';
            question_row += renderQuestionHtml(question);
            question_row += ' </div>';
        });
    }
    
    return question_row;
}

function addQuestion() {
    var question      = {'id':'0', 'sq_type':'1', 'sq_question':'', 'sq_options':{'0':'', '1':''}};
    renderIndependentForm(question);
    $('#add_question').css('visibility','hidden');
}


function renderIndependentForm(question) {
    var question_row  = '';
    // if(inArray(question['sq_type'], ['1','2','3']) == true) {
    //     question['sq_options'] = $.parseJSON(question['sq_options']);
    // }
    question_row += '<div class="survey-row" id="survey_question_'+ question['id'] +'" data-question="'+question['id']+'">';
    question_row += renderQuestionHtml(question);
    question_row += ' </div>';
    $('.survey-wrapper').append(question_row);
}



function saveSurveyLecture()
{
    var lecture_id                      = $('#lecture_id').val();
    var course_id                       = $('#course_id').val();
    var lecture_name                    = $('#lecture_name').val();
    var lecture_description             = $('#lecture_description').val();
    var tutor_id                        = $('#lecture_tutor').val();
    var survey_type                     = $('input[name="survey_type"]:checked').val();
    var tutor_name                      = '';
    var lecture_image                   = $('#lecture_image_add').attr('image_name');

    if(survey_type == 'regular') {
        tutor_id = '';
    }

    if(tutor_id != '') {
        tutor_name                      = $("#lecture_tutor option:selected").html();
    }
    
        // __lecture_access_limit          = $("input[type='radio'][name='cl_limited']:checked").val();
        
    var errorCount                      = 0;
    var errorMessage                    = '';
    //end

    //validation process
    if( lecture_name == '')
    {
        errorCount++;
        errorMessage += 'Please enter survey name <br />';
    }
    if( cb_has_lecture_image == 1 && lecture_image == 'default-lecture.jpg' && (($('#lecture_logo_btn')[0].files[0] == undefined)) )
    {
        errorCount++;
        errorMessage += 'Please upload lecture image.<br>';
    }
    if(survey_type == 'tutor' && tutor_id <= 0 ) {
        errorCount++;
        errorMessage += 'Please choose tutor <br />';
    }

    
    cleanPopUpMessage();
    if(errorCount > 0 )
    {
        $('#survey_form').prepend(renderPopUpMessage('error', errorMessage));
        scrollToTopOfPage();
        return false;
    }
    var formData = new FormData();
    formData.append('lecture_image', $('#lecture_logo_btn')[0].files[0]);
    formData.append('is_ajax', true);
    formData.append('lecture_id', lecture_id);
    formData.append('course_id', course_id);
    formData.append('lecture_name', lecture_name);
    formData.append('lecture_description', lecture_description);
    formData.append('tutor_id', tutor_id);
    formData.append('tutor_name', tutor_name);
    $.ajax({
        url: admin_url+'coursebuilder/save_survey_detail',
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
                $('#survey_form').prepend(renderPopUpMessage('error', data['message']));
                scrollToTopOfPage();
            }
            else
            {
                __lectureName = lecture_name;
                var messageObject = {
                    'body':data['message'],
                    'button_yes':'OK', 
                };
                callback_success_modal(messageObject);
                $('#lecture_name_title').html(lecture_name);
                $('#survey_title').html('PREVIEW - '+lecture_name);
            }
        }
    });
}

function cancelSurveyQuestion(questionId)
{
    if(questionId == 0) {
        $('#add_question').css('visibility','visible');
        $('#survey_question_0').remove();
    } else {
        $.ajax({
            url: admin_url+'coursebuilder/survey_question',
            type: "POST",
            data:{ "is_ajax":true, 
                    "q_id":questionId
                },
            success: function(response) {
                var data  = $.parseJSON(response);
                if(data['error'] == "false")
                {                
                    if(inArray(data['question']['sq_type'], ['1','2','3']) == true) 
                    {
                        data['question']['sq_options'] = $.parseJSON(data['question']['sq_options']);
                    }
                    $('#survey_question_'+questionId).html(renderQuestionHtml(data['question']));
                }
                else
                {
                    $('#survey_question_'+questionId).prepend(renderPopUpMessage('error', errMessage));
                    scrollToTopOfPage();
                }
            }
        });
        $('#survey_question_'+questionId).addClass('no-edit');
    }
    $('#add_question').css('visibility','visible');
}

var ajxRequestInProgress = false;
function saveSurveyQuestion(questionId, copy=false)
{
    if (ajxRequestInProgress == true) {
        return false;
    }

    var errCount      = 0;
    var errMessage    = '';
    var question      = $('#question_'+questionId).val();
    var question_type = $('#input_type_'+questionId).val();
    // var total_options = $('#question_option_wrapper .option-element').length;
    var total_options = $('#survey_question_'+questionId+' .survey-option-row').length;
    var options = [];
    var low_limit   = '';
    var high_limit   = '';
    if( question == '' )
    {
        errMessage += 'Question cannot be empty<br />';   
        errCount++;        
    }
    if( total_options  < 2 && (question_type != 4 && question_type != 5) )
    {
        errCount++;
        errMessage += 'Please add atleast two options<br />';
    }
	if( question_type != 4 && question_type != 5 )
	{
        $('#survey_question_'+questionId+' .question_options').each(function(){
            //console.log($(this).val());
            if($(this).val()=='')
			{
				errMessage += 'Options cannot be empty<br />';   
				errCount++;
				return false;
			} else {
                options.push($(this).val());
            }
        });
	}
    
    if( question_type == 5 )
    {
        low_limit   = $('#low_range_'+questionId).val();
        high_limit   = $('#high_range_'+questionId).val();
        if( low_limit >= high_limit )
        {
            errMessage += 'Higher range should be greater than lower range<br />';   
            errCount++;
        }
    }
    var is_required = '0';
    if($('#is_required_'+questionId).is(":checked"))
        is_required = '1';

        cleanPopUpMessage();
    if( errCount > 0 )
    {
        $('#survey_question_'+questionId).prepend(renderPopUpMessage('error', errMessage));
        scrollToTopOfPage();
        return false;        
    }
    var questionIdTemp = questionId;
    questionId = ( copy == true )? 0:questionId;
    ajxRequestInProgress = true;
    $.ajax({
        url: admin_url+'coursebuilder/save_survey_question',
        type: "POST",
        data:{ "is_ajax":true, 
                "lecture_id":__lecture_id,
                "survey_id": __survey_id,
                "q_id":questionId, 
                "q_question": question,
                "q_type": question_type,
                "is_required": is_required,
                "q_options": options,
                "q_course_id": __course_id,
                "q_low_range": low_limit,
                "q_high_range": high_limit
            },
        success: function(response) {
            var data  = $.parseJSON(response);
            if(data['error'] == "false")
            {                
                // var messageObject = {
                //     'body':((copy==true)?'Survey question copied successfully':data['message']),
                //     'button_yes':'OK', 
                // };
                // callback_success_modal(messageObject);
                $('#common_message_advanced').modal('hide');
                $('#add_question').css('visibility','visible');
                if(questionId == 0 || copy == true) {
                    $('#survey_question_0').remove();
                    question_row = '<div class="survey-row " id="survey_question_'+ data['question']['id'] +'" data-question="'+data['question']['id']+'">';
                    question_row +=     renderQuestionHtml(data['question']);
                    question_row += '</div>';
                    if(copy == true) {
                        $( question_row ).insertAfter( "#survey_question_"+questionIdTemp );
                    } else {
                        $('.survey-wrapper').append(question_row);
                    }
                    $('#survey_question_'+data['question']['id']).addClass('no-edit');    
                } else {
                    $('#survey_question_'+questionId).html(renderQuestionHtml(data['question']));
                    $('#survey_question_'+questionId).addClass('no-edit');    
                }
                updateSurveyQuestionOrder();
            }
            else
            {
                $('#survey_question_'+questionId).prepend(renderPopUpMessage('error', errMessage));
                scrollToTopOfPage();
            }
            ajxRequestInProgress = false;
        }
    });
}



$(document).on('change', '.ques-type-selector', function(){
    var question_type   = $(this).val();
    var questionId      = $(this).attr('data-id');
    $('#survey_question_'+questionId+' .option-type-dev').removeClass('single-choice-option').removeClass('multiple-choice-option').removeClass('dropdown-option');
    $('#survey_question_'+questionId+' .option-holder').hide();
    $('#survey_question_'+questionId+' .add-btn-wrapper').hide();
    $('#survey_question_'+questionId+' .linear-choice-table').parent().remove();
    $('#survey_question_'+questionId+' .linear-range-selector-holder').remove();
    switch(question_type) {
        case '1':
            if($('#survey_question_'+questionId+' .survey-option-row').length == 0) {
                $('#add_option_btn_'+questionId).trigger('click');
                $('#add_option_btn_'+questionId).trigger('click');
            } else {
                $('#survey_question_'+questionId+' .option-type-dev').addClass('single-choice-option');
            }
            $('#survey_question_'+questionId+' .option-holder').show();
            $('#survey_question_'+questionId+' .add-btn-wrapper').show();
        break;
        case '2':
            if($('#survey_question_'+questionId+' .survey-option-row').length == 0) {
                $('#add_option_btn_'+questionId).trigger('click');
                $('#add_option_btn_'+questionId).trigger('click');
            } else {
                $('#survey_question_'+questionId+' .option-type-dev').addClass('multiple-choice-option');
            }
            $('#survey_question_'+questionId+' .option-holder').show();
            $('#survey_question_'+questionId+' .add-btn-wrapper').show();
        break;
        case '3':
            if($('#survey_question_'+questionId+' .survey-option-row').length == 0) {
                $('#add_option_btn_'+questionId).trigger('click');
                $('#add_option_btn_'+questionId).trigger('click');
            } else {
                $('#survey_question_'+questionId+' .option-type-dev').addClass('dropdown-option');
            }
            $('#survey_question_'+questionId+' .option-holder').show();
            $('#survey_question_'+questionId+' .add-btn-wrapper').show();
        break;
        case '4':
            $('#survey_question_'+questionId+' .survey-option-row').remove();
            $('#survey_question_'+questionId+' .add-btn-wrapper').hide();
        break;
        case '5':
            var param = {'id':questionId, 'sq_low_limit':1, 'sq_high_limit':10};
            $('#survey_question_'+questionId+' .survey-option-row').remove();
            $('#survey_question_'+questionId+' .survey-option-wrapper').prepend(renderLinearRangeSelectorHtml(param));
        break;
    }
});



function deleteSurveyQuestion(survey_question_id, survey_id)
{
    var messageObject = {
        'body':'Are you sure to delete the question?',
        'button_yes':('DELETE'), 
        'button_no':'CANCEL',
        'continue_params':{'question_id':survey_question_id},
    };
    callback_warning_modal(messageObject, deleteSurveyQuestionConfirmed);
}

function deleteSurveyQuestionConfirmed(param)
{
    var questionId = param.data.question_id;
    $.ajax({
        url: admin_url+'coursebuilder/delete_survey_question',
        type: "POST",
        data:{ "is_ajax":true,"survey_id":__survey_id,"course_name":__courseName, "question_id":param.data.question_id },
        success: function(response) {
            var data  = $.parseJSON(response);
            if(data['error'] == "true")
            {                
                $('#survey_question_'+questionId).prepend(renderPopUpMessage('error', data['message']));
                scrollToTopOfPage();
            }
            else
            {
            //    var messageObject = {
            //         'body':'Survey question deleted successfully',
            //         'button_yes':'OK', 
            //     };
            //     callback_success_modal(messageObject);
            if(data['status'] == '1' )
                {
                    $('#lecture_status_text_'+data['id']).html(lang('active'));
                    $('#lecture_status_wraper_'+data['id']).removeClass('active-section').removeClass('Inactive-section').addClass('active-section');
                    $('#lecture_action_status_'+data['id']).attr('onclick', 'changeLectureStatus(\'1\', \''+btoa(__lectureName)+'\', \''+data['id']+'\')').html(lang('deactivate').toUpperCase()).removeClass('btn-green').removeClass('btn-orange').addClass('btn-orange');
                }
                else
                {
                    $('#lecture_status_text_'+data['id']).html(lang('inactive'));
                    $('#lecture_status_wraper_'+data['id']).removeClass('active-section').removeClass('Inactive-section').addClass('Inactive-section');
                    $('#lecture_action_status_'+data['id']).attr('onclick', 'changeLectureStatus(\'0\', \''+btoa(__lectureName)+'\', \''+data['id']+'\')').html(lang('activate').toUpperCase()).removeClass('btn-green').removeClass('btn-orange').addClass('btn-green');
                }
                $('#common_message_advanced').modal('hide');
                $('#survey_question_'+questionId).remove();
            }
        }
    });
}



var counter = 0;
$(document).ready(function () {
    
    var questions   = $.parseJSON(__questions);

    $('.survey-wrapper').html(renderSurveyQuestions(questions));
        
 });

 $(document).on('click', '.remove-survey-option', function(){
    $(this).closest(".survey-option-row").remove();       
    counter -= 1
});

$(document).on('click', '.edit-btn', function(){
    var wrapperDiv = $(this).closest('.survey-row');
        wrapperDiv.removeClass('no-edit');
        $('#add_question').css('visibility','hidden');
        //$("#"+wrapperDiv.attr('id'))[0].scrollIntoView();
        $('#'+wrapperDiv.attr('id')+' .linear-choice-table').parent().hide();
        $(this).hide();
})

 var defaultQuestionTypeClass = {'1':'single-choice-option', '2':'multiple-choice-option', '3':'', '4':'', '5':'' };
 function addOption(questionId) {
    var quetionType = $('#input_type_'+questionId).val();
    var newRow = $('<div class="survey-option-row">');
    var cols = "";
    cols += '<div class="form-group col-md-1 col-sm-1 clearfix">';
    cols += '<div class="option-type-dev '+defaultQuestionTypeClass[quetionType]+'"></div>';
    cols += '</div>';
    cols += '<div class="form-group col-md-10 col-sm-10 no-padding clearfix">';
    cols += '<input type="text" maxlength="80" placeholder="eg: Option 1" id="lecture_name" name="lecture_name" class="form-control question_options">';
    cols += '</div>';
    cols += '<div class="form-group col-md-1 col-sm-1 clearfix">';
    cols += '<div class="remove-survey-option">×</div>';
    cols += '</div>';

    newRow.append(cols);
    $("#survey_question_"+questionId+" .option-holder").append(newRow);
    counter++;
 }
// tooltip
$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip();   
});

function updateSurveyQuestionOrder() {
    var surveyQuestionOrder = [];
    $( '.survey-row' ).each(function( index ) {
        surveyQuestionOrder.push($(this).attr('data-question'));
    });
    $.ajax({
        url: admin_url+'coursebuilder/arrange_survey_question',
        type: "POST",
        async : false,
        data:{"survey_question_order":surveyQuestionOrder},
        success: function(response) {
        }
    });
}

