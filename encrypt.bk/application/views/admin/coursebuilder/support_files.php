<link rel="stylesheet" href="<?php echo assets_url() ?>css/datepicker.min.css">
<link rel="stylesheet" href="<?php echo assets_url() ?>css/jquery.timepicker.min.css">
<link rel="stylesheet" href="<?php echo assets_url() ?>css/jquery-ui.css">
<style>
.activity-count-label{
    padding: 5px 0 5px 15px;
}
.lecture-dropdown-wrapper{
    padding: 0 0 0 15px;
}
.close-activity-btn{
    font-size: 26px;
    font-weight: 800;
    color: #f70000;
    cursor: pointer;
    user-select: none;
}
.add-activity-btn{
    font-size: 26px;
    font-weight: 800;
    color: #09bf63;
    cursor: pointer;
    user-select: none;
}
.activity-selector{
    min-width: 100px;
    background: #fff;
    border: 1px solid #ccc;
    border-radius: 4px;
    padding: 6px;
}
.add-new-activity-btn,  .add-new-activity-btn:focus {
    color: #53b665;
    font-weight: 600;
}
.add-new-activity-btn:hover {
    color: #53b665;
    font-weight: 600;
    text-decoration: underline;
}
.allow_edit{
    background-color:#fff !important;
}
#support-files 
{
    display: flex;
    flex-wrap: wrap;
    background: #eaeaea;
    padding: 15px 25px;
    font-size: 13px;
}
#support-files .supported-formats{font-style: italic;color: #008fd4;}
#collapseSupportfiles{border-bottom: 1px solid #d9d9d9;}
#support_file_form
{
    background-color:#fff !important; 
}
.support-files-info{padding:0px 0px 20px 0px !important;}

.redactor-file-item {
    border: 1px solid #371744;
    color: #fff;
    background: #64277d;
    margin: 4px 2px;
    display: table;
    line-height: 1;
    padding: 4px 12px;
    border-radius: 16px;
}
.redactor-file-item a {
    color: #fff;
}
.redactor-file-remover {
    display: inline-block;
    padding: 0 3px;
    cursor: pointer;
    opacity: 1;
    margin-left: 2px;
    position: relative;
    right: -3px;
}
</style>
<div class="panel-heading">
    <h4 class="panel-title">
    <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseSupportfiles">
        <h4 class="coursebuilder-settingstab-title">Support files</h4>
    </a>
    </h4>
</div>
<?php  $cl_support_files = $lecture['cl_support_files']; ?>
<div id="collapseSupportfiles" class="panel-collapse collapse">
    <div id="support-files">
        <div class="col-md-12 support-files-info">
            <div class="col-md-5 no-padding">Recommended File Formats :</div> 
            <div class="col-md-7 supported-formats no-padding"> .mp4,.avi,.flv,.mp3,.doc,.docx, .pptx,.ppt,.pdf,.xlsx,.zip,.odt,.png,.jpg,.jpeg</div>
        </div>
        <?php
        foreach($cl_support_files as $key=>$cl_support_file)
        {

        ?>
        <span class="redactor-file-item"><a target="_blank" href="<?php echo supportfile_path(array('course_id' => $lecture['cl_course_id'])).$cl_support_file['file_raw_name']; ?>"><?php echo $cl_support_file['file_name']; ?></a><span onclick="removeSupportfile('<?php echo $key; ?>')" class="redactor-file-remover">×</span></span> <?php
        }
        ?>
        
    </div>
    <form id="support_file_form" onsubmit="return validateForm()" method="POST" action="<?php echo admin_url('coursebuilder/save_access_restriction') ?>">
        <div class="panel-body">
            <div class="builder-inner-from">
                <div class="form-group clearfix">
                    <div class="fle-upload">
                    
                        <label class="fle-lbl">BROWSE</label>
                        <input type="file" class="form-control upload" id="lecture_support_file_upload" name="file">
                        <input value="" readonly="" class="form-control upload-file-name" id="upload_support_file_name" type="text">
                        
                    </div>
                </div>
                <div class="text-right">

                    <input type="hidden" id="support_file_lecture_id" name="support_file_lecture_id" value="<?php echo $lecture['id'] ?>">
                    <button type="button" id="save_support_file" onclick="save_support_files()" class="btn btn-green"><?php echo lang('save') ?></button>
                </div>
            </div>
        </div>
</form>
</div>
<script type="text/javascript" src="<?php echo assets_url() ?>js/datepicker.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/datepicker.en.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/jquery.timepicker.min.js"></script>
<?php   
    $has_s3         = $this->settings->setting('has_s3');
    $has_s3_ofabee  = $this->settings->setting('has_s3_ofabee');
    
    if( ($has_s3['as_superadmin_value'] && $has_s3['as_siteadmin_value']) || ($has_s3_ofabee['as_superadmin_value'] && $has_s3_ofabee['as_siteadmin_value']) )
    {
        $upload_js = '<script type="text/javascript" src="'.assets_url().'js/file_s3_upload.js"></script>';    
    }
    else
    {
        $upload_js = '<script type="text/javascript" src="'.assets_url().'js/file_server_upload.js"></script>';
    }
    echo $upload_js;
 ?>
