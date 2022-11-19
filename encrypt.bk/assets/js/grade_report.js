var __institute_id      = '';
var __filter_dropdown   = '';
var __batch_id          = '';

$(document).ready(function () {

    var grades      = $.parseJSON(__grades);
    var gradesArray = [];
    $.each(grades, function(key, grade){
        gradesArray.push(grade['gr_name']);
    });
    var filter          = getQueryStringValue('filter');
    var keyword         = getQueryStringValue('keyword');
    var institute_id    = getQueryStringValue('institute_id');
    var batch_id        = getQueryStringValue('batch_id');
    if (filter != '') {
        __filter_dropdown = atob(filter);
        if(gradesArray.indexOf(__filter_dropdown) != -1) {
            $('#filter_report_text').html('Grade '+__filter_dropdown + '<span class="caret"></span>');
        } else {
            $('#filter_report_text').html($('#filer_dropdown_list_' + __filter_dropdown).text() + '<span class="caret"></span>');
        }
    }
    if (keyword != '') {
        keyword = keyword.split('-').join(' ');
        $('#user_keyword').val(keyword);
    }
    if (institute_id != '') {
        __institute_id = institute_id;
        var institude_code  = $('#filter_institute_' + institute_id).text().substring(0, $('#filter_institute_' + institute_id).text().indexOf('-'));
        if(institude_code == '')
        {
            $('#filter_institute').html('All Institutes <span class="caret"></span>');
            __institute_id = '';
        } else {
            $('#filter_institute').html($('#filter_institute_' + institute_id).text().substring(0, $('#filter_institute_' + institute_id).text().indexOf('-')) + '<span class="caret"></span>');
        }        
    }
    
    if (batch_id != '') {
        __batch_id = batch_id;
        $('#filter_batch').html('<span class="dropdown-filter" title="'+ $('#filter_batch_' + batch_id).text() +'">' +$('#filter_batch_' + batch_id).text() + '</span><span class="caret"></span>');
    }

});


var timeOut = '';
$(document).on('keyup', '#user_keyword', () =>  {
    clearTimeout(timeOut);
    timeOut = setTimeout(function () {
        __offset    = 1;
        getGradeReport();
    }, 600);
});

$(document).on('click', '#basic-addon2', () =>  {
    __offset = 1;
    var user_keyword = $('#user_keyword').val().trim();        
    if(user_keyword == '')
    {
        lauch_common_message('KEYWORD MISSING', 'Please enter a valid input to search.');
    }
    else
    {
        __offset    = 1;
        getGradeReport();
    }    
});
$(document).on('click', '#searchclear', () =>  {
    getGradeReport();
});
function filter_institute(institute_id) {
    if (institute_id == 'all') {
        __institute_id = '';
        $('#filter_institute').html('All Institutes <span class="caret"></span>');
    } else {
        __institute_id = institute_id;
        $('#filter_institute').html($('#filter_institute_' + institute_id).text().substring(0, $('#filter_institute_' + institute_id).text().indexOf('-')) + '<span class="caret"></span>');
    }
    __batch_id = '';
    __offset = 1;
    getGradeReport();
}

function filter_report_by(filter) 
{
    var grades    = $.parseJSON(__grades);
    var gradesArray = [];
    $.each(grades, function(key, grade){
        gradesArray.push(grade['gr_name']);
    });
    if (filter == 'all') {
        $('#user_keyword').val('');
    }
    __filter_dropdown = filter;

    if(gradesArray.indexOf(filter) != -1) {
        $('#filter_report_text').html('Grade '+filter + '<span class="caret"></span>');
    } else {
        $('#filter_report_text').html($('#filer_dropdown_list_' + filter).text() + '<span class="caret"></span>');
    }
    __offset = 1;
    getGradeReport();
}

function filter_batch(batch_id) {
    if (batch_id == 'all') {
        __batch_id = '';
        $('#filter_batch').html('All Batches <span class="caret"></span>');
    } else {
        __batch_id = batch_id;
        $('#filter_batch').html('<span class="dropdown-filter">' + $('#filter_batch_' + batch_id).text() + '</span><span class="caret"></span>');
    }
    __offset = 1;
    getGradeReport();
}

