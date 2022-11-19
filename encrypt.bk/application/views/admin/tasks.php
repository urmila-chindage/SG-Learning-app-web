<?php include_once "header.php";?>
<link rel="stylesheet" href="<?php echo assets_url() ?>css/datepicker.min.css">

<style type="text/css" media="screen">
 .rTableCell label.manage-stud-list{display: flex !important;} 
 .batch-carrot{top: 20px !important;}  
 #task_bulk{ width:96px !important;}
 .tooltip-inner {max-width: unset !important;}

 .selectize-dropdown-content .label {
    color: #5d5d5d;
}

</style>

<script>
var faculties       = '<?php echo $faculties; ?>';
var __assignee      = JSON.parse('<?php echo $faculties;?>');
var __userpath      = '<?php echo user_path();?>';
var __lastTaskId    = '<?php echo $lastTaskId; ?>';
</script>

       
        <!-- SIDEBAR RIGHT SIDE FIXED --> <!-- END -->

        <section class="content-wrap base-cont-top">

            <!-- Nav section inside this wrap  --> <!-- START -->
            <!-- =========================== -->

            <div class="container-fluid nav-content nav-course-content">

                <div class="row">
                    <div class="rTable content-nav-tbl" style="">
                        <div class="rTableRow flex-space">

                            <!-- <div class="rTableCell"> -->
                                <!--<a href="javascript:void(0)" class="select-all-style"><label> <input class="user-checkbox-parent" type="checkbox"><?php //echo lang('select_all') ?></label><span id="selected_task_count"></span></a>-->
                            <!-- </div> -->

                            <div class="rTableCell dropdown">

                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" id="filter_dropdown_text"> <?php echo 'All Tasks' ?> <span class="caret"></span></a>
                                    <ul class="dropdown-menu white">
                                        <li><a href="javascript:void(0)" id="filer_dropdown_list_all" onclick="filter_task_by('all')"><?php echo lang('all_tasks') ?></a></li>
                                        <li><a href="javascript:void(0)" id="filer_dropdown_list_new" onclick="filter_task_by('new')"><?php echo 'New'; ?></a></li>
                                        <li><a href="javascript:void(0)" id="filer_dropdown_list_pending" onclick="filter_task_by('pending')"><?php echo 'Pending'; ?></a></li>
                                        <li><a href="javascript:void(0)" id="filer_dropdown_list_progress" onclick="filter_task_by('progress')"><?php echo 'On Progress';?></a></li>
                                        <li><a href="javascript:void(0)" id="filer_dropdown_list_completed" onclick="filter_task_by('completed')"><?php echo 'Completed';?></a></li>
                                        <li role="separator" class="divider"></li>
                                        <li><a href="javascript:void(0)" id="filer_dropdown_list_deleted" onclick="filter_task_by('deleted')"><?php echo lang('deleted_tasks') ?></a></li>
                                    </ul>

                            </div>

                            <div class="rTableCell dropdown">

                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" id="priority_dropdown_text"> All priority<span class="caret"></span></a>
                                    <ul class="dropdown-menu white">
                                        <li><a href="javascript:void(0)" id="priority_dropdown_list_all" onclick="priority_task_by('all')"><?php echo 'All'; ?></a></li>
                                        <li><a href="javascript:void(0)" id="priority_dropdown_list_none" onclick="priority_task_by('none')"><?php echo 'none'; ?></a></li>
                                        <li><a href="javascript:void(0)" id="priority_dropdown_list_urgent" onclick="priority_task_by('urgent')"><?php echo 'Urgent'; ?></a></li>
                                        <li><a href="javascript:void(0)" id="priority_dropdown_list_high" onclick="priority_task_by('high')"><?php echo 'High'; ?></a></li>
                                        <li><a href="javascript:void(0)" id="priority_dropdown_list_normal" onclick="priority_task_by('normal')"><?php echo 'Normal';?></a></li>
                                        <li><a href="javascript:void(0)" id="priority_dropdown_list_low" onclick="priority_task_by('low')"><?php echo 'Low'; ?></a></li>
                                    </ul>

                            </div>
                            
                            <div class="rTableCell">
                                <div class="input-group">
                                    <input type="text" class="form-control srch_txt" id="task_keyword" placeholder="Search" />
                                    <span id="searchclear">×</span>
                                    <a class="input-group-addon" id="basic-addon2">
                                        <i class="icon icon-search"> </i>
                                    </a>
                                </div>
                            </div>

                            <div class="rTableCell">
                                <div class="col-sm-6">
                                    <a style="margin-top: 4px;" href="javascript:void(0);" data-toggle="modal" data-target="#create_tasks" id="add_new_tasks" class="btn btn-xs btn-light-green selected">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="#fff" height="24" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" width="24" style=" vertical-align: middle;"><line x1="12" x2="12" y1="5" y2="19"></line><line x1="5" x2="19" y1="12" y2="12"></line></svg>
                                        NEW TASK </a>
                                </div>
                            </div>

                            <div class="rTableCell" >
                                <!-- lecture-control start -->
                                <div class="btn-group lecture-control btn-right-align" style="margin-top: 0px; display:none;" id="task_bulk">
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
                                            <a href="javascript:void(0)" onclick="deleteTaskBulk()" ><?php echo lang('delete') ?></a>
                                        </li>
                                        
                                        <li>
                                            <a href="javascript:void(0)" onclick="changeUserStatusBulk('<?php echo lang('are_sure') . ' ' . lang('activate_selected_tasks') . ' ?' ?>', '1')" ><?php echo lang('account_activate') ?></a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0)" onclick="changeUserStatusBulk('<?php echo lang('are_sure') . ' ' . lang('deactivate_selected_tasks') . ' ?' ?>', '0')" ><?php echo lang('account_deactivate') ?></a>
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

            <div style="width: 126%;" class="left-wrap col-sm-12 pad0">

                <!-- Content Section --> <!-- START -->
                <!-- =========================== -->
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-12 course-cont-wrap" id="show_message_div">
                            <div>
                                <div class="pull-right">
                                    <h4 class="right-top-header user-count">
                                    
                                    <?php 
                                        $task_html  = '';
                                        if($total_tasks < 1) {
                                            $task_html = 'No Tasks';
                                        } else {
                                            $task_html .= ($total_tasks>1)?$total_tasks.' Tasks':$total_tasks.' Student';    
                                        }
                                        echo $task_html;
                                    ?>
                                    </h4>
                                </div>
                            </div>
                             <div class="table course-cont only-course rTable" style="" id="task_row_wrapper">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <!-- <div class="col-sm-12 text-center">
                            <a id="loadmorebutton" < ?php echo ((!$show_load_button)?'style="display:none;"':'') ?> class="btn btn-green selected" onclick="loadMoreUsers()">Load More < ?php echo $remaining_task ?></a>
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
    <script src="<?php echo assets_url() ?>js/tasks.js"></script>
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
            var __tasks  = atob('<?php echo base64_encode(json_encode($tasks)); ?>');
            var __offset = Number(<?php echo isset($_GET['offset']) ? $_GET['offset'] : '1' ?>);
            var __totalTasks    = <?php echo $total_tasks; ?>;
            var __shownUsers    = <?php echo sizeof($tasks); ?>;
            var task_permissions = '<?php echo json_encode($task_privilege); ?>';
            var __task_privilege = new User(task_permissions);

            $(function()
            {
                startTextToolbar();
                renderPagination(__offset, __totalTasks);
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
<script type="text/javascript" src="<?php echo assets_url() ?>js/datepicker.js"></script>
<script src="<?php echo assets_url() ?>js/datepicker.en.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo assets_url();?>css/toastr.min.css">
<script type="text/javascript" src="<?php echo assets_url();?>js/toastr.min.js"></script>
<script>

$( function() {
    $( ".hasDatepicker" ).datepicker({
        language: 'en',
        minDate: new Date(),
        dateFormat: 'yyyy-mm-dd',
        autoClose: true
    });
});


$(document).on("click", '#add_new_tasks', function(){
    $('#task_tittle').val('');
    $('#task_description').val('');
    $('#task_due_date').val('');
    $('#task_priority').val('');
});

toastr.options = {
    "closeButton": true,
    "debug": false,
    "newestOnTop": false,
    "progressBar": false,
    "positionClass": "toast-bottom-right",
    "preventDuplicates": false,
    "onclick": null,
    "showDuration": "300",
    "hideDuration": "1000",
    "timeOut": "5000",
    "extendedTimeOut": "1000",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
    }

</script>