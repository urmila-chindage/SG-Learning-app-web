$(document).ready(function(){
    var forceAssesment = window.location.hash.substr(1);
    if(forceAssesment == 'force')
    {
        $('#load_exam_button').click({}, loadExamAssets);    
        $('#instruction_tab').css('display', 'none');
        loadExamAssets();
    }
    if(forceAssesment == 'instruction')
    {
        $('#instruction_tab').css('display', 'block');
        $('#load_exam_button').click({}, loadExamAssets);    
    }
});

var autocollapse = function () {
    var tabs = $('#categories');
    var tabsHeight = tabs.innerHeight();
    if (tabsHeight >= 50) {
        while (tabsHeight > 50) {
            //console.log("new"+tabsHeight);
            var children = tabs.children('li:not(:last-child)');
            var count = children.size();
            $(children[count - 1]).prependTo('#collapsed');

            tabsHeight = tabs.innerHeight();
        }
    }
    else {/*
        var collapsed = $('#collapsed').children('li');
        
        while (tabsHeight < 50 && (tabs.children('li').size() > 0 && collapsed.size() > 0 )){
            var count = collapsed.size();
            $(collapsed[0]).insertBefore(tabs.children('li:last-child'));
            tabsHeight = tabs.innerHeight();
        }
        if (tabsHeight > 50) { // double chk height again
            autocollapse();
        }*/
    }
};

var __response          = null;
var __category          = null;
var __question_index    = 0;
var __answerSheet           = new Object;
    __answerSheet['review'] = new Object;
    __answerSheet['answer'] = new Object;
var __current_question  = null;
var __checked           = 'checked="checked"';

var __answered          = new Object;
var __not_answered      = new Object;
var __marked_review     = new Object;
var __visitedQuestions  = new Object;
function loadExamAssets()
{   
    $('#quesion_loading_tab').show();
    $('#load_exam_button').html('Loading exam assets...');
    $.ajax({
         url: __site_url+'/material/exam_assets',
         type: "POST",
         data:{ "is_ajax":true, 'course_id':__course_id, 'lecture_id':__lecture_id},
         success: function(response) {
                 __response = $.parseJSON(response);
             var categoryHtml   = '';
             var questionTabs   = '';
             var questionTypes  = '';
             var activeLi       = 'active';
             
            //rendering question type
            questionTypes += '<div class="tabs_top">';
            questionTypes += '    <div class="tabs_left" id="question_number_label">Question No : 1</div>';
            questionTypes += '    <div class="tabs_right">';
            questionTypes += '        <h3 class="tabs_move_right" id="question_type_label" >Question Type : MCQ</h3> ';
            questionTypes += '    </div>';
            questionTypes += '</div>';
            //end
            
            //rendering categories and question tabs
            if( __response['lecture']['assesment']['a_show_categories'] == '1' && Object.keys(__response['lecture']['categories']).length>0)
            {
                $.each(__response['lecture']['categories'], function( index, value ) {
                    categoryHtml += '<li class="'+activeLi+'" onclick="filterQuestionByCategory(\''+index+'\');"  id="category_tab_'+index+'"><a href="#category_tabs">'+value['qc_category_name']+'</a></li>';
                    //categoryHtml += '<li class="'+activeLi+'" onclick="filterQuestionByCategory(\''+index+'\');"  id="category_tab_'+index+'"><a href="#category_tabs">ffffffffff</a></li>';
                    activeLi      = '';
                    __category    = (__category==null)?index:__category;
                });
                categoryHtml += '<li id="lastTab"><a class="btn dropdown-toggle" data-toggle="dropdown" href="javascript:void(0)">More <span class="caret"></span></a>';
                categoryHtml += '    <ul class="dropdown-menu" id="collapsed"></ul>';
                categoryHtml += '</li>';
                $('#categories').html(categoryHtml);
                $('#current_category_title').html('You are viewing <br/> '+__response['lecture']['categories'][__category]['qc_category_name']);            
            }
            else
            {
                $('#current_category_title').html('You are viewing all questions');                
            }
                //questionTabs += '<div id="category_tab_'+__response['lecture']['categories'][i]['category_id']+'" class="tab-pane '+activeLi+'">';
                //questionTabs += '        <h4>'+__response['lecture']['categories'][i]['qc_category_name']+'</h4>';
                questionTabs += '<div id="category_tabs" class="tab-pane active">';
                questionTabs += questionTypes;
                questionTabs += '<div class="content" id="question_content">';
                questionTabs += '</div>';
                questionTabs += '<div class="buttons" id="question_footer">';
                questionTabs += '    <a href="javascript:void(0)" onclick="MarkForReviewAndNext();" class="review">Mark for Review &amp; Next</a>';
                questionTabs += '    <a href="javascript:void(0)" onclick="ClearResponse();" class="clear">Clear Response</a>';
                questionTabs += '    <a href="javascript:void(0)" onclick="SaveAndNext();" class="save">Save &amp; Next</a>';
                questionTabs += '</div>';
                questionTabs += '</div>';
                $('#question_tabs').html(questionTabs);
            //End of rendering categories   
            
            
            //rendering the first tab
            renderQuestion();
            renderQuestionPallette();
            $('#question_number_label').html('Question No : '+$('#pallette_block_'+__current_question).attr('data-id'));
            //End 
            
            //setting click action on button
            $('#load_exam_button').unbind();
            $('#load_exam_button').html('Resume Assement');
            $('#load_exam_button').click({}, resumeAssesment);    
            //End
            
            //renderign question paper
            loadQuestionPaper();
            //end
            
            $('#instruction_tab').css('display', 'none');
            $('.online_text_main').css('display', 'block');
            $('#quesion_loading_tab').hide();
            initDomActions();
         }
     }); 
}

