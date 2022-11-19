<?php  include_once 'header.php'; ?>
<?php include_once('report_tab.php') ?>
<style>
.course-report-filter{
    height: 50px;
    width: 100%;
    padding: 15px;
    border: none !important;
    border-radius: 0 !important;
    box-shadow: none !important;
    font-size: 14px;
    font-weight: 600;
}
.width-auto {width: auto !important;}
.dropdown-menu.white {height: 209px;overflow: auto;}
.dropdown-menu.white.multi-drop{padding-left:15px;}
.rTable.content-nav-tbl .rTableRow > .rTableCell label{ padding: 0 0 13px;}
.load-reports{width: 100px;display: inline-block;left: 27px;position: absolute;top: 60px;}
</style>
<?php $selected = "selected='selected'"; ?>
<div class='dashbrd-container pos-top50 main-content'>
    <div class="course-report" id="course_report_container_wrapper" <?php echo ((!empty($lectures)) && (!empty($subscribers)))?'':'style="visibility: hidden;"'  ?> >

           <!-- Generate student grade report -->
            <div class="stud_grade_report" style="visibility:visible;">
                <div class="report_title text-center"><h4>Generate Student Assignment Report</h4></div>
                <div class="grade_select_container">
                    <div class="half-width">
                    <div class="course-filter">
                        <select onchange="courseFilter();" class="course-report-filter" id="course-report-filter" >
                            <option value="0">Select Course</option>
                            <?php foreach($courses as $course){  ?>
                                <option  <?php echo ($course['id']==$course_id)?$selected:'' ?> value="<?php echo $course['id']; ?>"><?php echo $course['cb_code'].' - '.$course['cb_title']; ?></option>
                            <?php } ?>
                      
                        </select>
                    </div>
                    </div>
                    <div class="half-width">
                    <div class="course-filter">
                        <select class="course-report-filter" id="assignment_filter" onchange="updateUrlState()">
                            <option value="0" >Select Assignment</option>
                            <?php if(isset($assignments)): ?>
                            <?php foreach($assignments as $assignment): ?>
                                <option <?php echo ($assignment['id']==$assignment_id)?$selected:'' ?> value="<?php echo $assignment['id'] ?>" ><?php echo $assignment['cl_lecture_name'] ?></option>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                     </div>
                    <div class="grade-filters">
                    <?php //print_r($admin); die();  //role_manager ?>
                        <?php if(!isset($role_manager['institute_id'])): ?> 
                        <div class="choose-inst">
                            <select  class="no-select-style" id="institute_filter"  onchange="instituteFilter();">
                                <option value="all">Choose Institute</option>
                                <?php foreach($institutes as $institute): ?>
                                <option <?php echo ($institute['id']==$institute_id)?$selected:'' ?> value="<?php echo $institute['id']; ?>"><?php echo $institute['ib_institute_code'] . ' - ' . $institute['ib_name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php endif;?>

                        <?php 
                        $half_width ='';
                        if(isset($role_manager['institute_id'])): 
                        $half_width = 'width-auto';
                        ?> 
                        <div class="half-width">
                        <?php endif;?>
                        <div class="choose-batch <?php echo $half_width; ?>">
                            <select name="" class="no-select-style" id="batch_filter" onchange="updateUrlState()">
                                <option value="all">Choose Batch</option>
                                <?php if (!empty($batches)): ?>
                                    <?php foreach ($batches as $batch): ?>
                                    <option <?php echo ($batch['id']==$batch_id)?$selected:'' ?> value="<?php echo $batch['id'] ?>"><?php echo $batch['batch_name'] ?></option>
                                    <?php endforeach; ?>
                                <?php endif;?>
                            </select>
                        </div>
                        <?php if(isset($role_manager['institute_id'])): ?> 
                        </div>
                        <div class="half-width">
                        <?php endif;?>
                        <div class="filter-by <?php echo $half_width; ?>">
                            <select name="" class="no-select-style" id="filter-by" onchange="updateUrlState()">
                                <option value="all">Filter By</option>
                                <?php $filters = array('all' => 'All', 'submitted' => 'Submitted', 'not_submitted' => 'No Submitted', 'late_submit' => 'Late Submitted', 'to_evaluate' => 'To Evaluate') ?>
                                <?php if (!empty($filters)): ?>
                                    <?php foreach ($filters as $key => $filter): ?>
                                       <option <?php echo ($key==$filter_by)?$selected:'' ?> value="<?php echo $key; ?>"><?php echo $filter ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                <?php
                                if(!empty($grades)) {
                                    foreach($grades as $grade){
                                        ?>
                                        <option <?php echo ($filter_by==$grade['gr_name'])?$selected:'' ?> value="<?php echo $grade['gr_name'] ?>"><?php echo 'Grade '. $grade['gr_name'] ?></option>
                                        <?php
                                    }
                                }
                                ?>
                           
                            </select>
                        </div>
                        <?php if(isset($role_manager['institute_id'])): ?> 
                        </div>
                        <?php endif;?>
                    </div>
                    <div class="pull-right filter-btn">
                        <button class="btn btn-green selected" onclick="exportAssignmentReport()">EXPORT REPORT</button>
                        <button class="btn btn-green selected" onclick="getReport()">VIEW REPORT</button>
                    </div>
                </div>
            </div>
            <!-- Generate student grade report ends -->

    </div>

</div>
<script>
// $(document).ready(function() {
//     $("#course-report-filter").val('0');
//     $("#assignment_filter").val('0');
//     $("#institute_filter").val('all');
//     $("#batch_filter").val('all');
//     $("#filter-by").val('all');

//     var query = window.location.search.substring(1);
//     if(query!=''){
//         var qs = parse_query_string(query);
//         $('#course-report-filter option[value="'+qs.course+'"]').attr("selected",true);
//         $('#course-report-filter').val(qs.course);
//         courseFilter();

//     }
// });
 function exportAssignmentReport(){
        var course_id     = $('#course-report-filter').val();
        var assignment_id = $('#assignment_filter').val();
        if((course_id=='0') || (course_id=='all')){
            var messageObject = {
            'body':'Please choose course',
            'button_yes':'OK', 
            'button_no':'CANCEL'
            };
            callback_warning_modal(messageObject);
            return false;
        } 

        if((assignment_id=='0') || (assignment_id=='all')){
            var messageObject = {
            'body':'Please choose assignment',
            'button_yes':'OK', 
            'button_no':'CANCEL'
            };
            callback_warning_modal(messageObject);
            return false;
        }
        var params                  = {};
            params['filter']        = $('#filter-by').val();
            params['assignment_id'] = assignment_id;
            params['course_id']     = course_id;
            params['batch_id']      = $('#batch_filter').val();
            params['institute_id']  = $('#institute_filter').val(); 
            location.href = webConfigs('admin_url')+'report/export_assignment/'+btoa(JSON.stringify(params));
}

function courseFilter(){
    updateUrlState();
    var courseId                = $("#course-report-filter").val();
    $('#assignment_filter').html('');
    var assignmentsHtml      = '<option value="0" >Loading...</option>';
    $('#assignment_filter').html(assignmentsHtml);
    $.ajax({
            url: webConfigs('admin_url')+'report/get_assignments',
            type: "POST",
            data:{ "is_ajax":true,"course_id":courseId},
            success: function(response) {
                var data  = $.parseJSON(response);
                if(data['error'] == false)
                {
                    $('#assignment_filter').html('');
                    var assignmentHtml      = '<option value="all" >Select Assignment</option>';
                    if (data['results'].length > 0) {
                        for (var i in data['results']) {
                            assignmentHtml += '<option value="'+data['results'][i]['id']+'">'+data['results'][i]['cl_lecture_name']+'</option>';
                        }
                    } else {
                            assignmentHtml += '<option value="0" >No assignment to select</option>';
                    }
                    $('#assignment_filter').html(assignmentHtml);
                }
            }
        });
}
function instituteFilter(){
    updateUrlState();
    var instituteId                = $("#institute_filter").val();
    $('#batch_filter').html('');
    var batchHtml      = '<option value="0">Loading...</option>';
    $('#batch_filter').html(batchHtml);
    $.ajax({
            url: webConfigs('admin_url')+'report/get_batches',
            type: "POST",
            data:{ "is_ajax":true,"institute_id":instituteId},
            success: function(response) {
                var data  = $.parseJSON(response);
                if(data['error'] == false)
                {
                    $('#batch_filter').html('');
                    var batchesHtml      = '<option value="all">Choose Batch</option>';
                    if (data['batches'].length > 0) {
                        for (var i in data['batches']) {
                            batchesHtml += '<option value="'+data['batches'][i]['id']+'">'+data['batches'][i]['batch_name']+'</option>';
                        }
                    } else {
                            batchesHtml += '<option value="0" >No batch to select</option>';
                    }
                    $('#batch_filter').html(batchesHtml);
                }
            }
        });
}
function getReport(){
    var pathname                = '/admin/report/assignment_report';
    var link = window.location.protocol + "//" + window.location.host + pathname;
    var course_id               = $("#course-report-filter").val();
    var institute_id            = $("#institute_filter").val();
    var assignment_id           = $("#assignment_filter").val();
    var batch_id                = $("#batch_filter").val();
    var filter_by               = $("#filter-by").val();
    if((course_id=='0') || (course_id=='all')){
        var messageObject = {
        'body':'Please choose course',
        'button_yes':'OK', 
        'button_no':'CANCEL'
        };
        callback_warning_modal(messageObject);
        return false;
    } 

    if((assignment_id=='0') || (assignment_id=='all')){
        var messageObject = {
        'body':'Please choose assignment',
        'button_yes':'OK', 
        'button_no':'CANCEL'
        };
        callback_warning_modal(messageObject);
        return false;
    }
    
    if(course_id != '' || assignment_id != '' || institute_id != '' || batch_id != '' || filter_by != '') {
        link += '?';
    }

    if(course_id != '') {
        link += '&course='+course_id;
    }

    if(assignment_id != '') {
        link += '&assignment='+assignment_id;
    }

    if (institute_id != '') {
        //institute_id =(institute_id!='all')?institute_id:'0';
        link += '&institute_id='+institute_id;
    }

    if (batch_id != '') {
        link += '&batch_id='+batch_id;
    }

    if (filter_by != '') {
        link += '&filter='+btoa(filter_by);
    }
    window.location = link;
}


function updateUrlState() {
    if (history.pushState) {
        var link = window.location.protocol + "//" + window.location.host + window.location.pathname;
            link = link.substring(0, link.indexOf('?'));

        var courseId = $.trim($('#course-report-filter').val());
            if(courseId != '') {
                link += '?course_id=' + courseId;
                
                var quizId = $.trim($('#assignment_filter').val());
                    if(quizId != '') {
                        link += '&quiz_id=' + quizId;
                    }
                
            }
        var instituteId = $.trim($('#institute_filter').val());
            if(instituteId != '') {
                link += '&institute_id=' + instituteId;
                
                var batchId = $.trim($('#batch_filter').val());
                    if(batchId != '') {
                        link += '&batch_id=' + batchId;
                    }
            }
        var filterBy = $.trim($('#filter-by').val());
            if(filterBy != '') {
                filterBy = btoa(filterBy);
                link += '&filter_by=' + filterBy;
            }

            window.history.pushState({
                path: link
            }, '', link);
    }
}

</script>
<?php include_once 'footer.php'; ?>