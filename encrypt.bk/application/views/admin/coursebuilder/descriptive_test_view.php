<?php include_once 'lecture_header.php'; ?>
    <!-- ADDING REDACTOR PLUGIN INSIDE -->
    <!-- ############################# --> <!-- START -->
    <link rel="stylesheet" href="<?php echo assets_url() ?>css/redactor/css/redactor.css" />
    <link rel="stylesheet" href="<?php echo assets_url() ?>css/redactor/css/alignment.css" />
    <link rel="stylesheet" href="<?php echo assets_url() ?>css/datepicker.min.css">
    <link rel="stylesheet" href="<?php echo assets_url() ?>css/jquery-ui.css">
     <style>
        .redactor-attachment-wrapper {
            position: absolute;
            width: 325px;
            height: 310px;
            background: #f6f8fa;
            bottom: 0;
        }
        .redactor-attachment-header-wrapper {
            padding: 8px 15px;
            border-bottom: 1px solid #e5e5e5;
            min-height: 16.42857143px;
            background: #53b665;
            color: #fff;
            bottom: 298px;
            position: absolute;
            width: 100%;
            left: 0;
            border-radius: 5px 5px 0px 0px;
        }
        .attachment-close{
            font-size: 20px;
            line-height: 20px;
            vertical-align: middle;
            float: right;
            cursor:pointer;
        }
        .redactor-file-item {
            border: 1px solid #371744;
            color: #fff;
            background: #64277d;
            margin: 4px 2px;
            display: table;
        }
        .redactor-file-item a {
            color:#fff;
        }
        .redactor-file-remover {
            display: inline-block;
            padding: 0 3px;
            cursor: pointer;
            opacity: 1;
        }
        #file-target{
            padding: 5px 10px;
            overflow: auto;
            height: 240px;
            position: absolute;
            bottom: 59px;
            width: 100%;
            border: 1px solid #ededed;
            border-bottom: 0px;
        }
        .hide-attachment .redactor-attachment-header-wrapper{display:none;}
        .hide-attachment.redactor-attachment-wrapper {
            width: 360px;
            height: 60px;
            max-height: 60px;
            background: unset;
        }
        .hide-attachment #file-target {
            overflow: unset;
            height: 60px;
            padding-top: 14px;
            bottom: 0px;
            border: 0px;
        }
        .hide-attachment .redactor-file-item {
            display: inline-block !important;
        }
        .hide-attachment .redactor-file-item:nth-child(1n+3){
            display:none !important;
        }
        .hide-attachment .redactor-file-remover {
            position: unset;
            cursor: pointer;
            opacity: 1;
        }
        .display-more-item a{
            color: #53b665;
            cursor: pointer;
            padding:0px 8px;
        }
    </style>
    <!-- ############################# --> <!-- END -->
    <!-- ADDING REDACTOR PLUGIN INSIDE -->
    <!-- Manin Iner container start -->
    <div class='course-bulder-content-inner'>
    <div class="col-sm-6 course-bulder-content-inner builder-left-inner builder-left-inner-altr"><!-- !.Left side bar section -->
         
        <?php include_once 'descriptive_left_bar.php'; ?>
        <?php include_once 'lecture_override.php'; ?>
    </div>  <!-- !.Left side bar section -->
        <!-- top Header -->
        <div class="buldr-header inner-buldr-header clearfix">
                <div class="pull-left">
                    <!-- Header left items -->
                   <div class="lecture-icon-big <?php echo $lecture_icons[$lecture['cl_lecture_type']]['parent'] ?>"><i class="icon <?php echo $lecture_icons[$lecture['cl_lecture_type']]['child'] ?>"></i></div>
                            <h3 id="header-title"><?php echo $lecture['cl_lecture_name']; ?></h3>
                </div>
                
            <!-- !.Header left items -->

            <!-- !.Header right items -->
            <div class="pull-right rite-side">
            <!-- Questions buttons section, Add , Upload, import -->
                <a id="lecture_action_status_<?php echo $lecture['id'] ?>" class="btn <?php echo $status_button ?>"  onclick="changeLectureStatus('<?php echo $lecture['cl_status'] ?>', '<?php echo base64_encode($lecture['cl_lecture_name']) ?>', '<?php echo $lecture['id'] ?>')"><?php echo strtoupper(lang($status_label)) ?></a>

                <a href="<?php echo admin_url('coursebuilder/home/'.$lecture['cl_course_id']) ?>"><button class="btn btn-red" id="back_button"><i class="icon icon-left"></i><?php echo lang('back') ?></button></a>
                 <!-- !.Questions buttons section, Add , Upload, import -->

            </div>
        </div>
        <!-- !.top Header --> 


    <div class="col-sm-6 builder-right-inner no-padding"> <!-- right side bar section -->
        <!-- Preivew of  test content will show here -->
        <div >
            <div class="form-group clearfix">
                <textarea 
                    class="form-control " id="cl_lecture_content"><?php echo ($lecture['cl_lecture_content']!='')?$lecture['cl_lecture_content']:$default_instruction; ?></textarea>
            </div>
        </div>

        <div class="assignment-attach-footer">
            <?php 
                $file_object        = array();
                $uploaded_files     = json_decode($test_details['dt_uploded_files'], true);
                $uploaded_files     = ($uploaded_files)?$uploaded_files:array(); 
                $hide_attachment    = 'hide-attachment';
            ?>
            <div class="redactor-attachment-wrapper hide-attachment">
                <div class="redactor-attachment-header-wrapper">  
                    <span>ATTACHMENTS</span> 
                    <span onclick="hideAttachment()" class="attachment-close">&times;</span>         
                </div>
                <div id="file-target" class="list-all-attachment" style="display:none;">
                </div>
            </div>
            <!-- <button type="button" id="toggle_attachment" onclick="toggleAttachments()" class="<?php echo $hide_attachment ?> btn btn-green float-l selected">Show Attachments<ripples></ripples></button> -->
            <button type="button" onclick="saveDescriptiveDetail()" id="btn-descriptive-save" class="btn btn-green float-r"><?php echo lang('save') ?></button>
        </div>        
    </div><!-- right side bar section -->