function  loadQuestionPaper()
{
    var questionPaperHtml = '';
    var questionPaperIndex = Object.keys(__response['lecture']['questions']);
    var lecture = null;
    if(questionPaperIndex.length > 0 )
    {
        for (var key in questionPaperIndex)
        {
            if (questionPaperIndex.hasOwnProperty(key))
            {
                lecture = __response['lecture']['questions'][questionPaperIndex[key]];
                switch (lecture['q_type'])
                {
                    case "1":
                        questionPaperHtml +='<div class="qus_sheet question_paper one">';
                        questionPaperHtml +='    <div class="question question-table">'+lecture['q_question']+'</div>';
                        questionPaperHtml +='    <div class="options_main">';
                        if(lecture['options'].length > 0 )
                        {
                           for(var i=0;i<lecture['options'].length;i++)
                           {
                                questionPaperHtml +='            <div class="check">';
                                questionPaperHtml +='            <input type="radio" disabled="disabled" class="checkBox_ "> ';
                                questionPaperHtml +='            <label for="name1" class="checkText"><span></span></label>';
                                questionPaperHtml +='            <div>'+lecture['options'][i]['qo_options']+'</div></div>';
                           }
                        }
                        questionPaperHtml +='    </div>';
                        questionPaperHtml +='</div>';
                    break;
                    case "2":
                            questionPaperHtml +='<div class="qus_sheet question_paper one">';
                            questionPaperHtml +='    <div class="question question-table">'+lecture['q_question']+'</div>';
                            questionPaperHtml +='    <div class="options_main">';
                            if(lecture['options'].length > 0 )
                            {
                               for(var i=0;i<lecture['options'].length;i++)
                               {
                                    questionPaperHtml +='        <div class="check">';
                                    questionPaperHtml +='            <input type="checkbox" disabled="disabled" class="checkBox_ "> ';
                                    questionPaperHtml +='            <label for="name1" class="checkText"><span></span></label>';
                                    questionPaperHtml +='            <div>'+lecture['options'][i]['qo_options']+'</div></div>';
                               }
                            }
                            questionPaperHtml +='    </div>';
                            questionPaperHtml +='</div>';
                    break;
                    case "3":
                        questionPaperHtml +='<div class="qus_sheet question_paper one">';
                        questionPaperHtml +='    <div class="question question-table">'+lecture['q_question']+'</div>';
                        questionPaperHtml +='    <div class="options_main">';
                        questionPaperHtml +='    </div>';
                        questionPaperHtml +='</div>';
                    break;
                }
            }
        }
    }
    questionPaperHtml +='<a class="btn btn-altr" href="javascript:void(0)" onclick="resumeAssesment()">Resume Exam</a>';
    $('#quesion_paper_tab .question-content').html(questionPaperHtml);    
}

