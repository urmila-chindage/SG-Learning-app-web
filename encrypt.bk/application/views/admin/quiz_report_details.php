
<link rel="stylesheet" type="text/css" href="<?php echo assets_url() ?>css/quiz_details.css">
<link rel="icon" href="<?php echo base_url('favicon.png') ?>">
<style>
.evaluation-question {word-break: break-word;}
.dash_line {font-weight: bold;color: #000;border-bottom: 1px solid #000;min-width: 100px;display: inline-block;text-align:center;padding:0px 10px;}
.clickable-pointer {
    cursor: pointer;
}
</style>
<script>
    var __need_evaluation     = false;
    var __default_user_path   = '<?php echo default_user_path() ?>';
    var __user_path           = '<?php echo user_path() ?>';

var attempt = null;
function loadQuizAttemptDetails( attemptId, quizName ) {
        $('#create_box_title').html('QUIZ REPORT - '+(__sel_course_name+' - '+__sel_assessment_title).toUpperCase());
        attempt = new QuizAttemptManager();
        attempt.setAttemptId(attemptId);
        attempt.loadAttemptDetails();
        $('#startEvaluation').unbind('click');
        $('#startEvaluation').click(function(){
            attempt.startEvaluation();
        });
        $('#saveEvaluation').unbind('click');
        $('#saveEvaluation').click(function(){
            $(this).html('SAVING....');
            attempt.saveEvaluation();
        });
}

var QuizAttemptManager =function () {
    this._attemptId          = 0;
    this._adminUrl           = '<?php echo admin_url() ?>';
    this._attemptObjectStack = {};
    this.questionTypes       = {'1':'Single', '2':'Multiple', '3':'Explanatory', '4':'Fill in the Blanks'};
    this.correct             = 0;
    this.wrong               = 0;
    this.skipped             = 0;

    this.clearCache = function(){
        this.correct             = 0;
        this.wrong               = 0;
        this.skipped             = 0;
    }
    this.setAttemptId = function(attemptId) {
        return this._attemptId = attemptId;  
    };
    this.getAttemptId = function() {
        return this._attemptId;  
    };
    this.loadAttemptDetails = function() {
        this.clearCache();
        var _response = null;
        $.ajax({
            url: this._adminUrl+'assessments/attempt/'+this.getAttemptId(),
            type: "GET",
            async : false,
            data:{},
            success: function(response) {
                _response = $.parseJSON(response);
                //loading popup here
                $('#quiz_details_modal').modal();
            }
        });
        this._attemptObjectStack = _response;
        this.renderQuestionListHtml();
        this.renderAttemptDashboard();
    }
    this.attemptObject = function(index) {
        if( typeof index != 'undefined' && index != '' ) {
            return this._attemptObjectStack[index];
        } else {
            return this._attemptObjectStack;
        }
    }
    this.attemptReportDetails = function(index) {
        if( typeof index != 'undefined' && index != '' ) {
            return this.attemptObject('attempt_details')['aa_assessment_detail'][index];
        } else {
            return this.attemptObject('attempt_details')['aa_assessment_detail'];
        }
    }
    this.renderQuestionListHtml = function() {
        var questionListHtml = '';
        var questions        = this.attemptReportDetails('questions');
        var method           = this;
        var questionMark     = '';
        var questionClass    = '';
        var attendedWrapper  = ''; 
        var attendedTick     = '';
        var question_text    = '';
        var maxLength        = 45;
        var attempty_details = this.attemptObject('attempt_details');
        if(Object.keys(questions).length > 0 ) {
            $.each(questions, function(questionKey, question ) {
                attendedWrapper = '', 
                attendedTick    = '',
                questionMark    = '',
                questionClass   = '';
                if($.trim(question['user_answers']) != '') {
                    if(question['type'] == '1' || question['type'] == '2') {
                        var userAnswers = question['user_answers'].split(',');
                        var actualAnswer = [];
                        if(Object.keys(question['q_actual_answer']).length > 0) {
                            $.each(question['q_actual_answer'], function(answerKey, answer ) {
                                actualAnswer.push(answer['id']);
                            });
                        }
                        if (JSON.stringify(userAnswers) === JSON.stringify(actualAnswer)) {
                            attendedTick    = 'tick-green';
                            method.correct  = method.correct+1;                       
                        } else {
                            attendedTick    = 'tick-red';
                            method.wrong    = method.wrong+1;                       
                        }
                    } else {
                         __need_evaluation = true;
                        questionMark    = '',
                        questionClass   = 'edit-mark-option';
                        if(question['user_mark'] > 0) {
                            attendedTick    = 'tick-green';     
                            method.correct  = method.correct+1;                       
                        } else {
                            if(question['user_mark'] != '' ) {
                                attendedTick    = 'tick-red';
                                method.wrong    = method.wrong+1;                       
                            }
                        }
                    }
                } else {
                    if(question['type'] == '3' || question['type'] == '4') {
                        __need_evaluation = true;
                        questionMark      = '',
                        questionClass     = 'edit-mark-option';
                    }
                    method.skipped  = method.skipped+1;                       
                    attendedWrapper = 'unattended';
                    attendedTick    = 'tick-none';
                }

                questionListHtml += '<div class="quiz-report-list-row '+attendedWrapper+'" data-question-id="'+questionKey+'" id="question_'+questionKey+'">';
                questionListHtml += '   <div class="hash text-center"></div>';
                questionListHtml += '	<div class="margin-data">';
                
                question_text     = question['q_question'][1];
                if(question_text.length >= maxLength) {
                   //console.log(question_text);
                   question_text = htmlSubstring(question_text,maxLength);
                   //question_text = question_text.substr(0, maxLength) + "...";
                } else {
                    question_text = htmlSubstring(question_text,maxLength);
                } 

                questionListHtml += '		<div class="questions-title clickable-pointer '+attendedTick+' col-md-8" onclick="renderQuestion(\''+questionKey+'\')" >'+(question_text)+'</div>';
                questionListHtml += '		<div class="ques-type text-left clickable-pointer col-md-3" onclick="renderQuestion(\''+questionKey+'\')">'+(method.getQuestionTypes(question['type']))+'</div>';
                
                questionMark      = (typeof question['user_mark'] != 'undefined')?question['user_mark']:'';
                if(question['type'] == '1' || question['type'] == '2') {
                    questionClass     = '';
                }
                questionListHtml += '		<div class="mark-col-title text-blue text-center col-md-1 '+questionClass+'">';
                questionListHtml +=             questionMark;
                questionListHtml += '       </div>';
                questionListHtml += '	</div>';
                questionListHtml += '</div>';
            });
                questionListHtml += '<div class="col-md-12 text-right" style="padding:25px;"><span style="font-size: 18px;font-weight: 400;">Total mark</span> : <span style="font-size: 21px;font-weight: 600;color: green;letter-spacing: 1px;">'+attempty_details.aa_mark_scored+'</span></div>';
                
            $('#question_list_wrapper').html(questionListHtml);
        }
        $('#saveEvaluation').html('<div class="nav-next text-right"><button class="btn btn-success start-evaluate-btn" type="">Save Evaluation</button></div>').css('visibility', 'hidden');
    }
    this.getCorrectCount = function() {
        return this.correct;
    }
    this.getWrongCount = function() {
        return this.wrong;
    }
    this.getSkippedCount = function() {
        return this.skipped;        
    }
    this.saveEvaluation = function() {
        var current = this;
        var valuations = {};
        $('.save-mark-option').each(function(){
            valuations[$(this).attr('data-question-id')] = $(this).val();
        });
        var __attemptedId = this.getAttemptId();
        $.ajax({
            url: this._adminUrl+'assessments/save_evaluation',
            type: "POST",
            async : false,
            data:{"is_ajax":true, "attempt_id":this.getAttemptId(), "valuations":JSON.stringify(valuations)},
            success: function(response) {
                var data = JSON.parse(response);
                $('#saveEvaluation').html('Refreshing...');
                current.loadAttemptDetails();
                var faculty_img  = (data['faculty_img']!=null)?data['faculty_img']:'default.jpg';
                var faculty_name = (data['faculty_name']!=null)?data['faculty_name']+".":'';
                var user_img    = ((faculty_img == 'default.jpg')?webConfigs('default_user_path'):webConfigs('user_path'));
                $("#current_grade_"+__attemptedId).html(data['grade']);
                $("#evaluated_txt_"+__attemptedId).html('Evaluated');
                $("#evaluated_txt_"+__attemptedId).removeClass('evaluate-btn');
                $("#evaluated_txt_"+__attemptedId).addClass('evaluated-btn');
                $("#evaluate_checkbox_"+__attemptedId).remove();
                $("#faculty_img_"+__attemptedId).attr("onclick", "").unbind("click");
                $("#faculty_img_"+__attemptedId).attr("title", "");
                $("#faculty_img_"+__attemptedId).attr("data-original-title", "");
                 $("#faculty_img_"+__attemptedId).attr("title", faculty_name);
                $("#faculty_img_"+__attemptedId).attr("src", user_img+faculty_img);
                $("#user_register_"+__attemptedId).addClass('left-space');
                $("#current_mark_"+__attemptedId).html(data['mark_scored']);
            }
        });
    }
    this.renderQuestion = function(questionId) {
     
        var question = this.attemptReportDetails('questions')[questionId];
        
        var actualAnswer = {};
        if( Object.keys(question['q_actual_answer']).length > 0 ) {
            $.each(question['q_actual_answer'], function(optionKey, option){
                actualAnswer[option['id']] = option;
            });
        }

        var questionHtml = '';
            questionHtml +=' <span class="ques-no">#'+questionId+'</span>';
            questionHtml +=' <span class="ques-type">'+(this.getQuestionTypes(question['type']))+'</span>';
            questionHtml +=' <span class="ques-close">×</span>';

            questionHtml +=' <h4 class="question-view">'+question['q_question'][1]+'</h4>';
            if(question['type'] == 1 || question['type'] == 2) {
                var options     = $.parseJSON(question['q_option']);
                var answerClass = '';
                var answer      = [];
                if(Object.keys(options).length > 0 ) {
                    questionHtml +=' <ul class="ques-choices">';
                    $.each(options, function(optionKey, option ) {
                        answerClass = '';
                        if(typeof question['user_answers'] != 'undefined' && question['user_answers'] != '') {                            
                            answer      = question['user_answers'].split(',');
                            if(typeof actualAnswer[option['id']] != 'undefined' && inArray(option['id'], answer) == true) {
                                answerClass = 'right-answer';
                            } else {
                                if(typeof actualAnswer[option['id']] != 'undefined') {
                                    answerClass = 'selected-anwser';
                                }   
                            }
                            if(typeof actualAnswer[option['id']] == 'undefined' && inArray(option['id'], answer) == true) {
                                answerClass = 'wrong-answer';
                            }
                        } else {
                            if(typeof actualAnswer[option['id']] != 'undefined') {
                                answerClass = 'selected-anwser';
                            }
                        }
                        questionHtml +='     <li class="'+answerClass+'"><span class="option-count">'+toAlpha(optionKey+2)+')</span>  '+option['qo_options'][1]+'</li>';
                    });
                    questionHtml +=' </ul>';
                }
            } else if(question['type'] == 4){
                questionHtml +=' <div class="evaluation-question">'+question['user_answers'].replace(/&#(\d+);/g, function(match, dec) { return String.fromCharCode(dec);})+'</div>';
            }else {
                
                questionHtml +=' <div class="evaluation-question">'+question['user_answers']+'</div>';
            }
        $('#question_detailed_view').html(questionHtml).show();
        $('#quiz_report_grade, #quiz_report_dashboard').hide();
     
        //console.log(question);
    }
    this.renderAttemptDashboard = function() {
        //rendering dashboard
        var userId          = this.attemptReportDetails('user_id');
        var attemptDetails  = this.attemptObject();
        var users           = attemptDetails['users'];
        var assessment      = attemptDetails['attempt_details'];
        var dashboardHeader = '';
        var userImage       = ((users[userId]['us_image'] == 'default.jpg')?__default_user_path:__user_path)+users[userId]['us_image'];
            dashboardHeader+= '<div class="avatar-image-info">';
            dashboardHeader+= '     <img src="'+userImage+'" class="img-circle" alt="" width="35">';
            dashboardHeader+= '</div>';
            dashboardHeader+= '<div class="avatar-info">';
            dashboardHeader+= '    <span class="stud-name">'+users[userId]['us_name']+'</span>';
            dashboardHeader+= '    <span class="submition-date">'+assessment['aa_attempted_date_fm']+'</span>';
            dashboardHeader+= ' </div>';
        $('#dashboard_header').html(dashboardHeader).show();
        $('#question_detailed_view').hide();
        //End

        //rendering the circle
        var speed = Math.ceil(60/((assessment['aa_total_duration']/60)/(this.getCorrectCount()+this.getWrongCount())));
        var circleHtml = '';
            circleHtml += '<div class="ques-per-hour-holder">';
            circleHtml += '    <span class="ques-per-hour">'+speed+'</span>';
            circleHtml += '    <span class="ques-per-hour-title">QS / HOUR</span>';
            circleHtml += '</div>';
        //End
        var totalAttended = Number(this.getCorrectCount()+this.getWrongCount());
        $("#content-chart").html(circleHtml).circliful({
            animation: 1,
            animationStep: 5,
            animateInView: false,
            foregroundColor: "#e00000",
            backgroundColor: "#09bf63",
            foregroundBorderWidth: 20,
            backgroundBorderWidth: 20,
            percent: Math.round((this.getCorrectCount()/totalAttended)*100),
            percent: Math.round((this.getWrongCount()/totalAttended)*100),
            percentageTextSize: 0,
        });

        var accuracy      = Math.round((this.getCorrectCount()/(this.getWrongCount()+this.getCorrectCount()))*100);
            accuracy     = (!isNaN(parseFloat(accuracy)))?accuracy:'0';
        var dashboardInfo = '';
            dashboardInfo+= '<div class="col-md-6 quiz-result-col text-center">';
            dashboardInfo+= '    <span class="correct-count">'+(this.getCorrectCount())+'</span>';
            dashboardInfo+= '    <span class="correct-title">Correct</span>';
            dashboardInfo+= '</div>';
            dashboardInfo+= '<div class="col-md-6 quiz-result-col text-center">';
            dashboardInfo+= '    <span class="accuracy-percent">'+accuracy+'%</span>';
            dashboardInfo+= '    <span class="accuracy-title">Accuracy</span>';
            dashboardInfo+= '</div>';
            dashboardInfo+= '<div class="col-md-6 quiz-result-col text-center">';
            dashboardInfo+= '    <span class="wrong-count">'+(this.getWrongCount())+'</span>';
            dashboardInfo+= '    <span class="wrong-title">Wrong</span>';
            dashboardInfo+= '</div>';
            dashboardInfo+= '<div class="col-md-6 quiz-result-col text-center">';
            dashboardInfo+= '    <span class="time-count">'+(convertTime(assessment['aa_duration']))+'</span>';
            dashboardInfo+= '    <span class="time-title">Time Taken</span>';
            dashboardInfo+= '</div>';
        $('#dashboard_info').html(dashboardInfo);

        //render Grade
        var gradeHtml = '';
        var current = this;
        if(Object.keys(attemptDetails['grade']).length > 0 ) {
            $.each(attemptDetails['grade'], function(gradeKey, grade){
                gradeHtml += '<li '+((assessment['aa_grade'] == grade['gr_name'])?'class="active-grade"':'')+' data-grade="'+grade['gr_name']+'" onclick="saveGrade(\''+current.getAttemptId()+'\', \''+grade['gr_name']+'\')" data-toggle="tooltip" title="'+grade['gr_range_from']+'% - '+grade['gr_range_to']+'%">'+grade['gr_name']+'</li>';
            });
        }
        // <li class="border-list-left" data-toggle="tooltip" title="0% - 10%">F</li>
        // <li class="border-list-right" data-toggle="tooltip" title="80% - 90%">O</li>
        $('#grade_list').html(gradeHtml);
        $('[data-toggle="tooltip"]').tooltip(); 
        //end

        //rendering evaluation details
        if(+assessment['aa_valuated_by'] > 0 ) {
            $('#evaluated_by').html('Evaluated by : '+attemptDetails['users'][assessment['aa_valuated_by']]['us_name']);
            $('#evaluated_on').html('Evaluated on : '+assessment['aa_evaluated_date_fm']);
        }
        if(__need_evaluation == false){
            $("#startEvaluation").hide();
        } else {
            $("#startEvaluation").show();
        }
        $('#quiz_report_grade, #quiz_report_dashboard').show();
    }
    this.getQuestionTypes = function(questionType) {
        return this.questionTypes[questionType];
    }
    this.generateMarkOption = function(question) {
        // var optionHtml = '<option '+((+question['user_mark'] == 0)?'selected="selected"':'')+' value="'+(0)+'">0</option>';
        optionHtml = "";
        var negativeNumber = (Math.abs(question['q_negative_mark']) * -1)-0.5;
        // alert(negativeNumber+"--"+question['q_positive_mark']);
        var progress = true;
        while(progress == true) {
            negativeNumber = negativeNumber+0.5;
            optionHtml += '<option '+((question['user_mark'] == negativeNumber)?'selected="selected"':'')+' value="'+(negativeNumber)+'">'+negativeNumber+'</option>';
            if(negativeNumber >= question['q_positive_mark']) {
                progress = false;
            }
        }
        return optionHtml;
    }
    this.startEvaluation = function() {
        var questionId          = 0;
        var question            = 0;
        var method              = this;
        var questionListHtml    = '';
        $('.edit-mark-option').each(function(){
            
            questionId        = $(this).parents('.quiz-report-list-row').attr('data-question-id');
            question          = method.attemptReportDetails('questions')[questionId];
            questionListHtml  = '<select class="form-control save-mark-option" data-question-id="'+questionId+'">';
            questionListHtml +=     method.generateMarkOption(question);
            questionListHtml += '</select>';
            $(this).html(questionListHtml);
        });
        $('#saveEvaluation').css('visibility', 'visible');
    }
}
function convertTime(sec) {
    var hours = Math.floor(sec/3600);
    (hours >= 1) ? sec = sec - (hours*3600) : hours = '00';
    var min = Math.floor(sec/60);
    (min >= 1) ? sec = sec - (min*60) : min = '00';
    (sec < 1) ? sec='00' : void 0;

    (min.toString().length == 1) ? min = '0'+min : void 0;    
    (sec.toString().length == 1) ? sec = '0'+sec : void 0;    
    return hours+':'+min+':'+sec;
}

function saveGrade(attemptId, grade) {
    $.ajax({
        url: '<?php echo admin_url() ?>assessments/save_grade',
        type: "POST",
        async : false,
        data:{"attempt_id":attemptId, "grade":grade},
        success: function(response) {
            var data = JSON.parse(response);
            var faculty_img  = (data['faculty_img']!=null)?data['faculty_img']:'default.jpg';
            var faculty_name = (data['faculty_name']!=null)?data['faculty_name']+".":'';
            var user_img    = ((faculty_img == 'default.jpg')?webConfigs('default_user_path'):webConfigs('user_path'));
            $("#current_grade_"+attemptId).html(grade);
            $("#evaluated_txt_"+attemptId).html('Evaluated');
            $("#evaluated_txt_"+__attemptedId).removeClass('evaluate-btn');
            $("#evaluated_txt_"+__attemptedId).addClass('evaluated-btn');
            $("#evaluate_checkbox_"+attemptId).remove();
            $("#faculty_img_"+attemptId).attr("onclick", "").unbind("click");
            $("#faculty_img_"+attemptId).attr("title", "");
            $("#faculty_img_"+attemptId).attr("data-original-title", "");
            $("#faculty_img_"+attemptId).attr("title", faculty_name);
            $("#faculty_img_"+attemptId).attr("src", user_img+faculty_img);
            $("#user_register_"+attemptId).addClass('left-space');
        }
    });
}
$(document).on('click', '.grade-table li', function(){
    $('.grade-table li').removeClass('active-grade');
    $(this).addClass('active-grade');
});
$(document).on('click', '.ques-close', function(){
    $('#quiz_report_grade, #quiz_report_dashboard').show();
    $('#question_detailed_view').hide();
});
function renderQuestion(questionId) {
    attempt.renderQuestion(questionId);
}
function toAlpha(number){
    var alphabet = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];
    number--;
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

function htmlSubstring(s, n) {
    var m, r = /<([^>\s]*)[^>]*>/g,
        stack = [],
        lasti = 0,
        result = '';

    //for each tag, while we don't have enough characters
    while ((m = r.exec(s)) && n) {
        //get the text substring between the last tag and this one
        var temp = s.substring(lasti, m.index).substr(0, n);
        //append to the result and count the number of characters added
        result += temp;
        n -= temp.length;
        lasti = r.lastIndex;

        if (n) {
            result += m[0];
            if (m[1].indexOf('/') === 0) {
                //if this is a closing tag, than pop the stack (does not account for bad html)
                stack.pop();
            } else if (m[1].lastIndexOf('/') !== m[1].length - 1) {
                //if this is not a self closing tag than push it in the stack
                stack.push(m[1]);
            }
        }
    }

    //add the remainder of the string, if needed (there are no more tags in here)
    result += s.substr(lasti, n);

    //fix the unclosed tags
    while (stack.length) {
        result += '</' + stack.pop() + '>';
    }

    return result;

}



</script>


	<!-- quiz report modal starts -->
<div class="modal fade quiz-report-container" id="quiz_details_modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="z-index: 99999; overflow-y: hidden;">
    <div class="modal-dialog quiz-report" role="document">
        <div class="modal-content quiz-modal-height">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="icon icon-cancel-1"></span>
                </button>
                <h4 class="modal-title" id="create_box_title"></h4>
            </div>
            <div class="modal-body height100 no-padding">
                <div class="quiz-report-wrapper">
                    <div class="col-md-7 no-padding quiz-listing-holder">
                        <div class="quiz-report-container">
                            <div class="quiz-report-list-header">
                                <div class="hash">#</div>
                                <div class="questions-title col-md-10">QUESTIONS</div>
                                <div class="mark-col-title  col-md-2 text-left">MARK</div>
                            </div>

                            <div class="quiz-list-row-holder" id="question_list_wrapper">
                            </div>
                        </div>	
                        <div class="save-evaluation-footer" id="saveEvaluation" style="visibility:hidden;">
                            <div class="nav-next text-right">
                                <button class="btn btn-success start-evaluate-btn" type="">Save Evaluation</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5 no-padding quiz-evaluation-wrapper">
                        <div class="quiz-profile-info">
                            <div class="quiz-avatar-info" id="dashboard_header">
                            </div>
                            
                            <div class="nav-next text-right">
                                <button class="btn btn-success start-evaluate-btn" type="" id="startEvaluation">Start Evaluation</button>
                            </div>
                        </div>

                        <div class="col-md-12 quiz-report-chart" id="quiz_report_dashboard">
                            <div class="col-md-4 no-padding chart-box">
                                <div id="content-chart" >
                                </div>
                            </div>
                            <div class="col-md-8 no-padding chart-info" id="dashboard_info">
                            </div>
                        </div>

                        <div class="assessment-col" id="quiz_report_grade">
                            <h5 class="assesment-title title-inline">GRADE QUIZ</h5>
                            <div class="assesment-area">
                                <div class="col-md-12 no-padding">
                                    <label class="grade-title">Grade :</label>
                                    <ul class="text-center grade-table"  border="1" id="grade_list">
                                    </ul>
                                </div>

                                <div class="col-md-12 no-padding evaluation-info">
                                    <div class="col-md-6 text-left no-padding">
                                        <span class="blue-text" id="evaluated_by"></span>
                                    </div>
                                    <div class="col-md-6 text-right no-padding">
                                        <span class="blue-text" id="evaluated_on"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="quiz-evalute-detail-view" id="question_detailed_view" style="display:none;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo assets_url() ?>js/jquery.circliful.min.js"></script>