</div>
<script>
    var __lecture_id = '<?php echo $lecture_id ?>';
    var __course_id  = '<?php echo $lecture['cl_course_id'] ?>';
</script>
<script src="<?php echo assets_url() ?>js/redactor/js/redactor.js"></script>
<script src="<?php echo assets_url() ?>js/redactor/js/table.js"></script>
<script src="<?php echo assets_url() ?>js/redactor/js/alignment.js"></script>
<script src="<?php echo assets_url() ?>js/descriptive.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/system.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/jquery-ui.min.js"></script>
<script>
    function showAttachment() {
        $('.redactor-attachment-wrapper').removeClass('hide-attachment');
        $('#display_more_attachment').remove();
    }
    function hideAttachment() {
        $("#file-target > span.redactor-file-item:nth-child(1)").after("<span onclick='showAttachment()' id='display_more_attachment' class='display-more-item'><a href='javascript:void(0)'>"+totalAttachmentText()+"</a></span>");
        $('.redactor-attachment-wrapper').addClass('hide-attachment');
    }
    function totalAttachmentText() {
        var totalAttachments = Object.keys(__uploadedFiles).length;
        if(totalAttachments == 1) {
            return "more..";
        }
        if(totalAttachments > 1) {
            return (totalAttachments-1)+" more..";
        }
    }
    var __uploadedFiles   = $.parseJSON(atob('<?php echo base64_encode(json_encode($uploaded_files)) ?>'));
    var __deleteFile      = '';
    const __redactor_path = "<?php echo assignment_path(array('course_id' => $lecture['cl_course_id'], 'purpose' => 'assignment')); ?>";
    $(function(){
        if(Object.keys(__uploadedFiles).length > 0 ) {
            var attachmentHtml = '';
            $.each(__uploadedFiles, function(key, file){
                attachmentHtml += '<span class="redactor-file-item" id="redactor_file_item_'+key+'">';
                attachmentHtml += '    <a href="'+__redactor_path+file['file_name']+'">'+file['name']+'</a>';
                attachmentHtml += '    <span class="redactor-file-remover redactor-file-remover-edit" onclick="removeFile(\''+file['file_name']+'\');" >×</span>';
                attachmentHtml += '</span>';
            });
            $('#file-target').html(attachmentHtml);
            if(Object.keys(__uploadedFiles).length > 1 ) {
                $("#file-target > span.redactor-file-item:nth-child(1)").after("<span onclick='showAttachment()' id='display_more_attachment' class='display-more-item'><a href='javascript:void(0)'>"+totalAttachmentText()+"</a</span>");
            }
        }
        var today = new Date();
        $("#descriptive_submission_date").datepicker({
            language: 'en',
            minDate: today,
            dateFormat: 'dd-mm-yy',
            autoClose: true
        });
    });
    $('#total_mark,#descriptive_words_limit').bind("cut copy paste",function(e) {
        e.preventDefault();
    });
    function removeFile(fileId){
        var totalAttachments = Object.keys(__uploadedFiles).length;
        if( totalAttachments > 0 ) {
            $.each(__uploadedFiles,function(key,file) {
                 if(typeof file.file_name != 'undefined' && file.file_name == fileId) {
                    delete __uploadedFiles[key];
                    return;
                }
            });    
        }
        if( totalAttachments == 1 ) {
            $('#display_more_attachment').remove();
        }
    }
    $(document).on('click', '.redactor-file-remover', function(){
        if($(this).hasClass('redactor-file-remover-edit') == false) {
            var fileId = $(this).prev().attr('href');
                fileId = fileId.split("/");
                fileId = fileId[parseInt(fileId.length) - 1];
            removeFile(fileId);
        }
        $(this).parent('.redactor-file-item').remove();
    });
    
    $(document).ready(function(e) {
        $("#file-target").removeAttr('style');
        $('input[name="submission_method"]').click(function(){
            var value   = $(this).attr("value");
            if(value==1){
                $("#submission_date").show();
                $("#submission_days").hide();
            } else if(value==2){
                $("#submission_days").show();
                $("#submission_date").hide();
            }
        });
        var __file_lengths = 0;
        $('#cl_lecture_content').redactor({
                minHeight: '76vh',
                maxHeight: '76vh',
                height: '100%',
                fileUpload : admin_url+'configuration/assignment_redactor_file_upload/'+__course_id,
                imageUpload: admin_url+'configuration/redactore_image_upload',
                fileAttachment: '#file-target',
                plugins: ['table', 'alignment'],
                callbacks: {
                    upload: {
                        complete: function( response)
                        {
                            //console.log(response);
                            var isFileUploaded = false;
                            var totalUploads = Object.keys(__uploadedFiles).length;
                            $.each(response, function(fileKey, file){
                                if(typeof file.original_name != 'undefined' && inArray(file.original_name.split('.').pop(), ['jpg', 'jpeg', 'gif', 'png']) == false) {
                                    __uploadedFiles[totalUploads] = {"name":file['name'], "original_name":file['original_name'], "file_name":file['file_name']};
                                    totalUploads++;
                                    isFileUploaded = true;
                                }
                            });
                            if( isFileUploaded == true ) {
                                showAttachment();
                                var objDiv = document.getElementById("file-target");
                                objDiv.scrollTop = objDiv.scrollHeight;
                            }
                        },
                        error: function(response)
                        {
                            var messageObject = {
                            'body':response.message,
                            'button_yes':'OK', 
                            'button_no':'CANCEL'
                            };
                            callback_warning_modal(messageObject);
                            $('#redactor-overlay').trigger('click'); 
                            //return false;
                            // $("#redactor-modal").addClass('redactor-animate-hide');
                            // $("#redactor-overlay").hide();
                        } 
                    }
                }
        });
        
    });
    function saveDescriptiveDetail(){
        var lecture_content = $('#cl_lecture_content').val();
        var uploadedFiles   = [];
        var i               = 0;
        if(Object.keys(__uploadedFiles).length > 0) {
            $.each(__uploadedFiles,function(key,file) {
                uploadedFiles[i] = file;
                i++;
            });
        }
        $("#btn-descriptive-save").html('Saving..');
        //console.log(uploadedFiles);
        $.ajax({
            url: admin_url+'coursebuilder/save_descriptive_files',
            type: "POST",
            data:{ "is_ajax":true, "lecture_id":__lecture_id, "course_id":__course_id,'uploaded_files':JSON.stringify(uploadedFiles),'lecture_content': lecture_content},
            success: function(response) {
                var data  = $.parseJSON(response);
                if(data['error'] != false)
                {   
                    var messageObject = { 
                    'body': data['message'],
                    'button_yes':'OK', 
                    'button_no':'CANCEL'
                    };
                }
                else
                {
                    var messageObject = {
                        'body': data['message'],
                        'button_yes':'OK', 
                        'button_no':'CANCEL'
                        };
                    
                }
                callback_success_modal(messageObject);
                $("#btn-descriptive-save").html('Save');
            }
        });
    }
</script>
<?php include_once 'lecture_footer.php'; ?>