<?php include_once 'report_header.php'; ?>  
<link rel="stylesheet" href="<?php echo assets_url() ?>css/jquery.circliful.css">
<link rel="stylesheet" type="text/css" href="<?php echo assets_url() ?>css/style.css">
<link rel="stylesheet" type="text/css" href="<?php echo assets_url() ?>css/custom.css">
<style type="text/css">
    .rel-top50 {
        top: 8px;
    }
    .left-space{
        margin-left:1.5em;
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
    #filter_batch_div .caret {position: unset !important;right: unset;top: unset;margin: 0;}
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
    .feedback-comment-wrapper {
        border-bottom: 1px solid #d6d7da;
        padding-top: 12px;
        word-break: break-word;
    }
    #feedback_comment{
        padding-bottom: 25px;
    }
</style>


<section class="content-wrap create-group-wrap settings-top top90 reports-left no-padd-r">
    <!-- LEFT CONTENT --> <!-- STARTS -->
    <!-- ===========================  -->

    <div class="container-fluid nav-content nav-course-content add-question-block" style="width:100% !important;top: 0px !important;height: 50px;">
        <div class="col-sm-12 bottom-line question-head">
            <h3 class="question-title">Assignment Report List - <span class="text-green"><?php echo $selected_course.' - '.$selected_assignment; ?></span></h3>
            
            <span class="cb-close-qstn" style="right: 30px;"><i class="icon icon-cancel-1" onclick="goBackToAssignment()"></i></span>
        </div> 
    </div>


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
                           
                       
                        <div class="rTableCell dropdown" id="filter_batch_div" style="padding-top: 0;">
                       
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" id="filter_batch" style="max-width: unset;">All Batches <span class="caret"></span></a>
                                <ul class="dropdown-menu white inner-scroll" id="batch_filter_list">
                                    <li><a href="javascript:void(0)" id="filter_batch_all" onclick="filter_batch('all')">All Batches </a></li>
                                    <?php if (!empty($batches)): ?>
                                    <?php $batch_tooltip = ''; ?>
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
                                <?php $filters = array('all' => 'All', 'submitted' => 'Submitted','late_submit' => 'Late Submitted', 'to_evaluate' => 'To Evaluate') ?>
                                <?php if (!empty($filters)): ?>
                                    <?php foreach ($filters as $key => $filter): ?>
                                <li><a href="javascript:void(0)" id="filter_user_<?php echo $key; ?>" onclick="filterUser('<?php echo $key ?>', '<?php echo base64_encode($filter) ?>')" ><?php echo $filter ?></a></li>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                <?php
                                if(!empty($grade)) {
                                    foreach($grade as $grades){
                                        ?>
                                <li><a href="javascript:void(0)" id="filter_user_<?php echo $grades['gr_name']; ?>" onclick="filterUser('<?php echo $grades['gr_name'] ?>', '<?php echo base64_encode('Grade '.$grades['gr_name']) ?>')" ><?php echo 'Grade '. $grades['gr_name'] ?></a></li>
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
                                <li><a href="javascript:void(0)" id="filter_tutor_<?php echo $user['id']; ?>" onclick="filter_tutor(<?php echo $user['id']; ?>)">Assigned To me</a></li>
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
                            <div class="save-btn"><button class="pull-right btn btn-green" onclick="exportAssignmentReport();">EXPORT</button></div>
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
<div class="modal" data-backdrop="static" data-keyboard="false" id="assign_faculty" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-small" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" id="close_modal" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
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
<?php
$course_id = isset($_GET['course']) ? $_GET['course'] : '';
?>

</section>
<script src="<?php echo assets_url() ?>js/jquery.circliful.min.js"></script>
<script src="<?php echo assets_url() ?>js/system.js"></script>
<!-- <script src="<?php //echo assets_url() ?>js/jquery.slimscroll.min.js"></script> -->
<script>
    var __assignment_total_mark = Number('0');
    var __access_permission    = <?php echo json_encode($access_permission); ?>;
    var __sel_course_name      = '<?php echo $selected_course; ?>';
    var __sel_assignment_title = '<?php echo $selected_assignment; ?>';
    var __assignfacultyAttempts= {};
    var __attemptIdChecked     = 0;
    var __limit                = '<?php echo $limit; ?>';
    var __offset               = Number('<?php echo isset($_GET['offset']) ? $_GET['offset'] : '1' ?>');
    var __totalAttempts        = '<?php echo $total_attempts; ?>';
    var __course_id            = '<?php echo $course_id; ?>';
    var __assignment_id        = '<?php echo isset($_GET['assignment']) ? $_GET['assignment'] : '' ?>';
    var __institute_id         = '<?php echo isset($_GET['institute_id']) ? $_GET['institute_id'] : 'all' ?>';
    var __batch_id             = '<?php echo isset($_GET['batch_id']) ? $_GET['batch_id'] : 'all' ?>';
    var __filter               = '<?php echo isset($_GET['filter']) ? $_GET['filter'] : 'all' ?>';
    var __tutor                = '<?php echo isset($_GET['tutor']) ? $_GET['tutor'] : 'all' ?>';
    var __keyword              = '<?php echo isset($_GET['keyword']) ? $_GET['keyword'] : '' ?>';
    var __sort_by              = '<?php echo isset($_GET['sort_by']) ? $_GET['sort_by'] : 'all' ?>';
    var __grade                = '<?php echo json_encode($grade); ?>';
    var __default_user_path    = '<?php echo default_user_path(); ?>';
    var __user_path            = '<?php echo user_path(); ?>';
    var __attempts_ids         = new Array();
    var __assignment_path      = '<?php echo assignment_path(array("course_id" => $course_id, 'purpose' => 'assignment')); ?>';
    var __assignment_submission_path = '<?php echo assignment_path(array("course_id" => $course_id, 'purpose' => 'assignment_submission')); ?>';
 
    
    var __asset_url            = '<?php echo assets_url() ?>';
    var __assessmentObject = new Array;
        __assessmentObject = atob('<?php echo base64_encode(json_encode($assignments)) ?>'); 
        __assessmentObject = $.parseJSON(__assessmentObject);
    
    var __getLectures   = false;
    var __ins_selected  = 0;
        
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
            __institute_id = institute_id;
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

        // if (batch_id != '') {
         
        //     __batch_id = batch_id;
        //     $('#filter_batch').html('<span class="dropdown-filter" title="'+ $('#filter_batch_' + batch_id).text() +'">' + $('#filter_batch_' + batch_id).text() + '</span><span class="caret"></span>');
        // }

        // //  if (tutor != '') {
        // //    $('#filter_tutor').html('<span class="dropdown-filter" title="'+ $('#filter_tutor_' + tutor).text() +'">' + $('#filter_tutor_' + tutor).text() + '</span><span class="caret"></span>');
        // // }

        // if (keyword != '') {
        //     __keyword = keyword;
        //     keyword = keyword.split('-').join(' ');
        //     $('#attendees_keyword').val(keyword);
        // }

        // if(filter!=''){
        //     __filter = filter;
        //     $('#dropdown_role_filter').html('<span class="dropdown-filter" title="'+ $('#filter_user_'+ filter).text() +'">' + $('#filter_user_' + filter).text() + '</span><span class="caret"></span>');
        // }

        // if(sort_by!=''){
        //     __sort_by = sort_by;
        //     $('#dropdown_sort_filter').html('<span class="dropdown-filter" title="'+ $('#filter_sort_' + sort_by).text() +'">' + $('#filter_sort_' + sort_by).text() + '</span><span class="caret"></span>');
        // }
       
        $('#assignment_wrapper').html(renderAssignmentHtml(__assessmentObject));
        renderPagination(__offset, __totalAttempts);
    });
    var __userImage = '';

    var __assignmentId  = 0;
    $(".srch_txt").keyup(function(){
        $("#searchclear").toggle(Boolean($(this).val()));
    });
    $("#searchclear").toggle(Boolean($(".srch_txt").val()));
    $("#searchclear").click(function(){
        $(".srch_txt").val('').focus();
        $(this).hide();
        __keyword = '';
        getAssignments();
    });