<script>
    var has_s3_enabled = '<?php echo ($has_s3["as_superadmin_value"] && $has_s3["as_siteadmin_value"])? 1 : 0;?>';
    var asset_url       ='<?php echo assets_url() ?>';
    var __support_file  = "";
    var __uploads_url   = '';
    const __support_file_path = "<?php echo base_url().supportfile_upload_path(array('course_id' => $lecture['cl_course_id'])); ?>";
    $( document ).ready(function() {
        __allowed_files.push("png","jpg","jpeg","flv");  
    });
    
    $(document).on('change', '#lecture_support_file_upload', function(e){
        __support_file = e.currentTarget.files;
        if( __support_file.length > 1 )
        {
            lauch_common_message('Multiple file not allowed', 'more than one file not allowed.');    
            __support_file = '';
            return false;
        }
        var fileObj                     = new processFileName(__support_file[0]['name']);
            fileObj.uniqueFileName();   
   
        if(inArray(fileObj.fileExtension(), __allowed_files) == false)
        {
            lauch_common_message('Invalid File', 'This type of file is not allowed.');
            __support_uploading_file = '';
            return false;        
        }
        $('#upload_support_file_name').val(__support_file[0]['name']);
        __uploads_url = webConfigs('uploads_url');
    });

    function supportfileTouploadCompleted(file_response)
    {
        // //console.log(file_response, 'this is from supportfileTouploadCompleted');
        if(__support_file == '')
        {
            //alert('test');
            lauch_common_message('Invalid file', 'Please select any file.');
            return false;
        }
        
        var lecture_id = $("#support_file_lecture_id").val();
        $.ajax({
            url: webConfigs('admin_url')+'coursebuilder/save_support_file',
            type: "POST",
            data:{ "is_ajax":true,"lecture_id":lecture_id,"course_id":__course_id,"file_response":file_response},
            success: function(response) 
            {
                var file_data     = typeof file_response == "string" ? $.parseJSON(file_response) : file_response;
                var data          = $.parseJSON(response);
                //console.log(file_data);
                // //console.log(data);
                if(data['error'] == false)
                {
                    var encrypted_name      = file_data['file_object'].file_name
                    var file_name           = typeof file_data['file_object'].client_name != 'undefined' ? file_data['file_object'].client_name : encrypted_name;
                    var raw_filename        = file_data['file_object'].raw_name;
                    var support_file_html   = '';
                        support_file_html  += '<span class="redactor-file-item"><a target="_blank" href="'+__support_file_path+'/'+encrypted_name+'">'+file_name+'</a><span onclick=removeSupportfile("'+raw_filename+'"); class="redactor-file-remover">×</span></span>';
                }
                else
                {
                    lauch_common_message('Invalid File', 'This type of file is not allowed.'); 
                }
                $("#support-files").append(support_file_html);
                __support_file = '';
                $("#upload_support_file_name").val('');
                $("#lecture_support_file_upload").val('');
                $("#save_support_file").html('Save');
            }
        });
    }
    

    function supportfileTouploadCompletedS3(file_response)
    {
        //console.log(file_response, 'this is from supportfileTouploadCompleted');
        if(__support_file == '')
        {
            //alert('test');
            lauch_common_message('Invalid file', 'Please select any file.');
            return false;
        }
        
        var lecture_id = $("#support_file_lecture_id").val();
        $.ajax({
            url: webConfigs('admin_url')+'coursebuilder/save_support_file',
            type: "POST",
            data:{ "is_ajax":true,"lecture_id":lecture_id,"course_id":__course_id,"file_response":file_response},
            success: function(response) 
            {
                var file_data     = typeof file_response == "string" ? $.parseJSON(file_response) : file_response;
                var data          = $.parseJSON(response);
                // //console.log(file_data);
                // //console.log(data);
                if(data['error'] == false)
                {
                    var encrypted_name      = file_data['file_object'].file_name
                    var file_name           = typeof file_data['file_object'].client_name != 'undefined' ? file_data['file_object'].client_name : encrypted_name;
                    var raw_filename        = file_data['file_object'].raw_name;
                    var support_file_html   = '';
                        support_file_html  += '<span class="redactor-file-item"><a target="_blank" href="'+file_data['file_object'].full_path+'">'+file_name+'</a><span onclick=removeSupportfile("'+raw_filename+'"); class="redactor-file-remover">×</span></span>';
                }
                else
                {
                    lauch_common_message('Invalid File', 'This type of file is not allowed.'); 
                }
                $("#support-files").append(support_file_html);
                __support_file = '';
                $("#upload_support_file_name").val('');
                $("#lecture_support_file_upload").val('');
                $("#save_support_file").html('Save');
            }
        });
    }
    

    function removeSupportfile(file)
    {
        var lecture_id = $("#support_file_lecture_id").val();
        $.ajax({
            url: webConfigs('admin_url')+'coursebuilder/remove_support_file',
            type: "POST",
            data:{ "is_ajax":true,"lecture_id":lecture_id,"course_id":__course_id,"file":file},
            success: function(response) 
            {
                var data          = $.parseJSON(response);
                if(data['error'] == false)
                {
                }
            }
        });
    }
    $(document).on('click', '.redactor-file-remover', function(){
        $(this).parent('.redactor-file-item').remove();
    });
</script>