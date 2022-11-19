$(document).ready(function(){
    $('#load_exam_button').click({}, loadExamAssets);    
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
         url: __site_url+'/material/challenge_assets',
         type: "POST",
         data:{ "is_ajax":true, 'challenge_id':__challenge_id},
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
            if(  __response['challenge']['cz_show_categories'] == '1' && Object.keys(__response['challenge']['categories']).length>0)
            {
                var categoryIndex = 0;
                $.each(__response['challenge']['categories'], function( index, value ) {
                    categoryHtml += '<li class="'+activeLi+'" onclick="filterQuestionByCategory(\''+index+'\');"  id="category_tab_'+index+'"><a href="#category_tabs">'+value['qc_category_name']+'</a></li>';
                    activeLi      = '';
                    __category    = (__category==null)?index:__category;
                });
                categoryHtml += '<li id="lastTab"><a class="btn dropdown-toggle" data-toggle="dropdown" href="javascript:void(0)">More <span class="caret"></span></a>';
                categoryHtml += '    <ul class="dropdown-menu" id="collapsed"></ul>';
                categoryHtml += '</li>';

                $('#categories').html(categoryHtml);
                $('#current_category_title').html('You are viewing <br/> '+__response['challenge']['categories'][__category]['qc_category_name']);
            }
            else
            {
                $('#current_category_title').html('You are viewing all questions');                
            }
            
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
            $('#load_exam_button').html('Resume Challenge');
            $('#load_exam_button').click({}, resumeChallenge);    
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
    var questionPaperIndex = Object.keys(__response['challenge']['questions']);
    var challenge = null;
    if(questionPaperIndex.length > 0 )
    {
        for (var key in questionPaperIndex)
        {
            if (questionPaperIndex.hasOwnProperty(key))
            {
                challenge = __response['challenge']['questions'][questionPaperIndex[key]];
                switch (challenge['q_type'])
                {
                    case "1":
                        questionPaperHtml +='<div class="qus_sheet question_paper one">';
                        questionPaperHtml +='    <div class="question">'+challenge['q_question']+'</div>';
                        questionPaperHtml +='    <div class="options_main">';
                        if(challenge['options'].length > 0 )
                        {
                           for(var i=0;i<challenge['options'].length;i++)
                           {
                                questionPaperHtml +='        <div class="check">';
                                questionPaperHtml +='            <input type="radio" disabled="disabled" class="checkBox_ "> ';
                                questionPaperHtml +='            <label for="name1" class="checkText"><span></span></label>';
                                questionPaperHtml +='            <div>'+challenge['options'][i]['qo_options']+'</div></div>';
                           }
                        }
                        questionPaperHtml +='    </div>';
                        questionPaperHtml +='</div>';
                    break;
                    case "2":
                            questionPaperHtml +='<div class="qus_sheet question_paper one">';
                            questionPaperHtml +='    <div class="question">'+challenge['q_question']+'</div>';
                            questionPaperHtml +='    <div class="options_main">';
                            if(challenge['options'].length > 0 )
                            {
                               for(var i=0;i<challenge['options'].length;i++)
                               {
                                    questionPaperHtml +='        <div class="check">';
                                    questionPaperHtml +='            <input type="checkbox" disabled="disabled" class="checkBox_ "> ';
                                    questionPaperHtml +='            <label for="name1" class="checkText"><span></span></label>';
                                    questionPaperHtml +='            <div>'+challenge['options'][i]['qo_options']+'</div></div>';
                               }
                            }
                            questionPaperHtml +='    </div>';
                            questionPaperHtml +='</div>';
                    break;
                    case "3":
                        questionPaperHtml +='<div class="qus_sheet question_paper one">';
                        questionPaperHtml +='    <div class="question">'+challenge['q_question']+'</div>';
                        questionPaperHtml +='    <div class="options_main">';
                        questionPaperHtml +='    </div>';
                        questionPaperHtml +='</div>';
                    break;
                }
            }
        }
    }
    questionPaperHtml +='<a class="btn btn-altr" href="javascript:void(0)" onclick="resumeChallenge()">Resume Exam</a>';
    $('#quesion_paper_tab .question-content').html(questionPaperHtml);    
}

function renderInstruction()
{
    timerAction('pause');
    stopAnsweringTimeLog();
    $('#exam_asset_tab').css('display', 'none');
    $('#instruction_tab').css('display', 'block');
}

function resumeChallenge()
{
    timerAction('resume');
    startAnsweringTimeLog();
    $('#exam_asset_tab').css('display', 'block');
    $('#instruction_tab').css('display', 'none');
    $('#quesion_paper_tab').css('display', 'none');
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
    var challenge = null;
    if(__response['challenge']['cz_show_categories'] == '1' )
    {
        if(__category != null )
        {
            challenge = __response['challenge']['questions'][__response['challenge']['category_questions'][__category][__question_index]];
        } 
    }
    else
    {
        if(Object.keys(__response['challenge']['questions']).length > 0)
        {
            var currentIndex = 0;
            $.each(__response['challenge']['questions'], function( index, value ) {
                if( __question_index == currentIndex)
                {
                    challenge = value;
                    return false;
                }
                currentIndex++;
            });
        };
        
    }
    if(challenge != null )
    {
        switch (challenge['q_type'])
        {
            case "1":
                renderSingleChoice(challenge);
                break;
            case "2":
                renderMultipleChoice(challenge);
                break;
            case "3":
                renderSubjective(challenge);
                break;
        }
        //console.log(challenge);    
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
    $('#pallette_block_'+temp_question_id).removeClass('red_bg').removeClass('green_bg').addClass('purpal_bg');
}

function SaveAndNext()
{
    var lecture = __response['challenge']['questions'][__current_question];
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
}

function saveAnswer()
{
    var challenge = __response['challenge']['questions'][__current_question];
    switch (challenge['q_type'])
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

function reviewAndSubmitExam()		
{		
    var totalQuestion       = Object.keys(__response['challenge']['questions']).length;		
    var totalAnswered       = Object.keys(__answered).length;		
    var totalNotAnswered    = Object.keys(__not_answered).length;		
    var totalReview         = Object.keys(__marked_review).length;		
    var notVisited          = Number(totalQuestion) - Number(Object.keys(__visitedQuestions).length);
    var summaryHtml = '';		
        summaryHtml += '<div class="timmer-wraper">';		
        summaryHtml += '    <span class="timmer" id="timer_display"></span>';		
        summaryHtml += '</div>';		
        summaryHtml += '<div class="container-fluid modal-content-wrapper">';		
        summaryHtml += '    <span class="answer-row clearfix">';		
        summaryHtml += '        <span class="green-ans-wrap">';		
        summaryHtml += '            <span class="green-block"></span>';		
        summaryHtml += '            <span class="block-texts">Answered - '+totalAnswered+'</span>';		
        summaryHtml += '        </span>';		
        summaryHtml += '        <span class="red-ans-wrap">';		
        summaryHtml += '            <span class="red-block"></span>';		
        summaryHtml += '            <span class="block-texts">Not answered - '+totalNotAnswered+'</span>';		
        summaryHtml += '        </span>';		
        summaryHtml += '    </span>';		
        summaryHtml += '    <span class="answer-row clearfix">';		
        summaryHtml += '        <span class="green-ans-wrap">';		
        summaryHtml += '            <span class="green-block blue-block"></span>';		
        summaryHtml += '            <span class="block-texts">Marked Review - '+totalReview+'</span>';		
        summaryHtml += '        </span>';		
        summaryHtml += '        <span class="red-ans-wrap">';		
        summaryHtml += '            <span class="red-block not-vis"></span>';		
        summaryHtml += '            <span class="block-texts">Not Visited - '+notVisited+'</span>';		
        summaryHtml += '        </span>';		
        summaryHtml += '    </span>';		
        summaryHtml += '    <div class="modal-footer-btn">';		
        summaryHtml += '        <a href="javascript:void(0)" id="submit_exam_btn" onclick="submitExam()" class="btn modalorange-btn modal-width-expander">Submit</a>';		
        summaryHtml += '        <a href="javascript:void(0)" data-dismiss="modal" class="btn modalGrey-btn modal-width-expander">Review</a>';		
        summaryHtml += '    </div>';		
        summaryHtml += '</div>';		
    $('#assesment-summary .modal-body').html(summaryHtml);		
    $('#assesment-summary').modal();		
}
        
function ClearResponse()
{
    var challenge = __response['challenge']['questions'][__current_question];
    if(typeof __answerSheet['answer'][challenge['id']] != 'undefined')
    {
       delete __answerSheet['answer'][__current_question];
    }
    if(typeof __answerSheet['review'][challenge['id']] != 'undefined')
    {
        delete __answerSheet['review'].pop(__current_question);
    }
    loadQuestion(__current_question);
}

function loadQuestion(question_id)
{
    var challenge = __response['challenge']['questions'][question_id];
    switch (challenge['q_type'])
    {
        case "1":
            renderSingleChoice(challenge);
            break;
        case "2":
            renderMultipleChoice(challenge);
            break;
        case "3":
            renderSubjective(challenge);
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
        $('#pallette_block_'+__current_question).removeClass('red_bg').removeClass('purpal_bg').addClass('green_bg');
    }
    else
    {
        removeArrayIndex(__marked_review, __current_question);
        removeArrayIndex(__answered, __current_question);
        __not_answered[__current_question] = __current_question;
        $('#pallette_block_'+__current_question).removeClass('green_bg').removeClass('purpal_bg').addClass('red_bg');        
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
        $('#pallette_block_'+__current_question).removeClass('red_bg').removeClass('purpal_bg').addClass('green_bg');
    }
    else
    {
        removeArrayIndex(__marked_review, __current_question);
        removeArrayIndex(__answered, __current_question);
        __not_answered[__current_question] = __current_question;
        $('#pallette_block_'+__current_question).removeClass('green_bg').removeClass('purpal_bg').addClass('red_bg');        
    }
}

function submitExam()
{
    timerAction('pause');
    stopAnsweringTimeLog();
    $('#submit_exam_btn').html('SUBMITTING...');
    console.log(__answerSheet);
    $.ajax({
         url: __site_url+'/material/save_challenge',
         type: "POST",
         data:{ "is_ajax":true, 'time_taken':__time_taken, 'answer': JSON.stringify(__answerSheet), 'challenge_id':__challenge_id, 'answer_time_log': JSON.stringify(__answeringTimeLog)},
         success: function(response) {
            var data = $.parseJSON(response);
            location.href = __site_url+'/material/challenge_zone_report_item/'+data['attempt_id']
         }
     });
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
        $('#pallette_block_'+__current_question).removeClass('red_bg').removeClass('purpal_bg').addClass('green_bg');
    }
    else
    {
        removeArrayIndex(__marked_review, __current_question);
        removeArrayIndex(__answered, __current_question);
        __not_answered[__current_question] = __current_question;
        $('#pallette_block_'+__current_question).removeClass('green_bg').removeClass('purpal_bg').addClass('red_bg');        
    }
}

function filterQuestionByCategory(category_id)
{
    if(__category != category_id)
    {
        __category = category_id;
        $('#current_category_title').html('You are viewing <br/> '+__response['challenge']['categories'][__category]['qc_category_name']);
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
        if(__response['challenge']['cz_show_categories'] == '1')
        {
            if(Object.keys(__response['challenge']['sl_no'][__category]).length > 0)
            {
                $.each(__response['challenge']['sl_no'][__category], function( index, value ) {
                    palletteClass = '';
                    if(inArray(value, __not_answered))
                    {
                        palletteClass = 'red_bg';
                    }
                    if(inArray(value, __answered))
                    {
                        palletteClass = 'green_bg';
                    }
                    if(inArray(value, __marked_review))
                    {
                        palletteClass = 'purpal_bg';
                    }
                    palletteHtml += '<a class="qus_number '+palletteClass+'" id="pallette_block_'+value+'" onclick="loadQuestion('+value+')" data-id="'+index+'">'+index+'</a>';
                });
            }
        }
        else
        {
            if(Object.keys(__response['challenge']['questions']).length > 0)
            {
                var questionCount = 1;
                $.each(__response['challenge']['questions'], function( index, value ) {
                    palletteClass = '';
                    if(inArray(index, __not_answered))
                    {
                        palletteClass = 'red_bg';
                    }
                    if(inArray(index, __answered))
                    {
                        palletteClass = 'green_bg';
                    }
                    if(inArray(index, __marked_review))
                    {
                        palletteClass = 'purpal_bg';
                    }
                    palletteHtml += '<a class="qus_number '+palletteClass+'" id="pallette_block_'+index+'" onclick="loadQuestion('+index+')" data-id="'+questionCount+'">'+questionCount+'</a>';
                    questionCount++;
                });
            }
        }
    $('#question_pallette').html(palletteHtml);
}

function renderNextQuestion()
{
    __question_index++;
    if(  __response['challenge']['cz_show_categories'] == '1' &&  typeof __response['challenge']['category_questions'][__category][__question_index] == 'undefined')
    {
        var lockCategory = false;
        if(Object.keys(__response['challenge']['categories']).length>0)
        {
           for (key in __response['challenge']['categories'])
           {
               if(lockCategory == true)
               {
                   __category   = key;
                   break;
               }
               if (__response['challenge']['categories'].hasOwnProperty(key))
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
            $('#current_category_title').html('You are viewing <br/> '+__response['challenge']['categories'][__category]['qc_category_name']);
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

function renderMultipleChoice(challenge)
{
        __current_question = challenge['id'];
        __visitedQuestions[__current_question] = __current_question;
        startAnsweringTimeLog();
    var questionHtml = '';
        questionHtml +='<div class="qus_sheet one">';
        if(challenge['q_directions'] != '')
        {
            questionHtml +='    <div class="question question-direction"><h3>Direction</h3>'+challenge['q_directions']+'</div><br />';            
        }
        questionHtml +='    <div class="question">'+challenge['q_question']+'</div>';
        questionHtml +='    <div class="options_main">';
        if(challenge['options'].length > 0 )
        {
           for(var i=0;i<challenge['options'].length;i++)
           {
                questionHtml +='        <div class="check">';
                questionHtml +='            <input type="checkbox" '+(((typeof __answerSheet['answer'][challenge['id']] != 'undefined') && (inArray(challenge['options'][i]['id'], __answerSheet['answer'][challenge['id']])))?__checked:'')+' id="option_'+challenge['options'][i]['id']+'" value="'+challenge['options'][i]['id']+'" class="checkBox_ question-options"> ';
                //questionHtml +='            <input type="checkbox" '+(((typeof __answerSheet['answer'][lecture['id']] != 'undefined'))?__checked:'')+' id="option_'+lecture['options'][i]['id']+'" value="'+lecture['options'][i]['id']+'" class="checkBox_ question-options"> ';
                questionHtml +='            <label for="name1" class="checkText"><span></span></label>';
                questionHtml +='            <div>'+challenge['options'][i]['qo_options']+'</div></div>';
           }
        }
        questionHtml +='    </div>';
        questionHtml +='</div>';
    $('#question_content').html(questionHtml);
    $('#question_type_label').html('Question Type : '+__response['challenge']['question_types'][challenge['q_type']][0]);
    $('#question_number_label').html('Question No : '+$('#pallette_block_'+challenge['id']).attr('data-id'));
}

function renderSingleChoice(challenge)
{
        __current_question = challenge['id'];
        __visitedQuestions[__current_question] = __current_question;
        startAnsweringTimeLog();
    var questionHtml = '';
        questionHtml +='<div class="qus_sheet one">';
        if(challenge['q_directions'] != '')
        {
            questionHtml +='    <div class="question question-direction"><h3>Direction</h3>'+challenge['q_directions']+'</div><br />';            
        }
        questionHtml +='    <div class="question">'+challenge['q_question']+'</div>';
        questionHtml +='    <div class="options_main">';
        if(challenge['options'].length > 0 )
        {
           for(var i=0;i<challenge['options'].length;i++)
           {
                questionHtml +='        <div class="check">';
                questionHtml +='            <input type="radio" '+(((typeof __answerSheet['answer'][challenge['id']] != 'undefined') && (challenge['options'][i]['id'] == __answerSheet['answer'][challenge['id']]))?__checked:'')+' name="radio_option" id="option_'+challenge['options'][i]['id']+'" value="'+challenge['options'][i]['id']+'" class="checkBox_ question-options"> ';
                questionHtml +='            <label for="name1" class="checkText"><span></span></label>';
                questionHtml +='            <div>'+challenge['options'][i]['qo_options']+'</div></div>';
           }
        }
        questionHtml +='    </div>';
        questionHtml +='</div>';
    $('#question_content').html(questionHtml);
    $('#question_type_label').html('Question Type : '+__response['challenge']['question_types'][challenge['q_type']][0]);
    $('#question_number_label').html('Question No : '+$('#pallette_block_'+challenge['id']).attr('data-id'));
}

function renderSubjective(challenge)
{
        __current_question = challenge['id'];
        __visitedQuestions[__current_question] = __current_question;
        startAnsweringTimeLog();
    var questionHtml = '';
        questionHtml +='<div class="qus_sheet one">';
        if(challenge['q_directions'] != '')
        {
            questionHtml +='    <div class="question question-direction"><h3>Direction</h3>'+challenge['q_directions']+'</div><br />';            
        }
        questionHtml +='    <div class="question">'+challenge['q_question']+'</div>';
        questionHtml +='    <div class="options_main">';
        questionHtml +='        <div class="check"><textarea id="subjective_answer" style="width: 100%;height: 200px">'+((typeof __answerSheet['answer'][challenge['id']] != 'undefined' && __answerSheet['answer'][challenge['id']] !== null)?__answerSheet['answer'][challenge['id']]:'')+'</textarea></div>';
        questionHtml +='    </div>';
        questionHtml +='</div>';
    $('#question_content').html(questionHtml);
    $('#question_type_label').html('Question Type : '+__response['challenge']['question_types'][challenge['q_type']][0]);
    $('#question_number_label').html('Question No : '+$('#pallette_block_'+challenge['id']).attr('data-id'));
}

function changeQuestionType(questionTypeId)
{
    var questionLabel = '';
        questionLabel = 'All';
    if(questionTypeId!='')
    {
        questionLabel = __response['challenge']['question_types'][questionTypeId][0];
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
    var duration = __response['challenge']['cz_duration']*60;
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