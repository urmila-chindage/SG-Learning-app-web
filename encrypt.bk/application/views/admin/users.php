<?php include_once "header.php";?>

<style type="text/css" media="screen">
 .rTableCell label.manage-stud-list{display: flex !important;} 
 .batch-carrot{top: 20px !important;}  
 #user_bulk{ width:96px !important;}
 .tooltip-inner {max-width: unset !important;}
</style>

        <!-- SIDEBAR RIGHT SIDE FIXED --> <!-- START -->
        <div class="right-wrap base-cont-top container-fluid pull-right">
            <div class="row">
            <?php
            if (in_array('2', $user_privilege)):
            ?>
                <div class="col-sm-12">
                    <a href="javascript:void(0);" data-toggle="modal" data-target="#create_user" id="add_new_users" class="btn btn-big btn-light-green txt-left full-width-btn" >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="#fff" height="24" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" width="24" style=" vertical-align: middle;"><line x1="12" x2="12" y1="5" y2="19"></line><line x1="5" x2="19" y1="12" y2="12"></line></svg>
                        <?php echo lang('add_new_user') ?>
                    </a>
                </div>
                <div class="col-sm-12">
                    <a href="javascript:void(0);" data-toggle="modal" data-target="#addusers"  class="btn btn-big btn-blue txt-left full-width-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="#fff" height="24" viewBox="0 0 24 24" width="24" style=" vertical-align: middle; margin-right: 9px;"><path class="heroicon-ui" d="M11 14.59V3a1 1 0 0 1 2 0v11.59l3.3-3.3a1 1 0 0 1 1.4 1.42l-5 5a1 1 0 0 1-1.4 0l-5-5a1 1 0 0 1 1.4-1.42l3.3 3.3zM3 17a1 1 0 0 1 2 0v3h14v-3a1 1 0 0 1 2 0v3a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-3z"></path></svg>
                        <?php echo lang('bulk_import_users') ?>
                    </a>
                    <!--<input type="file" id="import_user" name="file" />-->
                </div>
                <?php /*
                <div class="col-sm-12">
                    <a href="javascript:void(0);" onclick="exportStudents()" id="user-export" class="btn btn-big btn-blue txt-left full-width-btn">
                        <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="24px" height="24px" style="vertical-align: middle;margin-right: 9px;" viewBox="0 0 24 24" enable-background="new 0 0 24 24" xml:space="preserve"><path fill="#FFFFFF" d="M13,5.406v11.59c0,0.553-0.447,1-1,1s-1-0.447-1-1V5.406l-3.3,3.3C7.278,9.063,6.646,9.01,6.291,8.588C5.973,8.211,5.977,7.658,6.3,7.286l5-5c0.389-0.382,1.011-0.382,1.4,0l5,5c0.362,0.417,0.318,1.049-0.099,1.411c-0.372,0.323-0.925,0.327-1.302,0.009L13,5.406z M3,17c0-0.553,0.448-1,1-1s1,0.447,1,1v3h14v-3c0-0.553,0.447-1,1-1s1,0.447,1,1v3c0,1.104-0.896,2-2,2H5c-1.104,0-2-0.896-2-2V17z"/></svg>
                        EXPORT STUDENTS
                    </a>
                    <!--<input type="file" id="import_user" name="file" />-->
                </div>
                 */ ?>
                <?php
