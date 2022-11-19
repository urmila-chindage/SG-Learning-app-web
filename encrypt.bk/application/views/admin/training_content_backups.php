<?php include_once 'training_header.php';?>
<section class="content-wrap cont-course-big nav-included content-wrap-align content-wrap-top">
    <?php if(in_array($this->privilege['add'], $this->backup_privilege) || in_array($this->privilege['edit'], $this->course_content_privilege)): ?>
    <div class="container-fluid nav-content nav-js-height content-filter-top content-filter-fullwidth">
        <div class="row">
            <div class="rTable content-nav-tbl borderleft-none" style="border:none;">
                <div class="rTableRow">
                    <div class="rTableCell">
                        <div class="col-sm-12 text-right" style="padding-top: 4px;">
                            <?php if(in_array($this->privilege['edit'], $this->course_content_privilege)): ?><a href="javascript:void(0)" onclick="restoreFromAnotherCourse()" class="btn btn-violet">RESTORE FROM ANOTHER SOURCE<ripples></ripples></a><?php endif; ?>
                            <?php if(in_array($this->privilege['add'], $this->backup_privilege)): ?><a href="javascript:void(0)" onclick="backupCourseLauncher('<?php echo $course['id'] ?>')" class="btn btn-violet">TAKE A BACKUP<ripples></ripples></a><?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <div class="left-wrap col-sm-12">
        <div class="row">
            <div class="col-sm-12 course-cont-wrap">
                <div class="pull-right">
                    <?php
                        $total_backups  = sizeof($backups);
                        if($total_backups)
                        {
                            $backup_html    = '<h4 class="right-top-header user-count">';
                            $backup_html   .=   $total_backups.' '.(($total_backups > 1) ? ' Backups' : ' Backup');
                            $backup_html   .= '</h4>';
                            echo $backup_html;
                        }
                    ?>
                </div>
                <div class="table course-cont rTable" id="backup_row_wrapper">
                <?php if (!empty($backups)): ?>
                    <?php foreach ($backups as $backup): ?>
                    <div class="rTableRow user-listing-row" id="backup_row_<?php echo $backup['id'] ?>">
                        <div class="rTableCell" style="padding-left:0px">
                            <label class="pointer-cursor">
                                <span class="blue"></span>
                                <img src="<?php echo assets_url('images').'backup.svg' ?>" width="20" />
                                <span class="normal-base-color">
                                    <span> <b><?php echo $backup['cbk_course_code'].' - '.$backup['cbk_course_name'].'  ' ?></b></span>
                                </span>
                            </label>
                            <span class="pull-right groups-student-count-holder">
                                <span class="label-active backup-total"><?php echo date("F j, Y, g:i a", strtotime($backup['cbk_backup_date'])); ?></span>
                            </span>
                            <span class="pull-right groups-student-count-holder">
                                <span class="label-inactive backup-total"><?php echo $backup['cbk_size'] ?></span>
                            </span>
                        </div>
                        <?php if(in_array($this->privilege['delete'], $this->backup_privilege) || in_array($this->privilege['edit'], $this->course_content_privilege)): ?>
                        <div class="td-dropdown rTableCell">
                            <div class="btn-group lecture-control">
                                <span class="dropdown-tigger" data-toggle="dropdown">
                                    <span class='label-text'>
                                        <i class="icon icon-down-arrow"></i>
                                    </span>
                                    <span class="tilder"></span>
                                </span>
                                <ul class="dropdown-menu pull-right" role="menu">
                                <?php if(in_array($this->privilege['edit'], $this->course_content_privilege)): ?>
                                    <li>
                                        <a href="javascript:void(0)" onclick="importBackup('<?php echo $backup['id']; ?>', '<?php echo $backup['cbk_course_id']; ?>')" >Restore Backup</a>
                                    </li>
                                <?php endif; ?>
                                <?php if(in_array($this->privilege['delete'], $this->backup_privilege)): ?>
                                    <li>
                                        <a href="javascript:void(0)" onclick="removeBackup('<?php echo $backup['id']; ?>')" >Remove Backup</a>
                                    </li>
                                <?php endif; ?>
                                </ul>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach;?>
                <?php else: ?>
                    <div id="popUpMessage" class="alert alert-danger">No backups found.</div>
                <?php endif;?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php 
