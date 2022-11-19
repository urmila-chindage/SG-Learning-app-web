let __course_id = window.location.pathname.split('/');
__course_id = __course_id[__course_id.length - 1];
let __history_obj = [];
let __rating_selected = 0;
__announcementLimit = 5;
__announcementOffset = 0;
__announcementCount = '0';
__userpath = '';
__defaultpath = '';
__activeElement = '';
$(document).ready(function () {
    __loaded['announcement'] = false;
    __curriculum.sections = $.parseJSON(__curriculum.sections);
    __curriculum.lectures = $.parseJSON(__curriculum.lectures);
    __curriculum.log = (__curriculum.log != '' )? $.parseJSON(__curriculum.log):{}; 
    var hashTag = getQueryStringValue('tab');
    var link = window.location.protocol + "//" + window.location.host + window.location.pathname;
    switch (hashTag) {
        case 'overview':
            link = link + '?tab=overview';
            $('#loadOverViewBtn').click();
            break;
        case 'qa':
            link = link + '?tab=qa';
            $('#loadQaBtn').click();
            break;
        case 'report':
            link = link + '?tab=report';
            $('#loadReportsBtn').click();
            break;
        case 'anouncements':
            link = link + '?tab=anouncements';
            $('#loadAnouncementsBtn').click();
            break;
        case 'quiz':
            link = link + '?tab=quiz';
            loadQuiz();
            $('#loadQuizBtn').click();
            break;
        case 'assignments':
            link = link + '?tab=assignments';
            loadAssignments();
            $('#loadAssignmentBtn').click();
            break;
        default:
            $('#loadCurriculumBtn').click();
            break;
    }

    __reviews = $.parseJSON(__reviews);
    renderReviews(__reviews.reviews);
    __my_rating = +__my_rating;
    $('#my_rating').barrating({
        theme: 'fontawesome-stars',
        readonly: (__my_rating != 0),
        onSelect: function (value, text) {
            __my_rating = value;
            if (__start == true) {
                rate_course(__my_rating);
            }
        }
    });
});

$(function () {
    $('#example_course_dashboard').barrating({
        theme: 'fontawesome-stars',
        readonly: __my_rating,
        onSelect: function (value, text) {
            __rating_selected = value;
            if (__start == true) {
                rate_course(__rating_selected);
            }
        }
    });
});

var __rated = false;

function rate_course(ratingSelected) {
    __rating_selected = ratingSelected;
    __start = false;
    $('#example2').barrating({
        theme: 'fontawesome-stars',
        readonly: false,
        onSelect: function (value, text) {
            __rating_selected = value;
            $('#example_course_dashboard').barrating('set', __rating_selected);
        }
    });
    $('#example2').barrating('set', __rating_selected);
    $('#rate_course').modal('show');
}


$(document).on('hidden.bs.modal', '#rate_course', function (e) {
    __start = true;
    $('#example_course_dashboard').barrating('clear');
    $('#review_course').val('');
    if(!__rated){
        $('#my_rating').barrating('clear');
    }
});
$(document).on('hidden.bs.modal', '#rate_course_preview', function (e) {
    __start = false;
    $('#rate_course_label').html('Your rating');
    $('#example_course_dashboard').barrating('set', __rating_selected);
    $('#example_course_dashboard').barrating('readonly', true);
});
$(document).on('click', '#submit_rating_course', function () {
    __rated = true;
    var __review = $('#review_course').val();
    $.ajax({
        url: __site_url + 'material/save_rating_review',
        type: "POST",
        async: false,
        data: {
            "is_ajax": true,
            'course_id': __course_id,
            'rating': __rating_selected,
            'review': __review
        },
        success: function (response) {
            var data = $.parseJSON(response);
            $('#rate_course').modal('hide');
            $('#rate_course').on('hidden.bs.modal', function (e) {
                $('#example4').barrating({
                    theme: 'fontawesome-stars',
                    readonly: true
                });
                $('#example4').barrating('set', __rating_selected);
                $('#my_rating').barrating('set', __rating_selected);
                $('#my_rating').barrating('readonly', true);
                $('#preview_review_course').text(__review);
                $("#rate_course_preview").modal('show');
            });
        }
    });
});

function renderReviews(reviews) {
    $('#review_list').html(
        `${reviews.map(rv => {
            return `<li class="profilelist-childs">
                            <div class="profile-list-photo">
                                <img src="${rv.cc_user_image === 'default.jpg' ? __user_path.default : __user_path.native + rv.cc_user_image}" class="olp-img-rounded img-responsive svg-common profile-pic">
                                <span class="profile-name-text">${rv.cc_user_name}</span>
                                <div class="star-ratings-sprite star-rating-vertical-top-super starr-vertical-top">
                                    <span style="width:${rv.cc_rating * 20}%" class="star-ratings-sprite-rating"></span>
                                </div>
                                <span class="sub-profile-text">${relative_time_ax(rv.created_date).day}</span>
                            </div><!--profile-list-photo-->
                            <p class="profil-des">
                                ${rv.cc_reviews}
                            </p>
                        </li>`;
        }).join('')
        + (+__reviews.count <= __reviews.reviews.length ? '' : ` < a href = "javascript:void(0)"
        onclick = "getReviews(${Math.ceil(__reviews.reviews.length / __reviews.limit) + 1})"
        id = "show_more_reviews" > Show more reviews < /a>`)}`
    );
}

