function loadingPopUpOnStart() {
    $('#start_test').hide();
    $('.instruction-text').html('Please wait we load your exam...');
    $('#instruction_content_wrapper').html('').css('text-align', 'center');    
    $('#instruction_popup').show();
    $('#close_button').removeAttr('style');
    $('#portfolio-popup').addClass("portfolio-popup-show");
    $('#portfolio-popup').animate({
        "opacity": 1
    }, 200);
}

function closeloadingPopUpOnStart() {
    $('#start_test').removeAttr('style');
    $('.instruction-text').html('Here are some important instructions to be followed');
    $('#test_wrapper').css('visibility', 'visible');
    closeOverview();
    $('#close_button').removeAttr('style');
    $('#start_test span').html('RESUME TEST');
    $('#start_test').unbind('click');
    $('#start_test').click(function () {
        closeOverview();
    });
}
var Settings = function () {
    var configs = new Configs();
    var _assesmentSettings = configs._assesmentSettings;
    //var _webLanguages = configs._webLanguages;
    var _assesmentId = configs._assesmentId;
    var _attemptId = configs._attemptId;
    var _testObjectStack = new Object;

    this.config = function () {
        return configs;
    };
    this.getSetting = function (key) {
        return (typeof _assesmentSettings[key]) ? _assesmentSettings[key] : '';
    };
    this.getInstruction = function () {
        return (typeof _assesmentSettings['a_instructions']) ? _assesmentSettings['a_instructions'] : '';
    };
    // this.getLanguages = function () {
    //     return _webLanguages;
    // };
    this.getAssesmentId = function () {
        return _assesmentId;
    };
    this.getAttemptId = function () {
        return _attemptId;
    };
    this.setAttemptId = function (attempt_id) {
        _attemptId = attempt_id;
    };

    this.loadTestAssets = function () {
        $.ajax({
            url: configs._siteUrl + 'material/test_assets/' + this.getAssesmentId() + '/' + this.getAttemptId(),
            type: "POST",
            async: false,
            data: { "is_ajax": true ,'token':__url_token},
            success: function (response) {
                _testObjectStack = $.parseJSON(response);
                closeloadingPopUpOnStart();
            }
        });
    };
    this.testObject = function () {
        //console.log(_testObjectStack);
        return _testObjectStack;
    };

};

var __settings = new Settings();
var __rowCount = 10, __miniView = false, __mobileView = false;
$(document).ready(function () {
    loadingPopUpOnStart();
    $(window).resize(function () {
        refreshPageView(true);
    });
    refreshPageView(false);
    __settings.loadTestAssets();
    if (Object.keys(__settings.testObject()['marked_preview']).length > 0) {
        $.each(__settings.testObject()['marked_preview'], function (index, question_id) {
            if( question_id != '' ) {
                __marked_review[question_id] = question_id;
                __visitedQuestions[question_id] = question_id;    
            }
        });
    }
    if (Object.keys(__settings.testObject()['attended_questions']).length > 0) {
        $.each(__settings.testObject()['attended_questions'], function (index, answer) {
            __visitedQuestions[answer['ar_question_id']] = answer['ar_question_id'];
            if (answer['ar_answer']) {
                __answered[answer['ar_question_id']] = answer['ar_question_id'];
                if (__settings.testObject()['questions'][answer['ar_question_id']]['q_type'] == 2) {
                    var answer_temp = new Object;
                    var splitAnswer = answer['ar_answer'].split(",");
                    for (var i = 0; i < splitAnswer.length; i++) {
                        answer_temp[splitAnswer[i]] = splitAnswer[i];
                    }
                    __answerSheet['answer'][answer['ar_question_id']] = answer_temp;
                }
                else {
                    __answerSheet['answer'][answer['ar_question_id']] = answer['ar_answer'];
                }
            }
            else {
                __not_answered[answer['ar_question_id']] = answer['ar_question_id'];
            }
        });
    }
    // alert(__settings.getInstruction(1));
    $('#instruction_content_wrapper').html(__settings.getInstruction(1)[1]);
    $('#instruction_popup').show();
    $('#portfolio-popup').addClass("portfolio-popup-show");
    $('#portfolio-popup').animate({
        "opacity": 1
    }, 200);
    $('#start_test').click(function () {
        renderTestHtml(__settings.testObject());
        $('#test_wrapper').css('visibility', 'visible');
        closeOverview();
        $('#close_button').removeAttr('style');
        $('#start_test span').html('RESUME TEST');
        $('#start_test').unbind('click');
        $('#start_test').click(function () {
            closeOverview();
        });
    });
});

function selectLanguage(language_id) {
    $('#instruction_content_wrapper').html(__settings.getInstruction(language_id));
}

$(document).on('click', '#question_pallette li', function (e) {
    // if ( inArray(__current_question, __marked_review) == false ) {
    //     __unsavedChanges = false;
    // }
    if ( inArray(__current_question, __marked_review) == false ) {
        if(isThereUnsavedChanges() === true) {
            saveCurrentQuestionPopUp();
            return false;
        }
    }
    if(__settings.getSetting('a_qgrouping') == '1' && __settings.getSetting('a_limit_navigation') == '0'){
        __question_index = parseInt($(this).attr('data-type')) - 1;
    }
    else if (__settings.getSetting('a_limit_navigation') == '0') {
        __question_index = parseInt($(this).attr('data-id')) - 1;
    }
});

function refreshPageView(refreshPallete) {
    var _windowWidth = $(window).width();
    if (__settings.getSetting('a_limit_navigation') == '1') {
        $('.arrow-left-dot, .arrow-right-dot').hide();
    }
    if (_windowWidth <= 991) {
        $('.arrow-left-dot, .arrow-right-dot').hide();
        __rowCount = 6;
        __miniView = true;
        __mobileView = false;
        if (_windowWidth <= 320) {
            __mobileView = true;
            __rowCount = 5;
        }
    }
    else {
        if (__settings.getSetting('a_limit_navigation') == '0') {
            $('.arrow-left-dot, .arrow-right-dot').show();
        }
        __rowCount = 10;
        __miniView = false;
    }
    if (refreshPallete == true) {
        renderQuestionPallette();
        $('#pallette_block_' + __current_question).addClass('pallet-highlight');
    }
}

var __subject = null;
var __question_index = 0;
var __subject_wise_question_index = 0;
var __activeLanguage = null;
function renderTestHtml(testObject) {
    //loading test name
    $('#course-tile').html(__settings.getSetting('a_course_title'));
    $('#test_name').html(__settings.getSetting('a_title'));
    //loading question subjects
    if (__settings.getSetting('a_qgrouping') == '1' && Object.keys(testObject['subjects']).length > 0) {
        var subjectHtml = '';
        var currentSubject = null;
        $.each(testObject['subjects'], function (index, subject) {
            //console.log(subject);
            subjectHtml += '<li role="presentation"><a role="menuitem" tabindex="-1" id="subject_' + subject['subject_id'] + '" onclick="filterQuestionBySubject(\'' + subject['subject_id'] + '\');" href="javascript:void(0)">' + subject['qs_subject_name'] + '</a></li>';
            __subject = (__subject == null) ? subject['subject_id'] : __subject;
            currentSubject = (currentSubject == null) ? subject['qs_subject_name'] : currentSubject;
        });
        
        var testMapHtml = '';
        testMapHtml += 'Your are viewing : ';
        testMapHtml += '<div class="dropdown">';
        testMapHtml += '    <button class="btn btn-default btn-need-default dropdown-toggle" type="button" id="subject_menu" data-toggle="dropdown" aria-expanded="false">' + currentSubject;
        testMapHtml += '            <svg fill="#999999" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">';
        testMapHtml += '                <path d="M7.41 7.84L12 12.42l4.59-4.58L18 9.25l-6 6-6-6z"></path>';
        testMapHtml += '                <path d="M0-.75h24v24H0z" fill="none"></path>';
        testMapHtml += '            </svg>';
        testMapHtml += '        </button>';
        testMapHtml += '    <ul class="dropdown-menu" role="menu" aria-labelledby="menu1">';
        testMapHtml += subjectHtml;
        testMapHtml += '    </ul>';
        testMapHtml += '</div>';
        $('#test_map').html(testMapHtml).removeAttr('style');
        
    }
    
    //checking acccess for arrow button
    if (__settings.getSetting('a_limit_navigation') == '1') {
        $('.arrow-left-dot, .arrow-right-dot').hide();
    }
    
    //loading languages    
    
    //enable mark container in allowed
    if (__settings.getSetting('a_show_mark') == '1') {
        $('#positive_mark').parent().removeAttr('style');
    }
    //rendering question pallette
    renderQuestionPallette();
    
    //rendering question
    renderQuestion();
    
    //rendering question and total question tab
    $('#current_question').html('1');
    $('#total_question').html(Object.keys(__settings.testObject()['questions_order']).length);
    
    //initializing the timer
    var duration = (__settings.getSetting('a_duration') * 60);
    if (typeof __settings.testObject()['attempt'] != 'undefined') {
        duration = duration - __settings.testObject()['attempt']['aa_duration'];
    }
    minutesToTime(__settings.getSetting('a_duration') * 60)
    startTimer(duration);
    //End    
    
    //renderSwipe
    // $("#question_answer_wrapper").on("swipeleft", swipeleftHandler);
    // $("#question_answer_wrapper").on("swiperight", swiperightHandler);

    //attach save and next button function
    $('#save_and_next').unbind('click');
    // alert(__settings.getSetting('a_que_report'));
    if (__settings.getSetting('a_que_report') == '1') {
        $('#save_and_next').html('Save & Next').click(function () {
            $('#save_and_next').html('Submitting..');
            saveAnswer();
        });
    }
    else {
        $('#save_and_next').html('Save & Next').click(function () {
            //$('#save_and_next').html('Saving..');
            SaveAndNext();
        });
    }
}