function renderInstruction()
{
    timerAction('pause');
    stopAnsweringTimeLog();
    $('#exam_asset_tab').css('display', 'none');
    $('#instruction_tab').css('display', 'block');
}

function resumeAssesment()
{
    timerAction('resume');
    startAnsweringTimeLog()
    $('#exam_asset_tab').css('display', 'block');
    $('#quesion_paper_tab').css('display', 'none');
    $('#instruction_tab').css('display', 'none');
}

function renderQuestionPaper()
{
    timerAction('pause');
    stopAnsweringTimeLog();
    $('#exam_asset_tab').css('display', 'none');
    $('#quesion_paper_tab').css('display', 'block');    
}

function renderQuestion()
{
    var lecture = null;
    if(__response['lecture']['assesment']['a_show_categories'] == '1' )
    {
        if(__category != null )
        {
           var lecture = __response['lecture']['questions'][__response['lecture']['category_questions'][__category][__question_index]];
            //console.log(lecture);    
        } 
    }
    else
    {
        if(Object.keys(__response['lecture']['questions']).length > 0)
        {
            var currentIndex = 0;
            $.each(__response['lecture']['questions'], function( index, value ) {
                if( __question_index == currentIndex)
                {
                    lecture = value;
                    return false;
                }
                currentIndex++;
            });
        };
        
    }
    if(lecture != null )
    {
        switch (lecture['q_type'])
        {
            case "1":
                renderSingleChoice(lecture);
                break;
            case "2":
                renderMultipleChoice(lecture);
                break;
            case "3":
                renderSubjective(lecture);
                break;
        }
    }
}

function MarkForReviewAndNext()
{
    var temp_question_id  = __current_question;
    console.log(__current_question);
    SaveAndNext();
    if(temp_question_id != null)
    {
        //__answerSheet['review'].push(temp_question_id);
        //__marked_review.pop(temp_question_id);
        __marked_review[temp_question_id] = temp_question_id;
        //removeArrayIndex(__answered, temp_question_id)
        removeArrayIndex(__not_answered, temp_question_id)
    }
    $('#pallette_block_'+temp_question_id).removeClass('not-answerd-palle').removeClass('answerd-pallet').addClass('marked-pallet');
}

function SaveAndNext()
{
    tempq =__current_question;
    var lecture = __response['lecture']['questions'][__current_question];
    switch (lecture['q_type'])
    {
        case "1":
            saveSingleChoice();
            break;
        case "2":
            saveMultipleChoice();
            break;
        case "3":
            saveSubjective();
            break;
    }
    saveAnsweringTimeLog();
    renderNextQuestion(); 
    $('#pallette_block_'+tempq).removeClass('not-answerd-palle').removeClass('marked-pallet').addClass('answerd-pallet');   
}

function saveAnswer()
{
    var lecture = __response['lecture']['questions'][__current_question];
    switch (lecture['q_type'])
    {
        case "1":
            saveSingleChoice();
            break;
        case "2":
            saveMultipleChoice();
            break;
        case "3":
            saveSubjective();
            break;
    }
    saveAnsweringTimeLog();
}

function ClearResponse()
{
    var lecture = __response['lecture']['questions'][__current_question];
    if(typeof __answerSheet['answer'][lecture['id']] != 'undefined')
    {
       delete __answerSheet['answer'][__current_question];
    }
    if(typeof __answerSheet['review'][lecture['id']] != 'undefined')
    {
        delete __answerSheet['review'].pop(__current_question);
    }
    loadQuestion(__current_question);
}

function loadQuestion(question_id)
{
    var lecture = __response['lecture']['questions'][question_id];
    switch (lecture['q_type'])
    {
        case "1":
            renderSingleChoice(lecture);
            break;
        case "2":
            renderMultipleChoice(lecture);
            break;
        case "3":
            renderSubjective(lecture);
            break;
    }
}

