<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Backup/Restore</title>
    <link rel="icon" href="<?php echo base_url('favicon.png') ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script type="text/javascript" src="<?php echo assets_url('js') ?>jquery.min.js"></script>
    <script>
        var __adminUrl = '<?php echo admin_url() ?>';

        function backupCourse(courseId) {
            $('#backup_process').html('Initializing backup process... Please wait...');
            setTimeout(() => {
                $.ajax({
                    url: __adminUrl+'backup/backup_initialize',
                    type: "POST",
                    data:{"is_ajax":true, "course_id":courseId},
                    success: function(response) {
                        var data = $.parseJSON(response);
                        if(data['error'] == true) {
                            $('#backup_process').html(data['message']);                        
                        } else {
                            $('#backup_process').html('Backuping your database...');                        
                            backupCourseDatabase(courseId);
                        }
                    }
                });                
            }, getTimeOut());
        }

        function backupCourseDatabase(courseId) {
            setTimeout(() => {
                $.ajax({
                    url: __adminUrl+'backup/backup_database',
                    type: "POST",
                    data:{"is_ajax":true, "course_id":courseId},
                    success: function(response) {
                        var data = $.parseJSON(response);
                        if(data['error'] == true) {
                            $('#backup_process').html(data['message']);                        
                        } else {
                            $('#backup_process').html('Backuping your files...');                        
                            backupCourseAssets(courseId, data['backup_id']);
                        }
                    }
                });                
            }, getTimeOut());
        }

        function backupCourseAssets(courseId, backupId) {
            setTimeout(() => {
                $.ajax({
                    url: __adminUrl+'backup/backup_assets',
                    type: "POST",
                    data:{"is_ajax":true, "course_id":courseId, "backup_id":backupId},
                    success: function(response) {
                        var data = $.parseJSON(response);
                        if(data['error'] == true) {
                            $('#backup_process').html(data['message']);                        
                        } else {
                            finalizingBackup(courseId, backupId);
                        }
                    }
                });                
            }, getTimeOut());            
        }

        function finalizingBackup(courseId, backupId) {
            $('#backup_process').html('Finalizing your backup...');                        
            setTimeout(() => {
                $.ajax({
                    url: __adminUrl+'backup/finalize_bakup',
                    type: "POST",
                    data:{"is_ajax":true, "course_id":courseId, "backup_id":backupId},
                    success: function(response) {
                        var data = $.parseJSON(response);
                        if(data['error'] == true) {
                            $('#backup_process').html(data['message']);                        
                        } else {
                            $('#backup_process').html('Completed your backup process');                        
                        }
                    }
                });                
            }, getTimeOut());
        }

        function restoreCourse(destinationCourseId, backupId) {
            $('#restore_process').html('Initializing restore process... Please wait...');
            setTimeout(() => {
                $('#restore_process').html('Building course hierarchy...');                                        
            }, getTimeOut());
            setTimeout(() => {
                $.ajax({
                    url: __adminUrl+'backup/restore_initialize',
                    type: "POST",
                    data:{"is_ajax":true, "course_id":destinationCourseId, "backup_id":backupId},
                    success: function(response) {
                        var data = $.parseJSON(response);
                        if(data['error'] == true) {
                            $('#restore_process').html(data['message']);                        
                        } else {
                            $('#restore_process').html('Restoring course assets...');                        
                            restoreCourseAssets(destinationCourseId, backupId);
                        }
                    }
                });                
            }, getTimeOut());
        }

        function restoreCourseAssets(destinationCourseId, backupId) {
            setTimeout(() => {
                $.ajax({
                    url: __adminUrl+'backup/restore_assets',
                    type: "POST",
                    data:{"is_ajax":true, "course_id":destinationCourseId, "backup_id":backupId},
                    success: function(response) {
                        var data = $.parseJSON(response);
                        if(data['error'] == true) {
                            $('#restore_process').html(data['message']);                        
                        } else {
                            $('#restore_process').html('Restoring simple lectures...');                        
                            restoreCourseSimpleLecture(destinationCourseId, backupId);
                        }
                    }
                });                
            }, getTimeOut());
        }

        function restoreCourseSimpleLecture(destinationCourseId, backupId) {
            setTimeout(() => {
                $.ajax({
                    url: __adminUrl+'backup/restore_database',
                    type: "POST",
                    data:{"is_ajax":true, "course_id":destinationCourseId, "backup_id":backupId, 'backup_engine':'simple_lecture'},
                    success: function(response) {
                        var data = $.parseJSON(response);
                        if(data['error'] == true) {
                            $('#restore_process').html(data['message']);                        
                        } else {
                            $('#restore_process').html('Restoring quizes...');                        
                            restoreCourseQuizLecture(destinationCourseId, backupId);
                        }
                    }
                });                
            }, getTimeOut());
        }

        function restoreCourseQuizLecture(destinationCourseId, backupId) {
            setTimeout(() => {
                $.ajax({
                    url: __adminUrl+'backup/restore_database',
                    type: "POST",
                    data:{"is_ajax":true, "course_id":destinationCourseId, "backup_id":backupId, 'backup_engine':'quiz'},
                    success: function(response) {
                        var data = $.parseJSON(response);
                        if(data['error'] == true) {
                            $('#restore_process').html(data['message']);                        
                        } else {
                            $('#restore_process').html('Restoring video lectutes...');                        
                            restoreCourseVideoLecture(destinationCourseId, backupId);
                        }
                    }
                });                
            }, getTimeOut());
        }

        function restoreCourseVideoLecture(destinationCourseId, backupId) {
            setTimeout(() => {
                $.ajax({
                    url: __adminUrl+'backup/restore_database',
                    type: "POST",
                    data:{"is_ajax":true, "course_id":destinationCourseId, "backup_id":backupId, 'backup_engine':'video'},
                    success: function(response) {
                        var data = $.parseJSON(response);
                        if(data['error'] == true) {
                            $('#restore_process').html(data['message']);                        
                        } else {
                            $('#restore_process').html('Restoring course assets...');                        
                            finalizingRestore(destinationCourseId, backupId);
                        }
                    }
                });                
            }, getTimeOut());
        }

        function finalizingRestore(destinationCourseId, backupId) {
            $('#restore_process').html('Checking and Finalizing the restore process...');                        
            setTimeout(() => {
                $.ajax({
                    url: __adminUrl+'backup/finalize_restore',
                    type: "POST",
                    data:{"is_ajax":true, "course_id":destinationCourseId, "backup_id":backupId},
                    success: function(response) {
                        var data = $.parseJSON(response);
                        if(data['error'] == true) {
                            $('#restore_process').html(data['message']);                        
                        } else {
                            $('#restore_process').html('Completed your backup process');                        
                        }
                    }
                });                
            }, getTimeOut());
        }

        function getTimeOut() {
            return Math.floor(1000 + (2000 - 1000) * Math.random());
        }
    </script>
</head>
<body>
<button onclick="backupCourse('<?php echo $course_id ?>')">Backup course <?php echo $course_id ?></button>
<br />
<p id="backup_process"></p>
<br /><br />
<br /><br />
<button onclick="restoreCourse('<?php echo $destination_course_id ?>', '<?php echo $backup_id ?>')">Restore course <?php echo $destination_course_id ?></button>
<br />
<p id="restore_process"></p>
</body>
</html>