// function swipeleftHandler(event) {
//     if (__miniView == true) {
//         nextQuestion();
//     }
// }

// function swiperightHandler(event) {
//     if (__miniView == true) {
//         previousQuestion();
//     }
// }


function filterQuestionBySubject(subject_id) {
    var subjectHtml = '';
    subjectHtml += $('#subject_' + subject_id).text();
    subjectHtml += '<svg fill="#999999" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">';
    subjectHtml += '    <path d="M7.41 7.84L12 12.42l4.59-4.58L18 9.25l-6 6-6-6z"></path>';
    subjectHtml += '    <path d="M0-.75h24v24H0z" fill="none"></path>';
    subjectHtml += '</svg>';
    if (__subject != subject_id) {
        __question_index = 0;
        __subject = subject_id;
        $('#subject_menu').html(subjectHtml);
        renderQuestionPallette();
        renderQuestion();
    }
}

function changeQuestionLanguage(language_id) {
    __activeLanguage = language_id;
    var languageWrapperHtml = $('#language_' + language_id).text();
    languageWrapperHtml += '        <svg fill="#999999" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">';
    languageWrapperHtml += '            <path d="M7.41 7.84L12 12.42l4.59-4.58L18 9.25l-6 6-6-6z"/>';
    languageWrapperHtml += '            <path d="M0-.75h24v24H0z" fill="none"/>';
    languageWrapperHtml += '        </svg>';
    $('#language_button').html(languageWrapperHtml);
    loadQuestion(__current_question);
}

function renderQuestion() {
    if(isThereUnsavedChanges() === true) {
        saveCurrentQuestionPopUp();
        return false;
    }
    var lecture = null;
    if (__settings.getSetting('a_qgrouping') == '1') {
        if (__subject != null) {
            var lecture = __settings.testObject()['questions'][__settings.testObject()['subject_questions'][__subject][__question_index]];  
        }
    }
    else {
        if (Object.keys(__settings.testObject()['questions_order']).length > 0) {
            var currentIndex = 0;
            $.each(__settings.testObject()['questions_order'], function (index, value) {
                if (__question_index == currentIndex) {
                    lecture = __settings.testObject()['questions'][value];
                    return false;
                }
                currentIndex++;
            });
        };
    }
    if (lecture != null) {
        switch (lecture['q_type']) {
            case "1":
                renderSingleChoice(lecture);
                break;
            case "2":
                renderMultipleChoice(lecture);
                break;
            case "3":
                renderSubjective(lecture);
                break;
            case "4":
                renderBlanks(lecture);
                break;
        }
        var currentQuestion = $('#pallette_block_' + lecture['id']).text();
        $('#current_question').html(currentQuestion);
        var percentage = (currentQuestion / Object.keys(__settings.testObject()['questions_order']).length) * 100;
        $('.progressbar-question-wrap .pogress-ans').css('width', percentage + '%');
        $('#question_pallette li').removeClass('pallet-highlight');
        $('#pallette_block_' + lecture['id']).addClass('pallet-highlight');
        if( currentQuestion == Object.keys(__settings.testObject()['questions_order']).length) {
            $('#save_and_next').html('Save');
            $('#next-question').hide();
        } 
        else 
        {
            $('#next-question').show();
        }
        if( currentQuestion == 1) 
        {
            $('#prev-question').hide();
        } 
        else 
        {
            $('#prev-question').show();
        }
        //rendering mark for review
        $('#mark_for_review_'+lecture['id']).hide();
        $('#discard_review_'+lecture['id']).hide();
   if (__settings.getSetting('a_attend_all') == '0' && __settings.getSetting('a_limit_navigation') == '0') {
       if ( inArray(lecture['id'], __marked_review) == true ) {
           $('#discard_review_'+lecture['id']).show();
           $('#mark_for_review_'+lecture['id']).hide();
       } else {
           $('#mark_for_review_'+lecture['id']).show();
           $('#discard_review_'+lecture['id']).hide();
       }
       $('#mark_for_review_'+lecture['id']).click(function () {
           $('#discard_review_'+lecture['id']).show();
           $('#mark_for_review_'+lecture['id']).hide();
           MarkForReview();
       });
       $('#discard_review_'+lecture['id']).click(function () {
           $('#mark_for_review_'+lecture['id']).show();
           $('#discard_review_'+lecture['id']).hide();
           DiscardReview();
       });
   }
    }
    if (__settings.getSetting('a_limit_navigation') == '1') {
        $('.arrow-left-dot, .arrow-right-dot').hide();
    }
}

function loadQuestion(question_id) {

    var lecture = __settings.testObject()['questions'][question_id];
    switch (lecture['q_type']) {
        case "1":
            renderSingleChoice(lecture);
            break;
        case "2":
            renderMultipleChoice(lecture);
            break;
        case "3":
            renderSubjective(lecture);
            break;
        case "4":
            renderBlanks(lecture);
            break;
    }
    var currentQuestion = $('#pallette_block_' + question_id).text();
    $('#current_question').html(currentQuestion);
    var percentage = (currentQuestion / Object.keys(__settings.testObject()['questions_order']).length) * 100;
    $('.progressbar-question-wrap .pogress-ans').css('width', percentage + '%');
    $('#question_pallette li').removeClass('pallet-highlight');
    $('#pallette_block_' + question_id).addClass('pallet-highlight');
    if( currentQuestion == Object.keys(__settings.testObject()['questions_order']).length) {
        $('#next-question').hide();
    }
    else 
    {
        $('#next-question').show();
    }
    if( currentQuestion == 1) 
    {
        $('#prev-question').hide();
    } 
    else 
    {
        $('#prev-question').show();
    }
    //rendering mark for review
    $('#mark_for_review_'+lecture['id']).hide();
    $('#discard_review_'+lecture['id']).hide();
    if (__settings.getSetting('a_attend_all') == '0' && __settings.getSetting('a_limit_navigation') == '0') {
        if ( inArray(lecture['id'], __marked_review) == true ) {
            $('#discard_review_'+lecture['id']).show();
            $('#mark_for_review_'+lecture['id']).hide();
        } else {
            $('#mark_for_review_'+lecture['id']).show();
            $('#discard_review_'+lecture['id']).hide();
        }
        $('#mark_for_review_'+lecture['id']).click(function () {
            $('#discard_review_'+lecture['id']).show();
            $('#mark_for_review_'+lecture['id']).hide();
            MarkForReview();
        });
        $('#discard_review_'+lecture['id']).click(function () {
            $('#mark_for_review_'+lecture['id']).show();
            $('#discard_review_'+lecture['id']).hide();
            DiscardReview();
        });
    }
}