function saveSingleChoice()
{
    var answer = $('input[name=radio_option]:checked').val();
    __answerSheet['answer'][__current_question] = answer;
    //console.log(__current_question);
    if(answer != '' && typeof answer != 'undefined' )
    {
        removeArrayIndex(__not_answered, __current_question);
        removeArrayIndex(__marked_review, __current_question);
        __answered[__current_question] = __current_question;
        $('#pallette_block_'+__current_question).removeClass('not-answerd-palle').removeClass('marked-pallet').addClass('answerd-pallet');
    }
    else
    {
        removeArrayIndex(__marked_review, __current_question);
        removeArrayIndex(__answered, __current_question);
        __not_answered[__current_question] = __current_question;
        $('#pallette_block_'+__current_question).removeClass('answerd-pallet').removeClass('marked-pallet').addClass('not-answerd-palle');        
    }
}

function saveMultipleChoice()
{
    var answers = new Object;
    var selectedAnswer = '';
    $( '.question-options' ).each(function( index ) {
        if($(this).prop('checked') == true)
        {
            selectedAnswer = $( this ).val();
            answers[selectedAnswer] = selectedAnswer;
        }
        selectedAnswer = '';
    });
    __answerSheet['answer'][__current_question] = answers;
    //console.log(__current_question);
    if( Object.keys(answers).length > 0 )
    {
        removeArrayIndex(__not_answered, __current_question);
        removeArrayIndex(__marked_review, __current_question);
        __answered[__current_question] = __current_question;
        $('#pallette_block_'+__current_question).removeClass('not-answerd-palle').removeClass('marked-pallet').addClass('answerd-pallet');
    }
    else
    {
        removeArrayIndex(__marked_review, __current_question);
        removeArrayIndex(__answered, __current_question);
        __not_answered[__current_question] = __current_question;
        $('#pallette_block_'+__current_question).removeClass('answerd-pallet').removeClass('marked-pallet').addClass('not-answerd-palle');        
    }
}

function saveSubjective()
{
    var answer = $('#subjective_answer').val();
        //console.log(__current_question);
        __answerSheet['answer'][__current_question] = answer;
    if( answer != '' )
    {
        removeArrayIndex(__not_answered, __current_question);
        removeArrayIndex(__marked_review, __current_question);
        __answered[__current_question] = __current_question;
        $('#pallette_block_'+__current_question).removeClass('not-answerd-palle').removeClass('marked-pallet').addClass('answerd-pallet');
    }
    else
    {
        removeArrayIndex(__marked_review, __current_question);
        removeArrayIndex(__answered, __current_question);
        __not_answered[__current_question] = __current_question;
        $('#pallette_block_'+__current_question).removeClass('answerd-pallet').removeClass('marked-pallet').addClass('not-answerd-palle');        
    }
}

function reviewAndSubmitExam()
{
    var totalQuestion       = Object.keys(__response['lecture']['questions']).length;
    var totalAnswered       = Object.keys(__answered).length;
    var totalNotAnswered    = Object.keys(__not_answered).length;
    var totalReview         = Object.keys(__marked_review).length;
    var notVisited          = 0;
    
    var notVisited          = Number(totalQuestion) - Number(Object.keys(__visitedQuestions).length);
    var summaryHtml = '';
    summaryHtml =`
    <div class="assesTimeWrap">
        <span class="time">Time&nbsp;-</span>
        <span class="time-counter" id="timer_display"></span>
    </div>
    <!-- assesTimeWrap -->
    <div class="assesment-anser-wrap">
        <div class="answer-row">
            <div class="left-side-answer">
                <div class="assesblock-wrap">
                    <span class="assesment-block assesment-magent"></span>
                    <span id="total_answered">Answered - `+totalAnswered+`</span>
                </div>
                <div class="assesblock-wrap">
                    <span class="assesment-block assesment-green"></span>
                    <span id="total_not_visited">Not Visited - `+notVisited+`</span>
                </div>
            </div>
            <!-- left-side-answer -->
            <div class="right-side-answer">
                <div class="assesblock-wrap">
                    <span class="assesment-block assesment-pink"></span>
                    <span id="total_not_answered">Not Answered - `+totalNotAnswered+`</span>
                </div>
                <div class="assesblock-wrap">
                    <span class="assesment-block assesment-grey"></span>
                    <span id="total_marked_review">Review - `+totalReview+`</span>
                </div>
            </div>

        </div>
        <!-- answer-row -->
    </div>
    <!-- assesment-anser-wrap -->
    <div class="assesment-btn-wrap">
        <input class="btn btn-grey btn-grey-right-margin" data-dismiss="modal" type="button" value="Review">
        <input class="btn btn-blue btn-blue-large" id="submit_exam_btn" type="button" onclick="submitExam()" value="Submit">
    </div>
`;
       
    $('#assesment-summary .modal-body').html(summaryHtml);
    $('#assesment-summary').modal();
}
    
