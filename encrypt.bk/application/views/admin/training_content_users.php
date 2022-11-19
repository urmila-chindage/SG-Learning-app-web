<?php include_once 'training_header.php';?>

<link rel="stylesheet" href="<?php echo assets_url() ?>css/datepicker.min.css">
<link rel="stylesheet" href="<?php echo assets_url() ?>css/jquery-ui.css">

<script type="text/javascript" src="<?php echo assets_url() ?>js/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/jquery-ui.min.js"></script>


<section class="content-wrap cont-course-big nav-included content-wrap-align content-wrap-top">

<style>
    .srch-filter-wrapper{min-width: 120px;}
    #filter_batch_div{padding-top: 5px;}
</style>

    <!-- Nav section inside this wrap  --> <!-- START -->
    <!-- =========================== -->

    <div class="container-fluid nav-content nav-js-height content-filter-top content-filter-fullwidth">

        <div class="row">
            <div class="rTable content-nav-tbl borderleft-none" style="">
                <div class="rTableRow d-flex align-center justify-between">

                    <div class="rTableCell selectall-width borderleft-none">
                        <a href="#" class="select-all-style"><label> <input name="" type="checkbox" class="user-checkbox-parent">  <span class="slct-all-text">Select All</span></label><span id="selected_user_count"></span></a>
                    </div>

                    <div class="rTableCell dropdown">
                        <a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" id="filter_dropdown_text"> Active Students <span class="caret"></span></a>
                        <ul class="dropdown-menu white">
                            <li><a href="javascript:void(0)" onclick="filter_user_by('all')" id="filer_dropdown_list_all"><?php echo 'All Students' ?></a></li>
                            <li><a href="javascript:void(0)" onclick="filter_user_by('completed')" id="filer_dropdown_list_completed"><?php echo lang('completed_Students') ?></a></li>
                            <li><a href="javascript:void(0)" onclick="filter_user_by('incompleted')" id="filer_dropdown_list_incompleted"><?php echo lang('incompleted_Students') ?></a></li>
                            <li><a href="javascript:void(0)" onclick="filter_user_by('not_started')" id="filer_dropdown_list_not_started"><?php echo lang('not_yet_started_users') ?></a></li>
                            <li><a href="javascript:void(0)" onclick="filter_user_by('active')" id="filer_dropdown_list_active"><?php echo lang('active_Students') ?></a></li>
                            <li><a href="javascript:void(0)" onclick="filter_user_by('suspended')" id="filer_dropdown_list_suspended">Suspended Students</a></li>
                        </ul>
                    </div>

                    <?php if (!empty($institutes)): ?>
                    <div class="rTableCell dropdown">

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

                    </div>
                    <?php endif;?>
                    <?php /* ?>
                    <div class="rTableCell dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" id="filter_branch">All Branches <span class="caret"></span></a>
                        <ul class="dropdown-menu white inner-scroll">
                            <li><a href="javascript:void(0)" id="filter_branch_all" onclick="filter_branch('all')">All Branches </a></li>
                            <?php if (!empty($branches)): 
                                $branch_tooltip = '';
                                ?>
                            <?php foreach ($branches as $branch): 
                                $branch_tooltip = (strlen($branch['branch_name']) > 15) ? ' title="' . $branch['branch_name'] . '"' : '';
                            ?>
                            <li><a href="javascript:void(0)" id="filter_branch_<?php echo $branch['id'] ?>" <?php echo $branch_tooltip; ?> onclick="filter_branch(<?php echo $branch['id'] ?>)"><?php echo $branch['branch_code'] . ' - ' . $branch['branch_name'] ?></a></li>
                            <?php endforeach;?>
                            <?php endif;?>
                        </ul>
                    </div>
                    <?php */ ?>

                    <div class="rTableCell dropdown" id="filter_batch_div" <?php echo empty($batches) ? 'style="display:none;"' : '' ?>>
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" id="filter_batch">All batches <span class="caret"></span></a>
                            <ul class="dropdown-menu white inner-scroll" id="batch_filter_list">
                                <li><a href="javascript:void(0)" id="filter_batch_all" onclick="filter_batch('all')">All batches </a></li>
                                <?php if (!empty($batches)): 
                                    $batch_tooltip = '';
                                    ?>
                                <?php foreach ($batches as $batch): 
                                    $batch_tooltip = (strlen($batch['batch_name']) > 15) ? ' title="' . $batch['batch_name'] . '"' : '';
                                    ?>
                                <li><a href="javascript:void(0)" id="filter_batch_<?php echo $batch['id'] ?>" <?php echo $batch_tooltip; ?> onclick="filter_batch(<?php echo $batch['id'] ?>)"><?php echo $batch['batch_name'] ?></a></li>
                                <?php endforeach;?>
                                <?php endif;?>
                            </ul>
                    </div>

                    <div class="rTableCell srch-filter-wrapper">
                        <div class="input-group">
                            <input type="text" class="form-control srch_txt" placeholder="Search" id="user_keyword" />
                            <span id="searchclear">×</span>
                            <a class="input-group-addon" id="basic-addon2">
                                <i class="icon icon-search"> </i>
                            </a>
                        </div>
                    </div>
                    <div class="rTableCell" style="border-left:0px;">
                        <!-- lecture-control start -->
                        <div id="course_bulk" class="btn-group lecture-control btn-right-align" style="margin-top: 0px; display: none;">
                            <span class="dropdown-tigger" data-toggle="dropdown">
                                <span class='label-text'>
                                   Bulk Action 
                                </span>
                                <span class="tilder"></span>
                            </span>
                            <ul class="dropdown-menu pull-right" role="menu">

                            <?php
                            
                                if (!empty($this->student_enroll_privilege)) {
                                if (in_array($this->privilege['view'], $this->student_enroll_privilege)) {
                                ?>
                                <li>
                                        <a href="javascript:void(0)" onclick="sendMessageToUser()"><?php echo lang('send_message') ?></a>
                                    </li>
                                    <?php
                                    }
                                }
                                ?>
                                                        <?php
                                if (!empty($this->student_enroll_privilege)) {
                                if (in_array($this->privilege['edit'], $this->student_enroll_privilege)) {
                                ?>
                                <li>
                                        <a href="javascript:void(0);" data-toggle="modal" data-target="#extend-validity" onclick="extendValidityForCourse('<?php echo $course['id'] ?>')">Change Validity Period</a>
                                    </li>
                                    <li>
                                        <a onclick="setAsComplete('<?php echo $course['id'] ?>')" href="javascript:void(0)"><?php echo lang('set_as_complete') ?></a>
                                    </li>
                                    <li>
                                        <a onclick="resetResult('<?php echo $course['id'] ?>')" href="javascript:void(0)"><?php echo lang('reset_result') ?></a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0)" onclick="approveSubscriptionStatusBulk('<?php echo $course['id'] ?>')"><?php echo lang('approve') ?></a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0)" onclick="suspendSubscriptionStatusBulk('<?php echo $course['id'] ?>')"><?php echo lang('suspend') ?></a>
                                    </li>
                                    <li>
                                        <a onclick="resetCertificate('<?php echo $course['id'] ?>')" href="javascript:void(0)">Reset Certificates</a>
                                    </li>
                                    
                                    <?php
                                }
                                }
                                ?>                          <?php
                                if (!empty($this->student_enroll_privilege)) {
                                if (in_array($this->privilege['delete'], $this->student_enroll_privilege)) {
                                ?>
                                <li>
                                        <a onclick="removeUserFromCourse('<?php echo $course['id'] ?>')" href="javascript:void(0)"><?php echo lang('remove_from_course') ?></a>
                                </li>
                                    <?php
                                }
                                }
                                ?>

                            </ul>
                        </div>
                        <!-- lecture-control end -->
                    </div>

                    <?php