function getReviews(offset) {
    $.ajax({
        url: __site_url + 'course/load_reviews/' + __course_id + '/' + offset,
        type: "GET",
        success: function (response) {
            var data = $.parseJSON(response);
            if (data['success'] === true) {
                __reviews.reviews = __reviews.reviews.concat(data['reviews']);
                renderReviews(__reviews.reviews);
            }
        }
    });
}

function getIcon(params){
    let className = 'grey';
    if(+params.attempts > 0){
        className = 'green';
    }else{
        if(+params.percentage > 0){
            className = 'blue';
        }
    }
    let cIcon = {
        1: `<span class="milestone-icon video-${className}"></span>`,
        10:`<span class="milestone-icon drop-${className}"></span>`,
        11: `<span class="milestone-icon video-${className}"></span>`,
        12: `<span class="milestone-icon audio-${className}"></span>`,
        4: `<span class="milestone-icon video-${className}"></span>`,
        6: `<span class="milestone-icon code-${className}"></span>`,
        5: `<span class="milestone-icon code-${className}"></span>`,
        2: `<span class="milestone-icon document-${className}"></span>`,
        7: `<span class="milestone-icon video-${className}"></span>`,
        3: `<span class="milestone-icon quiz-${className}"></span>`,
        9: `<span class="milestone-icon video-${className}"></span>`,
        15: `<span class="milestone-icon video-${className}"></span>`,
        14: `<span class="milestone-icon certificate-${className}"></span>`,
        8: `<span class="milestone-icon assignment-${className}"></span>`
    }
    var tailHtml = '';
    
    if(!params.first && !params.last){
        tailHtml = '<div class="tail-up"></div><div class="tail-down"></div>';
    }else{
        if(params.last && !params.first){
            tailHtml = '<div class="tail-up"></div>';
        }

        if(params.first && !params.last){
            tailHtml = '<div class="tail-down"></div>';
        }
    }
    
    return `<div class="milestone-holder">
                <span class="milestone">
                    ${+params.attempts>0?'<span class="ticked"></span>':''}
                    ${cIcon[+params.type]?cIcon[+params.type]:''}
                </span>
                ${tailHtml}
            </div>`;
}

function loadCurriculum() {
    $('.tab-pane').removeClass('active').hide();
    $('.nav-tabs li a').removeClass('active-bread-parent');
    $('#loadCurriculumBtn').addClass('active-bread-parent');

    var link = window.location.protocol + "//" + window.location.host + window.location.pathname;
    window.history.pushState({
        path: link
    }, '', link);
    let __curr;
    __curr = __curriculum.sections.map(sec => {
        return {
            id: sec.id,
            name: sec.s_name,
            lectures: []
        };
    });
    
    __curr = __curr.map(cur => {
        __curriculum.lectures.map(lec => {
            if (cur.id == lec.cl_section_id && +lec.cl_lecture_type !== 13) {
                cur.lectures.push({
                    id: lec.id,
                    name: lec.cl_lecture_name,
                    cl_limited_access : lec.cl_limited_access,
                    ll_attempt : lec.ll_attempt,
                    ll_percentage : lec.ll_percentage,
                    unique: lec.unique,
                    duration : lec.cl_duration,
                    cl_total_page: lec.cl_total_page,
                    type: lec.cl_lecture_type
                });
            }
        });
        return cur;
    });
    let __sechtml = '<div class="curriculam-container">';
    __sechtml += __curr.map((sec, sIndex) => {
        if (sec.lectures.length != 0) {
            return `<div class="curriculam-title"><h4>${sec.name}</h4></div>`+
            sec.lectures.map((lec, lIndex) => {
                    let calc = '';
                    switch (+lec.type) {
                         case 1: calc =  calculate_sec_to_hrs_min_sec(lec.duration);//lec.unique; 
                         break;
                        // case 12: calc = lec.unique; break;
                        // case 2: calc = lec.cl_total_page > 1 ? lec.cl_total_page + ' Pages' : lec.cl_total_page + ' Page'; break;
                        case 7: calc = lec.unique; break;
                        case 3: calc = lec.unique > 1 ? lec.unique + ' Questions' : lec.unique + ' Question'; break;
                        // case 8: calc = lec.unique > 1 ? lec.unique + ' Pages' : lec.unique + ' Page'; break;
                        case 4: calc =  calculate_sec_to_hrs_min_sec(lec.duration);//lec.unique; 
                        case 15: calc =  calculate_sec_to_hrs_min_sec(lec.duration);//lec.unique; 
                         break;
                        default: calc = ''; break;
                    }
                    lec.ll_attempt = ((__curriculum.log[+lec.id]!==undefined) &&  (__curriculum.log[+lec.id]!=='null'))  ? __curriculum.log[+lec.id].views : 0;
                    if((__curriculum.log[+lec.id] != undefined) && ((__curriculum.log[+lec.id].views == undefined) ||__curriculum.log[+lec.id].views == null)){
                        lec.ll_attempt = 0;
                    }
                    return `<div class="timeline-row">
                                <a href="${__site_url+'materials/course/'+__course_id+'/'+lec.id}">
                                    ${getIcon({
                                        type : [+lec.type],
                                        first:lIndex==0,
                                        last:(lIndex == sec.lectures.length-1),
                                        attempts : lec.ll_attempt,
                                        percentage : lec.ll_percentage
                                    })}
                                    <div class="timeline-text text-justify">
                                        <p class="curriculam-info-text col-md-8 col-sm-10 col-xs-9 info-bold">${lec.name}</p>
                                            <p class="curriculam-info-view text-right ${lec.ll_attempt > 0?'orange-text':'green-text'} col-md-2 col-sm-2 col-xs-3 ${lec.cl_limited_access == 0?'visible-hidden':''}">
                                                <span class="${lec.ll_attempt > 0?'orange-eye':'green-eye'}"></span> ${lec.ll_attempt == 0?'-':lec.ll_attempt} / ${lec.cl_limited_access}
                                            </p>
                                        <p class="curriculam-info-duration text-right col-md-2">${calc}</p>
                                    </div>
                                </a>
                            </div>`;
                }).join("")
        }
    }).join("");
    __sechtml += '</div>';

    if(__curr.length){
        $('#curriculum_div').html(__sechtml);
    }else{
        $('#curriculum_div').html(`
            <div class="container container-res-chnger-frorm-page">
                <div class="changed-container-for-forum">
                    <div class="no-discussion-wrap">
                        <img class="no-questions-svg" src="${__theme_url}/img/no-questons.svg">
                        <span class="no-discussion"><span>Oops! </span>No contents to show</span>
                    </div>
                </div>
            </div>
        `);
    }
    $('#curriculum').addClass('active').fadeIn('slow');
}