function submitExam()
{
    timerAction('pause');
    stopAnsweringTimeLog();
    $('#submit_exam_btn').html('SUBMITTING...');
    updateLecturePercentage(__lecture_id,  100);
    $.ajax({
         url: __site_url+'material/save_exam',
         type: "POST",
         data:{ "is_ajax":true, 'time_taken':__time_taken, 'answer': JSON.stringify(__answerSheet), 'course_id':__course_id, 'lecture_id':__lecture_id, 'assesment_id':__response['lecture']['assesment']['assesment_id'], 'answer_time_log': JSON.stringify(__answeringTimeLog)},
         success: function(response) {
            var data = $.parseJSON(response);
            location.href = __site_url+'/material/assesment_report_item/'+data['attempt_id'] ;         
         }
     });
     
}

function updateLecturePercentage(lecture_id, percentage){
    percentage = parseInt(percentage);
    //console.log(lecture_id+' ajax called '+percentage);
    
    $.ajax({
        url: __site_url+'/material/percentage',
        type: "POST",
        data:{"is_ajax":true, 'lecture_id':lecture_id, 'percentage':percentage},
        success: function(response) {
            //console.log(response);
        }
    });
}

function filterQuestionByCategory(category_id)
{
    if(__category != category_id)
    {
        __category = category_id;
        $('#current_category_title').html('You are viewing <br/> '+__response['lecture']['categories'][__category]['qc_category_name']);
        __question_index = 0;
        renderQuestion();           
        renderQuestionPallette();
        $('#question_number_label').html('Question No : '+$('#pallette_block_'+__current_question).attr('data-id'));
    }
}

function renderQuestionPallette()
{
    var palletteHtml = '';
    var palletteClass = '';
        if(__response['lecture']['assesment']['a_show_categories'] == '1')
        {
            if(Object.keys(__response['lecture']['sl_no'][__category]).length > 0)
            {
                $.each(__response['lecture']['sl_no'][__category], function( index, value ) {
                    palletteClass = '';
                    if(inArray(value, __not_answered))
                    {
                        palletteClass = 'not-answerd-palle';
                    }
                    if(inArray(value, __answered))
                    {
                        palletteClass = 'answerd-pallet';
                    }
                    if(inArray(value, __marked_review))
                    {
                        palletteClass = 'marked-pallet';
                    }
                    palletteHtml += '<li class="qus_number '+palletteClass+'" id="pallette_block_'+value+'" onclick="loadQuestion('+value+')" data-id="'+index+'">'+index+'</li>';
                });
            }
        }
        else
        {
            if(Object.keys(__response['lecture']['questions']).length > 0)
            {
                var questionCount = 1;
                $.each(__response['lecture']['questions'], function( index, value ) {
                    palletteClass = '';
                    if(inArray(index, __not_answered))
                    {
                        palletteClass = 'not-answerd-palle';
                    }
                    if(inArray(index, __answered))
                    {
                        palletteClass = 'answerd-pallet';
                    }
                    if(inArray(index, __marked_review))
                    {
                        palletteClass = 'marked-pallet';
                    }
                    
                    palletteHtml += '<li class="qus_number '+palletteClass+'" id="pallette_block_'+index+'" onclick="loadQuestion('+index+')" data-id="'+questionCount+'">'+questionCount+'</li>';
                    questionCount++;
                });
            }
        }
        
    $('#question_pallette').html(palletteHtml);
}