function renderGradeReport()
{
    var lecureNamesHtml     = '';
    var studentNamesHtml    = '';
    var lectureStatusHtml   = '';
    var subscriberCount     = Object.keys(__subscribers).length;
    var lectureCount        = Object.keys(__lectures).length;

    lecureNamesHtml     += '<tbody>';
    lecureNamesHtml     += '<tr>';
    $('#popUpMessage').remove();
    if(lectureCount > 0) {
        var emptyCountLectures   = 60 - lectureCount;
        $.each(__lectures, function(lectureKey, lecture){
            lecureNamesHtml     += '<td><span class="rt-text">'+ lecture['cl_lecture_name'] +'</span></td>';
        });
        if (emptyCountLectures > 0) {
            for (var i=0; i<emptyCountLectures; i++) {
                lecureNamesHtml += '<td><span class="rt-text"></span></td>';
            }
        }
    }
    lecureNamesHtml     += '</tr>';
    lecureNamesHtml     += '</tbody>';

    studentNamesHtml        += '<tbody>';
    if(subscriberCount > 0) {
        $('#course_report_container_wrapper').show();
        var emptyCountStudents      = 30 - subscriberCount;
        var toolTipHtml = '';
        $.each(__subscribers, function(subscriberKey, subscriber){
            if(subscriber['cs_user_name']!=null){
                if(subscriber['cs_user_name'].length > 13 ) {
                    toolTipHtml = 'data-toggle="tooltip" title="' + subscriber['cs_user_name'] + '" data-placement="right"';
                } 
            } 
            else
            {
                toolTipHtml = '';
            }
            studentNamesHtml    += '<tr class="standard-rows">';
            studentNamesHtml    += '    <td class="candidate-name" ><span '+toolTipHtml+' class="candidate-name-trim">' + subscriber['cs_user_name'] + '</span></td>';
            studentNamesHtml    += (subscriber['cs_percentage'] != 0)? '<td>'+subscriber['cs_percentage']+'</td>' : '<td>-</td>';
            //console.log(subscriber['cs_manual_grade'],'cs_manual_grade');
            //console.log(subscriber['cs_auto_grade'],'cs_auto_grade');
            var manuaGradeClass = (subscriber['cs_manual_grade'] != '') ? 'manual-grade' : ''; 
            var tdTitle  = (subscriber['cs_manual_grade'] != '') ? 'Manually graded, Double Click to Edit' : 'Double Click to Edit'; 
            var grade    = (subscriber['cs_manual_grade'] == '' || subscriber['cs_manual_grade'] == null)?((subscriber['cs_auto_grade']!='')?subscriber['cs_auto_grade']:'-'):subscriber['cs_manual_grade'];
            console.log(grade, 'grade');
            grade = ((typeof grade != 'undefined') ? grade : '-');
            studentNamesHtml    += '    <td class="'+manuaGradeClass+'" title="'+tdTitle+'" id="courseGrade_'+subscriber['id']+'" data-user="'+subscriber['cs_user_id']+'" data-grade="'+grade+'" ondblclick="changeCourseGrade(\''+subscriber['id']+'\')">'+grade+'</td>';
            studentNamesHtml    += '</tr>';
        });
    }else{
        if(__offset == 1){
            $('#popUpMessage').remove();
            $('#course_report_container_wrapper').hide();
            $('#course_report_container_wrapper').before('<div class="text-center"><div style="margin-top:15px;" id="popUpMessage" class="alert alert-danger"><a data-dismiss="alert" class="close">×</a>No users found.</div></div>');
            setTimeout(function(){
                $('#popUpMessage').hide();
            }, 1000)
        }
    }
    studentNamesHtml        += '</tbody>';

    lectureStatusHtml       +='<tbody>';
    if( subscriberCount > 0) {
        var emptyCountStudents      = 30 - subscriberCount;
        $.each(__subscribers, function(subscriberKey, subscriber){
            lectureStatusHtml       += '<tr>';
            if( lectureCount > 0) {
                var emptyCountLectures   = 60 - lectureCount;
                var lectureLog = ( subscriber['cs_lecture_log'] != null && subscriber['cs_lecture_log'] != '' )?$.parseJSON(subscriber['cs_lecture_log']):{};
                $.each(__lectures, function(lectureKey, lecture){
                    if( typeof lectureLog[lecture['id']] != 'undefined') {
                        var lecGrade = typeof lectureLog[lecture['id']].grade != 'undefined' ? lectureLog[lecture['id']].grade : '-';
                        lectureStatusHtml   += '<td data-lecture="'+lecture['id']+'" data-subscription="'+subscriber['id']+'" data-user="'+subscriber['cs_user_id']+'" id="lectureGrade_'+subscriber['id']+'_'+lecture['id']+'" data-grade="'+lecGrade+'" title="Double Click to Edit" ondblclick="changeCourseGrade(\''+subscriber['id']+'\',\''+lecture['id']+'\')">'+ lecGrade +'</td>';
                    } else {
                        lectureStatusHtml   += '<td data-lecture="'+lecture['id']+'" data-subscription="'+subscriber['id']+'" data-user="'+subscriber['cs_user_id']+'" id="lectureGrade_'+subscriber['id']+'_'+lecture['id']+'" data-grade="-" title="Not submitted.">-</td>';
                    }                            
                });
                if (emptyCountLectures > 0) {
                    for (var k=0; k<emptyCountLectures; k++) {
                        lectureStatusHtml += '<td></td>';
                    }
                }
            }
            lectureStatusHtml       += '</tr>';
        });
    }
    lectureStatusHtml       +='</tbody>';
    $('#container_lecture_names').html(lecureNamesHtml);
    $('#loaded_subscribers').html(subscriberCount);
    $('#total_subscribers').html(__total_subscribers);
    if(__offset == 1) {
        $('#container_student_names').html(studentNamesHtml);
        $('#container_lecture_status').html(lectureStatusHtml);
    } else {
        $('#container_student_names').append(studentNamesHtml);
        $('#container_lecture_status').append(lectureStatusHtml);
    }
    initToolTip();
}