// function to calculate seconds to hours : minutes : seconds 
function calculate_sec_to_hrs_min_sec(totalSeconds){
    var totalMinutes = Math.floor(totalSeconds / 60);
    var totalSeconds = totalSeconds - totalMinutes * 60;
    totalMinutes = String(totalMinutes).padStart(2, "0");
    totalSeconds = String(totalSeconds).padStart(2, "0");
    return totalMinutes + " : " + totalSeconds;
}

function loadAssignments() {
    var link = window.location.protocol + "//" + window.location.host + window.location.pathname;
    link = link + '?tab=assignments';
    window.history.pushState({
        path: link
    }, '', link);
    $('.tab-pane').removeClass('active').hide();
    $('.nav-tabs li a').removeClass('active-bread-parent');
    
    if($('#assignmentsArea').html().trim() == ''){
        $.ajax({
            url: __site_url + 'course/assignments/'+__course_id,
            type: "GET",
            success: function (response) {
                var data = $.parseJSON(response);
                if(data.data.length != 0){
                    $('#assignmentsArea').html(renderAssignments(data['data']));    
                }else{
                    $('#assignmentsArea').html(`
                        <div class="container container-res-chnger-frorm-page">
                            <div class="changed-container-for-forum">
                                <div class="no-discussion-wrap">
                                    <img class="no-questions-svg" src="${__theme_url}/img/no-questons.svg">
                                    <span class="no-discussion"><span>Oops! </span>No assignments to show</span>
                                </div>
                            </div>
                        </div>
                    `);
                }
            }
        });
    }

    $('#loadAssignmentBtn').addClass('active-bread-parent');
    $('#assignments').addClass('active').fadeIn('slow');
}

function renderAssignments(assignments){
    var html = `<div class="all-challenges">
                    <div class="container-altr assignment-wrapper">
                        <h2 class="assignment-title">Assignments</h2>`;
    $.each(assignments,function(a_key,assessment){
        html += renderAssignment(assessment);
    });
    return html+'</div></div>';
}