function renderNextQuestion()
{
    __question_index++;
    if( __response['lecture']['assesment']['a_show_categories'] == '1' && typeof __response['lecture']['category_questions'][__category][__question_index] == 'undefined')
    {
        var lockCategory = false;
        if(Object.keys(__response['lecture']['categories']).length>0)
        {
           for (key in __response['lecture']['categories'])
           {
               if(lockCategory == true)
               {
                   __category   = key;
                   break;
               }
               if (__response['lecture']['categories'].hasOwnProperty(key))
               {
                   if(key==__category)
                   {
                       lockCategory = true;
                   }
               }
           }
       }
       if(lockCategory==true)
       {
            $('#categories li').removeClass('active');
            $('#category_tab_'+__category).addClass('active');
            $('#current_category_title').html('You are viewing <br/> '+__response['lecture']['categories'][__category]['qc_category_name']);
            __question_index = 0;
            renderQuestionPallette();
            renderQuestion();           
       }
       else
       {
            saveAnswer();
       }
    }
    else
    {
        renderQuestion();
    }
}

function renderSingleChoice(lecture)
{
        __current_question = lecture['id'];
        __visitedQuestions[__current_question] = __current_question;
        startAnsweringTimeLog();
    var questionHtml = '';
        questionHtml +='<div class="qus_sheet one">';
        if(lecture['q_directions'] != '')
        {
            questionHtml +='    <div class="question question-direction"><h3>Direction</h3>'+lecture['q_directions']+'</div><br />';            
        }
        questionHtml +='    <div class="question question-table">'+lecture['q_question']+'</div>';
        questionHtml +='    <div class="options_main">';
        if(lecture['options'].length > 0 )
        {
           for(var i=0;i<lecture['options'].length;i++)
           {
                questionHtml +='        <div class="check">';
                questionHtml +='            <input type="radio" '+(((typeof __answerSheet['answer'][lecture['id']] != 'undefined') && (lecture['options'][i]['id'] == __answerSheet['answer'][lecture['id']]))?__checked:'')+' name="radio_option" id="option_'+lecture['options'][i]['id']+'" value="'+lecture['options'][i]['id']+'" class="checkBox_ question-options"> ';
                questionHtml +='            <label for="name1" class="checkText"><span></span></label>';
                questionHtml +='            <div>'+lecture['options'][i]['qo_options']+'</div></div>';
           }
        }
        questionHtml +='    </div>';
        questionHtml +='</div>';
    $('#question_content').html(questionHtml);
    $('#question_type_label').html('Question Type : '+__response['lecture']['question_types'][lecture['q_type']][0]);
    $('#question_number_label').html('Question No : '+$('#pallette_block_'+lecture['id']).attr('data-id'));
}

function renderMultipleChoice(lecture)
{
        __current_question = lecture['id'];
        __visitedQuestions[__current_question] = __current_question;
        startAnsweringTimeLog();
    var questionHtml = '';
        questionHtml +='<div class="qus_sheet one">';
        if(lecture['q_directions'] != '')
        {
            questionHtml +='    <div class="question question-direction"><h3>Direction</h3>'+lecture['q_directions']+'</div><br />';            
        }
        questionHtml +='    <div class="question question-table">'+lecture['q_question']+'</div>';
        questionHtml +='    <div class="options_main">';
        if(lecture['options'].length > 0 )
        {
           for(var i=0;i<lecture['options'].length;i++)
           {
                questionHtml +='        <div class="check">';
                questionHtml +='            <input type="checkbox" '+(((typeof __answerSheet['answer'][lecture['id']] != 'undefined') && (inArray(lecture['options'][i]['id'], __answerSheet['answer'][lecture['id']])))?__checked:'')+' id="option_'+lecture['options'][i]['id']+'" value="'+lecture['options'][i]['id']+'" class="checkBox_ question-options"> ';
                //questionHtml +='            <input type="checkbox" '+(((typeof __answerSheet['answer'][lecture['id']] != 'undefined'))?__checked:'')+' id="option_'+lecture['options'][i]['id']+'" value="'+lecture['options'][i]['id']+'" class="checkBox_ question-options"> ';
                questionHtml +='            <label for="name1" class="checkText"><span></span></label>';
                questionHtml +='            <div>'+lecture['options'][i]['qo_options']+'</div></div>';
           }
        }
        questionHtml +='    </div>';
        questionHtml +='</div>';
    $('#question_content').html(questionHtml);
    $('#question_type_label').html('Question Type : '+__response['lecture']['question_types'][lecture['q_type']][0]);
    $('#question_number_label').html('Question No : '+$('#pallette_block_'+lecture['id']).attr('data-id'));
}

