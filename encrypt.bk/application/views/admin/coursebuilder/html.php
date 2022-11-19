<?php include_once 'lecture_header.php'; ?>
    <!-- ADDING REDACTOR PLUGIN INSIDE -->
    <!-- ############################# --> <!-- START -->
        <link rel="stylesheet" href="<?php echo assets_url() ?>css/redactor/css/redactor.css" />
        <link rel="stylesheet" href="<?php echo assets_url() ?>css/redactor/css/alignment.css" />
    <!-- ############################# --> <!-- END -->
    <!-- ADDING REDACTOR PLUGIN INSIDE -->
<?php 
    
    $checked       = 'checked="checked"';

    $status_button = 'btn-green';
    $status_label  = 'activate';
    $status_mark   = 'Inactive';
    $status_lang   = 'inactive';
    if($lecture['cl_status'] == 1)
    {
        $status_button = 'btn-orange';
        $status_label  = 'deactivate';
        $status_mark   = 'active';
        $status_lang   = 'active';
    }
    
?>
    <!-- Manin Iner container start -->
    <div class='course-bulder-content-inner'>
                        <!-- top Header -->
                   <div class="buldr-header inner-buldr-header clearfix">
                        <div class="pull-left">
                            <!-- Header left items -->
                            <div class="lecture-icon-big <?php echo $lecture_icons[$lecture['cl_lecture_type']]['parent'] ?>"><i class="icon <?php echo $lecture_icons[$lecture['cl_lecture_type']]['child'] ?>"></i></div>
                            <h3><?php echo $lecture['cl_lecture_name']; ?></h3>
                        </div>
                        <!-- !.Header left items -->
                        <div class="pull-right rite-side">
                            <!-- Header right side items with buttons -->
                            <a id="lecture_action_status_<?php echo $lecture['id'] ?>" class="btn <?php echo $status_button ?>" onclick="changeLectureStatus('<?php echo $lecture['cl_status'] ?>', '<?php echo base64_encode($lecture['cl_lecture_name']) ?>', '<?php echo $lecture['id'] ?>')"><?php echo strtoupper(lang($status_label)) ?></a>
                            <a href="<?php echo admin_url('coursebuilder/home/'.$lecture['cl_course_id']) ?>"><button class="btn btn-red" id="back_button"><i class="icon icon-left"></i><?php echo lang('back') ?></button></a>
                        </div>
                    </div>
                <!-- !.top Header -->

        <div class="col-sm-6 builder-left-inner"><!-- !.Left side bar section -->
            <!-- accordion starts here -->
            <div class="panel-group" id="accordion">
                <div class="panel panel-default">
                    <div class="panel-heading">
                      <h4 class="panel-title">
                        <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
                          <h4 class="coursebuilder-settingstab-title">Basic Settings</h4>
                        </a>
                      </h4>
                    </div>
                    <div id="collapseOne" class="panel-collapse collapse in">
                        <div class="panel-body">
                            <div class="builder-inner-from" id="html_form">
                            <div class="form-group clearfix " style="<?php echo ($course_details['cb_has_lecture_image'] == 1) ? "display:block" : "display:none" ?>">
                                <label><?php echo lang('lecture_image') ?> :</label>  
                                <div class="section-create-wrapper text-center">
                                    <div class="section-card-container">
                                        <div class="section-card">

                                        <?php $lecture_image = explode('?', $lecture['cl_lecture_image']); 
                                                    
                                                    if(!isset($lecture_image[0]))
                                                    {
                                                        $lecture_image[0] = '';
                                                    }
                                                    if(isset($s3_lecture_image))
                                                    {
                                                        $lecture_image_url = $s3_lecture_image;
                                                    }
                                                    else
                                                    {
                                                        $lecture_image_url = ($lecture['cl_lecture_image'] && file_exists(course_lecture_upload_path_document(
                                                        array('course_id' => $lecture['cl_course_id'])). $lecture_image[0])) ? course_lecture_image_path(
                                                        array('course_id' => $lecture['cl_course_id'])) . $lecture['cl_lecture_image'] : default_course_path()."default-lecture.jpg";
                                                    }            
                                        ?>

                                        <img id="lecture_image_add" data-id="<?php ?>" image_name="<?php echo ($lecture['cl_lecture_image']) ?  $lecture['cl_lecture_image'] : "default-lecture.jpg"; ?>" class="img-responsive" 
                                        src="<?php echo $lecture_image_url; ?>">                                            
                                            <label>
                                                <button class="btn btn-green section-img-upload-btn">CHANGE IMAGE</button>
                                                <input id="lecture_logo_btn" name="lecture_image" class="fileinput logo-image-upload-btn lecture-image-upload"  accept="image/*" type="file">
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div style="text-align: center;margin: 5px 0px;">Allowed Minimum File Size : 500px x 354px</div>
                            </div>

                                <div class="form-group clearfix">
                                    <label><?php echo lang('lecture_title') ?> *:</label>
                                    <input type="text" placeholder="eg: Mathematical Calculations" maxlength="80" id="lecture_name" name="lecture_name" value="<?php echo isset($lecture['cl_lecture_name'])?htmlentities($lecture['cl_lecture_name']):''; ?>" class="form-control">
                                </div>

                                    <!-- Description area -->
                                    <div class="form-group clearfix">
                                        <label><?php echo lang('description') ?> *:</label>
                                        <textarea class="form-control" id="lecture_description" onkeyup="validateMaxLength(this.id)" maxlength="1000" name="lecture_description" ><?php echo isset($lecture['cl_lecture_description'])?$lecture['cl_lecture_description']:''; ?></textarea>
                                        <label class="pull-right" id="lecture_description_char_left"> <?php echo (1000 -strlen($lecture['cl_lecture_description'])).' '.lang('characters_left') ?></label>
                                    </div>
                                    <!-- !.Description area -->
                                    
                                    <!-- <div class="form-group clearfix">
                                        <div class="checkbox"><label><input type="checkbox" id="cl_sent_mail_on_lecture_creation" value="1" <?php //echo (($lecture['cl_sent_mail_on_lecture_creation'] == 1)?$checked:''); ?>><span class="ap_cont chk-box"><?php //echo lang('send_mail_on_completed') ?>.</span></label></div>
                                    </div> -->
                                <input type="hidden" value="<?php echo $lecture['cl_course_id'] ?>" id="course_id" >
                                <div class="text-right">
                                    <input type="hidden" id="lecture_id" value="<?php echo $lecture['id'] ?>">
                                    <button type="button" onclick="saveHtmlLecture()" class="btn btn-green"><?php echo lang('save') ?></button>
                                </div>
                            </div>
                      </div>
                    </div> 
                    <?php include_once "access_restriction.php" ?>
                    <?php include_once "support_files.php" ?>
                </div>
            </div>
        <!-- accordion starts here -->
    </div>  <!-- !.Left side bar section -->
    <div class="col-sm-6 builder-right-inner no-padding"> <!-- right side bar section -->
        <!-- !.Preivew of  youtube content will show here -->
        <div class="form-group clearfix">
            <!-- <label><?php echo lang('lecture_content') ?>*:</label> -->
            <textarea class="form-control h235" id="cl_lecture_content"><?php echo $lecture['cl_lecture_content'] ?></textarea>
            <div class="text-right code-save-footer">
                <button type="button" onclick="saveHtmlLecture()" class="btn btn-green float-r1">SAVE<ripples></ripples></button>
            </div>
        </div>
    </div><!-- right side bar section -->