function renderAssignment(assignment){
    var html = '';
    var timeLineDate    = '';
    var markScored      = '-';
    var grade           = '-';
    var attendable      = true;
    var attendLink      = __site_url+'materials/course/'+__course_id+'/'+assignment.id+'/2';
    var lectureLink     = __site_url+'materials/course/'+__course_id+'/'+assignment.id;
    var reportLink      = '';

    if(assignment.submitted){
        timeLineDate    = assignment.a_data.dt_last_date;
        if(assignment.o_data.lo_lecture_id){
            timeLineDate    = assignment.o_data.lo_end_date;
        }
        if(+assignment.submission.dtua_evaluated){
            markScored      = assignment.submission.mark;
            grade           = assignment.submission.dtua_grade;
            reportLink      = '<a href="'+attendLink+'" class="view-feedback report-icn">View Feedback</a>';
        }else{
            markScored      = '-';
            grade           = '-';
            reportLink      = '<a href="'+lectureLink+'" class="awaiting-result">Awaiting Result</a>';
        }
    }else{
        if(assignment.expired){
            reportLink      = '<a href="javascript:void(0)" class="time-expired">Time expired</a>';
        }
        timeLineDate    = assignment.a_data.dt_last_date;
        if(assignment.o_data.lo_lecture_id){
            totTime         = assignment.o_data.lo_duration;
            timeLineDate    = assignment.o_data.lo_end_date;
        }
    }

    var html = `
            <div class="col-md-6 assignment-col">
                <div class="assignment-box-content">
                <div class="assignment-ques-info">
                    <div class="assignment-date-info"><p>${timeLineDate!=''?timeLineDate:'No Last Date'}</p></div>
                    <h2 class="assignment-question">${assignment.cl_lecture_name}</h2>
                </div>
                <div class="assignment-tags">
                    <div class="assignment-info-col col-md-7 col-sm-7 col-xs-6">
                    <div class="submition-info">
                        ${assignment.submitted?
                            `<span class="submited-title">Submitted On</span>
                            <span class="submition-date">${assignment.submission.created_date}</span>`:
                            `<button class="btn ${assignment.expired?'':'btn-success'} submit-assignment-btn${assignment.expired?'-disabled':''}" onclick="${assignment.expired?'javascript:void(0)':`window.open('${lectureLink}','_self')`}" type="">Submit Assignment</button>`
                        }
                    </div>
                    </div>
                    <div class="assignment-info-col col-md-3 col-sm-3 col-xs-4 text-center">
                    <span class="marks-scored">${(assignment.submitted && +assignment.submission.dtua_evaluated)?markScored:'-'}</span>
                    <span class="marks-scored-title">Marks Scored</span>
                    </div>
                    <div class="assignment-info-col col-md-2 col-sm-2 col-xs-2 text-right">
                    <div class="grade-info">
                        <span class="grade-scored">${(assignment.submitted && +assignment.submission.dtua_evaluated)?grade:'-'}</span>
                        <span class="grade-title">Grade</span>
                    </div>
                    </div>
                </div>
                <div class="assignment-view-info">
                    <div class="option-tag col-md-6 col-sm-6 col-xs-6 text-left">
                        ${reportLink}
                    </div>
                    <div class="option-tag col-md-6 col-sm-6 col-xs-6 text-right">
                    <a href="${lectureLink}" class="read-instruct">Read Instruction</a>
                    </div>
                </div>
                </div>
            </div>`;

    return html;
}

function loadQuiz() {
    var link = window.location.protocol + "//" + window.location.host + window.location.pathname;
    link = link + '?tab=quiz';
    window.history.pushState({
        path: link
    }, '', link);
    $('.tab-pane').removeClass('active').hide();
    $('.nav-tabs li a').removeClass('active-bread-parent');
    $('#loadQuizBtn').addClass('active-bread-parent');
    $('#quiz').addClass('active').fadeIn('slow');
    if($('#quizArea').html().trim() == ''){
        $.ajax({
            url: __site_url + 'course/assessments/'+__course_id,
            type: "GET",
            success: function (response) {
                var data = $.parseJSON(response);
                if(data.data.length != 0){
                    wow = new WOW({
                        mobile:false
                    }).init();
                    $('#quizArea').html(renderAssessmentBlock(data['data']));
                }else{
                    $('#quizArea').html(`
                        <div class="container container-res-chnger-frorm-page">
                            <div class="changed-container-for-forum">
                                <div class="no-discussion-wrap">
                                    <img class="no-questions-svg" src="${__theme_url}/img/no-questons.svg">
                                    <span class="no-discussion"><span>Oops! </span>No quiz to show</span>
                                </div>
                            </div>
                        </div>
                    `);
                }
            }
        });
    }
}

function renderAssessmentBlock(data){
    var html = '';
    html += '<div class="timeline-banner wow fadeInDown"></div>';
    html += renderAssessments(data);
    return html;
}

function renderAssessments(data){
    var html = '';
    html += '<div class="timeline">';
    var i =1;
    var result = Object.keys(data).map(function(key) {
        if(key != 0){
            return [Number(key), data[key]];
        }
    });
    var sessions = [];
    $.each(result,function(r_key,res){
        sessions.push(res[1]);
    });
    $.each(sessions,function(s_key,section){
        var mod = i%2;
        var pos = '';
        if(mod == 0){
            pos = 'Right';
        }else{
            pos = 'Left';
        }
        i++;
        html += renderAssessmentSections(section,pos);
    });
    html += '</div>';
    return html;
}

function renderAssessmentSections(section,pos){
    var html = '<div class="section-title wow fadeInDown">';
            html += '<h2>'+section['s_name']+'</h2>';
        html += '</div>';
        html += renderAssessmentLecture(section['assessments'],pos);
    return html;
}