function renderSubjective(lecture)
{
        __current_question = lecture['id'];
        __visitedQuestions[__current_question] = __current_question;
        startAnsweringTimeLog();
    var questionHtml = '';
        questionHtml +='<div class="qus_sheet one">';
        if(lecture['q_directions'] != '')
        {
            questionHtml +='    <div class="question question-direction"><h3>Direction</h3>'+lecture['q_directions']+'</div><br />';            
        }
        questionHtml +='    <div class="question question-table">'+lecture['q_question']+'</div>';
        questionHtml +='    <div class="options_main">';
        questionHtml +='        <div class="check"><textarea id="subjective_answer" style="width: 100%;height: 200px">'+((typeof __answerSheet['answer'][lecture['id']] != 'undefined' && __answerSheet['answer'][lecture['id']] !== null)?__answerSheet['answer'][lecture['id']]:'')+'</textarea></div>';
        questionHtml +='    </div>';
        questionHtml +='</div>';
    $('#question_content').html(questionHtml);
    $('#question_type_label').html('Question Type : '+__response['lecture']['question_types'][lecture['q_type']][0]);
    $('#question_number_label').html('Question No : '+$('#pallette_block_'+lecture['id']).attr('data-id'));
}

function changeQuestionType(questionTypeId)
{
    var questionLabel = '';
        questionLabel = 'All';
    if(questionTypeId!='')
    {
        questionLabel = __response['lecture']['question_types'][questionTypeId][0];
    }
    $('#question_type_label').html('Question Type : '+questionLabel);
}

function initDomActions()
{
    $('.selectpicker').selectpicker('refresh');
    autocollapse(); // when document first loads
    $(window).on('resize', autocollapse); // when window is resized
    
    var wh = $(window).height();
    var lw = $('.legend').outerHeight();
    var tw = $('.time').outerHeight();
    var qw = $('.buttons').outerHeight();
    var tlw = lw + tw + qw + 2;


    $('.col-sm-3.wd2 .content').css('height', wh - tlw + "px");

    var lhw = $('.sbi').outerHeight();
    var llw = $('#categories').outerHeight();
    var ltw = $('.tabs_top').outerHeight();
    var lqw = $('.qus_heading').outerHeight();
    var ltlw = lhw + llw + ltw + lqw + 21;
    $('.col-sm-9.wd .content').css('height', wh - ltlw + "px");
    
    
    // Owl Slider
    $("#team").owlCarousel({
        items: 9,
        lazyLoad: true,
        navigation: true,
        auto: true
    });

    /*$(".qus_sheet").hide();
    $(".one").show();
    $(".qus_number").on("click", function () {
        var id = $(this).data("id");
        $(".content .qus_sheet").hide();
        $("#qus" + id).fadeIn(200);
    });*/
    $("#open").click(function () {
        $(".mobile_qus").addClass("show");
        $("#open").hide();
        $("#close").show();
    });
    $("#close").click(function () {
        $(".mobile_qus").removeClass("show");
        $("#open").show();
        $("#close").hide();
    });
    
    //initializing the timer
    var duration = __response['lecture']['assesment']['a_duration']*60;
    startTimer(duration);
    //End
        
    (function ($) {
        fakewaffle.responsiveTabs(['xs', 'sm']);
    })(jQuery);
}