function duration($start_date)
{
    return round((time() - strtotime($start_date)) / (60 * 60 * 24));    
}
?>
<script>
    var __adminUrl = '<?php echo admin_url() ?>';
    var __backupAnimator = '<?php echo assets_url('images').'upload-gif.gif' ?>';
    var __restoreAnimator = '<?php echo assets_url('images').'restore-gif.gif' ?>';
    var __backupSVG = '<?php echo assets_url('images').'backup.svg' ?>';
    function backupCourseLauncher(courseId) {
        step1();
        $('#process_title').html('TAKE A BACKUP');
        $('#backup_course_launcher').unbind('click');      
        $('#backup_course_launcher').click({"course_id": courseId}, backupCourse);        
        $('#backup_course').modal();
        $('#backup_animator').attr('src', __backupAnimator);
    }
    
    function backupCourse(params) {
        step2();
        var courseId = params.data.course_id;
        $('#status_display').html('Initializing backup process... Please wait...');
        setTimeout(() => {
            $.ajax({
                url: __adminUrl+'backup/backup_initialize/'+courseId,
                type: "POST",
                data:{"is_ajax":true, "course_id":courseId},
                success: function(response) {
                    var data = $.parseJSON(response);
                    if(data['error'] == true) {
                        $('#backup_course').modal('hide');           
                        var messageObject = {
                            'body': data['message'],
                            'button_yes': 'CLOSE',
                        };
                        callback_danger_modal(messageObject);
                    } else {
                        $('#status_display').html('Backuping your database...');                        
                        backupCourseDatabase(courseId);
                    }
                }
            });                
        }, getTimeOut());
    }

    function backupCourseDatabase(courseId) {
        setTimeout(() => {
            $.ajax({
                url: __adminUrl+'backup/backup_database/'+courseId,
                type: "POST",
                data:{"is_ajax":true, "course_id":courseId},
                success: function(response) {
                    var data = $.parseJSON(response);
                    if(data['error'] == true) {
                        $('#backup_course').modal('hide');           
                        var messageObject = {
                            'body': data['message'],
                            'button_yes': 'CLOSE',
                        };
                        callback_danger_modal(messageObject);
                    } else {
                        $('#status_display').html('Backuping your files...');                        
                        backupCourseAssets(courseId, data['backup_id']);
                    }
                }
            });                
        }, getTimeOut());
    }

    function backupCourseAssets(courseId, backupId) {
        setTimeout(() => {
            $.ajax({
                url: __adminUrl+'backup/backup_assets/'+courseId,
                type: "POST",
                data:{"is_ajax":true, "course_id":courseId, "backup_id":backupId},
                success: function(response) {
                    var data = $.parseJSON(response);
                    if(data['error'] == true) {
                        $('#backup_course').modal('hide');           
                        var messageObject = {
                            'body': data['message'],
                            'button_yes': 'CLOSE',
                        };
                        callback_danger_modal(messageObject);
                    } else {
                        finalizingBackup(courseId, backupId);
                    }
                }
            });                
        }, getTimeOut());            
    }

    function finalizingBackup(courseId, backupId) {
        $('#status_display').html('Finalizing your backup...');                        
        setTimeout(() => {
            $.ajax({
                url: __adminUrl+'backup/finalize_bakup/'+courseId,
                type: "POST",
                data:{"is_ajax":true, "course_id":courseId, "backup_id":backupId},
                success: function(response) {
                    var data = $.parseJSON(response);
                    if(data['error'] == true) {
                        $('#backup_course').modal('hide');           
                        var messageObject = {
                            'body': data['message'],
                            'button_yes': 'CLOSE',
                        };
                        callback_danger_modal(messageObject);
                    } else {
                        $('#status_display').html('Completed your backup process');    
                        setTimeout(() => {
                            location.reload();
                        }, getTimeOut());                    
                    }
                }
            });                
        }, getTimeOut());
    }

    function importBackup(backupId, destinationCourseId) {
        step3();
        $('#process_title').html('RESTORE BACKUP');
        $('#backup_course_launcher').unbind('click');      
        $('#backup_course_launcher').click({"destination_course_id": destinationCourseId, "backup_id": backupId}, restoreCourseBackup);        
        $('#backup_course').modal();
        $('#backup_animator').attr('src', __restoreAnimator);
    }

    function restoreCourseBackup(params) {
        step2();
        var backupId = params.data.backup_id;
        var destinationCourseId = params.data.destination_course_id;
        $('#status_display').html('Initializing restore process... Please wait...');
        setTimeout(() => {
            $('#status_display').html('Building course hierarchy...');                                        
        }, getTimeOut());
        setTimeout(() => {
            $.ajax({
                url: __adminUrl+'backup/restore_initialize/'+destinationCourseId,
                type: "POST",
                data:{"is_ajax":true, "course_id":destinationCourseId, "backup_id":backupId},
                success: function(response) {
                    var data = $.parseJSON(response);
                    if(data['error'] == true) {
                        $('#status_display').html(data['message']);                        
                    } else {
                        $('#status_display').html('Restoring course assets...');                        
                        restoreCourseAssets(destinationCourseId, backupId);
                    }
                }
            });                
        }, getTimeOut());
    }

    function restoreCourseAssets(destinationCourseId, backupId) {
        setTimeout(() => {
            $.ajax({
                url: __adminUrl+'backup/restore_assets/'+destinationCourseId,
                type: "POST",
                data:{"is_ajax":true, "course_id":destinationCourseId, "backup_id":backupId},
                success: function(response) {
                    var data = $.parseJSON(response);
                    if(data['error'] == true) {
                        $('#status_display').html(data['message']);                        
                    } else {
                        $('#status_display').html('Restoring simple lectures...');                        
                        restoreCourseSimpleLecture(destinationCourseId, backupId);
                    }
                }
            });                
        }, getTimeOut());
    }

    function restoreCourseSimpleLecture(destinationCourseId, backupId) {
        setTimeout(() => {
            $.ajax({
                url: __adminUrl+'backup/restore_database/'+destinationCourseId,
                type: "POST",
                data:{"is_ajax":true, "course_id":destinationCourseId, "backup_id":backupId, 'backup_engine':'simple_lecture'},
                success: function(response) {
                    var data = $.parseJSON(response);
                    if(data['error'] == true) {
                        $('#status_display').html(data['message']);                        
                    } else {
                        $('#status_display').html('Restoring quizes...');                        
                        restoreCourseQuizLecture(destinationCourseId, backupId);
                    }
                }
            });                
        }, getTimeOut());
    }

    function restoreCourseQuizLecture(destinationCourseId, backupId) {
        setTimeout(() => {
            $.ajax({
                url: __adminUrl+'backup/restore_database/'+destinationCourseId,
                type: "POST",
                data:{"is_ajax":true, "course_id":destinationCourseId, "backup_id":backupId, 'backup_engine':'quiz'},
                success: function(response) {
                    var data = $.parseJSON(response);
                    if(data['error'] == true) {
                        $('#status_display').html(data['message']);                        
                    } else {
                        $('#status_display').html('Restoring video lectures...');                        
                        restoreCourseVideoLecture(destinationCourseId, backupId);
                    }
                }
            });                
        }, getTimeOut());
    }

    function restoreCourseVideoLecture(destinationCourseId, backupId) {
        setTimeout(() => {
            $.ajax({
                url: __adminUrl+'backup/restore_database/'+destinationCourseId,
                type: "POST",
                data:{"is_ajax":true, "course_id":destinationCourseId, "backup_id":backupId, 'backup_engine':'video'},
                success: function(response) {
                    var data = $.parseJSON(response);
                    if(data['error'] == true) {
                        $('#status_display').html(data['message']);                        
                    } else {
                        $('#status_display').html('Restoring course assets...');                        
                        finalizingRestore(destinationCourseId, backupId);
                    }
                }
            });                
        }, getTimeOut());
    }

    function finalizingRestore(destinationCourseId, backupId) {
        $('#status_display').html('Checking and Finalizing the restore process...');                        
        setTimeout(() => {
            $.ajax({
                url: __adminUrl+'backup/finalize_restore/'+destinationCourseId,
                type: "POST",
                data:{"is_ajax":true, "course_id":destinationCourseId, "backup_id":backupId},
                success: function(response) {
                    var data = $.parseJSON(response);
                    if(data['error'] == true) {
                        $('#status_display').html(data['message']);                        
                    } else {
                        $('#status_display').html('Completed your backup process');  
                        setTimeout(() => {
                            $('#backup_course').modal('hide');           
                        }, getTimeOut());                    
                      
                    }
                }
            });                
        }, getTimeOut());
    }

    function removeBackup(backupId) {
        var messageObject = {
            'body': 'Are you sure to remove this backup?',
            'button_yes': 'REMOVE',
            'button_no': 'CANCEL',
            'continue_params': {
                "backupId": backupId
            },
        };
        callback_warning_modal(messageObject, removeBackupConfirmed);
    }

    function removeBackupConfirmed(params) {
        var backupId = params.data.backupId;
        $.ajax({
            url: __adminUrl+'backup/delete',
            type: "POST",
            data:{"is_ajax":true, "backup_id":backupId},
            success: function(response) {
                var data = $.parseJSON(response);
                if(data['error'] == true) {
                    var messageObject = {
                            'body': data['message'],
                            'button_yes': 'CLOSE',
                        };
                    callback_danger_modal(messageObject, removeBackupConfirmed);
                } else {
                    var messageObject = {
                            'body': data['message'],   
                            'button_yes': 'CLOSE',
                        };
                    callback_success_modal(messageObject);
                    
                    $('#backup_row_'+backupId).remove();
                    var totalBackup = $('.user-listing-row').length;
                    if( totalBackup == 0) {
                        $('.right-top-header').remove();
                        $('#backup_row_wrapper').html('<div class="alert alert-danger">No backups found.</div>');
                    } else {
                        $('.right-top-header').html(totalBackup+' '+(totalBackup > 1 ? 'Backups' : 'Backup'));
                    }
                }
            }
        });                
    }

    function restoreFromAnotherCourse() {
        $('#restore_from_another_source').modal();
        $('#other_backup_row_wrapper').html('Loading...');
        $.ajax({
                url: __adminUrl+'backup/backups',
                type: "POST",
                data:{"is_ajax":true, 'course_id':'<?php echo $course['id'] ?>'},
                success: function(response) {
                    $('#other_backup_row_wrapper').html('');
                    var data = $.parseJSON(response);
                    if(data['error'] == true) {
                        $('#backup_course').modal('hide');           
                        var messageObject = {
                            'body': data['message'],
                            'button_yes': 'CLOSE',
                        };
                        callback_danger_modal(messageObject);
                    } else {
                        $('#other_backup_row_wrapper').html(renderBackupHtml(data['backups']));
                    }
                }
            });  
    }

    function importOtherBackup(backupId, destinationCourseId) {
        $('#restore_from_another_source').modal('hide');
        setTimeout(() => {
            importBackup(backupId, destinationCourseId)
        }, 200);
    }

    function renderBackupHtml(backups) {
        var backupsHtml  = '';
        if(Object.keys(backups).length > 0 ) {
            $.each(backups, function(backupKey, backup ) {
                backupsHtml+= '<div class="rTableRow user-listing-row" id="other_backup_row_'+backup['id']+'">';
                backupsHtml+= '    <div class="rTableCell" style="padding-left:0px">';
                backupsHtml+= '        <label class="pointer-cursor pull-left">';
                backupsHtml+= '            <span class="blue"></span>';
                backupsHtml+= '            <img src="'+__backupSVG+'" width="20">';
                backupsHtml+= '            <span class="normal-base-color">';
                backupsHtml+= '                <span> <b>'+backup['cbk_course_code']+' - '+backup['cbk_course_name']+'  </b></span>';
                backupsHtml+= '            </span>';
                backupsHtml+= '        </label>';
                backupsHtml+= '        <span class="pull-right groups-student-count-holder">';
                backupsHtml+= '            <span class="label-active backup-total">'+backup['cbk_backup_date']+'</span>';
                backupsHtml+= '        </span>';
                backupsHtml+= '        <span class="pull-right groups-student-count-holder">';
                backupsHtml+= '            <span class="label-inactive backup-total">'+backup['cbk_size']+'</span>';
                backupsHtml+= '        </span>';
                backupsHtml+= '    </div>';
                backupsHtml+= '    <div class="td-dropdown rTableCell">';
                backupsHtml+= '        <div class="btn-group lecture-control">';
                backupsHtml+= '            <span class="dropdown-tigger" data-toggle="dropdown">';
                backupsHtml+= '                <span class="label-text">';
                backupsHtml+= '                    <i class="icon icon-down-arrow"></i>';
                backupsHtml+= '                </span>';
                backupsHtml+= '                <span class="tilder"></span>';
                backupsHtml+= '            </span>';
                backupsHtml+= '            <ul class="dropdown-menu pull-right" role="menu">';
                backupsHtml+= '                <li>';
                backupsHtml+= '                    <a href="javascript:void(0)" onclick="importOtherBackup(\''+backup['id']+'\', \'<?php echo $course['id'] ?>\')">Restore Backup</a>';
                backupsHtml+= '                </li>';
                backupsHtml+= '            </ul>';
                backupsHtml+= '        </div>';
                backupsHtml+= '    </div>';
                backupsHtml+= '</div>';
            });
        } else {
            backupsHtml += '<div class="alert alert-danger">No backups found.</div>';
        }
        return backupsHtml;
    }

    function step1() {
        $('#backup_course .modal-body').removeAttr('style');
        $('.step-2').hide();
        $('.step-3').hide();
        $('.step-1').show();
        $('.action-button-import').show();
    }

    function step2() {
        $('#backup_course .modal-body').css('background', '#fff');
        $('.step-1').hide();
        $('.step-3').hide();
        $('.step-2').show();
        $('.action-button-import').hide();
    }

    function step3() {
        $('#backup_course .modal-body').removeAttr('style');
        $('.step-1').hide();
        $('.step-2').hide();
        $('.step-3').show();
        $('.action-button-import').show();
    }

    function getTimeOut() {
        return Math.floor(1000 + (2000 - 1000) * Math.random());
    }
