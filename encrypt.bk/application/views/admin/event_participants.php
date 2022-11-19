<?php include_once 'header.php'; ?>

<?php include_once "event_tab.php"; ?>

<div class="right-wrap base-cont-top container-fluid pull-right">
    <a href="javascript:void(0)" class="btn btn-green btn-big full-width-btn" onclick="addGroups();">Add From Batch</a>
    <a href="javascript:void(0)" class="btn btn-green btn-big full-width-btn" onclick="addStudents();">Add From students</a>
</div>

<section class="content-wrap base-cont-top">
    <?php /* ?><div class="container-fluid nav-content">
        <div class="row">
            <div class="rTable content-nav-tbl" style="">
                <div class="rTableRow">
                    <div class="rTableCell">
                        <div class="input-group">
                            <input type="text" class="form-control srch_txt" id="event_keyword" placeholder="Search User" />
                            <a class="input-group-addon" id="basic-addon2">
                                <i class="icon icon-search"> </i>
                            </a>
                        </div> 
                    </div>

                </div>
            </div>

        </div>
    </div><?php */ ?>

    <div class="left-wrap col-sm-12 pad0">
        <div class="container-fluid">
            <div class="row">   
                <div class="col-sm-12 course-cont-wrap" id="show_message_div"> 
                    <div class="table course-cont only-course rTable" style="" id="event_row_wrapper">
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- JS -->
<?php /* ?><script>
    var __admin_url     = '<?php echo admin_url(); ?>';
    var __image_url     = '<?php echo user_path(); ?>';
    var __default_image = '<?php echo default_user_path(); ?>'
    var __participants  = atob('<?php echo base64_encode(json_encode($participants)) ?>');
    var __groups        = atob('<?php echo base64_encode(json_encode($groups)) ?>');
    var __institutes    = atob('<?php echo base64_encode(json_encode($institutes)) ?>');
    var __event_id      = <?php echo $event['id']; ?>;
</script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/event_participants.js"></script> 
<?php */ ?>
<?php include_once 'footer.php'; ?>

    <div class="modal fade" data-backdrop="static" data-keyboard="false" id="attach_event_users" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-small" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                    <h4 class="modal-title">Add Participants</h4>
                </div>
                <div class="modal-body ">
                    <div class="inside-box pos-rel pad-top50" id="users_new_group_wrapper">
                        <div class="container-fluid nav-content pos-abslt width-100p nav-js-height">
                            <div class="row">
                                <div class="rTable content-nav-tbl normal-tbl" style=""> 
                                    <div class="rTableRow">
                                        <div class="rTableCell" style="width: 30px;">
                                            <a href="javascript:void(0)" class="select-all-style"><label> <input value="1" class="select-users-new-group-parent user-checkbox-parent" type="checkbox">Select All</label></a>
                                        </div>
                                        <div class="rTableCell dropdown">
                                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" id="filter_dropdown_inst">All Students<span class="caret"></span></a>
                                            <ul class="dropdown-menu white" id="inst_ul">
                                                <li><a href="javascript:void(0)" id="" onclick="filter_event_by('all')">All Students</a></li>
                                                <li><a href="javascript:void(0)" id="" onclick="filter_event_by('inactive')">IIT</a></li>
                                                <li><a href="javascript:void(0)" id="" onclick="filter_event_by('active')">IIM</a></li>
                                                <li><a href="javascript:void(0)" id="" onclick="filter_event_by('deleted')">SGlearningapp</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal_users_list" id="" style="padding: 15px">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="javascript:void(0)" onclick="addSelectedParticipants()" type="button" class="btn btn-green">Add Selected</a>
                    <a type="button" class="btn btn-red " data-dismiss="modal" id="addStudentCancelin">CANCEL</a>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" data-backdrop="static" data-keyboard="false" id="attach_event_users_group" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-small" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                    <h4 class="modal-title">Add Participants</h4>
                </div>
                <div class="modal-body ">
                    <div class="inside-box pos-rel pad-top50" id="users_new_group_wrapper_group">
                        <div class="container-fluid nav-content pos-abslt width-100p nav-js-height">
                            <div class="row">
                                <div class="rTable content-nav-tbl normal-tbl" style=""> 
                                    <div class="rTableRow">
                                        <div class="rTableCell" style="width: 30px;">
                                            <a href="javascript:void(0)" class="select-all-style"><label> <input value="1" class="select-users-new-group-parent user-checkbox-parent" type="checkbox">Select All</label></a>     
                                        </div>
                                        <div class="rTableCell dropdown">
                                            <a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" id="filter_dropdown_gp">Select Batch<span class="caret"></span></a>
                                            <ul class="dropdown-menu white" id="gp_ul">
                                                <li><a href="javascript:void(0)" id="" onclick="filter_event_by('all')">All Students</a></li>
                                                <li><a href="javascript:void(0)" id="" onclick="filter_event_by('inactive')">IIT</a></li>
                                                <li><a href="javascript:void(0)" id="" onclick="filter_event_by('active')">IIM</a></li>
                                                <li><a href="javascript:void(0)" id="" onclick="filter_event_by('deleted')">SGlearningapp</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal_users_list" id="" style="padding: 15px">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="javascript:void(0)" onclick="addSelectedParticipants()" type="button" class="btn btn-green">Add Selected</a>
                    <a type="button" class="btn btn-red " data-dismiss="modal" id="addStudentCancelgp">CANCEL</a>
                </div>
            </div>
        </div>
    </div>