var timeRunning = false;
var __time_taken    = 0;
function startTimer(duration) {
    var tempDuration = duration;//10
    var timer = tempDuration, minutes, seconds;
    timeRunning = true;
    setInterval(function () {
        if(timeRunning == true)
        {
            minutes = parseInt(timer / 60, 10)
            seconds = parseInt(timer % 60, 10);

            minutes = minutes < 10 ? "0" + minutes : minutes;
            seconds = seconds < 10 ? "0" + seconds : seconds;

            $('#time_out_label, #timer_display').html(minutes + " : " + seconds);

            //__time_taken = parseInt(duration - parseInt((minutes*60)+seconds));
            //if (--timer < 0) {
            --timer;
            __time_taken++;
            if (__time_taken >= duration) {
                $('#time_out_label, #timer_display').html( "00 : 00");
                submitExam();
            }
        }
    }, 1000);
}

function timerAction(action)
{
    switch(action)
    {
        case "resume":
            timeRunning = true;
            break;
        case "pause":
            timeRunning = false;
            break;
    }
}

$(window).resize(function () {
    var wh = $(window).height();
    var lw = $('.legend').outerHeight();
    var tw = $('.time').outerHeight();
    var qw = $('.buttons').outerHeight();
    var tlw = lw + tw + qw + 2;


    $('.col-sm-3.wd2 .content').css('height', wh - tlw + "px");

    var lhw = $('.sbi').outerHeight();
    var llw = $('#categories').outerHeight();
    var ltw = $('.tabs_top').outerHeight();
    var lqw = $('.qus_heading').outerHeight();
    var ltlw = lhw + llw + ltw + lqw + 21;
    $('.col-sm-9.wd .content').css('height', wh - ltlw + "px");
});

//Tabs
$(document).on('click', '#categories a', function(e){
    e.preventDefault();
    $(this).tab('show');
    $('.selectpicker').val('').selectpicker('refresh');
});

$(document).on('click', '#question_pallette a', function(e){
   __question_index = $(this).index();
});


function inArray(needle, haystack) {
    if(typeof haystack == 'object')
    {
        var hasIndex = false;
        if(typeof haystack[needle] != 'undefined')
        {
            hasIndex = true;
        }
        return hasIndex;
    }
    else
    {
        var length = haystack.length;
        for(var i = 0; i < length; i++) {
            if(haystack[i] == needle) return true;
        }
        return false;        
    }
}

function removeArrayIndex(array, index) {
    if(typeof array == 'object')
    {
        delete array[index];
    }
    else
    {
      for(var i = array.length; i--;) {
          if(array[i] === index) {
              array.splice(i, 1);
          }
      }        
    }
}


var __tempAnsweringTimeLog  = new Object;
var __answeringTimeLog      = new Object;
var __answeringTimeInterval = null;
function startAnsweringTimeLog()
{
    stopAnsweringTimeLog();
    if( __current_question != null && __current_question > 0 )
    {
        if(typeof __tempAnsweringTimeLog[__current_question] == 'undefined')
        {
            __tempAnsweringTimeLog[__current_question] = 0;
        }
        __answeringTimeInterval = setInterval(function(){ 
            __tempAnsweringTimeLog[__current_question] = Number(__tempAnsweringTimeLog[__current_question]) + 1;
        }, 1000);
    }    
}

function stopAnsweringTimeLog()
{
    clearInterval(__answeringTimeInterval);
}

function saveAnsweringTimeLog()
{
    stopAnsweringTimeLog();
    if( __current_question != null && __current_question > 0 )
    {
        //defined time not defined in tempory array
        if(typeof __tempAnsweringTimeLog[__current_question] == 'undefined')
        {
            __tempAnsweringTimeLog[__current_question] = 0;
        }
        //defined time not defined in main array
        if(typeof __answeringTimeLog[__current_question] == 'undefined')
        {
            __answeringTimeLog[__current_question] = 0;
        }
        //Total time is sum of temporary time and main time
        __answeringTimeLog[__current_question]      = Number(__answeringTimeLog[__current_question])+Number(__tempAnsweringTimeLog[__current_question]);
        __tempAnsweringTimeLog[__current_question]  = 0;
    }        
}

