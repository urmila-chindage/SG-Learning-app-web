<h3 class="text-center social-heading">Backups</h3>
<div class="table course-cont rTable" id="other_backup_row_wrapper">
</div>
<script>
var __backupsFetched = false;
var __adminUrl = '<?php echo admin_url() ?>';
var __backupSVG = '<?php echo assets_url('images').'backup.svg' ?>';
function restoreFromAnotherCourse() {
    if(__backupsFetched == true) {
        return false;
    }
    __backupsFetched = true;
    $.ajax({
            url: __adminUrl+'backup/backups',
            type: "POST",
            data:{"is_ajax":true},
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
                    $('#other_backup_row_wrapper').html(renderBackupHtml(data['backups']));
                }
            }
        });  
}
function renderBackupHtml(backups) {
        var backupsHtml  = '';
        if(Object.keys(backups).length > 0 ) {
            $.each(backups, function(backupKey, backup ) {
                backupsHtml+= '<div class="rTableRow backup-listing-row" id="other_backup_row_'+backup['id']+'">';
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
                backupsHtml+= '                    <a href="javascript:void(0)" onclick="removeBackup(\''+backup['id']+'\')">Remove Backup</a>';
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
                    $('#other_backup_row_'+backupId).remove(); 
                }
            }
        });                
    }
</script>