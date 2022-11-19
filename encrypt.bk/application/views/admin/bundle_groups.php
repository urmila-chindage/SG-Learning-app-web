<?php include_once 'bundle_header.php';?>



<section class="content-wrap cont-course-big nav-included content-wrap-align content-wrap-top">

    <!-- Nav section inside this wrap  --> <!-- START -->
    <!-- =========================== -->

    <div class="container-fluid nav-content nav-js-height content-filter-top content-filter-fullwidth">

        <div class="row">
            <div class="rTable content-nav-tbl borderleft-none" style="">
                <div class="rTableRow">

                    <div class="rTableCell selectall-width borderleft-none">
                        <a href="#!." class="select-all-style"><label> <input class="group-checkbox-parent" type="checkbox">  <?php echo lang('select_all') ?></label><span id="selected_group_count"></span></a>

                    </div>

                    <?php if (!empty($institutes)): ?>
                    <div class="rTableCell dropdown">

                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" id="filter_institute">All Institutes <span class="caret"></span></a>
                            <ul class="dropdown-menu white inner-scroll">
                                <li><a href="javascript:void(0)" id="filter_institute_all" onclick="filter_institute('all')">All Institutes </a></li>

                                <?php foreach ($institutes as $institute): ?>
                                <li><a href="javascript:void(0)" id="filter_institute_<?php echo $institute['id'] ?>" onclick="filter_institute(<?php echo $institute['id'] ?>)"><?php echo $institute['ib_institute_code'] . ' - ' . $institute['ib_name'] ?></a></li>
                                <?php endforeach;?>

                            </ul>

                    </div>
                    <?php endif;?>

                    <div class="rTableCell">
                        <div class="input-group">
                            <input type="text" class="form-control srch_txt" placeholder="Search Batch" id="group_keyword" />
                            <span id="searchclear">×</span>
                            <a class="input-group-addon" id="search_group">
                                <i class="icon icon-search"> </i>
                            </a>
                        </div>
                    </div>
                    <div class="rTableCell" >
                        <!-- lecture-control start -->
                        <div class="btn-group lecture-control btn-right-align" id="group_bulk" style="margin-top: 0px; display: none;">
                            <span class="dropdown-tigger" data-toggle="dropdown">
                                <span class='label-text'>
                                   <?php echo lang('bulk_action') ?>  <!-- <span class="icon icon-down-arrow"></span> -->
                                </span>
                                <span class="tilder"></span>
                            </span>
                            <ul class="dropdown-menu pull-right" role="menu">
                            <?php
                            if (!empty($this->batch_enroll_privilege)) {
                                if (in_array($this->privilege['view'], $this->batch_enroll_privilege)) {
                                    ?>
                                <li>
                                    <a href="javascript:void(0)" onclick="sendMessageToGroups()"><?php echo lang('send_message') ?></a>
                                </li>
                                <?php
                                }
                            }
                            ?>
                            <?php
                            if (!empty($this->batch_enroll_privilege)) {
                                if (in_array($this->privilege['edit'], $this->batch_enroll_privilege)) {
                                    ?>
                                <li>
                                    <a href="javascript:void(0)" onclick="setAsCompleteGroup()"><?php echo lang('set_as_complete') ?></a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)" onclick="resetResultGroup()"><?php echo lang('reset_result') ?></a>
                                </li>
                                <!-- <li>
                                    <a href="javascript:void(0);" onclick="changeSubscriptionStatusGroupBulk('0')" >Suspend All Students</a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);" onclick="changeSubscriptionStatusGroupBulk('1')" >Approve All Students</a>
                                </li> -->
                                <?php
                            }
                            }
                            ?>

                            <?php
                            if (!empty($this->batch_enroll_privilege)) {
                                if (in_array($this->privilege['delete'], $this->batch_enroll_privilege)) {
                                    ?>
                                <li>
                                    <a href="javascript:void(0)" onclick="removeUserFromCourseGroup()"><?php echo lang('remove_from_course') ?></a>
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
if (!empty($this->batch_enroll_privilege)) {
    if (in_array($this->privilege['add'], $this->batch_enroll_privilege)) {
        ?>
                    <!-- enroll button -->
                    <div class="rTableCell">
                        <div class="col-sm-12">
                            <a href="<?php echo admin_url('course/enroll_groups/' . $course['id']) ?>" class="btn btn-violet">ENROLL BATCH<ripples></ripples></a>
                        </div>
                    </div>
                    <?php
}
}

?>
                    <!-- enroll button ends -->
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
                <div>
                    <div class="pull-right">
                        <h4 class="right-top-header user-count">
                        <?php
$user_html = '';
$user_html .= sizeof($course_groups) . ' / ' . $total_groups;
$user_html .= ($total_groups > 1) ? ' Batches' : ' Batch';
echo $user_html;
$remaining_groups = $total_groups - sizeof($course_groups);
$remaining_groups = ($remaining_groups > 0) ? '(' . $remaining_groups . ')' : '';
?>
                        </h4>
                    </div>
                </div>
                <div class="table course-cont rTable" style="" id="group_row_wrapper">
                    <?php if (!empty($course_groups)): ?>
                        <?php foreach ($course_groups as $group): ?>
                    <div class="rTableRow user-listing-row" id="group_row_<?php echo $group['id'] ?>" data-name="<?php echo $group['gp_name'] ?>">
                        <div class="rTableCell" style="padding-left:0px">
                            <label class="pointer-cursor">
                                <input type="checkbox" class="group-checkbox" value="<?php echo $group['id'] ?>" id="group_details_<?php echo $group['id'] ?>">
                                <span class="blue"></span>
                               <svg style="vertical-align: middle; margin: 0px 10px" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Layer_1" x="0px" y="0px" width="26px" height="18px" viewBox="0 0 26 18" enable-background="new 0 0 26 18" fill="#64277d" xml:space="preserve">
<g>
    <path d="M15.73,4.37h-0.5c0.005,0.626-0.278,1.617-0.73,2.384c-0.225,0.386-0.489,0.718-0.755,0.936   C13.477,7.911,13.228,8.01,13,8.01c-0.227,0-0.476-0.099-0.744-0.321c-0.4-0.328-0.791-0.917-1.061-1.55   c-0.273-0.63-0.428-1.311-0.425-1.779h-0.5l0.5,0.02c0.048-1.204,1.036-2.142,2.229-2.142L13.09,2.24h0h0   c1.16,0.048,2.093,0.981,2.142,2.142l0.5-0.021h-0.5v0.01H15.73h0.5V4.36V4.349V4.338c-0.072-1.68-1.419-3.027-3.1-3.098h0h0   l-0.131-0.003c-1.727,0-3.159,1.36-3.228,3.102l0,0.01v0.01c0.005,0.884,0.332,1.965,0.867,2.893   c0.27,0.462,0.592,0.883,0.98,1.206C12.002,8.779,12.472,9.009,13,9.01c0.527,0,0.997-0.229,1.382-0.549   c0.581-0.483,1.023-1.184,1.342-1.919c0.315-0.738,0.505-1.506,0.507-2.171H15.73z"/>
    <path d="M7.87,16.2c0-1.419,0.573-2.698,1.502-3.628c0.93-0.929,2.209-1.502,3.627-1.502c1.419,0,2.698,0.573,3.628,1.502   c0.929,0.93,1.502,2.209,1.502,3.628h1c0-3.387-2.743-6.13-6.13-6.13c-3.386,0-6.13,2.743-6.13,6.13H7.87z"/>
    <path d="M22.55,6.18h-0.5c0.005,0.512-0.231,1.36-0.609,2.012c-0.187,0.328-0.407,0.609-0.622,0.79   C20.6,9.166,20.406,9.241,20.24,9.24c-0.165,0-0.358-0.075-0.577-0.26c-0.326-0.273-0.654-0.777-0.879-1.315   C18.556,7.13,18.428,6.554,18.43,6.18c0.001-1.005,0.805-1.809,1.811-1.81c1.006,0,1.817,0.808,1.819,1.81h0.5v-0.5h-0.01h-0.5v0.5   H22.55v0.5h0.01h0.5v-0.5c0-1.558-1.267-2.807-2.819-2.81c-0.775,0-1.481,0.313-1.989,0.821C17.743,4.698,17.43,5.405,17.43,6.18   c0.006,0.759,0.284,1.689,0.741,2.5c0.23,0.403,0.507,0.774,0.844,1.062c0.335,0.285,0.751,0.497,1.226,0.498   c0.474-0.001,0.889-0.211,1.224-0.495c0.504-0.43,0.881-1.042,1.153-1.682c0.269-0.642,0.431-1.306,0.433-1.883H22.55v0.5V6.18z"/>
    <path d="M17.662,12.858c0.771-0.584,1.672-0.863,2.57-0.864c1.294,0.001,2.569,0.583,3.41,1.688l-0.002-0.002l-0.001-0.002   c0.556,0.745,0.86,1.65,0.86,2.581h1c0-1.149-0.375-2.264-1.06-3.179l-0.001-0.002l-0.002-0.002   c-1.036-1.362-2.613-2.083-4.205-2.083c-1.106,0-2.225,0.349-3.174,1.067L17.662,12.858z"/>
    <path d="M3.45,6.18h-0.5c0.005,0.768,0.284,1.7,0.741,2.508C3.922,9.09,4.199,9.459,4.536,9.746   c0.335,0.284,0.75,0.494,1.224,0.495c0.475-0.001,0.891-0.213,1.225-0.499c0.504-0.433,0.88-1.047,1.152-1.687   C8.406,7.412,8.567,6.751,8.57,6.18c0-0.775-0.313-1.482-0.821-1.989C7.242,3.684,6.535,3.37,5.76,3.37   C4.207,3.373,2.94,4.622,2.94,6.18v0.5h0.51V6.18h-0.5H3.45v-0.5H3.44v0.5h0.5c0.002-1.002,0.813-1.81,1.82-1.81   c1.006,0.001,1.809,0.804,1.81,1.81C7.575,6.681,7.339,7.531,6.96,8.185C6.773,8.514,6.553,8.798,6.337,8.98   C6.118,9.166,5.925,9.241,5.76,9.24c-0.167,0-0.36-0.074-0.578-0.258C4.856,8.711,4.528,8.211,4.304,7.674   C4.076,7.14,3.948,6.563,3.95,6.18v-0.5h-0.5V6.18z"/>
    <path d="M8.942,12.062c-0.949-0.719-2.068-1.067-3.174-1.067c-1.592,0-3.17,0.721-4.206,2.083l-0.001,0.002L1.56,13.081   C0.875,13.996,0.5,15.11,0.5,16.26h1c0-0.931,0.304-1.836,0.86-2.581l-0.001,0.002l-0.001,0.002   c0.841-1.105,2.116-1.688,3.41-1.688c0.898,0.001,1.799,0.28,2.57,0.864L8.942,12.062z"/>
</g>
</svg>
                                <!-- <span class="icon-wrap-round blue">
                                    <i class="icon icon-users"></i>
                                </span> -->

                                <span class="normal-base-color">
                                    <span><?php echo $group['batch_name'] ?> </span>

                                </span>
                            </label>
                            <span class="pull-right groups-student-count-holder">
                                <span class="label-active group-total"><?php echo $group['group_strength'] ?></span> <?php echo lang('users') ?>
                            </span>
                        </div>
                        <?php
$view   = false;
$edit   = false;
$delete = false;

if (!empty($this->batch_enroll_privilege)) {
    if (in_array($this->privilege['view'], $this->batch_enroll_privilege)) {

        if ($group['group_strength'] > 0) {
            $view = true;
        }
    }
    if (in_array($this->privilege['edit'], $this->batch_enroll_privilege)) {
        if ($group['group_strength'] > 0) {
            $edit = true;
        }
    }
    if (in_array($this->privilege['delete'], $this->batch_enroll_privilege)) {
        $delete = true;
    }

}

if ($view == true || $edit == true || $delete == true) {
    ?>
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
if ($view == true) {
        ?>

                                    <li>
                                        <a href="javascript:void(0)" id="invite-group" onclick="sendMessageToGroups('<?php echo $group['id']; ?>')" data-groupname="<?php echo $group['gp_name']; ?>" ><?php echo lang('send_message') ?></a>
                                    </li>
                                <?php
}
    ?>
                                <?php
if ($edit == true) {
        ?>
                                    <li>
                                        <a href="javascript:void(0)" onclick="setAsCompleteGroup('<?php echo $group['id'] ?>','<?php echo $group['gp_name'] ?>')"><?php echo lang('set_as_complete') ?></a>
                                    </li>
                                <?php
}
    ?>
                                <?php
if ($edit == true) {
        ?>
                                    <li>
                                        <a href="javascript:void(0)" onclick="resetResultGroup('<?php echo $group['id'] ?>','<?php echo $group['gp_name'] ?>')"><?php echo lang('reset_result') ?></a>
                                    </li>
                                    <?php
}
    ?>
                                <?php
if ($edit == true) {
        ?>
                                    <li>
                                        <a href="javascript:void(0);" onclick="changeSubscriptionStatusGroup('<?php echo $group['id'] ?>', '0','<?php echo $group['gp_name'] ?>')" >Suspend All Students</a>
                                    </li>
                                <?php
}
    ?>
                                <?php
if ($edit == true) {
        ?>
                                    <li>
                                        <a href="javascript:void(0);" onclick="changeSubscriptionStatusGroup('<?php echo $group['id'] ?>', '1','<?php echo $group['gp_name'] ?>')" >Approve All Students</a>
                                    </li>
                                <?php
}
    ?>
                                <?php
if ($delete == true) {
        ?>
                                    <li>
                                        <a href="javascript:void(0)" onclick="removeUserFromCourseGroup('<?php echo $group['id'] ?>','<?php echo $group['gp_name'] ?>')"><?php echo lang('remove_from_course') ?></a>
                                    </li>
                                <?php
}
    ?>
                                </ul>
                            </div>
                        </div>

                    <?php
}
?>
                    </div>
                    <?php endforeach;?>
                <?php else: ?>
                    <div id="popUpMessage" class="alert alert-danger">       No Batches found.</div>
                <?php endif;?>
                </div>
            </div>

        </div>
        <div class="row">
            <div class="col-sm-12 text-center">
                <a id="loadmorebutton" <?php echo ((!$show_load_button) ? 'style="display:none;"' : '') ?> class="btn btn-green selected" onclick="loadMoreGroups()">Load More <?php echo $remaining_groups ?></a>
            </div>
        </div>
        <!-- =========================== -->
        <!-- Content Section --> <!-- END -->

    </div>
    <!-- ==========================  -->
    <!--  LEFT CONTENT--> <!-- ENDS -->
</section>

    <!-- Enroll Batch modal starts -->
        <div class="modal enroll-modal fade" id="enroll-batch" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="z-index: 9999">
                <div class="modal-dialog modal-sm">
                  <div class="modal-content custom-width">
                    <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="enroll-filter-wrap white-bg cont-course-big nav-included container-fluid pull-right pad0">

                            <!-- Nav section inside this wrap  --> <!-- START -->
                            <!-- =========================== -->

                            <div class="container-fluid nav-content align-filter-col">

                                <div class="row">
                                    <div class="rTable content-nav-tbl" style="">
                                        <div class="rTableRow">
                                            <div class="rTableCell selectall-width">
                                                <a href="javascript:void(0)" class="select-all-style"><label> <input class="not-added-group-checkbox-parent" type="checkbox">  Select All</label></a>
                                            </div>
                                            <div class="rTableCell width-init  searchbar-width">
                                                <div class="input-group">
                                                    <input type="text" class="form-control srch_txt" placeholder="Search Batch" id="not_add_group_keyword" />
                                                    <a class="input-group-addon" id="search_not_add_groups">
                                                        <i class="icon icon-search"> </i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <!-- =========================== -->
                            <!-- Nav section inside this wrap  --> <!-- END -->

                            <!-- Content Section --> <!-- START -->
                            <!-- =========================== -->
                            <div class="container-fluid">

                                <div class="row">
                                    <div class="col-sm-12 course-cont-wrap">
                                        <div class="table course-cont filter-content-align rTable" style="" id="not_added_groups_wrapper">
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <!-- =========================== -->
                            <!-- Content Section --> <!-- END -->

                            <div class="container-fluid right-bottom-fixed align-bottom">
                                <div class="row">
                                    <div class="col-sm-12 bg-grey">
                                        <label style="display:inline-block;vertical-align: middle;cursor: pointer;">
                                            <input type="checkbox" value="1" id="mail_after_group_subscription" />
                                            Send Email Notification
                                        </label>
                                    </div>
                                    <div class="col-sm-12 button-padR cr-grp-btn">
                                        <a href="javascript:void(0)" onclick="createGroup()"  class="btn btn-blue">Create Batch</a>
                                        <div class="btn btn-light-green" style="float:right" onclick="addGroupToCourse()">Add To Course</div>
                                        <!-- <span class="small-font">or <a href="javascript:void(0)" onclick="location.reload();" class="cancel-group-creation">Cancel</a></span> -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer"></div>
                  </div>
                </div>
        </div>
    <!-- Enroll Batch modal ends -->
    <?php include_once 'training_footer.php';?>
<script type="text/javascript" src="<?php echo assets_url() ?>js/multi-select/jquery.tokenize.js"></script>
    <!-- END -->
    <!-- ADDING REDACTOR PLUGIN INSIDE -->
    <!-- ############################# --> <!-- START -->
        <!-- JS -->
        <script src="<?php echo assets_url() ?>js/redactor/js/redactor.js"></script>
        <script src="<?php echo assets_url() ?>js/redactor/js/table.js"></script>
        <script src="<?php echo assets_url() ?>js/redactor/js/alignment.js"></script>
        <script>
            var batchEnroll_permissions = '<?php echo json_encode($this->batch_enroll_privilege); ?>';
            let __batchEnroll_privilege = new User(batchEnroll_permissions);
            $(function()
            {
                startTextToolbar();
            });

            function startTextToolbar()
            {
                $('#redactor_group_bulk, #redactor_invite, #redactor_group_individual').redactor({
                    imageUpload: admin_url+'configuration/redactore_image_upload',
                    plugins: ['table', 'alignment', 'source'],
                    minHeight: '250px',
                    maxHeight: '250px'
                });
            }
            var __course_title = '<?php echo $title; ?>';
            var __limit         = <?php echo $limit; ?>;
            var __offset        = 2;
            var url = window.location.search;
                url = url.replace("?", ''); // remove the ?
                //console.log(url);
                if(url=='success'){
                    var messageObject = {
                        'body': 'Batches enrolled to Course',
                        'button_yes': 'OK',
                    };
                    callback_success_modal(messageObject);
                    var link = window.location.protocol + "//" + window.location.host + window.location.pathname;
                    window.history.pushState({
                        path: link
                    }, '', link);
                }
                
        </script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/course_groups.js"></script>