var __current_question = null;
var __checked = 'checked="checked"';
var __selected = 'selected="selected"';


var __answered = new Object;
var __not_answered = new Object;
var __marked_review = new Object;
var __visitedQuestions = new Object;

var __answerSheet = new Object;
__answerSheet['review'] = new Object;
__answerSheet['answer'] = new Object;

function renderSingleChoice(lecture) {
    __current_question = lecture['id'];
    __visitedQuestions[__current_question] = __current_question;

    //register as not answer if the question was not answered
    if ( inArray(__current_question, __answered) == false ) {
        __not_answered[__current_question] = __current_question;
        $('#pallette_block_' + __current_question).removeClass('answerd-pallet').addClass('not-answerd-pallet');
    }
    //End

    startAnsweringTimeLog();
    var question = (typeof lecture['q_question'][__activeLanguage] != 'undefined') ? lecture['q_question'][__activeLanguage] : lecture['q_question'][1];

    var questionHtml = '';
    questionHtml += '<h1 class="h1-question" >' + question + '</h1>';
    questionHtml += '<div class="radioBtn-answers-wrap">';
    questionHtml += '    <div class="table-radio">';
    if (lecture['options'].length > 0) {
        for (var i = 0; i < lecture['options'].length; i++) {
            questionHtml += '        <div class="table-row-radio">';
            questionHtml += '            <input type="radio" ' + (((typeof __answerSheet['answer'][lecture['id']] != 'undefined') && (lecture['options'][i]['id'] == __answerSheet['answer'][lecture['id']])) ? __checked : '') + ' name="radio_option" id="option_' + lecture['options'][i]['id'] + '" value="' + lecture['options'][i]['id'] + '" class="question-options question-options-save" />';
            var option = (typeof lecture['options'][i]['qo_options'][__activeLanguage] != 'undefined') ? lecture['options'][i]['qo_options'][__activeLanguage] : lecture['options'][i]['qo_options'][1];
            questionHtml += '            <label for="option_' + lecture['options'][i]['id'] + '"><span></span> <div class="table-row-number"></div>' + option + '</label>';
            questionHtml += '        </div>';
        }
    }

    questionHtml += '    </div>';
    questionHtml += '</div>';
    $('#question_answer_wrapper').html(questionHtml);
    var markReviewHtml  = '';
    markReviewHtml      += '<input type="button" id="mark_for_review_'+__current_question+'" class="btn btn-footer" value="Mark as review">';
    markReviewHtml      += '<input type="button" style="background:linear-gradient(135deg, #6489d3 0%, #94a0f0 100%)" id="discard_review_'+__current_question+'" class="btn btn-footer" value="Discard Review">';
    $('#mark_review_html').html(markReviewHtml);
    $('#question_type_label').html(__settings.testObject()['question_types'][lecture['q_type']][1]);
    $('#question_number_small').html('Q' + ($('#pallette_block_' + lecture['id']).text()) + ' : ');
    $('#question_number_big').html('QUESTION NO : ' + ($('#pallette_block_' + lecture['id']).text()));
    if (__settings.getSetting('a_show_mark') == '1') {
        $('#positive_mark').html(lecture['aq_positive_mark']);
    }

    // var languageHtml = '';
    // $.each(__settings.getLanguages(), function (index, language) {
    //     if (typeof lecture['q_question'][language['id']] != 'undefined') {
    //         languageHtml += '<li role="presentation"><a role="menuitem" onclick="changeQuestionLanguage(' + language['id'] + ');" tabindex="-1" id="language_' + language['id'] + '" href="javascript:void(0)">' + language['wl_name'] + '</a></li>';
    //         __activeLanguage = (__activeLanguage == null) ? language['id'] : __activeLanguage;
    //     }
    // });

    // var languageWrapperHtml = '';
    // languageWrapperHtml += '<div class="dropdown test" style="visibility:hidden;">';
    // languageWrapperHtml += '    <button class="btn btn-default btn-need-trans dropdown-toggle" type="button" id="language_button" data-toggle="dropdown">English';
    // languageWrapperHtml += '        <svg fill="#999999" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">';
    // languageWrapperHtml += '            <path d="M7.41 7.84L12 12.42l4.59-4.58L18 9.25l-6 6-6-6z"/>';
    // languageWrapperHtml += '            <path d="M0-.75h24v24H0z" fill="none"/>';
    // languageWrapperHtml += '        </svg>';
    // languageWrapperHtml += '    </button>';
    // languageWrapperHtml += '    <ul class="dropdown-menu englsih-drop-left" role="menu" aria-labelledby="menu1">';
    // languageWrapperHtml += languageHtml;
    // languageWrapperHtml += '    </ul>';
    // languageWrapperHtml += '</div>';
    // $('#test_language').html(languageWrapperHtml).removeAttr('style');
    

}

function renderMultipleChoice(lecture) {
    //console.log(lecture['options']);
    __current_question = lecture['id'];
    __visitedQuestions[__current_question] = __current_question;
    startAnsweringTimeLog();
    //register as not answer if the question was not answered
    if ( inArray(__current_question, __answered) == false ) {
        __not_answered[__current_question] = __current_question;
        $('#pallette_block_' + __current_question).removeClass('answerd-pallet').addClass('not-answerd-pallet');
    }
    //End

    var question = (typeof lecture['q_question'][__activeLanguage] != 'undefined') ? lecture['q_question'][__activeLanguage] : lecture['q_question'][1];
    var questionHtml = '';
    var correct_anwer = '';
    questionHtml += '<h1 class="h1-question">' + question + '</h1>';
    questionHtml += '<div class="radioBtn-answers-wrap">';
    questionHtml += '    <div class="table-radio">';
    if (lecture['options'].length > 0) {
        for (var i = 0; i < lecture['options'].length; i++) {
            correct_anwer = new Array();
            console.log(typeof __answerSheet['answer'][lecture['id']]);
            if((typeof __answerSheet['answer'][lecture['id']] != 'undefined') && (typeof __answerSheet['answer'][lecture['id']] != 'object'))
            {
                correct_anwer = __answerSheet['answer'][lecture['id']].split(',');
                correct_anwer.forEach(function(value) {
                    correct_anwer[value] = value;
                });
                correct_anwer = Object.assign({}, correct_anwer);
            }
            else
            {
                correct_anwer = __answerSheet['answer'][lecture['id']];
            }
            questionHtml += '        <div class="table-row-radio">';
            questionHtml += '            <input type="checkbox" ' + (((typeof correct_anwer != 'undefined') && (inArray(lecture['options'][i]['id'], correct_anwer))) ? __checked : '') + ' id="option_' + lecture['options'][i]['id'] + '" value="' + lecture['options'][i]['id'] + '" class="question-options question-options-save" />';
            var option = (typeof lecture['options'][i]['qo_options'][__activeLanguage] != 'undefined') ? lecture['options'][i]['qo_options'][__activeLanguage] : lecture['options'][i]['qo_options'][1];
            questionHtml += '            <label for="option_' + lecture['options'][i]['id'] + '"><span></span> <div class="table-row-number"></div>' + option + '</label>';
            questionHtml += '        </div>';
        }
    }

    questionHtml += '    </div>';
    questionHtml += '</div>';

    $('#question_answer_wrapper').html(questionHtml);
    var markReviewHtml  = '';
    markReviewHtml      += '<input type="button" id="mark_for_review_'+__current_question+'" class="btn btn-footer" value="Mark as review">';
    markReviewHtml      += '<input type="button" style="background:linear-gradient(135deg, #6489d3 0%, #94a0f0 100%)" id="discard_review_'+__current_question+'" class="btn btn-footer" value="Discard Review">';
    $('#mark_review_html').html(markReviewHtml);
    $('#question_type_label').html(__settings.testObject()['question_types'][lecture['q_type']][1]);
    $('#question_number_small').html('Q' + ($('#pallette_block_' + lecture['id']).text()) + ' : ');
    $('#question_number_big').html('QUESTION NO : ' + ($('#pallette_block_' + lecture['id']).text()));
    if (__settings.getSetting('a_show_mark') == '1') {
        $('#positive_mark').html(lecture['aq_positive_mark']);
    }
}