function renderAssessmentLecture(assessments,pos){
    var html = '';
    var i = 1;
    $.each(assessments,function (a_key,assessment){
        var mod = i%2;
        var pos = mod == 0?'right':'left';
        html += '<div class="timeline-container '+pos+'">';
        if(mod == 0){
            html += renderAssessmentDetails({assessment:assessment,ownPos:'Right',parentPos:pos});
            html += renderAssessmentReport({assessment:assessment,ownPos:'Left',parentPos:pos});
        }else{
            html += renderAssessmentReport({assessment:assessment,ownPos:'Right',parentPos:pos});
            html += renderAssessmentDetails({assessment:assessment,ownPos:'Left',parentPos:pos});
        }
        i++;
        html += '</div>';
    });
    return html;
}

function renderAssessmentDetails(data){
    var html = '';
    var assessment      = data.assessment;
    var totQuestions    = 0;
    var totTime         = '';
    var totMark         = 0;
    var timeLineDate    = '';
    var markScored      = '-';
    var elapsedTime     = '-';
    var grade           = '-';
    var attendable      = assessment.attemptable;
    var attendLink      = attendable?__site_url+'materials/course/'+__course_id+'/'+assessment.id:'javascript:void(0)';
    var reportLink      = 'javascript:void(0)';
    var buttonLabel     = 'Attend';

    if(assessment.attempted){
        totQuestions    = assessment.attempt_data.aa_total_questions;
        totTime         = assessment.attempt_data.aa_total_duration;
        totMark         = assessment.attempt_data.aa_total_mark;
        timeLineDate    = assessment.attempt_data.aa_attempted_date;
        if(+assessment.attempt_data.aa_completed == 1 && +assessment.attempt_data.aa_valuated == 1){
            markScored      = assessment.attempt_data.aa_mark_scored;
            elapsedTime     = assessment.attempt_data.aa_duration;
            grade           = assessment.attempt_data.aa_grade;
            reportLink      = __site_url+'material/assesment_report_item/'+assessment.attempt_data.id;
        }else{
            markScored      = '-';
            elapsedTime     = '-';
            grade           = '-';
        }

        if(+assessment.attempt_data.aa_completed == 1){
            buttonLabel     = 'Retry';
        }else{
            buttonLabel     = 'Resume';
        }
    }else{
        totQuestions    = assessment.a_data.a_questions;
        totTime         = assessment.a_data.a_duration;
        totMark         = assessment.a_data.a_mark;
        timeLineDate    = assessment.a_data.a_to;
        
        if(assessment.o_data.lo_lecture_id){
            totTime         = assessment.o_data.lo_duration;
            timeLineDate    = assessment.o_data.lo_end_date;  
        }
    }

    html += '<div class="timeline-content timeline-content-'+data.parentPos.toLowerCase()+' wow fadeIn'+data.ownPos+'">';
        if(timeLineDate != ''){
            html += `<div class="date-view-sm-only red">
                        <p>${timeLineDate}</p>
                    </div>`;
        }
        html += '<h2 class="quiz-title">'+assessment.cl_lecture_name+'</h2>';
        html += `<div class="quiz-tags">
                    <div class="quiz-info-col text-left">
                        <span class="ques-tag-info">${totQuestions}</span>
                        <span class="ques-tag-title">${+totQuestions>1?'Questions':'Question'}</span>
                    </div>
                    <div class="quiz-info-col text-center">
                        <span class="ques-tag-info">${totTime}</span><span class="ques-tag-title">${totTime.length > 5?'Hrs':'Min'}</span>
                    </div>
                    <div class="quiz-info-col text-right">
                        <span class="ques-tag-info">${totMark}</span>
                        <span class="ques-tag-title">${totMark>1?'Marks':'Mark'}</span>
                    </div>
                </div>`;
        html += '<div class="score-board-info-sm">';
            html += `<div class="score-info width-40 text-center">
                        <h5 class="blue">${markScored != null?markScored:0}</h5>
                        <span class="score-info-label">Marks Scored</span>
                    </div>`;
            html += `<div class="score-info width-40 text-center">
                        <h5 class="red">${elapsedTime}</h5>
                        <span class="score-info-label">Time Taken</span>
                    </div>`;
            html += `<div class="score-info width-20 text-center">
                        <h5 class="green">${grade}</h5>
                        <span class="score-info-label">Grade</span>
                    </div>`;
        html += '</div>';
        if(assessment.attempted && +assessment.attempt_data.aa_completed == 1){
            html += `<div class="quiz-option-tags">
                        <div class="option-tag text-left">
                            <a href="${reportLink}" class="ques-option-info ${+assessment.attempt_data.aa_valuated==1?'report-icn':'red'}">${+assessment.attempt_data.aa_valuated==1?'View Report':'Awaiting Result'}</a>
                        </div>
                    </div>`;
        }
        html += `<div class="option-tag text-right">
                    <a href="${attendLink}" class="btn attend-btn${attendable?'':'-disabled'}">${buttonLabel}</a>
                </div>`;
    html += '</div>';
    
    return html;
}