if (!empty($this->student_enroll_privilege)) {
 if (in_array($this->privilege['add'], $this->student_enroll_privilege)) {
  ?>
    <!-- enroll button -->
    <div class="rTableCell d-flex align-center">
        <a href="<?php echo admin_url('course/enroll_users/' . $course['id']) ?>" class="btn btn-violet" >ENROLL<ripples></ripples></a>
    </div>
    <!-- enroll button ends -->
    <?php
}
}
?>
                </div>
            </div>

        </div>
    </div>
    <!-- =========================== -->
    <!-- Nav section inside this wrap  --> <!-- END -->


    <!-- LEFT CONTENT --> <!-- STARTS -->
    <!-- ===========================  -->

    <div class="left-wrap col-sm-12">

        <!-- Content Section --> <!-- START -->
        <!-- =========================== -->
        <div class="row">
            <div class="col-sm-12 course-cont-wrap">
            <?php
$total_page = ceil($total_enrolled / $limit);
$_offset    = isset($_GET['offset']) ? $_GET['offset'] : '1';
?>
            <?php if ($_offset <= $total_page): ?>
                <div >
                    <div class="pull-right">
                        <h4 class="right-top-header user-count">
                        <?php
$user_html = '';
$user_html .= $total_enrolled;
$user_html .= ($total_enrolled > 1) ? ' Students' : ' Student';
echo $user_html;
?>
                        </h4>
                    </div>
                </div>
                <?php endif;?>
                <div class="table course-cont only-course rTable"  id="user_row_wrapper" >
                    <?php if (!empty($enrolled_users) && $filter == '') {?>
                        <?php foreach ($enrolled_users as $user): ?>
                    <div class="rTableRow user-listing-row" id="user_row_<?php echo $user['cs_user_id'] ?>" data-name="<?php echo $user['us_name'] ?>" data-email="<?php echo $user['us_email'] ?>">
                                <div class="rTableCell">
                                    <input type="checkbox" class="user-checkbox" value="<?php echo $user['cs_user_id'] ?>" id="user_details_<?php echo $user['cs_user_id'] ?>">
                                    <svg style="vertical-align: middle; margin: 0px 10px"  xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Layer_1" x="0px" y="0px" width="16px" height="18px" viewBox="0 0 16 18" enable-background="new 0 0 16 18" fill="#64277d" xml:space="preserve">
                                    <g>
                                        <path d="M8,1.54v0.5c1.293,0.002,2.339,1.048,2.341,2.343C10.339,5.675,9.293,6.721,8,6.724C6.707,6.721,5.66,5.675,5.657,4.382   C5.66,3.088,6.707,2.042,8,2.04V1.54v-0.5c-1.846,0-3.342,1.496-3.342,3.343c0,1.845,1.497,3.341,3.342,3.341   c1.846,0,3.341-1.496,3.341-3.341C11.341,2.536,9.846,1.04,8,1.04V1.54z"/>
                                        <path d="M2.104,16.46c0-1.629,0.659-3.1,1.727-4.168C4.899,11.225,6.37,10.565,8,10.565s3.1,0.659,4.168,1.727   c1.067,1.068,1.727,2.539,1.727,4.168h1c0-3.808-3.087-6.894-6.895-6.895c-3.808,0-6.895,3.087-6.895,6.895H2.104z"/>
                                    </g>
                                    </svg>
                                    <div class="manage-stud-list" style="display:inline !important;line-height: 11px;position:relative;top: 5px;">
                                        <a href="<?php echo admin_url('user/profile/' . $user['cs_user_id']) ?>" >
                                            <span class="list-user-name text-left text-blue" style="width: 35%;"><?php echo $user['us_name'] ?></span>
                                            <span class="list-institute-code text-left"><?php echo $user['us_institute_code'] ?></span>
                                            <span class="list-register-number"><?php echo $user['us_phone'] ?></span>
                                        </a>
                                    </div>
                                </div>
                                <div class="rTableCell pad0" style="min-width:100px;" id="progress_bar_user_<?php echo $user['cs_user_id'] ?>">
                                    <div class="cent-algn-txt float-r">
                                        <a href="javascrip:void(0)" class="link-style"><?php echo $user['cs_percentage'] ?>%</a>
                                    </div>
                                    <div class="progress sml-progress stud-course-progress">
                                        <div class="progress-bar" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $user['cs_percentage'] ?>%;">
                                            <span class="sr-only"><?php echo $user['cs_percentage'] ?>% <?php echo lang('complete') ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="td-dropdown rTableCell">
                                    <div class="btn-group lecture-control">
                                        <span class="dropdown-tigger" data-toggle="dropdown">
                                            <span class='label-text'>
                                                <i class="icon icon-down-arrow"></i>
                                            </span>
                                            <span class="tilder"></span>
                                        </span>
                                        <ul class="dropdown-menu pull-right" role="menu">
                                        <?php
                                        if (!empty($this->student_enroll_privilege)) {
                                            if (in_array($this->privilege['view'], $this->student_enroll_privilege) && $user['us_email'] != '') {
                                        ?>
                                        <li>
                                            <a href="javascript:void(0)" data-toggle="modal" onclick="sendMessageToUser('<?php echo $user['cs_user_id'] ?>')" id="invite_course_user_individual"><?php echo lang('send_message') ?></a>
                                        </li>
                                        <?php
                                            }
                                        }
                                        ?>
 <?php

 if (!empty($this->student_enroll_privilege)) {
  if (in_array($this->privilege['edit'], $this->student_enroll_privilege)) {
   ?>
    <li>
        <a href="javascript:void(0);" id="extend-time_<?php echo $user['cs_user_id'] ?>" data-toggle="modal" data-target="#extend-validity" onclick="extendValidityForCourse('<?php echo $user['cs_course_id'] ?>','<?php echo $user['cs_user_id'] ?>','<?php echo $user['cs_end_date'] ?>','<?php echo $user['us_name'] ?>')">Change Validity Period</a>
    </li>
    
<li id="complete_btn_<?php echo $user['cs_user_id'] ?>">
<?php
if($user['cs_percentage']>=0&&$user['cs_percentage']!=100){
    ?>
        <a href="javascript:void(0)" onclick="setAsComplete('<?php echo $user['cs_course_id'] ?>','<?php echo $user['cs_user_id'] ?>','<?php echo $user['us_name'] ?>')"><?php echo lang('set_as_complete') ?></a>
   <?php
}
   ?></li>

<li id="reset_btn_<?php echo $user['cs_user_id'] ?>">
<?php

if($user['cs_percentage']>=0&&$user['cs_percentage']!=0){
?>
        <a href="javascript:void(0)" onclick="resetResult('<?php echo $user['cs_course_id'] ?>','<?php echo $user['cs_user_id'] ?>','<?php echo $user['us_name'] ?>')"><?php echo lang('reset_result') ?></a>
        <?php
}

?>
    </li>

    
    

    <li id="status_btn_<?php echo $user['cs_user_id'] ?>">
        <?php $cb_status = ($user['cs_approved'] == 1) ? 'suspend' : 'approve';?>
        <?php $cb_action = $cb_status?>
        <a href="javascript:void(0);"  onclick="changeSubscriptionStatus('<?php echo $user['cs_user_id'] ?>', '<?php echo $user['cs_course_id'] ?>', '<?php echo ($user['cs_approved'] == '1') ? '0' : '1'; ?>')" ><?php echo lang($cb_status) ?></a>
    </li>
    <li>
        <a href="javascript:void(0)"  onclick="resetCertificate('<?php echo $user['cs_course_id'] ?>','<?php echo $user['cs_user_id'] ?>','<?php echo $user['us_name'] ?>')">Reset Certificates</a>
    </li>
    <?php
}
 }
 ?>
 <?php
if (!empty($this->student_enroll_privilege)) {
  if (in_array($this->privilege['delete'], $this->student_enroll_privilege)) {
   ?>
   <li>
        <a href="javascript:void(0)"  onclick="removeUserFromCourse('<?php echo $user['cs_course_id'] ?>','<?php echo $user['cs_user_id'] ?>','<?php echo addslashes($user['us_name']) ?>')"><?php echo lang('remove_from_course') ?></a>
    </li>
    <?php
}
 }
 ?>
    
    <li>
        <a href="javascript:void(0)" id="forum_opt_<?php echo $user['cs_user_id']; ?>" onclick="blockUserFromForum('<?php echo $user['cs_course_id'] ?>','<?php echo $user['cs_user_id'] ?>','<?php echo $user['cs_forum_blocked'] ?>')"><?php echo $user['cs_forum_blocked']==0?'Block from Forum':'Unblock from Forum' ?></a>
    </li>

                                        </ul>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach;?>
                    <?php
} else {
 echo ($filter == '') ? '<div id="popUpMessage" class="alert alert-danger">     No Students found.</div>' : '';
 ?>
                    <?php
}
?>
                </div>
            </div>

        </div>
        <div class="row">
            <div id="pagination_wrapper">
            </div>
        </div>
        <!-- =========================== -->
        <!-- Content Section --> <!-- END -->

    </div>
    <!-- ==========================  -->
    <!--  LEFT CONTENT--> <!-- ENDS -->