</div>
  
<script src="<?php echo assets_url() ?>js/redactor/js/redactor.js"></script>
<script src="<?php echo assets_url() ?>js/redactor/js/table.js"></script>
<script src="<?php echo assets_url() ?>js/redactor/js/alignment.js"></script>
<script src="<?php echo assets_url() ?>js/jquery.form-validator.min.js" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/html.js"></script>
<script>
    $(document).ready(function(e) {
    $('#cl_lecture_content').redactor({
        minHeight: '76vh',
        maxHeight: '76vh',
        height: '100%',
        imageUpload: admin_url+'configuration/redactore_image_upload',
        plugins: ['table', 'alignment', 'source'],
        callbacks: {
        imageUploadError: function(json, xhr)
        {
             var erorFileMsg = "This file type is not allowed. upload a valid image.";
             $('#html_form').prepend(renderPopUpMessage('error', erorFileMsg));
             scrollToTopOfPage();
             return false;
        }
    }   
    });
});
</script>
<?php include_once 'lecture_footer.php'; ?>
<script>
    var cb_has_lecture_image =  '<?php echo $course_details['cb_has_lecture_image'] ?>';
    var __course_loaded_img  = $('#lecture_image_add').attr('src');
    $(function(){
        $("#lecture_logo_btn").change(function() {
            readImageData(this); //Call image read and render function
        });
    });
    function readImageData(imgData) {
        console.log(imgData);
        if (imgData.files && imgData.files[0]) {
            var readerObj = new FileReader();
            
            readerObj.onload = function(element) {
                var img = new Image;
                $('#lecture_image_add').attr('src', element.target.result);
                var image_alowed_types = ['image/png', 'image/jpg', 'image/gif', 'image/jpeg', 'image/png'];
                if(jQuery.inArray($('#lecture_logo_btn')[0].files[0].type, image_alowed_types) < 0){
                    lauch_common_message('Image type', 'The file you have chosen is not allowed.');
                    $('#lecture_image_add').attr('src',__course_loaded_img);
                    $('#lecture_logo_btn').val("");
                    // $('#lecture_image_add').attr('data-id', 'default.jpg');
                    return false;
                }
                img.onload = function() {
                    if(img.width < 500 || img.height < 354){
                        lauch_common_message('Image Size', 'The image you have choosen is too small and cannot be uploaded.');
                        $('#lecture_image_add').attr('src',__course_loaded_img);
                        $('#lecture_logo_btn').val("");
                        // $('#lecture_image_add').attr('data-id', 'default.jpg');
                        return false;
                    }
                };

                img.src = element.target.result;
            }
            readerObj.readAsDataURL(imgData.files[0]);
        }
    }
            </script>
  