</script>
<script>
    function getQueryStringValue(key) {
        return decodeURIComponent(window.location.search.replace(new RegExp("^(?:.*[&\\?]" + encodeURIComponent(key).replace(/[\.\+\*]/g, "\\$&") + "(?:\\=([^&]*))?)?.*$", "i"), "$1"));
    }
    function renderAssignmentHtml(assignments)
    {
        
        __assignmentId  = 0;
        var assignmentHtml  = '';
        if(Object.keys(assignments).length > 0 )
        {
            $.each(assignments, function(assignmentKey, assignment )
            {
                if(__assignmentId == 0)
                {
                    __assignmentId = assignment['id'];
                }
                if(__course_id == 0)
                {
                    __course_id = assignment['cl_course_id'];                    
                }
                assignmentHtml += '<div class="list-row assignment-listing-row" id="assignment_'+assignment['id']+'" onclick="loadAssignmentAttendees('+assignment['id']+', '+assignment['cl_course_id']+')">';
                assignmentHtml += '    <div class="list-col">';
                assignmentHtml += '         <span class="wrap-mail ellipsis-hidden"> ';
                assignmentHtml += '            <div class="ellipsis-style">';
                assignmentHtml += '                <i class="icon icon-clipboard"></i> ';
                assignmentHtml += '                <a href="javascript:void(0)">'+assignment['cl_lecture_name']+'</a>';
                assignmentHtml += '            </div>';
                assignmentHtml += '        </span>';
                assignmentHtml += '    </div>';
                assignmentHtml += '</div>';
                
                if(typeof assignment['attempts'] != 'undefined')
                {
                    var attemptWrapperHtml = '';
                    var attemptLength = Object.keys(assignment['attempts']).length;
                    if(attemptLength!=0){
                        $.each(assignment['attempts'], function(attemptKey, attempt )
                        {
                            attemptWrapperHtml += attempts(attempt);
                        });
                        $('#attempt_detail_wrapper').html(assignment_header()+attemptWrapperHtml);
                    } else {
                        attemptWrapperHtml += '<div class="no-list-message text-center">No students found</div>';
                        $('#attempt_detail_wrapper').html(attemptWrapperHtml);
                    }

                    $("#attempt-count").html(attemptLength);
                    $("#total-count").html(__totalAttempts);
                    
                   
                }
            });
        }
        return assignmentHtml;
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
            __attempts_ids.push(+attempt['attempt_id']);
            __userImage  = ((attempt['us_image'] == 'default.jpg')?webConfigs('default_user_path'):webConfigs('user_path')); 
            var attemptHTml  = '';
            attemptHTml += '<div class="rTableRow assignment-listing-row">';
            attemptHTml += '    <div class="rTableCell"> ';
            var space_class = "";
            if( attempt['dtua_evaluated'] == 0){
                space_class = '';
                if(__access_permission.indexOf("3")!=-1){
                    attemptHTml += '<span id="evaluate_checkbox_'+attempt['attempt_id']+'_'+attempt['cs_user_id']+'"><input  type="checkbox" name="" value="'+attempt['attempt_id']+'" class="assignment-attempts" value="" placeholder=""></span>';
                } else {
                    space_class = 'left-space';  
                }
            } else{
                space_class = 'left-space'; 
            }
            attemptHTml += '<span id="user_register_'+attempt['attempt_id']+'_'+attempt['cs_user_id']+'" class="link-pointer '+space_class+'" onclick="getReport('+attempt['attempt_id']+')"></span>';
            attemptHTml += '    </div>';
            attemptHTml += '    <div class="rTableCell" onclick="getReport('+attempt['attempt_id']+')"> ';
            // attemptHTml += '        <span class="icon-wrap-round img">';
            // attemptHTml += '            <img src="'+__userImage+''+attempt['us_image']+'">';
            // attemptHTml += '        </span>';
            attemptHTml += '        <span class="wrap-mail ellipsis-hidden"> ';
            attemptHTml += '            <div class="ellipsis-style">';
            //if(attempt['dtua_evaluated'] == 0){
            attemptHTml += '                <a>'+attempt['us_name']+'</a>';
            // }else{
            //     //attemptHTml += '                <a href="javascript:void(0)">'+attempt['us_name']+'</a>';
            // }
            attemptHTml += '            </div>';
            attemptHTml += '        </span>';
            attemptHTml += '    </div>';
            attemptHTml += '    <div class="rTableCell" onclick="getReport('+attempt['attempt_id']+')">';
            attemptHTml += '        <span class="wrap-mail ellipsis-hidden"> ';
            attemptHTml += '            <div class="ellipsis-style">';
            if(attempt['created_date'] != null)
            {
            var createdDate = attempt['created_date'];
                //createdDate = new Date(createdDate);  
                attemptHTml += '<span class="link-pointer" >'+createdDate+'</span>';             
            }
            else
            {                
                attemptHTml += '-';            
            }
            attemptHTml += '            </div>';
            attemptHTml += '        </span>';
            attemptHTml += '    </div> ';
            attemptHTml += '    <div class="rTableCell pad0 text-center" onclick="getReport('+attempt['attempt_id']+')" style="min-width:100px;">';
            if( attempt['mark'] != null && attempt['mark'] > -1 )
            {
                attemptHTml += '        <span id="current_mark_'+attempt['attempt_id']+'_'+attempt['cs_user_id']+'" class="green-text">'+attempt['mark']+'</span>';
            }
            else
            {       
               attemptHTml += '<span  class="link-pointer">-</span>';                    
            }
            attemptHTml += '    </div>';
            attemptHTml += '    <div class="rTableCell pad0 text-center" style="min-width:100px;">';
            
            if((attempt['dtua_grade'] == '') || (attempt['dtua_grade'] ==null)) 
            {
                if(__access_permission.indexOf("3")!=-1)
                {
                    attemptHTml += '<span class="green-text" data-toggle="tooltip" title="Double click to edit." id="current_grade_'+attempt['attempt_id']+'_'+attempt['cs_user_id']+'" ondblclick="editgrade('+attempt['attempt_id']+','+attempt['cs_user_id']+')" style="cursor:pointer;">-</span>';
                } 
                else 
                {
                    attemptHTml += '<span class="green-text">-</span>';   
                }
            }
            else 
            {
                if(__access_permission.indexOf("3")!=-1)
                {
                    attemptHTml += '<span class="green-text" data-toggle="tooltip" title="Double click to edit." id="current_grade_'+attempt['attempt_id']+'_'+attempt['cs_user_id']+'" ondblclick="editgrade('+attempt['attempt_id']+','+attempt['cs_user_id']+')">'+attempt['dtua_grade']+'</span>';   
                }
                else 
                {
                    attemptHTml += '<span class="green-text">'+attempt['dtua_grade']+'</span>';   
                }
            }
            attemptHTml += '<select  id="change_grade_'+attempt['attempt_id']+'_'+attempt['cs_user_id']+'" onChange="changeGrade('+attempt['attempt_id']+','+attempt['cs_user_id']+')" style="display:none;">';
            var grade_data = JSON.parse(__grade);
            attemptHTml += '<option value="0" selected>Select</option>'; 
            for (var i in grade_data) 
            {
                attemptHTml += '<option value="'+grade_data[i].id+'">'+grade_data[i].gr_name+'</option>';    
            }
            attemptHTml += '</select>';
            attemptHTml += '    </div>';
            attemptHTml += '    <div class="rTableCell text-center"> ';
            attemptHTml += '        <span class="wrap-mail ellipsis-hidden"> ';
            attemptHTml += '            <div class="ellipsis-style">';
            if((attempt['dtua_evaluated'] == 0)||(attempt['dtua_evaluated'] == 1))
            {   
                if(__access_permission.indexOf("3")!=-1){
                    var faculty_img  = (attempt['faculty_image']!=null)?attempt['faculty_image']:'default.jpg';
                    var faculty_name = (attempt['faculty_name']!=null)?attempt['faculty_name']+".":'';
                    var user_img = ((faculty_img == 'default.jpg')?webConfigs('default_user_path'):webConfigs('user_path'));
                    if(attempt['dtua_evaluated'] == 0){
                        attemptHTml += '<a class="link-pointer evaluate-btn" onclick="getReport('+attempt['attempt_id']+')" ><span id="evaluated_txt_'+attempt['attempt_id']+'_'+attempt['cs_user_id']+'">Evaluate</span><ripples></ripples></a><div class="quiz-list-avatar bold"><img class="img-cirlce img-responsive" data-toggle="tooltip" title="'+faculty_name+' Click to assign a faculty." id="faculty_img_'+attempt['attempt_id']+'" src="'+user_img+faculty_img+'" onclick="assignFaculty('+attempt['attempt_id']+')" width="26"></div>';
                    } else {
                            attemptHTml += '<a class="link-pointer evaluated-btn" onclick="getReport('+attempt['attempt_id']+')"><span id="evaluated_txt_'+attempt['attempt_id']+'_'+attempt['cs_user_id']+'">Evaluated</span><ripples></ripples></a><div class="quiz-list-avatar bold"><img class="img-cirlce img-responsive" data-toggle="tooltip" title="'+faculty_name+'" id="faculty_img_'+attempt['attempt_id']+'" src="'+user_img+faculty_img+'" width="26"></div>';
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
        getAssignments();
        
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
        getAssignments();
    }

    function filter_batch(batch_id) {
        if (batch_id == 'all') {
            __batch_id = 'all';
            $('#filter_batch').html('All Batches <span class="caret"></span>');
        } else {
            __batch_id = batch_id;
            $('#filter_batch').html('<span class="dropdown-filter" title="'+ $('#filter_batch_' + batch_id).text() +'">' + $('#filter_batch_' + batch_id).text() + '</span><span class="caret"></span>');
        }
        __offset = 1;
        getAssignments();
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
       getAssignments();
    }

    function filterUser(filter, label)
    {
         __filter = filter;
        $('#dropdown_role_filter').html('<span class="dropdown-filter" title="'+ atob(label) +'">' + atob(label) + '</span><span class="caret"></span>');
        __keyword = $('#attendees_keyword').val();
        getAssignments();
        
    }
    function filterSortby(filter, label)
    {
         __sort_by = filter;
        $('#dropdown_sort_filter').html('<span class="dropdown-filter" title="'+ atob(label) +'">' + atob(label) + '</span><span class="caret"></span>');
        __keyword = $('#attendees_keyword').val();
        getAssignments();
    }
    
    

    function getAssignments(){
        
        if (history.pushState) {
            var link = window.location.protocol + "//" + window.location.host + window.location.pathname;

            if (__course_id != '' || __assignment_id!='' || __institute_id != '' || __batch_id != '' || __filter != '' || __keyword != '' || __sort_by !='') {
                link += '?';
            }
            if(__course_id != '') {
                link += '&course=' + __course_id;
            }

            if(__assignment_id != '') {
                link += '&assignment=' + __assignment_id;
            }

            if (__institute_id != '') {
                link += '&institute_id=' + __institute_id;
            }

            if (__batch_id != '') {
                link += '&batch_id=' + __batch_id;
            }

            if (__filter != '') {
                link += '&filter=' + btoa(__filter);
            }

            if (__keyword != '') {
                link += '&keyword=' + __keyword;
            }

            if(__tutor !=''){
                link += '&tutor=' + __tutor;
            }

            if(__sort_by !=''){
                link += '&sort_by=' + __sort_by;
            }
            __offset = (typeof __offset == 'undefined') ? 1 : __offset;
            link += '&offset=' + __offset;
            window.history.pushState({
                path: link
            }, '', link);
        }

        
        $.ajax({
            url: webConfigs('admin_url')+'report/course_assignment',
            type: "POST",
            data:{ "is_ajax":true,"course_id":__course_id,"assignment_id":__assignment_id,"institute_id":__institute_id,"batch_id":__batch_id, "filter":btoa(__filter),"keyword":__keyword,"tutor":__tutor,"sort_by":__sort_by,"limit":__limit,"offset":__offset},
            success: function(response) {
                var data  = $.parseJSON(response);
                __totalAttempts = data['total_attempts'];
                renderPagination(__offset, __totalAttempts);
                if(data['error'] == false)
                {
                    if(typeof data['assignments'] != 'undefined')
                    {
                        $('#attempt_detail_wrapper').html('');
                        $('#assignment_wrapper').html(renderAssignmentHtml(data['assignments']));
                        refreshListing();
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
                    }
                    else {
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
    
    function exportAssignmentReport()
    {
        var filter  = $('#dropdown_role_filter').attr('data-filter'), 
            keyword = $('#attendees_keyword').val();
        var params                  = {};
            params['filter']        = __filter;
            params['keyword']       = __keyword;
            params['assignment_id'] = __assignment_id;
            params['course_id']     = __course_id;
            params['sort_by']       = __sort_by;
            params['batch_id']      = __batch_id;
            params['institute_id']  = __institute_id;
            params['tutor']         = __tutor;
            params['limit']         = __limit;
            params['offset']        = __offset;
            location.href = webConfigs('admin_url')+'report/export_assignment/'+btoa(JSON.stringify(params));
    }
    
    function assignment_header()
    {
        return `<div class="rTableRow">
                <span data-toggle="tooltip" title="Click to Select All" class="wrap-mail ellipsis-hidden">
                    <!--<div class="ellipsis-style">
                        <a href="#">
                            <label> 
                                <input class="attempts-checkbox-parent" type="checkbox" onclick="selectAll();"  id="selectall">
                            </label>
                            <span id="selected_institutes_count"></span>
                        </a>
                    </div>-->
                </span>
            
            <div class="rTableCell"><span class="wrap-mail ellipsis-hidden"><div class="ellipsis-style"><a href="javascript:void(0)"><strong>Student Name</strong></a></div></span></div><div class="rTableCell"><span class="wrap-mail ellipsis-hidden"><div class="ellipsis-style"><a href="javascript:void(0)"><strong>Submitted Date</strong></a></div></span></div><div class="rTableCell pad0 text-center" style="min-width:100px;"><span><strong>Marks</strong></span></div><div class="rTableCell pad0 text-center" style="min-width:100px;"><span><strong>Grade</strong></span></div><div class="rTableCell text-center"><span class="wrap-mail ellipsis-hidden"><div class="ellipsis-style"><a href="javascript:void(0)"><strong>Status</strong></a></div></span></div></div>`;
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
        getAssignments();
    });

 

    function refreshListing() {
        if ($('.pagination li.active a').hasClass('pagination-last-page') == true) {
            if ($('.assignment-listing-row').length == 0) {
                __offset = $('.pagination li.active a').attr('data-page');
                __offset = __offset - 1;
                if (__offset == 0) {
                    __offset = 1;
                }
               // getAssignments();
            }
        } else {
            if ($('.assignment-listing-row').length == 0) {
                __offset = $('.pagination li.active a').attr('data-page');
                //getAssignments();
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
            url: admin_url+'report/assign_faculty',
            type: "POST",
            data:{"is_ajax":true, "attempt_ids":JSON.stringify(__assignfacultyAttempts),"faculty_id":faculty_id},
            success: function(response) {
                var data = $.parseJSON(response);
                var attempt_ids = data.results['attempt_id'];
                if(data.error== false)
                {
                    for (var i in attempt_ids) {
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
        $("#current_grade_"+id+"_"+__sel_userid).hide();
        $("#change_grade_"+id+"_"+__sel_userid).show();
    } 

    $('body').click(function(evt){ 
       if(evt.target.id != "change_grade_"+__sel_attempt+"_"+__sel_userid){
            $("#current_grade_"+__sel_attempt+"_"+__sel_userid).show();
            $("#change_grade_"+__sel_attempt+"_"+__sel_userid).hide();
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
                url: admin_url+'report/assign_grade',
                type: "POST",
                data:{"is_ajax":true, "attempt_id":attempt_id,"grade_id":sel_grade,"grade_txt":grade_txt,"assignment_id":__assignment_id,"user_id":user_id},
                success: function(response) {
                    var data = $.parseJSON(response);
                    if(data['error'] == false)
                    {
                        $("#current_grade_"+id+"_"+user_id).html(grade_txt);
                        $("#current_grade_"+id+"_"+user_id).show();
                        $("#change_grade_"+id+"_"+user_id).hide();
                        $('[data-toggle="tooltip"]').tooltip();
                    }
                }
            });
        } else {
            return false;
        }
        
    }

    function updateReportgrade(id,gradeid){
        $.ajax({
            url: admin_url+'report/update_grade_report',
            type: "POST",
            data:{"is_ajax":true, "attempt_id":id,"grade_id":gradeid,"assignment_id":__assignment_id},
            success: function(response) {
                var data = $.parseJSON(response);
                //console.log(data);
                if(data['error'] == false)
                {
                    //alert('test1');
                    
                    var faculty_img  = (data['faculty_img']!=null)?data['faculty_img']:'default.jpg';
                    var faculty_name = (data['faculty_name']!=null)?data['faculty_name']+".":'';
                    var user_img    = ((faculty_img == 'default.jpg')?webConfigs('default_user_path'):webConfigs('user_path'));
                    //alert(faculty_img);
                    $("#current_grade_"+id+"_"+__sel_userid).html(data['grade_name']);
                    $("#current_grade_"+id+"_"+__sel_userid).show();
                    $("#change_grade_"+id+"_"+__sel_userid).hide();
                    $("#evaluated_txt_"+id+"_"+__sel_userid).html('Evaluated');
                    $("#evaluated_txt_"+id+"_"+__sel_userid).removeClass('evaluate-btn');
                    $("#evaluated_txt_"+id+"_"+__sel_userid).addClass('evaluated-btn');
                    $("#evaluate_checkbox_"+id+"_"+__sel_userid).remove();
                    $("#faculty_img_"+id).attr("onclick", "").unbind("click");
                    $("#faculty_img_"+id).attr("title", "")
                    $("#user_register_"+id+"_"+__sel_userid).addClass('left-space');
                    $("#faculty_img_"+id).attr("onclick", "").unbind("click");
                    $("#faculty_img_"+id).attr("title", "");
                    $("#faculty_img_"+id).attr("data-original-title", "");
                    $("#faculty_img_"+id).attr("title", faculty_name);
                    $("#faculty_img_"+id).attr("src", user_img+faculty_img);
                }
            }
        });
    }

    function addComment(id){
        var feedback_text = $("#feedback-text").val();
        if(feedback_text==''){
            var messageObject = {
                'body':'Feedback should not be empty!',
                'button_yes':'OK', 
                'button_no':'CANCEL'
            }; 
            $('.feedback_error').html('<span style = "color:red">Feedback should not be empty!</span>');
            // callback_warning_modal(messageObject);
            
            return false;
        }
        else {
            $('.feedback_error').html('');
            $.ajax({
                url: admin_url+'report/add_feedback_comment',
                type: "POST",
                data:{"is_ajax":true, "attempt_id":id,"feedback_text":feedback_text},
                success: function(response) {
                    var data = $.parseJSON(response);
                    if(data['error'] == false)
                    {
                        var rederComment = '';
                        rederComment += '<div class="feedback-comment-wrapper">';
                        rederComment += '   <p class="text-justify">'+feedback_text+'</p>';
                        rederComment += '   <span class="feedback-status">Feedback Send On : '+data['updated_date']+'</span>';
                        rederComment += '</div>';
                        $("#feedback_comment").append(rederComment);
                        $("#feedback-text").val('');
                    }
                }
            });
        }
    }

    function updateMark(id){
        var assignment_mark = Number($("#assignment_mark").val());
        var grade_details   = JSON.parse(__grade);
        var messageObject   = '';
        var mark_percentage = Number('0');
        if(assignment_mark >__assignment_total_mark){
            messageObject += '<div id="popUpMessage" class="alert alert-danger">Assignment mark should not be greater than '+__assignment_total_mark+'!<br><a data-dismiss="alert" class="close">×</a></div>';
            $("#error-msg").html(messageObject);
            return false;
        } else {
            mark_percentage = ((Number(assignment_mark) / __assignment_total_mark) * 100);
            for (var i in grade_details) {
                if((mark_percentage >= grade_details[i].gr_range_from) && (mark_percentage <= grade_details[i].gr_range_to)){
                    //$('#grade-1').addClass('active-grade');
                    //updateReportgrade(id,grade_details[i].id);
                    $.ajax({
                        url: admin_url+'report/update_grade_report',
                        type: "POST",
                        data:{"is_ajax":true, "attempt_id":id,"grade_id":grade_details[i].id,"assignment_id":__assignment_id,"assignment_mark":assignment_mark},
                        success: function(response) {
                            var data = $.parseJSON(response);
                            if(data['error'] == false)
                            {
                                var faculty_img  = (data['faculty_img']!=null)?data['faculty_img']:'default.jpg';
                                var faculty_name = (data['faculty_name']!=null)?data['faculty_name']+".":'';
                                var user_img    = ((faculty_img == 'default.jpg')?webConfigs('default_user_path'):webConfigs('user_path'));
                                //alert('#grade_'+id+'_'+data['grade']);
                                // $('#grade_'+id+'_'+data['grade']).addClass('active-grade');
                                $('#grade_'+id+'_'+data['grade']).css('background-color', 'red');
                                $("#current_grade_"+id+"_"+__sel_userid).html(data['grade_name']);
                                $("#current_grade_"+id+"_"+__sel_userid).show();
                                $("#change_grade_"+id+"_"+__sel_userid).hide();
                                $("#current_mark_"+id+"_"+__sel_userid).html(assignment_mark);
                                $("#evaluated_txt_"+id+"_"+__sel_userid).html('Evaluated');
                                $("#evaluated_txt_"+id+"_"+__sel_userid).removeClass('evaluate-btn');
                                $("#evaluated_txt_"+id+"_"+__sel_userid).addClass('evaluated-btn');
                                $("#evaluate_checkbox_"+id+"_"+__sel_userid).remove();
                                $("#faculty_img_"+id).attr("onclick", "").unbind("click");
                                $("#faculty_img_"+id).attr("title", "");
                                $("#faculty_img_"+id).attr("onclick", "").unbind("click");
                                $("#faculty_img_"+id).attr("data-original-title", "");
                                $("#faculty_img_"+id).attr("title", faculty_name);
                                $("#faculty_img_"+id).attr("src", user_img+faculty_img);
                                $("#user_register_"+id+"_"+__sel_userid).addClass('left-space');
                                getReport(id);
                            }
                        }
                    });
                }      
            }
        }
        
    }

    //chart settings
    $( document ).ready(function() { // 6,32 5,38 2,34
        $("#content-chart").circliful({
            animation: 1,
            animationStep: 5,
            animateInView: false,
            foregroundColor: "#e00000",
            backgroundColor: "#0b94c8",
            foregroundBorderWidth: 20,
            backgroundBorderWidth: 20,
            percent: 30,
            percentageTextSize: 0
        });
    });

    // tooltip
    function getReport(id){
        if(__access_permission.indexOf("3")!=-1){
        var attempt_id = id;
        $.ajax({
            url: admin_url+'report/get_assign_report', 
            type: "POST",
            data:{"is_ajax":true, "attempt_id":attempt_id,"assignment_id":__assignment_id},
            success: function(response) {
                var data = $.parseJSON(response);
                //console.log(data);
                __assignment_total_mark = Number(data['assignment'][0].dt_total_mark);
                __sel_userid   = data['assignment'][0].dtua_user_id;
                var users_img  = (data['assignment'][0].us_image!=null)?data['assignment'][0].us_image:'default.jpg';
                var img_path   = ((users_img == 'default.jpg')?webConfigs('default_user_path'):webConfigs('user_path'));
                var renderAssignmentReport = '';
                renderAssignmentReport += '<div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>';
	            renderAssignmentReport += '<h4 class="modal-title" id="create_box_title">ASSIGNMENT REPORT - <span id="report-assignment-title">'+(__sel_course_name+' - '+__sel_assignment_title).toUpperCase()+'</span></h4></div>';
                renderAssignmentReport += '<div class="modal-body no-padding"><div class="asssignment-report-wrapper"><div class="col-md-7 no-padding assignment-answer-wrapper"><div class="assignment-question-wrapper-toggle"><div class="view-assignment-question">';
                renderAssignmentReport += '<h4 class="assignment-question-main">'+data['assignment'][0].dt_description+'</h4>';
                renderAssignmentReport += '<p class="">'+data['assignment'][0].dt_instruction+'</p>';
                renderAssignmentReport += '<div class="assignment-attachment-holder">';
                var user_uploaded_files =(data['assignment'][0].dt_uploded_files!='')?data['assignment'][0].dt_uploded_files:'[]';
                
                if(user_uploaded_files != undefined && user_uploaded_files != '[]'){
                    uploaded_data = $.parseJSON(user_uploaded_files);
                    $.each(uploaded_data, (key,file) =>
                    {
                        renderAssignmentReport += '<span class="attach-assignment-file"><a href="'+__assignment_path+file.file_name+'">'+file.name+'</a></span>';   
                        //console.log(key,file);
                    }
                    );
                }
                
		        renderAssignmentReport += '</div></div><div class="view-assignment-ques text-center"><span class="view-assignment-toggle-text">View Assignment Question</span></div></div>';
                renderAssignmentReport += '			<div class="assignment-container"><h2 class="text-center student-responds">Student Responds</h2>';
                
		        //renderAssignmentReport += '			<h4 class="assignment-question">Why do we use it?</h4>';
                var users_comments  = (data['assignment'][0].dtua_comments!='')?data['assignment'][0].dtua_comments:'[]';
                if(users_comments != undefined && users_comments != '[]'){
                var __comments_txt  = users_comments;
                var comments_data =JSON.parse(users_comments);
                    $.each(comments_data, function (key,comment_data)
                    {
                        //console.log(key,comment_data);
                        renderAssignmentReport += '<div class="assinment-content"><p class="assignment-answer">';
                        if((comment_data.comment!=null) && (comment_data.user_type=='0'))
                        {
                        renderAssignmentReport += comment_data.comment;
                        }
                        renderAssignmentReport += '</p></div>';
                        renderAssignmentReport += '<div class="assignment-attachment-holder">';
                        if((comment_data.file!='') && (comment_data.user_type==0))
                        {
                            var assignment_file = $.parseJSON(comment_data.file);
                            $.each(assignment_file, function (key,assignment_file)
                            {
                                renderAssignmentReport += '<span class="attach-assignment-file"><a href="'+__assignment_submission_path+assignment_file.encrypted+'">'+assignment_file.file+'</a></span>';
                            });
                        }
                        renderAssignmentReport += '</div>';
                    });
                }
                

                renderAssignmentReport += '</div></div>';
                renderAssignmentReport += '<div class="col-md-5 no-padding assignment-evaluation-wrapper">';
                renderAssignmentReport += '<div class="assignment-profile-info"><div class="assignment-avatar-info"><div class="avatar-image-info"><img src="'+img_path+users_img+'" class="img-circle" alt="" width="35"></div>';
		        renderAssignmentReport += '<div class="avatar-info"><span class="stud-name">'+data['assignment'][0].us_name+'</span><span class="submition-date">'+data['assignment'][0].created_date+'</span></div></div>';
                renderAssignmentReport += '<div class="nav-next text-right">';  
                if(__attempts_ids[__attempts_ids.length-1]!=attempt_id){
                    renderAssignmentReport += '<a class="next-btn" href="#" onclick="getNextreport('+attempt_id+');">NEXT<svg version="1.1" id="Layer_1" xmlns:sketch="http://www.bohemiancoding.com/sketch/ns" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="9px" height="12px" viewBox="0 0 9 12" enable-background="new 0 0 9 12" xml:space="preserve"><title></title><desc></desc><g id="Page-1"><g id="Core" transform="translate(-218.000000, -90.000000)"><g id="chevron-left" transform="translate(218.500000, 90.000000)"><path id="Shape" fill="#2C78D6" d="M4.601,6L0,10.6L1.4,12l6-6l-6-6L0,1.4L4.601,6z"/></g></g></g></svg></a>';  
                } 			
		        renderAssignmentReport += '</div></div>';

				renderAssignmentReport += '<div class="assessment-col"><h5 class="assesment-title">GRADE ASSIGNMENT</h5><div class="assesment-area"><div id="error-msg"></div><div class="col-md-10 no-padding"><label>Grade :</label><ul class="text-center grade-table"  border="1">';
                grade_data = data['grade'];
                var length = data['grade'].length;
                
                $.each(grade_data, function (key,grade)
                {
                    var  __class_name ='';
                    var grade_name = data['assignment'][0].dtua_grade;
                    if(key=='1'){
                        __class_name +="border-list-left";
                    } else {
                        __class_name +='';  
                    }
                    if(key=='9'){
                      __class_name +="border-list-right";
                    }

                    if(grade_name==grade.gr_name){
                        __class_name +=" active-grade";
                    }
                    renderAssignmentReport += '<li id="#grade_'+attempt_id+'_'+grade.id+'" class="'+__class_name+'" onclick="updateReportgrade('+id+','+grade.id+')" data-toggle="tooltip" title="'+grade.gr_range_from+'% - '+grade.gr_range_to+'%">'+grade.gr_name+'</li>';
                });
                var evaluated_by       = (data['assignment'][0].dtua_evaluated!=0)?data['assignment'][0].evaluated_by:' Not yet assigned';
                var evaluated_on       = (data['assignment'][0].dtua_evaluated!=0)?data['assignment'][0].updated_date:' Not yet evaluated';
                renderAssignmentReport += '</ul></div><div class="col-md-2 no-padding"><label>Mark :</label><input type="text" class="form-control mark" maxlength="3" onchange="updateMark('+attempt_id+')" id="assignment_mark" name="" value="'+data['assignment'][0].mark+'" placeholder="" ></div>';

		        renderAssignmentReport += '<div class="col-md-12 no-padding evaluation-info"><div class="col-md-6 text-left no-padding"><span class="blue-text">Evaluated By :  '+evaluated_by+' </span></div><div class="col-md-6 text-right no-padding">';
				renderAssignmentReport += '<span class="blue-text">Evaluated On : '+evaluated_on+'</span></div></div></div></div>';
                //renderAssignmentReport += '<div class="col-md-12 no-padding text-right"><button class="btn btn-success">SAVE</button></div>';
                /* Plagarism Checking */
                //renderAssignmentReport += '<div class="col-md-12 assignment-report-chart"><h5 class="assesment-title">Assignment content uniqueness</h5><div class="col-md-5 no-padding chart-box"><div id="content-chart"></div></div>';
		        //renderAssignmentReport += '<div class="col-md-7 chart-info"><div class="col-md-6 no-padding text-center"><span class="unique-percent">60%</span><span class="unique-percent-title">Unique Content</span></div>';
		        //renderAssignmentReport += '<div class="col-md-6 no-padding text-center"><span class="copy-percent">40%</span><span class="copy-percent-title">Copied Content</span></div></div><div class="option-tag text-center"><a href="#" class="ques-option-info report-icn">View Report</a></div></div>';

                renderAssignmentReport += '<div class="col-md-12 assignment-feedback"><h5 class="assesment-title">Assignment feedback</h5>';
               
                renderAssignmentReport += '<div id="feedback_comment">';
                var feedback_comments  = (data['assignment'][0].dtua_comments!='')?data['assignment'][0].dtua_comments:'[]';
                if(feedback_comments != undefined && feedback_comments != '[]'){
                var comment_data =JSON.parse(feedback_comments);
                    $.each(comment_data, function (key,feedback_data) {
                        
                        var assignmentReportHtml = '';
                        if((feedback_data.comment!='') && (feedback_data.user_type=='1')) {
                            // //console.log(feedback_data.comment);
                            assignmentReportHtml += '<p class="text-justify">';
                            assignmentReportHtml += feedback_data.comment;
                            assignmentReportHtml += '</p>';
                        }
                    
                        if((feedback_data.update_date!='') && (feedback_data.user_type=='1')) {
                            assignmentReportHtml += '<span class="feedback-status"> Feedback Send On : '+feedback_data.update_date+'</span>';
                        }
                        if( assignmentReportHtml != '' ) {
                            renderAssignmentReport += '<div class="feedback-comment-wrapper">';
                            renderAssignmentReport += assignmentReportHtml;
                            renderAssignmentReport += '</div>';
                        }
                    });
                }
                renderAssignmentReport += '</div>';
               
		        
		        // renderAssignmentReport += '<div class="border-line"></div>';
		        renderAssignmentReport += '<div class="feedback_error"></div><textarea class="form-control feedback-text" placeholder="Feedback" id="feedback-text" rows="5" name="" resize="no"></textarea>';

		        renderAssignmentReport += '<div class="feedback-btn-holder text-right"><button class="btn btn-success" type="" onclick="addComment('+id+')">SEND FEEDBACK</button></div>';
		        renderAssignmentReport += '</div></div></div></div>';
                $("#grade_report_detail").modal();
                $("#assignment-report-content").html(renderAssignmentReport);
                $(".view-assignment-toggle-text").click(function(){
                    $(".view-assignment-question").slideToggle();
                    $(".view-assignment-question").css('display', 'block');
                });
                $(".grade-table li").bind('click', function(){
                    if($(".grade-table li.active-grade").length) $(".grade-table li.active-grade").removeClass('active-grade');
                    $(this).addClass('active-grade');
                }); 
                /* Plagrism Checking */
                // $("#content-chart").circliful({
                //     animation: 1,
                //     animationStep: 5,
                //     animateInView: false,
                //     foregroundColor: "#e00000",
                //     backgroundColor: "#0b94c8",
                //     foregroundBorderWidth: 20,
                //     backgroundBorderWidth: 20,
                //     percent: 30,
                //     percentageTextSize: 0
                // });
                
            }
        });
        } else {
            return false;
        }
       // $('[data-toggle="tooltip"]').tooltip(); 
    }

    // question viewer 
    $(document).ready(function(){
        $(".view-assignment-toggle-text").click(function(){
            $(".view-assignment-question").slideToggle();
            $(".view-assignment-question").css('display', 'block');
        });
    });


    function getGradeNamebyid(id){
        var grade_data = JSON.parse(__grade);
        var grade_name = '';
        for (var i in grade_data) {
            if(grade_data[i].id==id){
                grade_name = grade_data[i].gr_name;
                break;
            }   
        }
        return grade_name;
    }

    function getNextreport(id){
        get_index = __attempts_ids.indexOf(+id);
        if(get_index >= 0 && get_index < __attempts_ids.length-1){
            var nextItem = __attempts_ids[get_index+ 1];
            getReport(nextItem);
        } else {
            return false;
        }
    }
  
    function goBackToAssignment(){
        var link = "<?php echo admin_url() ?>"+"report/assignment";

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
                
                if(typeof params['assignment'] != 'undefined') {
                    link += '&assignment=' + params['assignment'];
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

<script>
    // $(document).ready(function(){
    //     $('[data-toggle="tooltip"]').tooltip();   
    // });
    $('[data-toggle="tooltip"]').tooltip({
        trigger : 'hover'
    })  
</script>