</section>

            <!-- enroll-student modal starts-->
            <div class="modal enroll-modal fade" id="enroll-student" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="z-index: 9999">
                <div class="modal-dialog modal-sm">
                  <div class="modal-content custom-width">
                    <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                            <!-- enroll modal contant --> <!-- START -->
                            <?php if ($admin): ?>
                            <div class="enroll-filter-wrap white-bg cont-course-big nav-included container-fluid pull-right pad0">
                                <!-- Nav section inside this wrap  -->
                                <div class="container-fluid nav-content align-filter-col">
                                    <div class="row">
                                        <div class="rTable content-nav-tbl" >
                                            <div class="rTableRow">
                                                <div class="rTableCell selectall-width">
                                                    <a href="javascript:void(0)" class="select-all-style"><label> <input name="" type="checkbox" class="user-checkbox-not-subscribed-parent" value="1">
                                                        <span class="slct-all-text"><?php echo lang('select_all') ?></span></label><span id="selected_not_sub_user_count"></span> </a>
                                                </div>

                                                <div class="rTableCell searchbar-width" width="100%">
                                                    <div class="input-group">
                                                        <input type="text" class="form-control srch_txt" placeholder="Search by name"  id="not_subscriber_keyword" />
                                                        <a class="input-group-addon" id="search_non_subscribers">
                                                            <i class="icon icon-search"> </i>
                                                        </a>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <!-- Nav section inside this wrap  --> <!-- END -->
                                <!-- Content Section --> <!-- START -->
                                <div class="container-fluid right chrds-email">

                                    <div class="row">
                                        <div class="col-sm-12 course-cont-wrap filter-maxheight">
                                            <div class="table course-cont filter-content-align rTable" style="" id="user_not_subscribed_row_wrapper">
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <!-- Content Section --> <!-- END -->
                                <div class="container-fluid right-bottom-fixed align-bottom">
                                    <div class="row">
                                        <div class="col-sm-12 bg-grey">
                                            <label style="display:inline-block;vertical-align: middle;cursor: pointer;">
                                                <input type="checkbox" value="1" id="mail_after_subscription" />
                                                <?php echo lang('email_notification') ?>
                                            </label>
                                        </div>
                                        <div class="col-sm-12">
                                            <a href="<?php echo admin_url('user') ?>" class="btn btn-blue"><?php echo lang('add_new_user') ?></a>
                                            <div class="btn btn-light-green pull-right" onclick="subscribeUsers()"><?php echo lang('add_to_course') ?></div>
                                            <!-- <span class="small-font">or <a href="javascript:void(0)" onclick="location.reload();" class="cancel-group-creation">Cancel</a></span> -->
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <?php endif;?>
                            <!-- enroll modal contant ends-->
                    </div>
                    <div class="modal-footer"></div>
                  </div>
                </div>
            </div>
            <!-- enroll-student ends -->
            <?php include_once 'training_footer.php';?>
    <!-- initialising the tag plugin using tokenize  -->
    <script type="text/javascript" src="<?php echo assets_url() ?>js/multi-select/jquery.tokenize.js"></script>

    <!-- END -->
    <!-- ADDING REDACTOR PLUGIN INSIDE -->
    <!-- ############################# --> <!-- START -->
        <!-- JS -->
        <script src="<?php echo assets_url() ?>js/redactor/js/redactor.js"></script>
        <script src="<?php echo assets_url() ?>js/redactor/js/table.js"></script>
        <script src="<?php echo assets_url() ?>js/redactor/js/alignment.js"></script>


    <script type="text/javascript" src="<?php echo assets_url() ?>js/course_user.js"></script>

        <script>
            $(function()
            {
                <?php if (isset($filter) && $filter == 'active_users'): ?>
                    $("#filer_dropdown_list_active").trigger('click');
                <?php endif;?>
                renderPagination(__offset, __totalUsers);
            });
            var __limit         = <?php echo $limit; ?>;
            var __offset        = Number(<?php echo $_offset ?>);
            var __totalUsers    = <?php echo $total_enrolled; ?>;

            var studentEnroll_permissions = '<?php echo json_encode($this->student_enroll_privilege); ?>';
            let __studentEnrollPrivilege = new User(studentEnroll_permissions);
            var __course_title = "<?php echo addslashes($title); ?>";

                var url = window.location.search;
                url = url.replace("?", ''); // remove the ?
                //console.log(url);
                if(url=='success'){
                    var messageObject = {
                        'body': 'Students enrolled to course',
                        'button_yes': 'OK',
                    };
                    callback_success_modal(messageObject);
                    var link = window.location.protocol + "//" + window.location.host + window.location.pathname;
                    window.history.pushState({
                        path: link
                    }, '', link);
                }
                
   </script>


   <div class="modal modal-small fade" id="extend-validity" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-small" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                    <h4 class="modal-title" id="myModalLabel">Change Validity Period</h4>
                </div>
                <div class="modal-body">

                <div>
                    <p id='status_text'></p>
                    <p id="validity_time" style="display:none">Current Validity Date is:</p>
                    <input class="form-control" type="text" placeholder="Select Date" name='extenddate' id="extend_date" readonly style="background-color: #fff;">
                </div>

                </div>

                <div class="modal-footer">
                    <a type="button" class="btn btn-red" data-dismiss="modal">CANCEL</a>
                    <a href="javascript:void(0);" type="button" id="update_extended_validity" class="btn btn-green">UPDATE</a>
                    
                </div>
            </div>
        </div>
    </div>