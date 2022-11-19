<?php include_once "header.php"; ?>  
<?php
$fullwidth_class = 'nopad';
if(in_array($this->__access['add'], $this->__notification_privilege)):
$fullwidth_class = ''; 
?>
<div class="right-wrap base-cont-top container-fluid pull-right">
    <div class="row">
        <div class="col-sm-12">
            <a href="javascript:void(0)" class="btn btn-green btn-big full-width-btn" data-toggle="modal" data-target="#create_notification" onclick="createNotification('<?php echo lang('create_new_notification') ?>', '<?php echo lang('notification_title') ?>*:');">
            <svg xmlns="http://www.w3.org/2000/svg" fill="#fff" height="24" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" width="24" style=" vertical-align: middle;"><line x1="12" x2="12" y1="5" y2="19"></line><line x1="5" x2="19" y1="12" y2="12"></line></svg>
                <?php echo lang('information_bar') ?>
            </a>
            <h5>what's the use ?</h5>
            <p>Information bars that are created here shall be displayed on the home page. For creating the information bar click on the above button, add a title and add the contents. The preview for the same can be seen at the right side of the information bar creation page.</p>
        </div>
    </div>
</div>
<?php endif; ?>

<section class="content-wrap base-cont-top <?php echo $fullwidth_class; ?>">
    <div class="container-fluid nav-content nav-course-content">
        <div class="row">
            <div class="rTable content-nav-tbl" style="">
                <div class="rTableRow">
                    <div class="rTableCell">
                        <a href="javascript:void(0)" class="select-all-style">
                            <label> 
                                <input class="notification-checkbox-parent" type="checkbox"><?php echo lang('select_all') ?>
                            </label>
                            <span id="selected_notification_count"></span>
                        </a>
                    </div>
                    <div class="rTableCell dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" id="filter_dropdown_text"> <?php echo lang('all_notifications') ?> <span class="caret"></span></a>
                        <ul class="dropdown-menu white">
                            <li><a href="javascript:void(0)" id="filer_dropdown_list_all" onclick="filter_notification_by('all')"><?php echo lang('all_notifications') ?></a></li>
                            <li><a href="javascript:void(0)" id="filer_dropdown_list_inactive" onclick="filter_notification_by('inactive')"><?php echo lang('inactive_notifications') ?></a></li>
                            <li><a href="javascript:void(0)" id="filer_dropdown_list_active" onclick="filter_notification_by('active')"><?php echo lang('active_notifications') ?></a></li>
                            <li><a href="javascript:void(0)" id="filer_dropdown_list_expired" onclick="filter_notification_by('expired')"><?php echo lang('expired_notifications') ?></a></li>
                        </ul>
                    </div>
                    <div class="rTableCell">
                        <div class="input-group">
                            <input type="text" class="form-control srch_txt" id="notification_keyword" placeholder="<?php echo lang('search_by_name') ?>" />
                            <span id="searchclear">&times;</span>
                            <a class="input-group-addon" id="basic-addon2">
                                <i class="icon icon-search"> </i>
                            </a>
                        </div> 
                    </div>
                    <div class="rTableCell" >
                        <div class="btn-group lecture-control btn-right-align" style="margin-top: 0px; display:none;" id="notification_bulk">
                            <span class="dropdown-tigger" data-toggle="dropdown">
                                <span class='label-text'>
                                    <?php echo lang('bulk_action') ?>
                                </span>
                                <span class="tilder"></span>
                            </span>
                            <ul class="dropdown-menu pull-right" role="menu">
                                <li>
                                    <a href="javascript:void(0)" onclick="deleteNotificationBulk('<?php echo lang('delete_selected_notifications') ?>', '1')"><?php echo lang('delete_notifications') ?></a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)" onclick="changeNotificationStatusBulk('<?php echo lang('activate_selected_notifications') ?>', '1')" ><?php echo lang('notification_activate') ?></a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)" onclick="changeNotificationStatusBulk('<?php echo lang('deactivate_selected_notifications') ?>', '0')" ><?php echo lang('notification_deactivate') ?></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="left-wrap pad0">
        <div class="container-fluid">
            <div class="row">   
                <div class="col-sm-12 course-cont-wrap" id="show_message_div" style="margin-bottom:60px;"> 
                    <div class="pull-right">
                       <h4 class="right-top-headedfgr course-count"><span id="total-notifications"></span></h4>
                    </div>
                    <div class="table course-cont only-course rTable ui-sortable" style="" id="notification_row_wrapper">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div id="pagination_wrapper">
        </div>
    </div>
</section>
<script type="text/javascript">
var __notifications         = atob('<?php echo base64_encode(json_encode($notifications)) ?>');
var __limit                 = '<?php echo $limit ?>';
var __totalNotifications    = '<?php echo $total_notifications; ?>';
var __offset                = <?php echo isset($offset)?$offset:'1'; ?>;
const __previlages__        = atob('<?php echo base64_encode(json_encode($this->__notification_privilege)) ?>');
</script>
<script src="<?php echo assets_url() ?>js/notification.js"></script>
<?php include_once 'footer.php'; ?>