</script>
<?php include_once 'training_footer.php';?> 

<div class="modal warning-alert fade padd-r20" id="backup_course" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false" >
    <div class="modal-dialog modal-small" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                <h4 class="modal-title" id="process_title">TAKE A BACKUP</h4>
            </div>
            <div class="modal-body">
                <div class="icon-align mb30 action-button-import text-center">
                    <span class="alert-icon"></span>
                </div>
                <div class="form-group mb30 step-1">
                    <p><b>1.</b> Do not refresh the page while the backup process is on progress</p>
                </div>
                <div class="form-group mb30 step-1">
                    <p><b>2.</b> We will backup only the course contents excluding the live lecture</p>
                </div>
                <div class="form-group mb30 step-1">
                    <p><b>3.</b> Logs created by students will not be recorded during this backup.</p>
                </div>
                <div class="form-group mb30 step-1">
                    <p><b>4.</b> Click the continue button to take a backup</p>
                </div>
                <div class="form-group mb30 step-2 text-center">
                    <img id="backup_animator" src="" style="width:80%" />
                </div>
                <div class="form-group clearfix step-2 text-center">
                    <b id="status_display"></b>
                </div>

                <div class="form-group mb30 step-3">
                    <p><b>1.</b> Do not refresh the page while the restoring process is on progress</p>
                </div>
                <div class="form-group mb30 step-3">
                    <p><b>2.</b> The course will be restored at this point.</p>
                </div>
                <div class="form-group mb30 step-3">
                    <p><b>3.</b> Restoring process remove old course contents and logs created by students. </p>
                </div>
                <div class="form-group mb30 step-3">
                    <p><b>4.</b> Click the continue button to restore this backup</p>
                </div>
                <div class="form-group clearfix step-2 text-center">
                    <b id="status_display"></b>
                </div>
                
                <div class="modal-footer action-button-import">
                    <button type="button" class="btn btn-green"  data-dismiss="modal">CANCEL</button>
                    <button type="button" class="btn btn-red" id="backup_course_launcher" >CONTINUE</button>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal warning-alert fade padd-r20" id="restore_from_another_source" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false" >
    <div class="modal-dialog modal-small" role="document" style="margin:0 auto;width: 950px;top: 0px;">
        <div class="modal-content" style="height: 90vh;margin: 30px;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                <h4 class="modal-title" id="process_title">RESTORE FROM ANOTHER SOURCE</h4>
            </div>
            <div class="modal-body text-center" style="overflow-y: auto;height: 81vh;">
                <div class="table course-cont rTable" id="other_backup_row_wrapper">
                </div>
            </div>
        </div>
    </div>
</div>