function renderAssessmentReport(data){
    var html = '';
    var className = data.parentPos=='right'?'score-board-left':'';
    var dateClass = data.parentPos=='right'?'text-right':'';

    var assessment      = data.assessment;
    var timeLineDate    = '';
    var markScored      = '-';
    var elapsedTime     = '-';
    var grade           = '-';
    if(assessment.attempted){
        timeLineDate    = assessment.attempt_data.aa_attempted_date;
        if(+assessment.attempt_data.aa_completed == 1 && +assessment.attempt_data.aa_valuated == 1){
            markScored      = assessment.attempt_data.aa_mark_scored;
            elapsedTime     = assessment.attempt_data.aa_duration;
            grade           = assessment.attempt_data.aa_grade;
        }else{
            markScored      = '-';
            elapsedTime     = '-';
            grade           = '-';
        }
    }else{
        timeLineDate    = assessment.a_data.a_to;
        
        if(assessment.o_data.lo_lecture_id){
            timeLineDate    = assessment.o_data.lo_end_date;
        }
    }

    html += '<div class="score-board '+className+' wow fadeIn'+data.ownPos+'">';
        if(timeLineDate != ''){
            html += `<div class="date-view ${dateClass} date-${data.parentPos.toLowerCase()=='left'?'right':'left'}">
                        <p>${timeLineDate}</p>
                    </div>`;
        }
        html += '<div class="score-board-info">';
            html += `<div class="score-info width-40 text-center">
                        <h5 class="blue">${markScored != null?markScored:0}</h5>
                        <span class="score-info-label">Marks Scored</span>
                    </div>`;
            html += `<div class="score-info width-40 text-center">
                        <h5 class="red">${elapsedTime}</h5>
                        <span class="score-info-label">Time Taken</span>
                    </div>`;
            html += `<div class="score-info width-20 text-center">
                        <h5 class="green">${grade}</h5>
                        <span class="score-info-label">Grade</span>
                    </div>`;
        html += '</div>';
    html += '</div>';

    return html;
}

function loadAnouncements() {
    var link = window.location.protocol + "//" + window.location.host + window.location.pathname;
    link = link + '?tab=anouncements';
    window.history.pushState({
        path: link
    }, '', link);
    $('.tab-pane').removeClass('active').hide();
    $('.nav-tabs li a').removeClass('active-bread-parent');

    $('#loadAnouncementsBtn').addClass('active-bread-parent');
    $('#anouncements').addClass('active').fadeIn('slow');

    if (__loaded['announcement']) {
        return;
    } else {
        load_more_anouncement();
    }

}

function load_more_anouncement() {

    $('#loadmorebutton').css('display', 'none');
    var flag = __announcementOffset;

    $.ajax({
        url: __site_url + '/dashboard/announcement/',
        type: "POST",
        // beforeSend: function () {
        //     $('#announcement_div').append('<p>Loading...</p>');
        // },
        data: {
            // "count":if(flag == 0){""}
            "is_ajax": '1',
            'limit': __announcementLimit,
            'offset': __announcementOffset,
            'course_id': __course_id,
            "count": __announcementCount
        },
        success: function (response) {
            __loaded['announcement']    = true;
            var data                    = $.parseJSON(response);
            __announcementCount         = data.total_records;
            __defaultpath               = data.default_user_path;
            __userpath                  = data.user_path;

            if (data['success'] === true) {

                $('#loadmorebutton').hide();

                __announcementOffset    = data['start'];
                var groupsHtml          = '';
                if (Object.keys(data['announcement']).length > 0) {

                    $.each(data['announcement'], function (announcementsid, announcements) {
                        groupsHtml      += renderhtml(announcements);
                    });
                    var load_button = '<div class="rTableCell text-center">' +
                        '<button id="loadmorebutton"  class="btn btn-success selected margin-12 mar-bot" onclick="load_more_anouncement()">Load More' +
                        '<ripples></ripples>' +
                        '</button>' +
                        '</div>';
                    if (flag == 0) {

                        $('#announcement_div').append(groupsHtml);
                        $('#anouncements').append(load_button);
                    } else {

                        $('#announcement_div').append(groupsHtml);
                    }

                    if (data['show_load_button'] == true) {
                        $('#loadmorebutton').show();
                    } else {
                        __loaded['announcement'] = true;
                        $('#loadmorebutton').hide();
                    }
                }
                else {
                    $('#announcement_div').html('');
                    $('#anouncements').fadeIn('slow');
                    $('.nav-tabs li a').removeClass('active-bread-parent');
                    $('#loadAnouncementsBtn').addClass('active-bread-parent');
                    var errorHtml  = '';
                        errorHtml += '<div class="changed-container-for-forum">';
                        errorHtml += '<div class="no-discussion-wrap">';
                        errorHtml += '<img class="no-questions-svg" src="'+__assets_url+'themes/ofabee/img/no-questons.svg">';
                        errorHtml += '<span class="no-discussion"><span>Oops! </span>No Announcements to show</span>';
                        errorHtml += '</div>';
                        errorHtml += '</div>';
                    $('#announcement_div').html(errorHtml);
                    return;
                }
            } else {
                $('#announcement_div').html('');
                $('#anouncements').fadeIn('slow');
                $('.nav-tabs li a').removeClass('active-bread-parent');
                $(e).addClass('active-bread-parent');
                var errorHtml  = '';
                        errorHtml += '<div class="changed-container-for-forum">';
                        errorHtml += '<div class="no-discussion-wrap">';
                        errorHtml += '<img class="no-questions-svg" src="'+__assets_url+'/themes/ofabee/img/no-questons.svg">';
                        errorHtml += '<span class="no-discussion"><span>Oops! </span>No Announcements to show</span>';
                        errorHtml += '</div>';
                        errorHtml += '</div>';
                    $('#announcement_div').html(errorHtml);
                return;
            }
        }
    });

}

