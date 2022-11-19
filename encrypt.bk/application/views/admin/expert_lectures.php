<?php include_once "header.php"; ?> 

<?php include_once "cms_tab.php"; ?>

<!-- SIDEBAR RIGHT SIDE FIXED --> <!-- START -->
<div class="right-wrap base-cont-top container-fluid pull-right">

    <a href="javascript:void(0)" class="btn btn-green btn-big full-width-btn" data-toggle="modal" data-target="#create_expert_lecture" onclick="createExpertLecture('<?php echo lang('create_new_expert_lecture') ?>', '<?php echo lang('expert_lecture_title').'* :' ?>');">
        <?php echo lang('create_new_expert_lecture') ?>
    </a>
</div>
<!-- SIDEBAR RIGHT SIDE FIXED --> <!-- END -->



<section class="content-wrap base-cont-top">

    <!-- Nav section inside this wrap  --> <!-- START -->
    <!-- =========================== -->

    <div class="container-fluid nav-content">

        <div class="row">
            <div class="rTable content-nav-tbl" style="">
                <div class="rTableRow">

                    <div class="rTableCell">
                        <a href="javascript:void(0)" class="select-all-style"><label> <input class="expert-lecture-checkbox-parent" type="checkbox"><?php echo lang('select_all') ?></label><span id="selected_expert_lecture_count"></span></a>
                    </div>

                    <div class="rTableCell dropdown">

                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" id="filter_dropdown_text"> <?php echo lang('all_expert_lectures') ?> <span class="caret"></span></a>
                        <ul class="dropdown-menu white">
                            <li><a href="javascript:void(0)" id="filer_dropdown_list_all" onclick="filter_video_by('all')"><?php echo lang('all_expert_lectures') ?></a></li>
                            <li><a href="javascript:void(0)" id="filer_dropdown_list_inactive" onclick="filter_video_by('inactive')"><?php echo lang('inactive_expert_lectures') ?></a></li>
                            <li><a href="javascript:void(0)" id="filer_dropdown_list_active" onclick="filter_video_by('active')"><?php echo lang('active_expert_lectures') ?></a></li>
                            <li role="separator" class="divider"></li>
                            <li><a href="javascript:void(0)" id="filer_dropdown_list_deleted" onclick="filter_video_by('deleted')"><?php echo lang('deleted_expert_lectures') ?></a></li>
                        </ul>

                    </div>

                    <div class="rTableCell">
                        <div class="input-group">
                            <input type="text" class="form-control srch_txt" id="expert_lecture_keyword" placeholder="<?php echo lang('search_by_name') ?>" />
                            <a class="input-group-addon" id="basic-addon2">
                                <i class="icon icon-search"> </i>
                            </a>
                        </div> 
                    </div>

                    <div class="rTableCell" >
                        <!-- lecture-control start -->
                        <div class="btn-group lecture-control btn-right-align" style="margin-top: 0px; display:none;" id="expert_bulk">
                            <span class="dropdown-tigger" data-toggle="dropdown">
                                <span class='label-text'>
                                    <?php echo lang('bulk_action') ?>  <!-- <span class="icon icon-down-arrow"></span> --> 
                                </span>
                                <span class="tilder"></span>
                            </span>
                            <ul class="dropdown-menu pull-right" role="menu">
                                <li>
                                    <a href="javascript:void(0)" onclick="deleteExpertLectureBulk('<?php echo lang('are_you_sure_to').' '.lang('delete_selected_expert_lectures').' ?' ?>', '1','<?php echo lang('delete') ?>')" data-target="#activate_expert_lecture" ><?php echo lang('delete_expert_lectures') ?></a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)" onclick="changeExpertLectureStatusBulk('<?php echo lang('are_you_sure_to').' '.lang('activate_selected_expert_lectures').' ?' ?>', '1' ,'<?php echo lang('activate') ?>')" ><?php echo lang('expert_lectures_activate') ?></a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)" onclick="changeExpertLectureStatusBulk('<?php echo lang('are_you_sure_to').' '.lang('deactivate_selected_expert_lectures').' ?' ?>', '0', '<?php echo lang('deactivate') ?>')" ><?php echo lang('expert_lectures_deactivate') ?></a>
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
                    <div class="table course-cont only-course rTable" style="" id="expert_lectures_row_wrapper">
                        <?php if (!empty($expert_lectures)): ?>
                            <?php foreach ($expert_lectures as $expert_lecture): ?> 
                        
                            <?php
                            //set the database value
                            $action_label = $expert_lecture['wa_name'];
                            $action = $expert_lecture['wa_name'];
                            $action_date = date("d M Y", strtotime($expert_lecture['updated_date']));
                            $action_author = ($expert_lecture['wa_name_author'] != '') ? $expert_lecture['wa_name_author'] : 'Admin';

                            //consider the record is deleted and set the value if record deleted
                            $label_class = 'spn-delete';
                            $action_class = 'label-danger';
                            $item_deleted   = 'item-deleted';
                            $item_inactive  = '' ;
                            //case if record is not deleted
                            if ($expert_lecture['el_deleted'] == 0) {
                                $item_deleted = '';
                                if ($expert_lecture['action_id'] == 1) {
                                    $action_class = 'label-warning';
                                    $action_date = date("d M Y", strtotime($expert_lecture['created_date']));
                                    $label_class = 'spn-inactive';
                                    $item_inactive    = 'item_inactive';
                                } else {
                                    if ($expert_lecture['el_status'] == 1) {
                                        $action_class = 'label-success';
                                        $label_class = 'spn-active';
                                        $action = lang('active');
                                    } else {
                                        $action_class = 'label-warning';
                                        $label_class = 'spn-inactive';
                                        $item_inactive  = 'item_inactive';
                                        $action = lang('inactive');
                                    }
                                }
                            }
                            ?>
                                <div class="rTableRow" id="expert_lecture_row_<?php echo $expert_lecture['id'] ?>" data-name="<?php echo $expert_lecture['el_title'] ?>">
                                    <div class="rTableCell"> 
                                        <input type="checkbox" class="expert-lecture-checkbox <?php echo $item_deleted.' '.$item_inactive ?>" value="<?php echo $expert_lecture['id'] ?>" id="expert_lecture_details_<?php echo $expert_lecture['id'] ?>"> 
                                        <span class="icon-wrap-round">
                                            <small class="icon-custom"><?php echo strtoupper(trim(substr($expert_lecture['el_title'], 0, 1))); ?></small>
                                        </span>                                        
                                        <span class="wrap-mail ellipsis-hidden"> 
                                            <div class="ellipsis-style" style="font-size:14px">
                                                <?php if($expert_lecture['el_deleted'] != '1'): ?>
                                                    <a href="<?php echo admin_url('expert_lectures/basics/'.$expert_lecture['id']) ?>"  >
                                                <?php endif; ?>
                                                    <?php echo $expert_lecture['el_title'] ?>
                                                <?php if($expert_lecture['el_deleted'] != '1'): ?>        
                                                </a> <?php endif; ?><br>
                                            </div>
                                        </span>
                                    </div>
                                    
                                    <div class="rTableCell pad0">
                                        <div class="col-sm-12 pad0">
                                            <label class="pull-right label <?php echo $action_class ?>" id="action_class_<?php echo $expert_lecture['id'] ?>">
                                    <?php echo $action ?>
                                            </label>
                                        </div>
                                        <div class="col-sm-12 pad0 pad-vert5 pos-inhrt">   
                                            <span class="pull-right <?php echo $label_class ?>" id="label_class_<?php echo $expert_lecture['id'] ?>"> <?php echo $action_label ?> by- <?php echo $action_author ?> on <?php echo $action_date ?></span>
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
                                            <ul class="dropdown-menu pull-right" role="menu" id="expert_lecture_action_<?php echo $expert_lecture['id'] ?>">
                                                <?php if ($expert_lecture['el_deleted'] == 0): ?>
                                                    <li id="status_btn_<?php echo $expert_lecture['id'] ?>">
                                                    <?php $cb_status = ($expert_lecture['el_status']) ? 'deactivate' : 'activate'; ?>
                                                    <?php $cb_action = $cb_status ?>
                                                        <a href="javascript:void(0);" data-toggle="modal" data-target="#activate_expert_lecture" onclick="changeVideoStatus('<?php echo $expert_lecture['id'] ?>', '<?php echo base64_encode(lang('are_you_sure_to').' '.lang($cb_action) . ' ' . lang('expert_lecture') . ' - ' . $expert_lecture['el_title'].'?') ?>', '<?php echo lang('change_status_message') . ' ' . lang($cb_action) ?>', '<?php echo lang($cb_action) ?>')"><?php echo lang($cb_status) ?></a>
                                                    </li>
                                                    <li>
                                                        <a href="<?php echo admin_url('expert_lectures/basics/'.$expert_lecture['id']) ?>" id="delete_btn_<?php echo $expert_lecture['id'] ?>" ><?php echo lang('settings') ?></a>
                                                    </li>
                                                    <li>
                                                        <a href="javascript:void(0);" id="delete_btn_<?php echo $expert_lecture['id'] ?>" data-toggle="modal" onclick="deleteVideo('<?php echo $expert_lecture['id'] ?>', '<?php echo base64_encode(lang('are_you_sure_to').' '.lang('delete_expert_lecture') . ' - ' . $expert_lecture['el_title'] . ' ?') ?>', '<?php echo lang('delete') ?>')" data-target="#activate_expert_lecture"><?php echo lang('delete') ?></a>
                                                    </li>
                                                <?php else: ?>
                                                    <li>
                                                        <a href="javascript:void(0);" id="restore_btn_<?php echo $expert_lecture['id'] ?>" data-toggle="modal" onclick="restoreVideo('<?php echo $expert_lecture['id'] ?>', '<?php echo base64_encode(lang('are_you_sure_to').' '.lang('restore') . ' - ' . $expert_lecture['el_title'] . ' ?') ?>', '<?php echo lang('restore') ?>')" data-target="#activate_expert_lecture"><?php echo lang('restore') ?></a>
                                                    </li>
                                                <?php endif; ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>    
                                            <?php endforeach; ?>
                                        <?php else: ?>
                            <div class="rTableRow">
                                <div class="rTableCell cours-fix ellipsis-hidden"> 
                                    <div class="ellipsis-style">  
                                        <a href="javascript:void(0)" class="cust-sm-6 padd0"> <?php echo lang('no_expert_lectures_found') ?></a>
                                    </div>
                                </div>
                            </div>    
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <!-- =========================== -->
        <!-- Content Section --> <!-- END -->




    </div>
    <!-- ==========================  -->
    <!--  LEFT CONTENT--> <!-- ENDS -->


</section>

<!-- Basic All Javascript -->
<script src="<?php echo assets_url() ?>js/expert_lecture.js"></script>
<!-- END -->
<?php include_once 'footer.php'; ?>