function renderSubjective(lecture) {
    __current_question = lecture['id'];
    __visitedQuestions[__current_question] = __current_question;
    
    //register as not answer if the question was not answered
    if ( inArray(__current_question, __answered) == false ) {
        __not_answered[__current_question] = __current_question;
        $('#pallette_block_' + __current_question).removeClass('answerd-pallet').addClass('not-answerd-pallet');
    }
    //End
    
    var question = (typeof lecture['q_question'][__activeLanguage] != 'undefined') ? lecture['q_question'][__activeLanguage] : lecture['q_question'][1];
    startAnsweringTimeLog();
    var questionHtml = '';
    questionHtml += '<h1 class="h1-question">' + question + '</h1>';
    questionHtml += '<div class="text-area-wrap">';
    questionHtml += '    <textarea id="subjective_answer" style="width: 100%;height: 350px">' + ((typeof __answerSheet['answer'][lecture['id']] != 'undefined' && __answerSheet['answer'][lecture['id']] !== null) ? __answerSheet['answer'][lecture['id']] : '') + '</textarea>';
    questionHtml += '</div>';
    $('#question_answer_wrapper').html(questionHtml);
    var markReviewHtml  = '';
    markReviewHtml      += '<input type="button" id="mark_for_review_'+__current_question+'" class="btn btn-footer" value="Mark as review">';
    markReviewHtml      += '<input type="button" style="background:linear-gradient(135deg, #6489d3 0%, #94a0f0 100%)" id="discard_review_'+__current_question+'" class="btn btn-footer" value="Discard Review">';
    $('#mark_review_html').html(markReviewHtml);
    $('#question_type_label').html(__settings.testObject()['question_types'][lecture['q_type']][1]);
    $('#question_number_small').html('Q' + ($('#pallette_block_' + lecture['id']).text()) + ' : ');
    $('#question_number_big').html('QUESTION NO : ' + ($('#pallette_block_' + lecture['id']).text()));
    if (__settings.getSetting('a_show_mark') == '1') {
        $('#positive_mark').html(lecture['aq_positive_mark']);
    }
}

