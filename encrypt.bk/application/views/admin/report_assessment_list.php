<?php include_once 'report_header.php'; ?>  
<link rel="stylesheet" href="<?php echo assets_url() ?>css/jquery.circliful.css">
<link rel="stylesheet" type="text/css" href="<?php echo assets_url() ?>css/custom.css">
<style type="text/css">
    .rel-top50 {
        top: 8px;
    }
    .left-space{
        margin-left:1.5em
    }
    .assign-report-nav .nav-content.assessments-content {
        top: 130px;
    }

    .assign-report-nav .nav-content.faculty-nav-content {
        width: calc(100% - 66px);
    }

    .assign-report-nav .nav-content {
        width: calc(50% - 33px);
        position: fixed;
        top: 80px;
    }
    .no-padd-r{padding-right:0 !important;}
    .top-85{top:90px;}
    .quiz-list-avatar{
        display:inline-block;
        padding-left: 5px;
        vertical-align: sub;
    }
    .courses-tab {
        position: fixed;
        z-index: 1100;
        width: 100%;
        top:0px;
    }
    .fullwidth{
        width:100% !important;
    }
    .link-pointer{
        cursor:pointer;
        vertical-align: super;
    }
    .active-grade{background-color: #1bc001;color: #fff;}
    .active-grade:hover{background-color: #1bc001 !important;color: #fff;}
    .top-reset{top: 50px !important;}
    .settings-top.top90{top:90px !important;}
    body.modal-open{padding:0 !important;overflow:hidden;}
    .no-list-message {
        font-size: 24px;
        color: #8d8d8d;
        padding: 30px;
    }
    .question-count{
        display:  block;
        padding: 25px 15px 15px 0px;
        font-size: 16px;
        font-weight: 600;
        color: #6a6a6a;
        text-align:  right;
    }
    #filter_batch_div .dropdown-toggle{max-width: none;}

    @media screen and (-ms-high-contrast: active), (-ms-high-contrast: none) {
        .modal {
             padding-left:0px !important;
        }
    }
</style>

<section class="content-wrap create-group-wrap settings-top top90 reports-left no-padd-r">
    <!-- LEFT CONTENT --> <!-- STARTS -->
    <!-- ===========================  -->

    <div class="container-fluid nav-content nav-course-content add-question-block" style="width:100% !important;top: 0px !important;height: 50px;">
        <div class="col-sm-12 bottom-line question-head">
            <h3 class="question-title">Quiz Report List - <span class="text-green"><?php echo $selected_course.' - '.$selected_assessment; ?></span></h3>
            <span class="cb-close-qstn" style="right: 30px;"><i class="icon icon-cancel-1" onclick="goBackToAssessment()"></i></span>
        </div> 
    </div>
<?php
//print_r($listing_assessments['assessment']);
//die();
?>

    <div class="col-sm-12 pad0 assign-report-nav">
        <!-- Nav section inside this wrap  --> <!-- START -->
        <!-- =========================== -->
        <div class="col-sm-12 nav-content faculty-nav-content top-reset assessments-content fullwidth">
            <div class="row">
                <div class="rTable content-nav-tbl" style="">
                    <div class="rTableRow">
                        
                            <div class="rTableCell dropdown">
                             <?php if(!isset($role_manager['institute_id'])): ?>    
                                <?php if (!empty($institutes)): ?>
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" id="filter_institute">All Institutes <span class="caret"></span></a>
                                    <ul class="dropdown-menu white inner-scroll">
                                        <li><a href="javascript:void(0)" id="filter_institute_all" onclick="filter_institute('all')">All Institutes </a></li>
                                        <?php $institute_tooltip = ''; ?>
                                        <?php foreach ($institutes as $institute): 
                                            $institute_tooltip = (strlen($institute['ib_name']) > 15) ? ' title="' . $institute['ib_institute_code'] . ' - ' . $institute['ib_name'] . '"' : '';
                                            ?>
                                        <li><a href="javascript:void(0)" id="filter_institute_<?php echo $institute['id'] ?>" <?php echo $institute_tooltip; ?> onclick="filter_institute(<?php echo $institute['id'] ?>)"><?php echo $institute['ib_institute_code'] . ' - ' . $institute['ib_name'] ?></a></li>
                                        <?php endforeach;?>

                                    </ul>
                                 <?php endif;?>
                               <?php endif;?>
                            </div>
                           
                       
                        <div class="rTableCell dropdown" id="filter_batch_div" >
                            
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" id="filter_batch">All Batches <span class="caret"></span></a>
                                <ul class="dropdown-menu white inner-scroll" id="batch_filter_list">
                                    <li><a href="javascript:void(0)" id="filter_batch_all" onclick="filter_batch('all')">All Batches </a></li>
                                    <?php if (!empty($batches)): ?>
                                    <?php $batch_tooltip = '';?>
                                    <?php foreach ($batches as $batch):
                                        $batch_tooltip = (strlen($batch['batch_name']) > 15) ? ' title="' . $batch['batch_name'] . '"' : '';
                                        ?>
                                        <li><a href="javascript:void(0)" id="filter_batch_<?php echo $batch['id'] ?>" <?php echo $batch_tooltip; ?> onclick="filter_batch(<?php echo $batch['id'] ?>)"><?php echo $batch['batch_name'] ?></a></li>
                                        <?php endforeach;?>
                                    <?php endif;?>
                                 </ul>
                           
                        </div>
                        
                        
                        
                        <div class="rTableCell dropdown">
                            <a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" id="dropdown_role_filter" data-filter="all" data-filter-label="<?php echo base64_encode('All') ?>" ><?php echo 'Filter By' ?><span class="caret"></span></a>
                            <ul class="dropdown-menu white" >
                                <?php $filters = array('all' => 'All', 'submitted' => 'Submitted', 'late_submit' => 'Late Submitted', 'to_evaluate' => 'To Evaluate') ?>
                                <?php if (!empty($filters)): ?>
                                    <?php foreach ($filters as $key => $filter): ?>
                                <li><a href="javascript:void(0)" id="filter_user_<?php echo $key; ?>" onclick="filterUser('<?php echo $key ?>', '<?php echo base64_encode($filter) ?>')" ><?php echo $filter ?></a></li>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                <?php
                                if(!empty($grade)) {
                                    foreach($grade as $grades){
                                        ?>
                                <li><a href="javascript:void(0)" id="filter_user_<?php echo $grades['id'] ?>" onclick="filterUser('<?php echo $grades['gr_name'] ?>', '<?php echo base64_encode($grades['gr_name']) ?>')" ><?php echo 'Grade '. $grades['gr_name'] ?></a></li>
                                <?php
                                    }
                                }
                                ?>
                            </ul>
                        </div>


                         <div class="rTableCell dropdown">
                            <a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" id="dropdown_sort_filter" data-filter="all" data-filter-label="<?php echo base64_encode('All') ?>" ><?php echo 'Sort By' ?><span class="caret"></span></a>
                            <ul class="dropdown-menu white">
                                <?php $sortby = array('all' => 'All', 'name_a_z' => ' Name A - Z', 'marks_high_low' => 'Marks High - Low', 'marks_low_high' => 'Marks Low - High') ?>
                                <?php if (!empty($sortby)): ?>
                                    <?php foreach ($sortby as $key => $sort_by): ?>
                                <li><a href="javascript:void(0)" id="filter_sort_<?php echo $key ?>" onclick="filterSortby('<?php echo $key ?>', '<?php echo base64_encode($sort_by) ?>')" ><?php echo $sort_by ?></a></li>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </ul>
                        </div>

                        <div class="rTableCell dropdown" id="filter_tutor_div" >
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" id="filter_tutor">Assigned To<span class="caret"></span></a>
                            <ul class="dropdown-menu white inner-scroll" id="tutor_filter_list">
                                <li><a href="javascript:void(0)" id="filter_tutor_<?php echo $user['id']; ?>" onclick="filter_tutor(<?php echo $user['id']; ?>)">Assigned to me</a></li>
                                <li><a href="javascript:void(0)" id="filter_tutor_all" onclick="filter_tutor('all')">Assigned to all</a></li>
                                <?php if (!empty($tutors)):
                                $tutor_tooltip = '';
                                ?>
                                <?php foreach ($tutors as $tutor):
                                    $tutor_tooltip = (strlen($tutor['us_name']) > 15) ? ' title="' . $tutor['us_name'] . '"' : '';
                                    ?>
                                    <li><a href="javascript:void(0)" id="filter_tutor_<?php echo $tutor['id'] ?>" <?php echo $tutor_tooltip; ?> onclick="filter_tutor(<?php echo $tutor['id'] ?>)"><?php echo $tutor['us_name'] ?></a></li>
                                    <?php endforeach;?>
                                <?php endif;?>
                            </ul>
                        </div>


           
                        <div class="rTableCell">
                            <div class="input-group">
                                <input class="form-control srch_txt" id="attendees_keyword" placeholder="Search by name" type="text">
                                <span id="searchclear">×</span>
                                <a class="input-group-addon" id="faculty_search">
                                    <i class="icon icon-search"> </i>
                                </a>
                            </div> 
                        </div>
                        <div class="rTableCell">
                            <div class="btn-group lecture-control btn-right-align" id="generate_test_bulk_action" style="margin-top: 0px;visibility: hidden;">
                                <span class="dropdown-tigger" data-toggle="dropdown" aria-expanded="false">
                                    <span class="label-text">
                                    Bulk Action  <!-- <span class="icon icon-down-arrow"></span> -->
                                    </span>
                                    <span class="tilder"></span>
                                </span>
                                <ul class="dropdown-menu pull-right" role="menu">
                                    <li><a href="javascript:void(0)" class="list-disabled list-button" onclick="assignFaculty('0')">Assign</a></li>

                                </ul>
                            </div>
                        </div>
                        <div class="rTableCell">
                            <div class="save-btn"><button class="pull-right btn btn-green" onclick="exportAssessmentReport();">EXPORT</button></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>     
        <!-- Group content section  -->
    </div>
    <!-- <div class="col-sm-6 pad0 right-content list-right tp175"> -->
    <div class="">
        <!-- =========================== -->
        <div class="container-fluid right-box list-bx">
            <div class="row">
                <div class="col-sm-12 course-cont-wrap"> 
                <span class="question-count"><span id="attempt-count"></span>/<span id="total-count"></span></span>
                    <div class="table course-cont rTable right-table" style="" id="attempt_detail_wrapper">
                    </div>
                    <div class="row">
                        <!-- <div class="col-sm-12 text-center">
                            <a id="loadmorebutton" < ?php echo ((!$show_load_button)?'style="display:none;"':'') ?> class="btn btn-green selected" onclick="loadMoreUsers()">Load More < ?php echo $remaining_user ?></a>
                        </div> -->
                        <div class="col-sm-12 text-center" id="pagination_wrapper">
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!-- ==========================  -->
    <!--  LEFT CONTENT--> <!-- ENDS -->

    <!-- Modal pop up contents :: Create html -->
<div class="modal fade" data-backdrop="static" data-keyboard="false" id="assign_faculty" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-small" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" id="close_modal"  data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                <h4 class="modal-title" id="myModalLabel">Faculty Assign</h4>
            </div>
            <div class="modal-body">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label>Assign to *:</label>
                        <select class="form-control" id="faculty_selected_assign">
                        <option value="0">Select Faculty</option>
                        <?php foreach ($tutors as $tutor):
                                    $tutor_tooltip = (strlen($tutor['us_name']) > 15) ? ' title="' . $tutor['us_name'] . '"' : '';
                            ?>
                            <option value="<?php echo $tutor['id'] ?>"><?php echo $tutor['us_name'] ?></option>
                        <?php endforeach;?>
                        </select>
                    </div>
                </div>
                 
            </div>
            <div class="modal-footer">
                 <button type="button" class="btn btn-red" data-dismiss="modal">CANCEL</button>
                 <button type="button" class="btn btn-green" id="save_faculty_assign" >ASSIGN</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal pop up contents :: Create html -->
<!-- assignment report modal starts -->
    <div class="modal" id="grade_report_detail" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="z-index: 9999999;overflow-y:scroll;padding:0px !importrant;">
	    <div class="modal-dialog assignment-report" role="document">
	        <div class="modal-content" id="assignment-report-content">
	           
	        </div>
	    </div>
	</div>
	<!-- assignment report modal ends -->
    <?php include_once 'quiz_report_details.php'; ?> 
</section>

<script>
    var __access_permission    = '<?php echo json_encode($access_permission); ?>';
    var __sel_course_name      = '<?php echo $selected_course; ?>';
    var __sel_assessment_title = '<?php echo $selected_assessment ?>';
    var __assignfacultyAttempts= {};
    var __attemptIdChecked     = 0;
    var __limit                = '<?php echo $limit; ?>';
    var __offset               = Number('<?php echo isset($_GET['offset']) ? $_GET['offset'] : '1' ?>');
    var __totalAttempts        = '<?php echo $total_attempts; ?>';
    var __course_id            = '<?php echo isset($_GET['course']) ? $_GET['course'] : '' ?>';
    var __assessment_id        = '<?php echo isset($_GET['assessment']) ? $_GET['assessment'] : '' ?>';
    var __institute_id         = '<?php echo isset($_GET['institute_id']) ? $_GET['institute_id'] : '' ?>';
    var __batch_id             = '<?php echo isset($_GET['batch_id']) ? $_GET['batch_id'] : '' ?>';
    var __filter               = '<?php echo isset($_GET['filter']) ? $_GET['filter'] : '' ?>';
    var __tutor                = '<?php echo isset($_GET['tutor']) ? $_GET['tutor'] : 0 ?>';
    var __keyword              = '<?php echo isset($_GET['keyword']) ? $_GET['keyword'] : 0 ?>';
    var __sort_by              = '<?php echo isset($_GET['sort_by']) ? $_GET['sort_by'] : 'all' ?>';
    var __grade                = '<?php echo json_encode($grade); ?>';
    var __default_user_path    = '<?php echo default_user_path(); ?>';
    var __user_path            = '<?php echo user_path(); ?>';
    
  
 
    
    var __asset_url            = '<?php echo assets_url() ?>';
    var __assessmentObject = new Array;
        __assessmentObject = atob('<?php echo base64_encode(json_encode($assessments)) ?>'); 
        __assessmentObject = $.parseJSON(__assessmentObject);
    

    var __getLectures   = false;
    var __ins_selected  = 0;
        
    $(".srch_txt").keyup(function(){
        $("#searchclear").toggle(Boolean($(this).val()));
    });
    $("#searchclear").toggle(Boolean($(".srch_txt").val()));
    $("#searchclear").click(function(){
    $(".srch_txt").val('').focus();
    $(this).hide();
    __keyword = '';
    getAssessments();
    });
    $(document).ready(function (e) {
        var filter       = getQueryStringValue('filter');
        var keyword      = getQueryStringValue('keyword');
        var institute_id = getQueryStringValue('institute_id');
        var batch_id     = getQueryStringValue('batch_id');
        var assignment   = getQueryStringValue('assignment');
        var course       = getQueryStringValue('course');
        var tutor        = getQueryStringValue('tutor');
        var sort_by      = getQueryStringValue('sort_by');
        if (institute_id != '') {
            if(institute_id=='all'){
                $('#filter_institute').html($('#filter_institute_' + institute_id).text() + '<span class="caret"></span>');
            } else {
                $('#filter_institute').html($('#filter_institute_' + institute_id).text().substring(0, $('#filter_institute_' + institute_id).text().indexOf('-')) + '<span class="caret"></span>');  
            }
            
        }
        
        if (batch_id != '') {
            __batch_id = batch_id;
            $('#filter_batch').html('<span class="dropdown-filter" title="'+ $('#filter_batch_' + batch_id).text() +'">' + $('#filter_batch_' + batch_id).text() + '</span><span class="caret"></span>');
        }

        if(filter!=''){
            __filter = atob(filter);
            if(__grade.indexOf(__filter) != -1) {
                    $('#dropdown_role_filter').html('Grade '+__filter + '<span class="caret"></span>');
                } else {
                    $('#dropdown_role_filter').html($('#filter_user_' +__filter).text() + '<span class="caret"></span>');
                }
           
        }

        $('#assignment_wrapper').html(renderAssessmentHtml(__assessmentObject));
        renderPagination(__offset, __totalAttempts);
        //loadQuizAttemptDetails('9', btoa('sample'));
    });
    var __userImage = '';
    var __months    = new Array();
        __months[1] = 'JAN';
        __months[2] = 'FEB';
        __months[3] = 'MAR';
        __months[4] = 'APR';
        __months[5] = 'MAY';
        __months[6] = 'JUN';
        __months[7] = 'JUL';
        __months[8] = 'AUG';
        __months[9] = 'SEP';
        __months[10] = 'OCT';
        __months[11] = 'NOV';
        __months[12] = 'DEC';
    var __assignmentId  = 0;
    var __courseId      = 0;
</script>
<script>
    function getQueryStringValue(key) {
        return decodeURIComponent(window.location.search.replace(new RegExp("^(?:.*[&\\?]" + encodeURIComponent(key).replace(/[\.\+\*]/g, "\\$&") + "(?:\\=([^&]*))?)?.*$", "i"), "$1"));
    }
    function renderAssessmentHtml(assessment)
    {
        __assessmentId  = 0;
        __courseId      = 0;
        var assessmentHtml  = '';
        if(Object.keys(assessment).length > 0 )
        {
            $.each(assessment, function(assessmentKey, assessment)
            {
                if(__assessmentId == 0)
                {
                    __assessmentId = assessment['id'];
                }
                if(__courseId == 0)
                {
                    __courseId = assessment['cl_course_id'];                    
                }
                assessmentHtml += '<div class="list-row assignment-listing-row" id="assignment_'+assessment['id']+'" onclick="loadAssessmentAttendees('+assessment['id']+', '+assessment['cl_course_id']+')">';
                assessmentHtml += '    <div class="list-col">';
                assessmentHtml += '         <span class="wrap-mail ellipsis-hidden"> ';
                assessmentHtml += '            <div class="ellipsis-style">';
                assessmentHtml += '                <i class="icon icon-clipboard"></i> ';
                assessmentHtml += '                <a href="javascript:void(0)">'+assessment['cl_lecture_name']+'</a>';
                assessmentHtml += '            </div>';
                assessmentHtml += '        </span>';
                assessmentHtml += '    </div>';
                assessmentHtml += '</div>';
                if(typeof assessment['attempts'] != 'undefined')
                {
                    var attemptWrapperHtml = '';
                    var attemptLength = Object.keys(assessment['attempts']).length;
                    if(attemptLength!=0){ 
                    $.each(assessment['attempts'], function(attemptKey, attempt )
                    {
                        attemptWrapperHtml += attempts(attempt);
                        
                    });
                    $('#attempt_detail_wrapper').html(assessment_header()+attemptWrapperHtml);
                    } else {
                        attemptWrapperHtml += '<div class="no-list-message text-center">No students found</div>';
                        $('#attempt_detail_wrapper').html(attemptWrapperHtml);
                    }
                    $("#attempt-count").html(attemptLength);
                    $("#total-count").html(__totalAttempts);
                }
            });
        }
        return assessmentHtml;
    }
    
   

    function loadInstituteReport(instituteId, instituteName)
    {
        $('#selected_institute_label').html('<span id="institute_label">'+atob(instituteName)+'</span><span class="caret"></span>');
        __ins_selected  = atob(instituteId);
        __offset    = 1;
        __getLectures   = true;
        getCourseReport(true);     
    }
    
    function attempts(attempt)
    {
       
            __userImage  = ((attempt['us_image'] == 'default.jpg')?webConfigs('default_user_path'):webConfigs('user_path')); 
            var attemptHTml  = '';
            attemptHTml += '<div class="rTableRow assignment-listing-row">';
            attemptHTml += '    <div class="rTableCell" >';
            if((attempt['aa_valuated'] == 0)||(attempt['aa_valuated'] == 1)){
                var space_class = "";
                if(attempt['aa_valuated'] == 0){
                    if(__access_permission.indexOf("3")!=-1){
                        attemptHTml += '        <span id="evaluate_checkbox_'+attempt['attempt_id']+'"><input  type="checkbox" name="" value="'+attempt['attempt_id']+'" class="assignment-attempts" value="" placeholder=""></span>';
                    } else {
                        space_class = 'left-space';  
                    }
                } else {
                    space_class = 'left-space';
                }
                //attemptHTml += '        <span id="user_register_'+attempt['attempt_id']+'"  class="link-pointer '+space_class+'" >'+attempt['us_phone']+'</span>';
            } 
                
            attemptHTml += '    </div>';
            attemptHTml += '    <div class="rTableCell" onclick="getReport('+attempt['attempt_id']+')"> ';
            attemptHTml += '        <span class="wrap-mail ellipsis-hidden"> ';
            attemptHTml += '            <div class="ellipsis-style">';
            attemptHTml += '                <a>'+attempt['us_name']+'</a>';
            attemptHTml += '            </div>';
            attemptHTml += '        </span>';
            attemptHTml += '    </div>';
            attemptHTml += '    <div class="rTableCell" onclick="getReport('+attempt['attempt_id']+')">';
            attemptHTml += '        <span class="wrap-mail ellipsis-hidden"> ';
            attemptHTml += '            <div class="ellipsis-style">';
            if(attempt['aa_attempted_date'] != null)
            {
            var createdDate = attempt['aa_attempted_date'];
                attemptHTml += '<span class="link-pointer">'+createdDate+'</span>';             
            }
            else
            {                
                attemptHTml += '-';            
            }
            attemptHTml += '            </div>';
            attemptHTml += '        </span>';
            attemptHTml += '    </div> ';
            attemptHTml += '    <div class="rTableCell pad0 text-center" onclick="getReport('+attempt['attempt_id']+')" style="min-width:100px;">';
            if(attempt['aa_mark_scored']!= null){
                var split_mark  = attempt['aa_mark_scored'].split(".");
                var scored_mark = (split_mark[1]==00)?split_mark[0]:attempt['aa_mark_scored'];
            } else {
                var scored_mark = '0';
            }
            
            if((attempt['aa_valuated'] == 0)||(attempt['aa_valuated'] == 1)){
                attemptHTml += '        <span  id="current_mark_'+attempt['attempt_id']+'" class="green-text">'+scored_mark+'</span>';
            }
            else
            {       
                if(attempt['aa_attempted_date'] == null)
                {
                    attemptHTml += '<span  class="link-pointer">-</span>';                    
                }
            }
            attemptHTml += '    </div>';
            attemptHTml += '    <div class="rTableCell pad0 text-center" style="min-width:100px;">';
            if((attempt['aa_valuated'] == 0)||(attempt['aa_valuated'] == 1)){
                if(__access_permission.indexOf("3")!=-1){
                    attemptHTml += '<span class="green-text" data-toggle="tooltip" title="Double click to edit." id="current_grade_'+attempt['attempt_id']+'" ondblclick="editgrade('+attempt['attempt_id']+','+attempt['aa_user_id']+')" style="cursor:pointer;">'+attempt['aa_grade']+'</span>';
                    attemptHTml += '<select  id="change_grade_'+attempt['attempt_id']+'_'+attempt['aa_user_id']+'" onChange="changeGrade('+attempt['attempt_id']+','+attempt['aa_user_id']+')" style="display:none;">';
                    var grade_data = JSON.parse(__grade);
                    attemptHTml += '<option value="0" selected>Select</option>';    
                    for (var i in grade_data) {
                        attemptHTml += '<option value="'+grade_data[i].id+'">'+grade_data[i].gr_name+'</option>';    
                            
                    }
                    attemptHTml += '</select>';
                } else {
                    attemptHTml += '<span class="green-text" id="current_grade_'+attempt['attempt_id']+'" >'+attempt['aa_grade']+'</span>'; 
                }
            }
            attemptHTml += '    </div>';
            attemptHTml += '    <div class="rTableCell text-center"> ';
            attemptHTml += '        <span class="wrap-mail ellipsis-hidden"> ';
            attemptHTml += '            <div class="ellipsis-style">';
            if((attempt['aa_valuated'] == 0)||(attempt['aa_valuated'] == 1)){
                if(__access_permission.indexOf("3")!=-1){
                    var faculty_img  = (attempt['faculty_image']!=null)?attempt['faculty_image']:'default.jpg';
                    var faculty_name = (attempt['faculty_name']!=null)?attempt['faculty_name']+".":'';
                    //attempt['faculty_name']
                    var user_img    = ((faculty_img == 'default.jpg')?webConfigs('default_user_path'):webConfigs('user_path'));
                    if((attempt['aa_valuated'] == 0)){
                        attemptHTml += '<a class="link-pointer evaluate-btn" onclick="getReport('+attempt['attempt_id']+')"><span id="evaluated_txt_'+attempt['attempt_id']+'">Evaluate</span><ripples></ripples></a><div class="quiz-list-avatar bold"><img class="img-cirlce img-responsive" data-toggle="tooltip" title="'+faculty_name+' Click to assign a faculty." id="faculty_img_'+attempt['attempt_id']+'" src="'+user_img+faculty_img+'" onclick="assignFaculty('+attempt['attempt_id']+')" width="26"></div>';          
                    } else {
                        attemptHTml += '<a class="link-pointer evaluated-btn" onclick="getReport('+attempt['attempt_id']+')"><span id="evaluated_txt_'+attempt['attempt_id']+'">Evaluated</span><ripples></ripples></a><div class="quiz-list-avatar bold"><img class="img-cirlce img-responsive" data-toggle="tooltip" title="'+faculty_name+'" id="faculty_img_'+attempt['attempt_id']+'" src="'+user_img+faculty_img+'" width="26"></div>';          
                    }
                } else {
                    attemptHTml += '<span>Access Denied</span>';
                }
            }
            else
            {                
                attemptHTml += '-';            
            }
            attemptHTml += '            </div>';
            attemptHTml += '        </span>';
            attemptHTml += '    </div>';
          
            attemptHTml += '</div>';
            return attemptHTml;
            $('[data-toggle="tooltip"]').tooltip();
    }
    
    $(document).on('click', '.fetch-course-assignment', function(){
        __course_id = $(this).attr('data-id');
        getAssessments();
    });

    function filter_institute(institute_id) {
        if (institute_id == 'all') {
            __institute_id = 'all';
            $('#filter_institute').html('All Institutes <span class="caret"></span>');
        } else {
            __institute_id = institute_id;
            $('#filter_institute').html($('#filter_institute_' + institute_id).text().substring(0, $('#filter_institute_' + institute_id).text().indexOf('-')) + '<span class="caret"></span>');
        }
        __batch_id = '';
        __offset = 1;
        getAssessments();
    }

    function filter_batch(batch_id) {
        if (batch_id == 'all') {
            __batch_id = '';
            $('#filter_batch').html('All Batches <span class="caret"></span>');
        } else {
            __batch_id = batch_id;
            $('#filter_batch').html('<span class="dropdown-filter" title="'+ $('#filter_batch_' + batch_id).text() +'">' + $('#filter_batch_' + batch_id).text() + '</span><span class="caret"></span>');
        }
        __offset = 1;
        getAssessments();
    }

    function filter_tutor(tutor_id) {
        if (tutor_id == 'all') {
            __tutor = 'all';
            $('#filter_tutor').html('Assigned to all<span class="caret"></span>');
        } else {
            __tutor = tutor_id;
            $('#filter_tutor').html('<span class="dropdown-filter" title="'+ $('#filter_tutor_' + tutor_id).text() +'">' + $('#filter_tutor_' + tutor_id).text() + '</span><span class="caret"></span>');
        }
        __offset = 1;
        getAssessments();
    }

    function filterUser(filter, label)
    {
         __filter = filter;
        $('#dropdown_role_filter').html('<span class="dropdown-filter" title="'+ atob(label) +'">' + atob(label) + '</span><span class="caret"></span>');
        __keyword = $('#attendees_keyword').val();
        getAssessments();
       
    }
    function filterSortby(filter, label)
    {
         __sort_by = filter;
        $('#dropdown_sort_filter').html('<span class="dropdown-filter" title="'+ atob(label) +'">' + atob(label) + '</span><span class="caret"></span>');
        __keyword = $('#attendees_keyword').val();
        getAssessments();
    }
    
    

    function getAssessments(){
        
        if (history.pushState) {
        var link = window.location.protocol + "//" + window.location.host + window.location.pathname;

        if (__course_id != '' || __assessment_id!='' || __institute_id != '' || __batch_id != '' || __filter != '' || __keyword != '' || __sort_by !='') {
             link += '?';
        }
        if(__course_id != '') {
             link += '&course=' +__course_id;
        }

        if(__assessment_id != '') {
             link += '&assessment=' +__assessment_id;
        }

        if (__institute_id != '') {
             link += '&institute_id=' +__institute_id;
        }

        if (__batch_id != '') {
            link += '&batch_id=' +__batch_id;
        }

        if (__filter != '') {
            link += '&filter=' + btoa(__filter);
        }

        if (__keyword != '') {
            link += '&keyword=' +__keyword;
        }

        if(__tutor !=''){
            link += '&tutor=' +__tutor;
        }

        if(__sort_by !=''){
            link += '&sort_by=' +__sort_by;
        }
        __offset = (typeof __offset == 'undefined') ? 1 : __offset;
        link += '&offset=' + __offset;
        window.history.pushState({
            path: link
        }, '', link);
        }

        $.ajax({
            url: webConfigs('admin_url')+'report/course_assessment',
            type: "POST",
            data:{ "is_ajax":true,"course_id":__course_id,"assessment_id":__assessment_id,"institute_id":__institute_id,"batch_id":__batch_id, "filter":btoa(__filter),"keyword":__keyword,"tutor":__tutor,"sort_by":__sort_by,"limit":__limit,"offset":__offset},
            success: function(response) {
                var data  = $.parseJSON(response);
                __totalAttempts = data['total_attempts'];
                renderPagination(__offset, __totalAttempts);
                if(data['error'] == false)
                {
                    if(typeof data['assessments'] != 'undefined')
                    {
                       //console.log(data['assessments']);
                       refreshListing();
                        $('#attempt_detail_wrapper').html('');
                        $('#assignment_wrapper').html(renderAssessmentHtml(data['assessments']));
                        
                    }
                    if (data['batches'].length > 0) {
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
                        $('#filter_batch_div').hide();  
                    }

                    if (data['tutors'].length > 0) {
                        $('#filter_tutor_div').attr('style', '');
                        var tutorHtml = '<li><a href="javascript:void(0)" id="filter_tutor_all" onclick="filter_tutor(\'all\')">Assigned to all</a></li>';
                        tutorHtml    += ' <li><a href="javascript:void(0)" id="filter_tutor_<?php echo $user['id']; ?>" onclick="filter_tutor(<?php echo $user['id']; ?>)">Assigned to me</a></li>';
                        for (var i in data['tutors']) {
                            var tutorNameToolTip = '';
                            if (data['tutors'][i]['us_name'].length > 15) {
                                tutorNameToolTip = 'data-toggle="tooltip" title="' + data['tutors'][i]['us_name'] + '"';
                            }
                            tutorHtml += '<li><a href="javascript:void(0)" id="filter_tutor_' + data['tutors'][i]['id'] + '" onclick="filter_tutor(' + data['tutors'][i]['id'] + ')" ' + tutorNameToolTip + '>' + data['tutors'][i]['us_name'] + '</a></li>';
                        }
                        $('#tutor_filter_list').html(tutorHtml);
                        if (__tutor == '') {
                            $('#filter_tutor').html('Assinged to<span class="caret"></span>');
                        }
                    }

                }
                

            }
        });
    }
    
    
    var timeOut = '';
    $(document).on('keyup', '#attendees_keyword', function(){
        clearTimeout(timeOut);
        timeOut = setTimeout(function(){
            var filter = $('#dropdown_role_filter').attr('data-filter'), label = $('#dropdown_role_filter').attr('data-filter-label');
            filterUser(filter, label);
        }, 600);
    });
    
    function exportAssessmentReport()
    {
        var filter  = $('#dropdown_role_filter').attr('data-filter'), 
            keyword = $('#attendees_keyword').val();
        var params                  = {};
            params['filter']        = __filter;
            params['keyword']       = __keyword;
            params['assessment_id'] = __assessment_id;
            params['course_id']     = __course_id;
            params['sort_by']       = __sort_by;
            params['batch_id']      = __batch_id;
            params['institute_id']  = __institute_id;
            params['tutor']         = __tutor;
            params['limit']         = __limit;
            params['offset']        = __offset;
            location.href = webConfigs('admin_url')+'report/export_assessment/'+btoa(JSON.stringify(params));
    }
    
    function assessment_header()
    {
        return `<div class="rTableRow">
                    <div class="rTableCell">
                        <span class="wrap-mail ellipsis-hidden">
                            <div class="ellipsis-style">
                                <a href="#">
                                    <label> 
                                        <input class="attempts-checkbox-parent" type="checkbox" onclick="selectAll();"  id="selectall">
                                        <!--<span class="select-span1" id="sel_all">
                                            <strong>Phone Number</strong>
                                        </span>-->
                                    </label>
                                    <span id="selected_institutes_count"></span>
                                </a>
                            </div>
                        </span>
                    </div>
                    <div class="rTableCell">
                        <span class="wrap-mail ellipsis-hidden">
                            <div class="ellipsis-style">
                                <a href="javascript:void(0)">
                                    <strong>Student Name</strong>
                                </a>
                            </div>
                        </span>
                    </div>
                    <div class="rTableCell">
                        <span class="wrap-mail ellipsis-hidden">
                            <div class="ellipsis-style">
                                <a href="javascript:void(0)">
                                    <strong>Submitted Date</strong>
                                </a>
                            </div>
                        </span>
                    </div>
                    <div class="rTableCell pad0 text-center" style="min-width:100px;">
                        <span>
                            <strong>Marks</strong>
                        </span>
                    </div>
                    <div class="rTableCell pad0 text-center" style="min-width:100px;">
                        <span>
                            <strong>Grade</strong>
                        </span>
                    </div>
                    <div class="rTableCell text-center">
                        <span class="wrap-mail ellipsis-hidden">
                            <div class="ellipsis-style">
                                <a href="javascript:void(0)">
                                    <strong>Status</strong>
                                </a>
                            </div>
                        </span>
                    </div>
                </div>`;
    }
    /* pagination */
    function renderPagination(offset, totalAttempts) {
        offset = Number(offset);
        totalAttempts = Number(totalAttempts);
        if ((totalAttempts > __limit) > 0) {
            var paginationHtml = '';
            paginationHtml += '<ul class="pagination">';
            paginationHtml += generatePagination(offset, Math.ceil(totalAttempts / __limit));
            paginationHtml += '</ul>';
            $('#pagination_wrapper').html(paginationHtml);
            scrollToTopOfPage();
        } else {
            $('#pagination_wrapper').html('');
        }
    }
    $(document).on('click', '.locate-page', function () {
        __offset = $(this).attr('data-page');
        getAssessments();
    });

 

    function refreshListing() {
        if ($('.pagination li.active a').hasClass('pagination-last-page') == true) {
            if ($('.assignment-listing-row').length == 0) {
                __offset = $('.pagination li.active a').attr('data-page');
                __offset = __offset - 1;
                if (__offset == 0) {
                    __offset = 1;
                }
            }
        } else {
            if ($('.assignment-listing-row').length == 0) {
                __offset = $('.pagination li.active a').attr('data-page');
            }
        }
    }
    function assignFaculty(id){
        $("#assign_faculty").modal();
        if(id!=0){
            __assignfacultyAttempts              = {};
            __attemptIdChecked                   = id;
            __assignfacultyAttempts[__attemptIdChecked] = __attemptIdChecked;
        }
    }

    
    function selectAll(){
        if($("#selectall").is(":checked")){
            $('.assignment-attempts').each(function() {
                __attemptIdChecked = $(this).val();
                $(this).prop('checked', true);
                if($(this).prop('checked') == true)
                {
                    __assignfacultyAttempts[__attemptIdChecked] = __attemptIdChecked;
                }
            });
        }else{
            $('.assignment-attempts').each(function(){
                $(this).prop('checked', false);
            });
            __assignfacultyAttempts = {};
        } 
        var attemptsCountHtml = Object.keys(__assignfacultyAttempts).length; 
        if(Object.keys(__assignfacultyAttempts).length > 0 )
        {
            $('#generate_test_bulk_action').css("visibility", "visible");
            $('#sel_all').html('<strong>Phone Number</strong> ('+attemptsCountHtml+')');
        }
        else
        {
            $('#generate_test_bulk_action').css("visibility", "hidden");
            $('#sel_all').html('<strong>Phone Number</strong>');       
        } 
    }     
   // });

    $(document).on('change', '.assignment-attempts', function(){
        __attemptIdChecked = $(this).val();
        if($(this).prop('checked') == true)
        {
            __assignfacultyAttempts[__attemptIdChecked] = __attemptIdChecked;
        }
        else
        {
            delete __assignfacultyAttempts[__attemptIdChecked];
        }
        var attemptsCountHtml = Object.keys(__assignfacultyAttempts).length; 
        if(Object.keys(__assignfacultyAttempts).length > 0 )
        {
            $('#generate_test_bulk_action').css("visibility", "visible");
            $('#sel_all').html('<strong>Phone Number</strong> ('+attemptsCountHtml+')');
        }
        else
        {
            $('#generate_test_bulk_action').css("visibility", "hidden");
            $('#sel_all').html('<strong>Phone Number</strong>');       
        }      
    });


    $("#save_faculty_assign").click(function(){
        var faculty_id     = $("#faculty_selected_assign").val();
        var __results_arr  = '';
        $.ajax({
            url: admin_url+'report/assign_assessment_faculty',
            type: "POST",
            data:{"is_ajax":true, "attempt_ids":JSON.stringify(__assignfacultyAttempts),"faculty_id":faculty_id},
            success: function(response) {
                var data = $.parseJSON(response);
                var attempt_ids = data.results['attempt_id'];
                if(data.error== false)
                {
                    for (var i in attempt_ids) {
                // //console.log(attempt_ids[i]);
                        var user_img = ((data.results['faculty_img'] == 'default.jpg')?webConfigs('default_user_path'):webConfigs('user_path'));
                        $("#faculty_img_"+attempt_ids[i]).attr('title', data.results['faculty_name']+'. Click to assign other.');
                        $("#faculty_img_"+attempt_ids[i]).attr('src', user_img+data.results['faculty_img']);
                    }
                    $("#close_modal").trigger('click');
                    __assignfacultyAttempts   = {};
                    $("#faculty_selected_assign").val('0');
                    $('.assignment-attempts').each(function(){
                        $(this).prop('checked', false);
                    });
                    $('#generate_test_bulk_action').css("visibility", "hidden");
                    $('#sel_all').html('<strong>Phone Number</strong>');  
                    $("#selectall").prop('checked', false);
                }
            }
        });
    });
    var __sel_attempt = '';
    var __sel_userid  = '';
    function editgrade(id,user_id){
        __sel_attempt     = id;
        __sel_userid      = user_id;
        $("#current_grade_"+id).hide();
        $("#change_grade_"+id+"_"+__sel_userid).show();
    } 

    $('body').click(function(evt){    
        if(evt.target.id != "change_grade_"+__sel_attempt+"_"+__sel_userid){
            //console.log(evt.target.id);
            $("#change_grade_"+__sel_attempt+"_"+__sel_userid).hide();
            $("#current_grade_"+__sel_attempt).show();
       }
    });
    
    function changeGrade(id,userid){
        var sel_grade     = $("#change_grade_"+id+"_"+userid).val();
        if(sel_grade!=0)
        {
            var grade_txt     = $("#change_grade_"+id+"_"+userid+" option:selected").text();
            var attempt_id    = id;
            var user_id    = userid;
            $.ajax({
                url: admin_url+'report/assign_assessment_grade',
                type: "POST",
                data:{"is_ajax":true, "attempt_id":attempt_id,"grade_id":sel_grade,"grade_txt":grade_txt,"assessment_id":__assessment_id,"user_id":user_id},
                success: function(response) {
                    var data = $.parseJSON(response);
                    if(data['error'] == false)
                    {
                        $("#current_grade_"+id).html(grade_txt);
                        $("#current_grade_"+id).show();
                        $("#change_grade_"+id+"_"+user_id).hide();
                        $('[data-toggle="tooltip"]').tooltip();
                    }
                }
            });
        } else {
            return false;
        }
        
        
    }

    // tooltip
    function getReport(id){
        if(__access_permission.indexOf("3")!=-1){
            loadQuizAttemptDetails(id, btoa(''));
        } else {
            return false;
        }
    }

    // question viewer 
    $(document).ready(function(){
        $(".view-assignment-toggle-text").click(function(){
            $(".view-assignment-question").slideToggle();
            $(".view-assignment-question").style('display', 'block');
        });
    });

    function goBackToAssessment(){
        var link = "<?php echo admin_url() ?>"+"report/assessments";

        var params = {};
        if (location.search) {
            var parts = location.search.substring(1).split('&');

            for (var i = 0; i < parts.length; i++) {
                var nv = parts[i].split('=');
                if (!nv[0]) continue;
                params[nv[0]] = nv[1] || true;
            }
        }

            if(typeof params['course'] != 'undefined') {
                link += '?course_id=' + params['course'];
                
                if(typeof params['assessment'] != 'undefined') {
                    link += '&quiz_id=' + params['assessment'];
                }
                
            }
            if(typeof params['institute_id'] != 'undefined') {
                link += '&institute_id=' + params['institute_id'];
                
                if(typeof params['batch_id'] != 'undefined') {
                    link += '&batch_id=' + params['batch_id'];
                }
            }
            if(typeof params['filter'] != 'undefined') {
                link += '&filter_by=' + params['filter'];
            }
            location.href= link;
    }

</script>
<?php include_once 'report_footer.php'; ?>