function changeCourseGrade(target_id,lecture_id = 0){
    if(+lecture_id != 0){
        var grade = $('#lectureGrade_'+target_id+'_'+lecture_id).attr('data-grade');
        $('#lectureGrade_'+target_id+'_'+lecture_id).html(gradeHtml(grade,target_id,lecture_id));
    }else{
        var grade = $('#courseGrade_'+target_id).attr('data-grade');
        $('#courseGrade_'+target_id).html(gradeHtml(grade,target_id));
    }
}

function gradeHtml(current = '-',target_id,lecture_id=0){
    var transformedGrades = $.parseJSON(__grades);
    transformedGrades = Object.values(transformedGrades);
    var html = '';
    html = `<select class="grade-selector" onchange="gradeChanged(this,'${target_id}','${lecture_id}')">
                <option  ${current=='-'?'selected':''} value="-">-</option>`;
    $.each(transformedGrades,function(gCount,grageObj){
        html +=     `<option ${grageObj.gr_name == current?'selected':''} value="${grageObj.gr_name}">${grageObj.gr_name}</option>`;
    });
    html += `</select>`;

    return html;
}

function gradeChanged(instance,target_id,lecture_id = 0){
    var mainGrade;
    var user;
    if(lecture_id != 0){
        mainGrade   = $(instance).val();
        user = $('#lectureGrade_'+target_id+'_'+lecture_id).attr('data-user');
    }else{
        mainGrade   = $(instance).val();
        user = $('#courseGrade_'+target_id).attr('data-user');
    }
    //console.log(mainGrade,'mainGrade');
    $.ajax({
        url: webConfigs('admin_url')+ 'report/change_grade',
        type: "POST",
        data: {
            'course':__course_id,
            'lecture':lecture_id,
            'subscription':target_id,
            'grade':mainGrade,
            'user':user
        },
        success: function (response) {
            //console.log(response);
            var data = $.parseJSON(response);
            if(data['success'] == true){
                if(lecture_id != 0){
                    $('#lectureGrade_'+target_id+'_'+lecture_id).attr('data-grade',mainGrade);
                    $('#lectureGrade_'+target_id+'_'+lecture_id).html(mainGrade);
                }
                $('#courseGrade_'+target_id).attr('data-grade',data.grade);
                $('#courseGrade_'+target_id).html(data.grade);
                $('#courseGrade_'+target_id).addClass('manual-grade');
                $('#courseGrade_'+target_id).attr("title", "Manually graded, Double Click to Edit");
                //console.log(data.grade,'data.grade');
            }else{
                lauch_common_message('Error Occured', data['message']);
            }
        }
        
    });
}