function renderBlanks(lecture) {
    __current_question = lecture['id'];
    __visitedQuestions[__current_question] = __current_question;
    
    //register as not answer if the question was not answered
    if ( inArray(__current_question, __answered) == false ) {
        __not_answered[__current_question] = __current_question;
        $('#pallette_block_' + __current_question).removeClass('answerd-pallet').addClass('not-answerd-pallet');
    }
    //End
    
    var question = (typeof lecture['q_question'][__activeLanguage] != 'undefined') ? lecture['q_question'][__activeLanguage] : lecture['q_question'][1];
    startAnsweringTimeLog();
    var fill_type_question = question.split('[_____]').join(' ______ ');
    var fill_type_answer = question.split('[_____]').join('<span contenteditable="true" class="dash_line"></span>');
    var questionHtml = '';
    questionHtml += '<h1 class="h1-question"></h1>';
    questionHtml += '<div class="text-area-wrap" id="fill_blank_answer">';
    questionHtml += '    <p id="fill_blank_answer">' + ((typeof __answerSheet['answer'][lecture['id']] != 'undefined' && __answerSheet['answer'][lecture['id']] !== null) ? __answerSheet['answer'][lecture['id']].replace(/&#(\d+);/g, function(match, dec) { return String.fromCharCode(dec);}) : fill_type_answer) + '</p>';
    questionHtml += '</div>';
    $('#question_answer_wrapper').html(questionHtml);
    var markReviewHtml  = '';
    markReviewHtml      += '<input type="button" id="mark_for_review_'+__current_question+'" class="btn btn-footer" value="Mark as review">';
    markReviewHtml      += '<input type="button" style="background:linear-gradient(135deg, #6489d3 0%, #94a0f0 100%)" id="discard_review_'+__current_question+'" class="btn btn-footer" value="Discard Review">';
    $('#mark_review_html').html(markReviewHtml);
    $('#question_type_label').html(__settings.testObject()['question_types'][lecture['q_type']][1]);
    $('#question_number_small').html('Q' + ($('#pallette_block_' + lecture['id']).text()) + ' : ');
    $('#question_number_big').html('QUESTION NO : ' + ($('#pallette_block_' + lecture['id']).text()));
    if (__settings.getSetting('a_show_mark') == '1') {
        $('#positive_mark').html(lecture['aq_positive_mark']);
    }
}

function renderQuestionPallette() {
    var palletteHtml = '';
    var palletteClass = '';
    if (__settings.getSetting('a_qgrouping') == '1') {
        if (Object.keys(__settings.testObject()['subject_questions']).length > 0) {
            var sl_no = 0;
            var lockSubject = false;
            $.each(__settings.testObject()['subject_questions'], function (subject_id, lectures) {
                if (lockSubject == false) {
                    if (Object.keys(lectures).length > 0 && subject_id == __subject) {
                        lockSubject = true;
                        $.each(lectures, function (index, value) {
                            __subject_wise_question_index++;
                            sl_no++;
                            palletteClass = '';
                            if (inArray(value, __not_answered)) {
                                palletteClass = 'not-answerd-pallet';
                            }
                            if (inArray(value, __answered)) {
                                palletteClass = 'answerd-pallet';
                            }
                            if (inArray(value, __marked_review)) {
                                palletteClass = 'marked-pallet';
                            }
                            palletteHtml += '<li id="pallette_block_' + value + '" onclick="loadQuestionInit(' + value + ')" data-id="' + sl_no + '" data-type="' + __subject_wise_question_index + '"  class="' + palletteClass + '">' + sl_no + '</li>';
                        });
                    }
                    else {
                        sl_no = sl_no + Object.keys(lectures).length;
                    }
                }
            });
            if(lockSubject == true){
                __subject_wise_question_index = 0;
            }
        }
    }
    else {
        if (Object.keys(__settings.testObject()['questions_order']).length > 0) {
            var questionCount = 1;
            $.each(__settings.testObject()['questions_order'], function (index, value) {
                palletteClass = '';
                if (inArray(value, __not_answered)) {
                    palletteClass = 'not-answerd-pallet';
                }
                if (inArray(value, __answered)) {
                    palletteClass = 'answerd-pallet';
                }
                if (inArray(value, __marked_review)) {
                    palletteClass = 'marked-pallet';
                }
                palletteHtml += '<li id="pallette_block_' + value + '" onclick="loadQuestionInit(' + value + ')" data-id="' + questionCount + '"  class="' + palletteClass + '">' + questionCount + '</li>';
                questionCount++;
            });
        }
    }

    $('#question_pallette').html(palletteHtml);
    processPalletePosition();
}
function processPalletePosition() {
    var palleteSize = $('#question_pallette li').size();
    var palletteHtml = '';

    if (palleteSize < __rowCount) {
        palletteHtml = '<ul>' + $('#question_pallette').html() + '</ul>';
        $('#question_pallette').html(palletteHtml);
    }
    else {
        var lastRow = palleteSize % __rowCount;
        if (lastRow > 0) {
            var palletteHtmlLastRow = '';
            for (var i = 0; i < lastRow; i++) {
                palletteHtmlLastRow = $("#question_pallette li").last()[0].outerHTML + palletteHtmlLastRow;
                $("#question_pallette li").last().remove();
            }
            $('#question_pallette').append('<ul>' + palletteHtmlLastRow + '</ul>');
        }
    }
}

function inArray(needle, haystack) {
    if (typeof haystack == 'object') {
        var hasIndex = false;
        if (typeof haystack[needle] != 'undefined') {
            hasIndex = true;
        }
        return hasIndex;
    }
    else {
        var length = haystack.length;
        for (var i = 0; i < length; i++) {
            if (haystack[i] == needle) return true;
        }
        return false;
    }
}

function removeArrayIndex(array, index) {
    if (typeof array == 'object') {
        delete array[index];
    }
    else {
        for (var i = array.length; i--;) {
            if (array[i] === index) {
                array.splice(i, 1);
            }
        }
    }
}


function ClearResponse() {
    __unsavedChanges = false;
    var lecture = __settings.testObject()['questions'][__current_question];
    if (typeof __answerSheet['answer'][lecture['id']] != 'undefined') {
        delete __answerSheet['answer'][__current_question];
    }
    if (typeof __answerSheet['review'][lecture['id']] != 'undefined') {
        delete __answerSheet['review'].pop(__current_question);
    }
    loadQuestion(__current_question);
}

function MarkForReview() {
    var temp_question_id = __current_question;
    $.ajax({
        url: __settings.config()._siteUrl + 'material/mark_as_review/' + __settings.getAssesmentId() + '/' + __settings.getAttemptId(),
        type: "POST",
        // async: false,
        data: { "is_ajax": true, 'question_id': __current_question,'token':__url_token },
        success: function (response) {
            var data = $.parseJSON(response);
            if (data['error'] == false) {
                if (__settings.getAttemptId() == '0') {
                    __settings.setAttemptId(data['attempt_id']);
                }
            }
            if (temp_question_id != null && temp_question_id != '') {
                __marked_review[temp_question_id] = temp_question_id;
                // removeArrayIndex(__not_answered, temp_question_id)
            }
            $('#pallette_block_' + temp_question_id).removeClass('not-answerd-pallet').removeClass('answerd-pallet').addClass('marked-pallet');
        }
    });
}

function DiscardReview() {
    var temp_question_id = __current_question;
    $.ajax({
        url: __settings.config()._siteUrl + 'material/discard_review/' + __settings.getAssesmentId() + '/' + __settings.getAttemptId(),
        type: "POST",
       // async: false,
        data: { "is_ajax": true, 'question_id': __current_question,'token':__url_token },
        success: function (response) {
            var data = $.parseJSON(response);
            if (data['error'] == false) {
                if (__settings.getAttemptId() == '0') {
                    __settings.setAttemptId(data['attempt_id']);
                }
            }
            if (temp_question_id != null && temp_question_id != '') {
                removeArrayIndex(__marked_review, temp_question_id);
            }
            if ( inArray(__current_question, __answered) == false ) {
                $('#pallette_block_' + temp_question_id).addClass('not-answerd-pallet').removeClass('marked-pallet');
            } else {
                $('#pallette_block_' + temp_question_id).addClass('answerd-pallet').removeClass('marked-pallet');
            }
        }
    });
}


var __queueSize = 5;
var __answerQueue = {};
function saveAnswer() {
    __unsavedChanges = false;
    var status = false;
    var lecture = __settings.testObject()['questions'][__current_question];

    switch (lecture['q_type']) {
        case "1":
            status = saveSingleChoice();
            break;
        case "2":
            status = saveMultipleChoice();
            break;
        case "3":
            status = saveSubjective();
            break;
        case "4":
            status = saveFillblank();
            break;
    }
    if (status == false) {
        loadFinalOverview(lecture);
        return false;
    } else {
        loadFinalOverview(lecture);
    }

    if (inArray(lecture['id'], __marked_review)) {
        $.ajax({
            url: __settings.config()._siteUrl + 'material/unmark_from_review/' + __settings.getAssesmentId() + '/' + __settings.getAttemptId(),
            type: "POST",
            async: false,
            data: { "is_ajax": true, 'question_id': lecture['id'],'token':__url_token },
            success: function (response) {
                var data = $.parseJSON(response);
                if (data['error'] == false) {
                    if (__settings.getAttemptId() == '0') {
                        __settings.setAttemptId(data['attempt_id']);
                    }
                }
            }
        });
    }
    saveAnsweringTimeLog();

    if (__settings.getSetting('a_que_report') == '1' || __settings.getSetting('a_submit_immediate') == '1') {
        __queueSize = (__settings.getSetting('a_que_report') == '1') ? 1 : __queueSize;
        __answerQueue[__current_question] = {};
        __answerQueue[__current_question]['question_id'] = lecture['id'];
        __answerQueue[__current_question]['answer'] = __answerSheet['answer'][__current_question];
        __answerQueue[__current_question]['duration'] = __answeringTimeLog[__current_question];
        if (Object.keys(__answerQueue).length >= __queueSize) {
            $('#save_and_next').html('Submitting..');
            $.ajax({
                url: __settings.config()._siteUrl + 'material/save_answer',
                type: "POST",
                async: false,
                data: { "is_ajax": true,'token':__url_token, 'assesment_id': __settings.getAssesmentId(), 'attempt_id': __settings.getAttemptId(), 'answer_queue': JSON.stringify(__answerQueue) },
                success: function (response) {
                    var data = $.parseJSON(response);
                    if (data['error'] == false) {
                        if (__settings.getAttemptId() == '0') {
                            __settings.setAttemptId(data['attempt_id']);
                        }
                    }
                    if(lecture['q_type'] == '3' || lecture['q_type'] == '4')
                    {
                        $('#save_and_next').html('Save');
                    }
                    else
                    {
                        $('#save_and_next').html('Save & Next');
                    }
                    if (__settings.getSetting('a_que_report') == '1') {
                        renderReport(data['report']);
                    }
                    __answerQueue = {};
                }
            });
        }
    }

}

function loadFinalOverview(lecture) {
    var currentQuestion = $('#pallette_block_' + lecture['id']).text();
    if(currentQuestion == Object.keys(__settings.testObject()['questions_order']).length) {
        if (__settings.getSetting('a_que_report') != '1') {
            renderOverviewPopUp();
        }else if( lecture['q_type'] == '3' || lecture['q_type'] == '4' ){
            renderOverviewPopUp();
        }
    }
}

function renderReport(report) {
    timerAction('pause');
    stopAnsweringTimeLog();
    var lecture = __settings.testObject()['questions'][report['question_id']];
    console.log(report);
    if (lecture != null) {
        lecture['report'] = report;
        switch (lecture['q_type']) {
            case "1":
                renderSingleChoiceReport(lecture);
                break;
            case "2":
                renderMultipleChoiceReport(lecture);
                break;
            case "3":
                var currentQuestion = $('#pallette_block_' + lecture['id']).text();
                if(currentQuestion == Object.keys(__settings.testObject()['questions_order']).length) {
                    loadFinalOverview(lecture);
                }else{
                    closeOverview();
                    renderNextQuestion();
                }
                break;
            case "4":
                var currentQuestion = $('#pallette_block_' + lecture['id']).text();
                if(currentQuestion == Object.keys(__settings.testObject()['questions_order']).length) {
                    loadFinalOverview(lecture);
                }else{
                    closeOverview();
                    renderNextQuestion();
                }
                break;
        }
    }
}

function renderSingleChoiceReport(lecture) {
    var question = (typeof lecture['q_question'][__activeLanguage] != 'undefined') ? lecture['q_question'][__activeLanguage] : lecture['q_question'][1];

    var questionHtml = '';
    questionHtml += '<h1 class="h1-question">' + question + '</h1>';
    questionHtml += '<div class="radioBtn-answers-wrap">';
    questionHtml += '    <div class="table-radio ' + ((lecture['report']['marked_right'] == true) ? 'radio-suc break-cursor' : 'radio-error') + '">';
    if (lecture['options'].length > 0) {
        for (var i = 0; i < lecture['options'].length; i++) {
            var this_answered = '';
            var answered_status = '';

            if ((typeof __answerSheet['answer'][lecture['id']] != 'undefined')) {
                if (lecture['options'][i]['id'] == __answerSheet['answer'][lecture['id']]) {
                    this_answered = __checked;
                    answered_status = 'radio-error-checked';
                    if (lecture['report']['marked_right'] == true) {
                        answered_status = 'radio-success';
                    }
                }
                else {
                    if (lecture['report']['ar_answer'] == lecture['options'][i]['id']) {
                        answered_status = 'radio-success';
                    }
                }
            }
            else {
                if (lecture['options'][i]['id'] == lecture['report']['ar_answer']) {
                    answered_status = 'radio-success';
                }
            }
            questionHtml += '        <div class="table-row-radio ' + answered_status + '">';
            questionHtml += '            <input type="radio" ' + this_answered + ' name="radio_option_report" id="option_report_' + lecture['options'][i]['id'] + '" value="' + lecture['options'][i]['id'] + '" class="question-options" />';
            var option = (typeof lecture['options'][i]['qo_options'][__activeLanguage] != 'undefined') ? lecture['options'][i]['qo_options'][__activeLanguage] : lecture['options'][i]['qo_options'][1];
            questionHtml += '            <label for="option_report_' + lecture['options'][i]['id'] + '"><span></span> <div class="table-row-number"></div>' + option + '</label>';
            questionHtml += '        </div>';
        }
    }

    questionHtml += '    </div>';
    questionHtml += '</div>';

    $('#question_answer_report_wrapper').html(questionHtml);
    $('#question_type_reort_label').html(__settings.testObject()['question_types'][lecture['q_type']][1]);
    $('#question_number_small_report').html('Q' + ($('#pallette_block_' + lecture['id']).text()) + ' : ');
    $('#question_number_big_report').html('QUESTION NO : ' + ($('#pallette_block_' + lecture['id']).text()));
    $('#positive_mark_report').html(lecture['aq_positive_mark']);
    $('#report_wrapper').show();
    $('#close_button').hide();
    $('#portfolio-popup').addClass("portfolio-popup-show");
    $('#portfolio-popup').animate({
        "opacity": 1
    }, 200);
}

function renderMultipleChoiceReport(lecture) {
    var question = (typeof lecture['q_question'][__activeLanguage] != 'undefined') ? lecture['q_question'][__activeLanguage] : lecture['q_question'][1];

    var questionHtml = '';
    questionHtml += '<h1 class="h1-question">' + question + '</h1>';
    questionHtml += '<div class="radioBtn-answers-wrap">';
    questionHtml += '    <div class="table-radio break-cursor">';
    if (lecture['options'].length > 0) {
        for (var i = 0; i < lecture['options'].length; i++) {
            var this_answered = '';
            var answered_status = '';
            if ((typeof __answerSheet['answer'][lecture['id']] != 'undefined')) {
                if (jQuery.inArray(lecture['options'][i]['id'], __answerSheet['answer'][lecture['id']].split(",")) >= 0) {
                    this_answered = __checked;
                    answered_status = 'checkbox-error-checked';
                    if (jQuery.inArray(lecture['options'][i]['id'], lecture['report']['ar_answer'].split(",")) >=0 ) {
                        answered_status = 'checkbox-success-checked';
                    }
                }
                else {
                   
                    if (jQuery.inArray(lecture['options'][i]['id'], lecture['report']['ar_answer'].split(",")) >= 0 ) {
                        answered_status = 'checkbox-success';
                    }
                }
            }
            else {
                if (jQuery.inArray(lecture['options'][i]['id'], lecture['report']['ar_answer'].split(",")) >= 0 ) {
                    answered_status = 'checkbox-success';
                }
            }


            questionHtml += '        <div class="table-row-radio ' + answered_status + '">';
            questionHtml += '            <input type="checkbox" ' + this_answered + ' id="option_report_' + lecture['options'][i]['id'] + '" value="' + lecture['options'][i]['id'] + '" class="question-options" />';
            var option = (typeof lecture['options'][i]['qo_options'][__activeLanguage] != 'undefined') ? lecture['options'][i]['qo_options'][__activeLanguage] : lecture['options'][i]['qo_options'][1];
            questionHtml += '            <label for="option_report_' + lecture['options'][i]['id'] + '"><span></span> <div class="table-row-number"></div>' + option + '</label>';
            questionHtml += '        </div>';
        }
    }

    questionHtml += '    </div>';
    questionHtml += '</div>';
    $('#question_answer_report_wrapper').html(questionHtml);
    $('#question_type_reort_label').html(__settings.testObject()['question_types'][lecture['q_type']][1]);
    $('#question_number_small_report').html('Q' + ($('#pallette_block_' + lecture['id']).text()) + ' : ');
    $('#question_number_big_report').html('QUESTION NO : ' + ($('#pallette_block_' + lecture['id']).text()));
    $('#positive_mark_report').html(lecture['aq_positive_mark']);
    $('#report_wrapper').show();
    $('#close_button').hide();
    $('#portfolio-popup').addClass("portfolio-popup-show");
    $('#portfolio-popup').animate({
        "opacity": 1
    }, 200);
}

function closeReport() {
    //closeOverview();
    var currentQuestion = $('#current_question').text();
    if(currentQuestion == Object.keys(__settings.testObject()['questions_order']).length) {
        $('#overview_popup, #instruction_popup, #report_wrapper').hide();
        timerAction('resume');
        startAnsweringTimeLog();
        renderOverviewPopUp();
    }
    else 
    {
        closeOverview();
        renderNextQuestion();
    }
}

function SaveAndNext() {
    if (saveAnswer() != false) {
        renderNextQuestion();
    }
}

function saveSingleChoice() {
    var answer = $('input[name=radio_option]:checked').val();
    //cheking question mandatory to attempt
    if (__settings.getSetting('a_attend_all') == '1') {
        if (typeof answer == 'undefined' || answer == '' || answer == null) {
            showCommonModal('Heading', 'Please answer the question', 3);
            return false;
        }
    }

    __answerSheet['answer'][__current_question] = answer;
    //console.log(__current_question);
    if (answer != '' && typeof answer != 'undefined') {
        removeArrayIndex(__not_answered, __current_question);
        __answered[__current_question] = __current_question;
        if ( inArray(__current_question, __marked_review) == true ) {
            $('#pallette_block_' + __current_question).removeClass('not-answerd-pallet');
            }else{
            $('#pallette_block_' + __current_question).removeClass('not-answerd-pallet').removeClass('marked-pallet').addClass('answerd-pallet');
            }
    }
    else {
        removeArrayIndex(__answered, __current_question);
        __not_answered[__current_question] = __current_question;
        if ( inArray(__current_question, __marked_review) == true ) {
            $('#pallette_block_' + __current_question).removeClass('answerd-pallet');
            }else{
            $('#pallette_block_' + __current_question).removeClass('answerd-pallet').removeClass('marked-pallet').addClass('not-answerd-pallet');
            }
    }
    return true;
}

function saveMultipleChoice() {
    var answers = new Object;
    var selectedAnswer = '';
    $('.question-options-save').each(function (index) {
        if ($(this).prop('checked') == true) {
            selectedAnswer = $(this).val();
            answers[selectedAnswer] = selectedAnswer;
        }
        selectedAnswer = '';
    });

    //converting data type of answer to string
    var answersTemp = new Array;
    if( Object.keys(answers).length > 0 ) {
        $.each(answers, function (index, answer) {
            answersTemp.push(answer);
        });
    }
    //end

    //cheking question mandatory to attempt
    if (__settings.getSetting('a_attend_all') == '1') {
        if (Object.keys(answers).length == 0) {
            showCommonModal('Heading', 'Please answer the question', 3);
            return false;
        }
    }

    __answerSheet['answer'][__current_question] = answersTemp.join(',');
    //console.log(__current_question);
    if (Object.keys(answers).length > 0) {
        removeArrayIndex(__not_answered, __current_question);
        __answered[__current_question] = __current_question;
        if ( inArray(__current_question, __marked_review) == true ) {
            $('#pallette_block_' + __current_question).removeClass('not-answerd-pallet');
            }else{
        $('#pallette_block_' + __current_question).removeClass('not-answerd-pallet').removeClass('marked-pallet').addClass('answerd-pallet');
            }
    }
    else {
        removeArrayIndex(__answered, __current_question);
        __not_answered[__current_question] = __current_question;
        if ( inArray(__current_question, __marked_review) == true ) {
            $('#pallette_block_' + __current_question).removeClass('answerd-pallet');
            }else{
        $('#pallette_block_' + __current_question).removeClass('answerd-pallet').removeClass('marked-pallet').addClass('not-answerd-pallet');
            }
    }
    return true;
}

function saveSubjective() {
    var answer = $('#subjective_answer').val();
    //console.log(__current_question);

    //cheking question mandatory to attempt
    if (__settings.getSetting('a_attend_all') == '1') {
        if (typeof answer == 'undefined' || answer == '' || answer == null) {
            showCommonModal('Heading', 'Please answer the question', 3);
            return false;
        }
    }

    __answerSheet['answer'][__current_question] = answer;
    if (answer != '') {
        removeArrayIndex(__not_answered, __current_question);
        __answered[__current_question] = __current_question;
        if ( inArray(__current_question, __marked_review) == true ) {
            $('#pallette_block_' + __current_question).removeClass('not-answerd-pallet');
            }else{
        $('#pallette_block_' + __current_question).removeClass('not-answerd-pallet').removeClass('marked-pallet').addClass('answerd-pallet');
            }
    }
    else {
        removeArrayIndex(__answered, __current_question);
        __not_answered[__current_question] = __current_question;
        if ( inArray(__current_question, __marked_review) == true ) {
            $('#pallette_block_' + __current_question).removeClass('answerd-pallet');
            }else{
        $('#pallette_block_' + __current_question).removeClass('answerd-pallet').removeClass('marked-pallet').addClass('not-answerd-pallet');
            }
    }
    return true;
}

function saveFillblank() {
    var answer = $('#fill_blank_answer').html();
    if (__settings.getSetting('a_attend_all') == '1') {
        // var data = 'Javascript is called client side programming because it runs on the <span contenteditable="true" class="dash_line"></span> not on the web server.<span contenteditable="true" class="dash_line">santhosh</span>';
        var myregexp = /<span[^>]+?class="dash_line".*?>([\s\S]*?)<\/span>/g;
        var match = myregexp.exec(answer);
        //var result = new Array();
        while (match != null) {
            if (typeof RegExp.$1 == 'undefined' || RegExp.$1 == '' || RegExp.$1 == null) {
                showCommonModal('Heading', 'Please answer the question', 3);
                return false;
            }
            //result['match'] = RegExp.$1;
            match = myregexp.exec(answer);
        }
    }

    __answerSheet['answer'][__current_question] = answer.replace(/[\u00A0-\u9999<>\&]/gim, function(i) { return '&#'+i.charCodeAt(0)+';';});
    if ($('#fill_blank_answer .dash_line').html() != '') {
        removeArrayIndex(__not_answered, __current_question);
        __answered[__current_question] = __current_question;
        if ( inArray(__current_question, __marked_review) == true ) {
            $('#pallette_block_' + __current_question).removeClass('not-answerd-pallet');
            }else{
        $('#pallette_block_' + __current_question).removeClass('not-answerd-pallet').removeClass('marked-pallet').addClass('answerd-pallet');
            }
    }
    else {
        removeArrayIndex(__answered, __current_question);
        __not_answered[__current_question] = __current_question;
        if ( inArray(__current_question, __marked_review) == true ) {
            $('#pallette_block_' + __current_question).removeClass('answerd-pallet');
            }else{
        $('#pallette_block_' + __current_question).removeClass('answerd-pallet').removeClass('marked-pallet').addClass('not-answerd-pallet');
            }
    }
    return true;
}

var __tempAnsweringTimeLog = new Object;
var __answeringTimeLog = new Object;
var __answeringTimeInterval = null;
function startAnsweringTimeLog() {
    stopAnsweringTimeLog();
    if (__current_question != null && __current_question > 0) {
        if (typeof __tempAnsweringTimeLog[__current_question] == 'undefined') {
            __tempAnsweringTimeLog[__current_question] = 0;
        }
        __answeringTimeInterval = setInterval(function () {
            __tempAnsweringTimeLog[__current_question] = Number(__tempAnsweringTimeLog[__current_question]) + 1;
        }, 1000);
    }
}

function stopAnsweringTimeLog() {
    clearInterval(__answeringTimeInterval);
}

function saveAnsweringTimeLog() {
    stopAnsweringTimeLog();
    if (__current_question != null && __current_question > 0) {
        //defined time not defined in tempory array
        if (typeof __tempAnsweringTimeLog[__current_question] == 'undefined') {
            __tempAnsweringTimeLog[__current_question] = 0;
        }
        //defined time not defined in main array
        if (typeof __answeringTimeLog[__current_question] == 'undefined') {
            __answeringTimeLog[__current_question] = 0;
        }
        //Total time is sum of temporary time and main time
        __answeringTimeLog[__current_question] = Number(__answeringTimeLog[__current_question]) + Number(__tempAnsweringTimeLog[__current_question]);
        __tempAnsweringTimeLog[__current_question] = 0;
    }
}

function renderNextQuestion() {
    __question_index++;
    if (__settings.getSetting('a_qgrouping') == '1' && typeof __settings.testObject()['subject_questions'][__subject][__question_index] == 'undefined') {
        var lockCategory = false;
        var subjectLocked = false;
        if (Object.keys(__settings.testObject()['subjects']).length > 0) {
            $.each(__settings.testObject()['subjects'], function (index, subject) {
                if (lockCategory == true && subjectLocked == false) {
                    __subject = subject['subject_id'];
                    subjectLocked = true;
                }
                if (subject['subject_id'] == __subject) {
                    lockCategory = true;
                }

            });
        }
        if (lockCategory == true) {
            __question_index = 0;
            var subjectHtml = '';
            subjectHtml += $('#subject_' + __subject).text();
            subjectHtml += '<svg fill="#999999" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">';
            subjectHtml += '    <path d="M7.41 7.84L12 12.42l4.59-4.58L18 9.25l-6 6-6-6z"></path>';
            subjectHtml += '    <path d="M0-.75h24v24H0z" fill="none"></path>';
            subjectHtml += '</svg>';
            __question_index = 0;
            $('#subject_menu').html(subjectHtml);
            renderQuestionPallette();
            renderQuestion();
        }
        else {
            saveAnswer();
        }
    }
    else {
        renderQuestion();
    }
}

function nextQuestion() {
    if ( inArray(__current_question, __marked_review) == false ) {
        if(isThereUnsavedChanges() === true) {
            saveCurrentQuestionPopUp();
            return false;
        }
    }

    if ($('#pallette_block_' + __current_question).next().is("li")) {
        $('#pallette_block_' + __current_question).next().trigger('click');
    } else if ($('#pallette_block_' + __current_question).next().is("ul")) {
        $('#pallette_block_' + __current_question).next().children().first().trigger('click');
    }
}

function previousQuestion() {
    if ( inArray(__current_question, __marked_review) == false ) {
        if(isThereUnsavedChanges() === true) {
            saveCurrentQuestionPopUp();
            return false;
        }
    }

    if ($('#pallette_block_' + __current_question).prev().is("li")) {
        $('#pallette_block_' + __current_question).prev().trigger('click');
    } else {
        $('#pallette_block_' + __current_question).parent().prev().trigger('click');
    }
}

var timeRunning = false;
var __time_taken = 0;
var __consumedTime = 0;
function startTimer(duration) {
    var tempDuration = duration;//10
    var timer = tempDuration, hours, minutes, seconds;
    timeRunning = true;
    setInterval(function () {
        if (timeRunning == true) {
            hours = parseInt((timer / 3600) % 24, 10)
            minutes = parseInt((timer / 60) % 60, 10)
            seconds = parseInt(timer % 60, 10);

            seconds = seconds < 10 ? "0" + seconds : seconds;
            var mobMinutes = (hours * 60) + minutes;
            mobMinutes = mobMinutes < 10 ? "0" + mobMinutes : mobMinutes;

            $('.min-left-mob #min_left').html(mobMinutes + ":" + seconds);
            hours = hours < 10 ? "0" + hours : hours;
            minutes = minutes < 10 ? "0" + minutes : minutes;

            $('#time_remaining').html("<span>" + hours + "</span>:<span>" + minutes + "</span>:<span>" + seconds + "</span>");
            $('#timer_display').html(hours + ":" + minutes + ":" + seconds);
            --timer;
            __time_taken++;
            if (typeof __settings.testObject()['attempt'] != 'undefined') {
                __consumedTime = parseInt(__settings.testObject()['attempt']['aa_duration']) + parseInt(__time_taken);
            } else {
                __consumedTime = __time_taken;
            }
            if (__time_taken >= duration) {
                timeRunning = false;
                submitExam();
            }
        }
    }, 1000);
}

setInterval(function () {
    var remain_time = 0;
    if (typeof __settings.testObject()['attempt'] != 'undefined') {
        remain_time = parseInt(__settings.testObject()['attempt']['aa_duration']) + parseInt(__time_taken);
    } else {
        remain_time = __time_taken;
    }
    if (timeRunning == true) {
        $.ajax({
            async: false,
            url: __settings.config()._siteUrl + '/material/save_time',
            type: "POST",
            data: { "is_ajax": true, 'attempt_id': __settings.getAttemptId(), 'time_taken': remain_time,'token':__url_token }
        });
    }
}, 5000);

function minutesToTime(duration) {
    var tempDuration = duration;//10
    var timer = tempDuration, hours, minutes, seconds;

    hours = parseInt((timer / 3600) % 24, 10)
    minutes = parseInt((timer / 60) % 60, 10)
    seconds = parseInt(timer % 60, 10);

    seconds = seconds < 10 ? "0" + seconds : seconds;
    var mobMinutes = (hours * 60) + minutes;
    mobMinutes = mobMinutes < 10 ? "0" + mobMinutes : mobMinutes;

    $('.min-left-mob #min_left').html(mobMinutes + ":" + seconds);
    hours = hours < 10 ? "0" + hours : hours;
    minutes = minutes < 10 ? "0" + minutes : minutes;
    
    $('#total_time, #time_remaining').html("<span>" + hours + "</span>:<span>" + minutes + "</span>:<span>" + seconds + "</span>");
    $('#timer_display').html(hours + ":" + minutes + ":" + seconds);
}

function timerAction(action) {
    switch (action) {
        case "resume":
            timeRunning = true;
            break;
        case "pause":
            timeRunning = false;
            break;
    }
}

function loadQuestionInit(question_id) {
    if ( inArray(__current_question, __marked_review) == false ) {
        if(isThereUnsavedChanges() === true) {
            saveCurrentQuestionPopUp();
            return false;
        }
    }
    if (__settings.getSetting('a_limit_navigation') == '0') {
        loadQuestion(question_id);
    }
}

function submitExamInit() {
    var _canSubmit = true;
    if (__settings.getSetting('a_attend_all') == '1') {
        if (Object.keys(__settings.testObject()['questions']).length > 0) {
            $.each(__settings.testObject()['questions'], function (question_id, question) {
                if (typeof __answerSheet['answer'][question_id] == 'undefined') {
                    _canSubmit = false;
                    showCommonModal('Heading', 'Please answer all the questions', 3);
                    return false;
                }
            });
        }
    }
    if (_canSubmit == true) {
        submitExam();
    }
}

var examSubmittingOnProgress = false;
function submitExam() {
    if( examSubmittingOnProgress == true ) {
        return false;
    }
    examSubmittingOnProgress = true;
    $('#submit_exam_btn').val('Submitting...');
    $('#submit_exam_btn').prop('onclick', null).off('click');
    
    $.ajax({
        url: __settings.config()._siteUrl + 'material/save_exam',
        type: "POST",
        data: { "is_ajax": true,'token':__url_token, 'attempt_id': __settings.getAttemptId(), 'time_taken': __consumedTime, 'answer': JSON.stringify(__answerSheet), 'assesment_id': __settings.getAssesmentId(), 'answer_time_log': JSON.stringify(__answeringTimeLog),'answer_queue': JSON.stringify(__answerQueue) },
        success: function (response) {
            var data = $.parseJSON(response);
            if(__url_token == '') {
                location.href = __settings.config()._siteUrl + 'material/test_response/' + __settings.getAssesmentId() + '/' + data['attempt_id'];
            } else {
                location.href = __settings.config()._siteUrl + 'material/test_response/' + __settings.getAssesmentId() + '/' + data['attempt_id']+'?token='+__url_token;
            }
        }
    });

}

function renderOverviewPopUp() {
    var totalQuestion = Object.keys(__settings.testObject()['questions']).length;
    var totalAnswered = Object.keys(__answered).length;
    //var totalAnswered = Object.keys(__answerSheet.answer).length;
    var totalNotAnswered = Object.keys(__not_answered).length;
    //var totalNotAnswered = Number(totalQuestion) - Number(totalAnswered);
    var totalReview = Object.keys(__marked_review).length;
    var notVisited = Number(totalQuestion) - Number(Object.keys(__visitedQuestions).length);

    $('#total_answered').html('Answered - ' + ((totalAnswered>0)?totalAnswered:0));
    $('#total_not_visited').html('Not Visited - ' + ((notVisited>0)?notVisited:0));
    $('#total_not_answered').html('Not Answered - ' + ((totalNotAnswered>0)?totalNotAnswered:0));
    $('#total_marked_review').html('Review - ' + ((totalReview>0)?totalReview:0));


    $('#overview_popup').show();
    $('#portfolio-popup').addClass("portfolio-popup-show");
    $('#close_button').removeAttr('style');
    $('#portfolio-popup').animate({
        "opacity": 1
    }, 200);
}

function closeOverview() {
    $('#portfolio-popup').animate({
        "opacity": 0
    }, 300, function () {
        $('#portfolio-popup').removeClass("portfolio-popup-show");
    });
   $('#overview_popup, #instruction_popup, #report_wrapper').hide();
   timerAction('resume');
   startAnsweringTimeLog();
}

function renderInstructionPopUp() {
    //timerAction('pause');
    //stopAnsweringTimeLog();
    $('#instruction_content_wrapper').html(__settings.getInstruction(1)[1]);
    $('#instruction_popup').show();
    $('#close_button').removeAttr('style');
    $('#portfolio-popup').addClass("portfolio-popup-show");
    $('#portfolio-popup').animate({
        "opacity": 1
    }, 200);
}




var __unsavedChanges = false;
function isThereUnsavedChanges() {
    return __unsavedChanges === true;
}

function saveCurrentQuestionPopUp() {
    showCommonModal('Heading', 'There are unsaved changes. Please save to continue.', 3);
}


$(document).on('change', 'input[name=radio_option]', function(){
    if(typeof __answerSheet['answer'][__current_question] != 'undefined') {
        if($('input[name=radio_option]:checked').val() != parseInt(__answerSheet['answer'][__current_question])) {
            __unsavedChanges = true;
        } else {
            __unsavedChanges = false;
        }
    } else {
        __unsavedChanges = true;
    }
});

$(document).on('change', 'input[type=checkbox].question-options', function(){ 
    if(typeof __answerSheet['answer'][__current_question] != 'undefined') {
        var answerHistory = __answerSheet['answer'][__current_question];
        var currentAnswer = new Object;
        $('.question-options-save').each(function (index) {
            if ($(this).prop('checked') == true) {
                currentAnswer[$(this).val()] = $(this).val();
            }
        });
        if(JSON.stringify(answerHistory) == JSON.stringify(currentAnswer) ) {
            __unsavedChanges = false;
        } else {
            __unsavedChanges = true;
        }
    } else {
        __unsavedChanges = true;
    }
});

$(document).on('keyup', '#subjective_answer', function(){
    if(typeof __answerSheet['answer'][__current_question] != 'undefined') {
        if($(this).val() != __answerSheet['answer'][__current_question]) {
            __unsavedChanges = true;
        } else {
            __unsavedChanges = false;
        }
    } else {
        __unsavedChanges = true;
    }
});

$(document).on('keyup', '.dash_line', function(){
    if(typeof __answerSheet['answer'][__current_question] != 'undefined') {
        if($('#fill_blank_answer').html() != __answerSheet['answer'][__current_question]) {
            __unsavedChanges = true;
        } else {
            __unsavedChanges = false;
        }
    } else {
        __unsavedChanges = true;
    }
});