endif;
?>
            </div>
        </div>
        <!-- SIDEBAR RIGHT SIDE FIXED --> <!-- END -->

        <section class="content-wrap base-cont-top">

            <!-- Nav section inside this wrap  --> <!-- START -->
            <!-- =========================== -->

            <div class="container-fluid nav-content nav-course-content">

                <div class="row">
                    <div class="rTable content-nav-tbl" style="">
                        <div class="rTableRow flex-space">

                            <div class="rTableCell">
                                <a href="javascript:void(0)" class="select-all-style"><label> <input class="user-checkbox-parent" type="checkbox"><?php echo lang('select_all') ?></label><span id="selected_user_count"></span></a>
                            </div>

                            <div class="rTableCell dropdown">

                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" id="filter_dropdown_text"> <?php echo lang('active_users') ?> <span class="caret"></span></a>
                                    <ul class="dropdown-menu white">
                                        <li><a href="javascript:void(0)" id="filer_dropdown_list_all" onclick="filter_user_by('all')"><?php echo lang('all_users') ?></a></li>
                                        <!-- <li><a href="javascript:void(0)" id="filer_dropdown_list_not-approved" onclick="filter_user_by('not-approved')"><?php echo 'Waiting Approval'; ?></a></li> -->
                                        <li><a href="javascript:void(0)" id="filer_dropdown_list_inactive" onclick="filter_user_by('inactive')"><?php echo lang('inactive_users') ?></a></li>
                                        <li><a href="javascript:void(0)" id="filer_dropdown_list_active" onclick="filter_user_by('active')"><?php echo lang('active_users') ?></a></li>
                                        <li role="separator" class="divider"></li>
                                        <li><a href="javascript:void(0)" id="filer_dropdown_list_deleted" onclick="filter_user_by('deleted')"><?php echo lang('deleted_users') ?></a></li>
                                    </ul>

                            </div>

                        <?php if( $admin['us_institute_id'] == '0' ): ?>
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

                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" id="filter_batch">All Batches <span class="caret batch-carrot"></span></a>
                                    <ul class="dropdown-menu white inner-scroll" id="batch_filter_list">
                                        <li><a href="javascript:void(0)" id="filter_batch_all" onclick="filter_batch('all')">All Batches </a></li>
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

                            <div class="rTableCell">
                                <div class="input-group">
                                    <input type="text" class="form-control srch_txt" id="user_keyword" placeholder="Search" />
                                    <span id="searchclear">×</span>
                                    <a class="input-group-addon" id="basic-addon2">
                                        <i class="icon icon-search"> </i>
                                    </a>
                                </div>
                            </div>

                            <div class="rTableCell" >
                                <!-- lecture-control start -->
                                <div class="btn-group lecture-control btn-right-align" style="margin-top: 0px; display:none;" id="user_bulk">
                                    <span class="dropdown-tigger" data-toggle="dropdown">
                                        <span class='label-text'>
                                           <?php echo lang('bulk_action') ?>  <!-- <span class="icon icon-down-arrow"></span> -->
                                        </span>
                                        <span class="tilder"></span>
                                    </span>
                                    <ul class="dropdown-menu pull-right" role="menu">
                                        <li>
                                            <a href="javascript:void(0)"  onclick="sendMessageToUser()">Send Message</a>
                                        </li>
                                        
                                        <li>
                                            <a href="javascript:void(0)" onclick="deleteUserBulk()" ><?php echo lang('delete') ?></a>
                                        </li>
                                        
                                        <li>
                                            <a href="javascript:void(0)" onclick="changeUserStatusBulk('<?php echo lang('are_sure') . ' ' . lang('activate_selected_users') . ' ?' ?>', '1')" ><?php echo lang('account_activate') ?></a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0)" onclick="changeUserStatusBulk('<?php echo lang('are_sure') . ' ' . lang('deactivate_selected_users') . ' ?' ?>', '0')" ><?php echo lang('account_deactivate') ?></a>
                                        </li>

                                    </ul>
                                </div>
                                <!-- lecture-control end -->
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <!-- =========================== -->
            <!-- Nav section inside this wrap  --> <!-- END -->


            <!-- LEFT CONTENT --> <!-- STARTS -->
            <!-- ===========================  -->

            <div class="left-wrap col-sm-12 pad0">

                <!-- Content Section --> <!-- START -->
                <!-- =========================== -->
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-12 course-cont-wrap" id="show_message_div">
                            <div>
                                <div class="pull-right">
                                    <h4 class="right-top-header user-count">
                                    
                                    <?php 
                                        $user_html  = '';
                                        if($total_users < 1) {
                                            $user_html = 'No Students';
                                        } else {
                                            $user_html .= ($total_users>1)?$total_users.' Students':$total_users.' Student';    
                                        }
                                        echo $user_html;
                                    ?>
                                    </h4>
                                </div>
                            </div>
                             <div class="table course-cont only-course rTable" style="" id="user_row_wrapper">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <!-- <div class="col-sm-12 text-center">
                            <a id="loadmorebutton" < ?php echo ((!$show_load_button)?'style="display:none;"':'') ?> class="btn btn-green selected" onclick="loadMoreUsers()">Load More < ?php echo $remaining_user ?></a>
                        </div> -->
                        <div id="pagination_wrapper">
                        </div>
                    </div>
                </div>
                <!-- =========================== -->
                <!-- Content Section --> <!-- END -->




            </div>
            <!-- ==========================  -->
            <!--  LEFT CONTENT--> <!-- ENDS -->


        </section>

        <?php include_once 'footer.php';?>
<!-- Basic All Javascript -->
    <script src="<?php echo assets_url() ?>js/user.js"></script>
    <!-- initialising the tag plugin using tokenize  -->
    <script type="text/javascript" src="<?php echo assets_url() ?>js/multi-select/jquery.tokenize.js"></script>
    <!-- END -->
    <!-- ADDING REDACTOR PLUGIN INSIDE -->
    <!-- ############################# --> <!-- START -->
        <!-- JS -->
        <script src="<?php echo assets_url() ?>js/redactor/js/redactor.js"></script>
        <script src="<?php echo assets_url() ?>js/redactor/js/table.js"></script>
        <script src="<?php echo assets_url() ?>js/redactor/js/alignment.js"></script>
        <script>
            var __limit  = <?php echo $limit; ?>;
            var __users  = atob('<?php echo base64_encode(json_encode($users)); ?>');
            var __offset = Number(<?php echo isset($_GET['offset']) ? $_GET['offset'] : '1' ?>);
            var __totalUsers    = <?php echo $total_users; ?>;
            var __shownUsers    = <?php echo sizeof($users); ?>;
            var user_permissions = '<?php echo json_encode($user_privilege); ?>';
            var __user_privilege = new User(user_permissions);
            var batchEnroll_permissions = '<?php echo json_encode($batch_enroll_privilege); ?>';
            let __batchEnrollPrivilege = new User(batchEnroll_permissions);
            var studentEnroll_permissions = '<?php echo json_encode($student_enroll_privilege); ?>';
            let __studentEnrollPrivilege = new User(studentEnroll_permissions);
            $(function()
            {
                startTextToolbar();
                renderPagination(__offset, __totalUsers);
            });
            
            function startTextToolbar()
            {
                $R("#redactor_invite", {
                                placeholder: "Type here...",
                                source: !1,
                                style: !1,
                                linkTarget: "_blank",
                                height: "100%",
                                callbacks: {
                                    drop: function(e) {
                                        return !1
                                    },
                                    inserted: function(e) {
                                        for (var t = 0; t < e.length; t++) {
                                            var r = e[t];
                                            "FIGURE" === r.nodeName && r.parentNode.removeChild(r)
                                        }
                                    }
                                }
                            })
                $('#redactor').redactor({
                    imageUpload: admin_url+'configuration/redactore_image_upload',
                    source: false,
                    plugins: ['table', 'alignment'],
                    callbacks: {
                        imageUploadError: function(json, xhr)
                        {
                             alert('Please select a valid image');
                             return false;
                        }
                    }
                });
            }
            var __enrolStudentInProgress = false;
            
        </script>
    <!-- ############################# -->
<!-- END -->