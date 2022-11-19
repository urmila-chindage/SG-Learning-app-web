<?php include_once "header.php"; ?> 

<section class="courses-tab base-cont-top"> 
    <ol class="nav nav-tabs offa-tab">
        <li class="active">
            <a href="<?php echo admin_url('grade'); ?>">Grades</a>
            <span class="active-arrow" style="background: rgb(255, 255, 255);"></span>
        </li>
    </ol>
</section>
<!-- SIDEBAR RIGHT SIDE FIXED --> <!-- START -->
<div class="right-wrap base-cont-top container-fluid pull-right">

   

</div>
<!-- SIDEBAR RIGHT SIDE FIXED --> <!-- END -->



<section class="content-wrap base-cont-top">
    <!-- LEFT CONTENT --> <!-- STARTS -->
    <!-- ===========================  -->

    <div class="left-wrap col-sm-12 pad0">

        <!-- Content Section --> <!-- START -->
        <!-- =========================== -->
        <div class="container-fluid">
            <div class="row">   
                <div class="col-sm-12 course-cont-wrap" id="show_message_div"> 
                    <div class="table course-cont only-course rTable" style="" id="event_row_wrapper">
                        
                        <div class="rTableRow" id="notification_row_1" data-name="Test">
                            <div class="rTableCell"> 
                                <input type="checkbox" class="notification-checkbox" value="1" id="notification_details_1"> 
                                <span class="icon-wrap-round">
                                    <small class="icon-custom">T</small>
                                </span>                                        
                                <span class="wrap-mail ellipsis-hidden"> 
                                    <div class="ellipsis-style">
                                        <a href="http://sdpk.ofabee.com/index.php/admin/notification/basics/1/">Test</a> <br>
                                    </div>
                                </span>
                            </div>
                            <div class="rTableCell pad0">
                                <div class="col-sm-12 pad0">
                                    <label class="pull-right label label-warning" id="action_class_1">Inactive</label>
                                </div>
                                <div class="col-sm-12 pad0 pad-vert5 pos-inhrt">   
                                    <span class="pull-right spn-inactive" id="label_class_1"> Updated by- SDPK Admin on 10 Aug 2017</span>
                                </div>
                            </div>
                            <div class="td-dropdown rTableCell">
                                <div class="btn-group lecture-control">
                                    <span class="dropdown-tigger" data-toggle="dropdown">
                                        <span class="label-text">
                                            <i class="icon icon-down-arrow"></i>
                                        </span>
                                        <span class="tilder"></span>
                                    </span>
                                    <ul class="dropdown-menu pull-right" role="menu" id="notification_action_1">
                                            <li id="status_btn_1"></li>
                                            <li>
                                                <a href="http://sdpk.ofabee.com/index.php/admin/notification/basics/1/" id="delete_btn_1">Settings</a>
                                            </li>
                                            <li>
                                                <a href="javascript:void(0);" id="delete_btn_1" data-toggle="modal" onclick="deleteNotification('1', 'QXJlIHlvdSBzdXJlIHRvICBEZWxldGUgTm90aWZpY2F0aW9uIC0gVGVzdCA/')" data-target="#activate_notification">Delete Notification</a>
                                            </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>
    <!-- ==========================  -->
    <!--  LEFT CONTENT--> <!-- ENDS -->


</section>

<script type="text/javascript">
    var __grades            = atob('<?php echo base64_encode(json_encode($grades)) ?>');
    var __actions           = atob('<?php echo base64_encode(json_encode($actions)) ?>');
    __actions               = jQuery.parseJSON(__actions);
    var __admin_url         = '<?php echo admin_url(); ?>';
</script>

<!-- Basic All Javascript -->
<script src="<?php echo assets_url() ?>js/grade.js"></script>
<!-- END -->



<?php include_once 'footer.php'; ?>


<!-- !.Modal pop up contents :: Delete Section popup-->