function loadReports() {
    var link = window.location.protocol + "//" + window.location.host + window.location.pathname;
    link = link + '?tab=report';
    window.history.pushState({
        path: link
    }, '', link);
    $('.tab-pane').removeClass('active').hide();
    $('.nav-tabs li a').removeClass('active-bread-parent');

    if($('#report_div').html().trim() == ''){
        $.ajax({
            //url: __site_url + 'course/get_topic_progress/'+__course_id,
            url: __site_url + 'course/get_subject_progress/'+__course_id,
            type: "GET",
            success: function (response) {
                var data = $.parseJSON(response);
                if(data.success && data.data.length != 0){
                    $('#report_div').html(renderReports(data['data']));    
                }else{
                    $('#report_div').html(`
                        <div class="container container-res-chnger-frorm-page">
                            <div class="changed-container-for-forum">
                                <div class="no-discussion-wrap">
                                    <img class="no-questions-svg" src="${__theme_url}/img/no-questons.svg">
                                    <span class="no-discussion"><span>Oops! </span>No reports to show</span>
                                </div>
                            </div>
                        </div>
                    `);
                }
            }
        });
    }
    $('#loadReportsBtn').addClass('active-bread-parent');
    $('#reports').addClass('active').fadeIn('slow');
}

function renderReports(reports){
    var html = '';

    if(reports.length != 0){
        //html += `<h3 class="formpage-heading graph-heading">Topic Wise Strength</h3>
                    //<div class="ling-graph-wrap">
                        //<div class="holder">`;
        html += `<h3 class="formpage-heading graph-heading">Subject Wise Strength</h3>
        <div class="ling-graph-wrap">
            <div class="holder">`;
        $.each(reports,function(r_key,report){
            html += renderReport(report);
        });
        html += `   </div>`;
        html +=     reportFooter();
        html += `</div>`
    }else{
        html = `
            <div class="no-discussion-wrap">
                <img class="no-questions-svg" src="${__theme_url}/img/no-questons.svg">
                <span class="no-discussion"><span>Oops! </span>No reports to show</span>
            </div>
        `;
    }

    return html;
}

function renderReport(report){
    return `
        <div class="bar-wrap">
            <div class="leftprogressTexr" data-toggle="tooltip" title="${report.name}">${report.name}</div>
            <div class="progressBar-wrap">
                <span class="prgressBarchild bar bar-${percentage_class(report.percentage)} cf" style="width: ${report.percentage}%;">
                    <span class="count">${report.percentage}%</span>
                </span>
            </div>
        </div>
    `;
}

function percentage_class(percentage){
    if(percentage>90){
        return 'green';
    }
    if(percentage>80&&percentage<=90){
        return 'blue';
    }
    if(percentage>=60&&percentage<=80){
        return 'violet';
    }
    if(percentage<60){
        return 'peach';
    }
}

function reportFooter(){
    return `
        <div class="bar-details">
            <div class="parent-bar-details">
                <span class="bar-tunnel green-bar"></span>
                <span class="bar-text">Excellent (above 90%)</span>
            </div>  
            <div class="parent-bar-details">
                <span class="bar-tunnel blue-bar"></span>
                <span class="bar-text">Good (80% - 90%)</span>
            </div>
        </div>   
        <div class="bar-details bardetails-second">
            <div class="parent-bar-details">
                <span class="bar-tunnel bar-violet"></span>
                <span class="bar-text">Average (60% - 80%)</span>
            </div>
            <div class="parent-bar-details">
                <span class="bar-tunnel bar-peach"></span>
                <span class="bar-text">Needs Improvement (below 60%)</span>
            </div>                
        </div>
    `;
}

function loadQa() {
    // AndroidInterface.showToast('hellloooo this is working');
    
    var link = window.location.protocol + "//" + window.location.host + window.location.pathname;
    link = link + '?tab=qa';
    request_for_action: link;
    window.history.pushState({
        path: link
    }, '', link);
    $('.tab-pane').removeClass('active').hide();
    $('.nav-tabs li a').removeClass('active-bread-parent');
    $('#loadQaBtn').addClass('active-bread-parent');
    $('#discussions').addClass('active').fadeIn('slow');
}

function loadOverView() {
    var link = window.location.protocol + "//" + window.location.host + window.location.pathname;
    link = link + '?tab=overview';
    window.history.pushState({
        path: link
    }, '', link);
    $('.tab-pane').removeClass('active').hide();
    $('.nav-tabs li a').removeClass('active-bread-parent');
    $('#loadOverViewBtn').addClass('active-bread-parent');
    $('#overview').css('visibility', 'visible');
    $('#overview').addClass('active').fadeIn('slow');
}