var __requestOnProgress     = false;
function getGradeReport() 
{
    var keyword = $('#user_keyword').val().trim();

    if (history.pushState) {
        var link = window.location.protocol + "//" + window.location.host + window.location.pathname;

        if (__course_id != '') {
            link += '?&course=' + __course_id;
        }
        if (__filter_dropdown != '') {
            link += '&filter=' + btoa(__filter_dropdown);
        }
        if (keyword != '') {
            var uSearch = keyword.split(' ').join('-');
            link += '&keyword=' + uSearch;
        }
        if (__institute_id != '') {
            link += '&institute_id=' + __institute_id;
        }
        if (__batch_id != '') {
            link += '&batch_id=' + __batch_id;
        }
       
        window.history.pushState({
            path: link
        }, '', link);
    }
    if(__requestOnProgress  == false){
        __requestOnProgress = true;
        $.ajax({
            url: webConfigs('admin_url')+ 'report/grade_report_json',
            type: "POST",
            data: {
                "is_ajax": true,
                "filter": btoa(__filter_dropdown),
                'course_id': __course_id,
                'keyword': keyword,
                'institute_id': __institute_id,
                'batch_id': __batch_id,
                'offset': __offset,
                'limit': __limit
            },
            success: function (response) {
                __requestOnProgress     = false;
                var data    = $.parseJSON(response);
                __load_more     = (data['load_more'] == true)? 'true':'false';
                if (typeof data['batches'] != 'undefined' && Object.keys(data['batches']).length > 0) {
                    $('#filter_batch_div').attr('style', '');
                    var batchHtml = '<li><a href="javascript:void(0)" id="filter_batch_all" onclick="filter_batch(\'all\')">All Batches </a></li>';
                    for (var i in data['batches']) {
                        var batchNameToolTip = '';
                        if (data['batches'][i]['batch_name'].length > 15) {
                            batchNameToolTip = 'data-toggle="tooltip" title="' + data['batches'][i]['batch_name'] + '"';
                        }
                        batchHtml += '<li><a href="javascript:void(0)" id="filter_batch_' + data['batches'][i]['id'] + '" onclick="filter_batch(' + data['batches'][i]['id'] + ')" ' + batchNameToolTip + '>' + data['batches'][i]['batch_name'] + '</a></li>';
                    }
                    $('#batch_filter_list').html(batchHtml);
                    if (__batch_id == '') {
                        $('#filter_batch').html('All Batches <span class="caret"></span>');
                    }
                } else {
                    $('#filter_batch_div').css('display', 'none');
                }
                __subscribers        = data['subscribers'];
                __lectures           = data['lectures'];
                __total_subscribers  = data['total_subscribers'];
    
                renderGradeReport();

            }
            
        });
    }
}

var scrollEl = el.getScrollElement();
var preventBodyScroll = false;

window.addEventListener('mousewheel', ev => {
    const { scrollHeight, scrollTop, offsetHeight } = scrollEl;
    const { wheelDelta } = ev;

    if (preventBodyScroll) {
        if (wheelDelta < 0 && scrollHeight === scrollTop + offsetHeight ) {
            if(__load_more  == 'true' && __requestOnProgress == false){
                __offset++;
                getGradeReport();
            }                
        }
    }
});
scrollEl.addEventListener('mouseenter', () => preventBodyScroll = true);
scrollEl.addEventListener('mouseleave', () => preventBodyScroll = false);

function exportGradeReport() {
    var course_id               = getQueryStringValue('course');
    var institute_id            = __institute_id;
    var user_keyword            = $('#user_keyword').val();
    var batch_id                = __batch_id;
    var filter_by               = __filter_dropdown;
    if(course_id==""){
        var messageObject = {
            'body':'Please choose course',
            'button_yes':'OK', 
            'button_no':'CANCEL'
        };
        callback_warning_modal(messageObject);
        return false;
    } 
    var param = {
        "course_id" : course_id,
        "institute" : institute_id,
        "batch"     : batch_id,
        "keyword"   : user_keyword,
        "filter"    : btoa(filter_by)
    };
    param = JSON.stringify(param);
    // console.log(param);
    var pathname                = '/admin/report/export_grade_report';
    var link = window.location.protocol + "//" + window.location.host + pathname;
    window.location = link + '/' + btoa(param);
}

function getQueryStringValue(key) {
    return decodeURIComponent(window.location.search.replace(new RegExp("^(?:.*[&\\?]" + encodeURIComponent(key).replace(/[\.\+\*]/g, "\\$&") + "(?:\\=([^&]*))?)?.*$", "i"), "$1"));
}

$(".content-nav-tbl").click(function() {
    $('.close').trigger('click');
});