function relative_time_ax(date_str) {
    var date_time = new Object();
    var d = new Date(date_str);
    var month = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    var date = d.getDate() + " " + month[d.getMonth()] + " " + d.getFullYear();
    var time = d.toLocaleTimeString().toLowerCase();
    date_time.day = date;
    date_time.time = time;
    return date_time;
}

function relative_time(date_str) {
    if (!date_str) {
        return;
    }
    date_str = $.trim(date_str);
    date_str = date_str.replace(/\.\d\d\d+/, ""); // remove the milliseconds
    date_str = date_str.replace(/-/, "/").replace(/-/, "/"); //substitute - with /
    date_str = date_str.replace(/T/, " ").replace(/Z/, " UTC"); //remove T and substitute Z with UTC
    date_str = date_str.replace(/([\+\-]\d\d)\:?(\d\d)/, " $1$2"); // +08:00 -> +0800
    var parsed_date = new Date(date_str);
    var relative_to = (arguments.length > 1) ? arguments[1] : new Date(); //defines relative to what ..default is now
    var delta = parseInt((relative_to.getTime() - parsed_date) / 1000);
    delta = (delta < 2) ? 2 : delta;
    var r = '';
    if (delta < 60) {
        r = delta + 'few seconds ago';
    } else if (delta < 120) {
        r = 'one minute ago';
    } else if (delta < (45 * 60)) {
        r = (parseInt(delta / 60, 10)).toString() + ' minutes ago';
    } else if (delta < (2 * 60 * 60)) {
        r = 'an hour ago';
    } else if (delta < (24 * 60 * 60)) {
        r = '' + (parseInt(delta / 3600, 10)).toString() + ' hours ago';
    } else if (delta < (48 * 60 * 60)) {
        r = 'one day ago';
    } else {
        r = (parseInt(delta / 86400, 10)).toString() + ' days ago';
    }
    return r;
}

function renderhtml(announcements) {

    user_img = (announcements['us_image'] == 'default.jpg') ? __defaultpath : __userpath;
    user_img = user_img + announcements['us_image'];
    access = '';
    return `
            <div class="panel-group anouncement-pannel" id="an_id` + announcements['id'] + `" data-id="` +
        announcements['id'] + `" data-title="` + announcements['an_title'] + `" data-anto="` +
        announcements['an_sent_to'] + `" data-batch="` + announcements['an_batch_ids'] + `" data-ins="` +
        announcements['an_institution_ids'] +
        `">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="anouncement-holder">
                            <div class="width-95">
                                <div class="media">
                                    <div class="media-left">
                                        <span class="icon-wrap-round img">
                                            <img class="img-circle" width="50" src="` + user_img + `">
                                        </span>
                                    </div>
                                    <div class="media-body" style="padding-left: 10px;">
                                        <span class="media-heading announcement-name" style="font-size: 20px;">
                                            ` + announcements['us_name'] + `
                                        </span>
                                        <p>posted an announcement - ` + dateFormat(announcements['an_created_date']) + `
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                        <div class="anouncement-content">
                            <div class="anouncement-title">
                                ` + announcements['an_title'] + `
                            </div>
                            <div id="an_` + announcements['id'] +
        `_des" class="redactor-editor">
                                ` + announcements['an_description'] + `
                            </div>
                        </div>
                    </div>
                </div>
            </div>`;
}

function dateFormat(data) {
    var mydate = new Date(data);
    var month = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
        "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
    ][mydate.getMonth()];
    str = mydate.getFullYear() + ' ' + month + ' ' + mydate.getDate();
    return str;
}

function getQueryStringValue(key) {
    return decodeURIComponent(window.location.search.replace(new RegExp("^(?:.*[&\\?]" + encodeURIComponent(key).replace(/[\.\+\*]/g, "\\$&") + "(?:\\=([^&]*))?)?.*$", "i"), "$1"));
}

window.onpopstate = function () {
    var hashTag = getQueryStringValue('tab');
    var link = window.location.protocol + "//" + window.location.host + window.location.pathname;
    switch (hashTag) {
        case 'overview':
            $('#loadOverViewBtn').click();
            break;
        case 'qa':
            $('#loadQaBtn').click();
            break;
        case 'report':
            $('#loadReportsBtn').click();
            break;
        case 'anouncements':
            $('#loadAnouncementsBtn').click();
            break;
        case 'quiz':
            loadQuiz();
            $('#loadQuizBtn').click();
            break;
        case 'assignments':
            loadAssignments();
            $('#loadAssignmentBtn').click();
            break;
        default:
            $('#loadCurriculumBtn').click();
            break;
    }
    __history_obj.push(getQueryStringValue('tab'));
    if (__history_obj[__history_obj.length - 1] == __history_obj[__history_obj.length - 2]) {
        window.location.href = '/dashboard/courses';
    }
}