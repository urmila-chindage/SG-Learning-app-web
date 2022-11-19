<?php include_once 'lecture_header.php'; ?>
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
                    <!-- Header left items -->
                </div>
                <!-- !.Header left items -->
                <div class="pull-right rite-side">
                    <!-- Header right side items with buttons -->
                    <?php if($content_security_status == 1 && ($lecture['cl_conversion_status'] == 3 || $lecture['cl_conversion_status'] == 5)): ?>
                    <a href="javascript:void(0)" class="btn btn-blue selected" id="reinitialize_button" onClick="reinitializeVideoLecture('<?php echo $lecture['id'] ?>', '<?php echo base64_encode(htmlentities($lecture['cl_lecture_name'])) ?>')">REINITIALIZE</a>
                    <?php endif; ?>
                    <a id="lecture_action_status_<?php echo $lecture['id'] ?>" class="btn <?php echo $status_button ?>"  data-toggle="modal" data-target="#active-lecture" onclick="changeLectureStatus('<?php echo $lecture['cl_status'] ?>', '<?php echo base64_encode(htmlentities($lecture['cl_lecture_name'])) ?>', '<?php echo $lecture['id'] ?>')"><?php echo strtoupper(lang($status_label)) ?></a>
                    <a href="<?php echo admin_url('coursebuilder/home/'.$lecture['cl_course_id']) ?>"><button class="btn btn-red" id="back_button"><i class="icon icon-left"></i><?php echo lang('back') ?></button></a>
                </div>
            </div>
        <!-- !.top Header -->
        

        <div class="col-sm-6 builder-left-inner"><!-- !.Right side bar section -->
            <!-- Form elements Right side :: lecture Documents-->

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
                        <div class="builder-inner-from" id="video_form">
                        <div class="form-group clearfix" style="<?php echo ($course_details['cb_has_lecture_image'] == 1) ? "display:block" : "display:none" ?>" >
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
                                <label><?php echo lang('description') ?> :</label>
                                <textarea class="form-control" id="lecture_description" onkeyup="validateMaxLength(this.id)" maxlength="1000" name="lecture_description" ><?php echo isset($lecture['cl_lecture_description'])?$lecture['cl_lecture_description']:''; ?></textarea>
                                <label class="pull-right" id="lecture_description_char_left"> <?php echo (1000 -strlen($lecture['cl_lecture_description'])).' '.lang('characters_left') ?></label>
                            </div>
                            <!-- !.Description area -->
                            <!-- Access and maximum access count -->
                            <div class="form-group clearfix row">
                                <div class="col-sm-6" id="cl_limited_access_wrapper" style="display:<?php echo ($lecture['cl_limited_access']>0)?'block':'none'; ?>">
                                    <div class="form-group">
                                        <label class="mb10"><?php echo lang('maximum_access_count') ?>* :</label>
                                        <input type="text" class="form-control" id="cl_limited_access" maxlength="3" name="cl_limited_access" placeholder="Eg: 5" value="<?php echo $lecture['cl_limited_access'] ?>">
                                    </div>
                                </div>
                            </div>
                            <!-- !.Access and maximum access count -->
                            <div class="form-group clearfix">
                                <div class="fle-upload">
                                    <label class="fle-lbl">BROWSE</label>
                                    <input type="file" class="form-control upload" id="lecture_file_upload_manual" name="file">
                                    <input value="" readonly="" class="form-control upload-file-name" id="upload_file_name" type="text">
                                </div>
                            </div>
                            <div class="clearfix progress-custom" id="lecture_upload_progress" style="display: none;">
                                <div class="progress width100">
                                      <div style="width: 0%;" aria-valuemax="100" aria-valuemin="0" aria-valuenow="60" role="progressbar" class="progress-bar progress-bar-success">
                                        <span class="sr-only">0% Complete</span>
                                    </div>
                                </div>
                                <span class="">Uploading...<b class="percentage-text" id="percentage_count">0%</b></span>
                            </div>

                            <div class="d-flex align-center">
                                <div class="text-left col-md-8 p0">
                                    <span>Lecture Preview:</span>
                                    <label class="control-label" style="margin-left:15px;">
                                        <input type="radio" class="lecture_preview" name="lecture_preview" <?php echo ($lecture['cl_lecture_preview']=='0')?'checked':''; ?> value="0" id="lecture_preview_no">
                                        <span>Disabled</span>
                                    </label>
                                    <label class="control-label" style="margin-left:15px;">
                                        <input type="radio" class="lecture_preview" name="lecture_preview" <?php echo ($lecture['cl_lecture_preview']=='1')?'checked':''; ?> value="1" id="lecture_preview_yes">
                                        <span>Enabled</span>
                                    </label>
                                </div>
                                <div class="text-right col-md-4 p0">
                                    <input type="hidden" id="lecture_id" value="<?php echo $lecture['id'] ?>">
                                    <input type="hidden" id="section_id" value="<?php echo $lecture['cl_section_id'] ?>">
                                    <input type="hidden" id="course_id" value="<?php echo $lecture['cl_course_id'] ?>">
                                    <button type="button" onclick="saveLectureDetail()" class="btn btn-green"><?php echo lang('save') ?></button>
                                </div>
                                
                            </div>
                        </div>
                      </div>
                    </div>
                </div>
                <?php include_once "access_restriction.php"; ?>
                <?php include_once "support_files.php" ?>
            </div>
            <!-- accordion ends here -->
            <!-- !.Form elements Right side :: lecture Documents-->
        </div>  <!-- !.Right side bar section -->

        <div class="col-sm-6 builder-right-inner"> <!-- right side bar section -->
        <!-- top Header with drop down and action buttons -->
            <div class="buldr-header inner-buldr-header clearfix"></div>
            <!-- !.top Header with drop down and action buttons -->  
            <!-- Preivew of  youtube content will show here -->
            <div class="preivew-area  text-center">
                <?php

                 $content_security = $this->settings->setting('has_content_security');
                 $content_security_status = $content_security['as_setting_value']['setting_value']->content_security_status;

                 if(!$content_security_status)
                 {  
                    //$file_name      = 'ce3a0d9e9de82047e98e706c0040fed9';//$lecture['cl_filename'];
                    $file_name      = explode('/',$lecture['cl_filename']);
                    $file_url = isset($file_name[2])?$file_name[2]:false;
                    if($file_url){
                        include_once 'video_player.php';
                    }else{
                        echo '<div class="default-view-txt">
                                <h3>'.lang('error_unknown').' !</h3>
                                <p>'. lang('error_unknown_message').'....</p>
                            </div>';
                    }
                 }
                 else
                {
                
                switch ($lecture['cl_conversion_status'])
                {
                    case '1':
                        echo '<div class="default-view-txt">
                                <h3>'.lang('file_upload_completed').'!</h3>
                                <p>'.lang('file_upload_completed_message').'</p>
                            </div>';
                    break;
                    case '2':
                        echo '<div class="default-view-txt">
                                <h3>'.lang('conversion_progress').' !</h3>
                                <p>'.lang('conversion_progress_message').'</p>
                            </div>';
                    break;
                    case '3':
                        //$file_name      = 'ce3a0d9e9de82047e98e706c0040fed9';//$lecture['cl_filename'];
                        $file_name      = $lecture['cl_filename'];
                        //$file_directory = substr($file_name, 1, -4 ).'/'.$file_name;
                        //$file_directory = $file_name.'/'.$file_name.'.m3u8';
                        //$file_url = uploads_url('videos/'.config_item('acct_domain').'/'.$file_directory);
                        $file_url = video_path(array('course_id'=> $lecture['cl_course_id'])).$file_name;
                        include_once 'video_player.php';
                    break;
                    case '4':
                        echo '<div class="default-view-txt">
                                <h3>'.lang('file_upload_completed').' !</h3>
                                <p>'.lang('file_upload_completed_s3_message').'</p>
                            </div>';
                    break;

                    default:
                        echo '<div class="default-view-txt">
                                <h3>'.lang('error_unknown').' !</h3>
                                <p>'. lang('error_unknown_message').'....</p>
                            </div>';
                        break;
                } 
              }
                ?>
            </div>
             <!-- !.Preivew of  youtube content will show here -->
        </div><!-- right side bar section -->
    </div>
    <script type="text/javascript" src="<?php echo assets_url() ?>js/document_video.js"></script>
    <?php // please dont remove this comment, the script is loaded in support_files.php which is included to this file
        /*if(!isset($lecture_save_file_loaded) || (isset($lecture_save_file_loaded) && !$lecture_save_file_loaded))
        {
            $has_s3         = $this->settings->setting('has_s3');
            $has_s3_ofabee  = $this->settings->setting('has_s3_ofabee');
            if( ($has_s3['as_superadmin_value'] && $has_s3['as_siteadmin_value']) || ($has_s3_ofabee['as_superadmin_value'] && $has_s3_ofabee['as_siteadmin_value'])  )
            {
                $upload_js = '<script type="text/javascript" src="'.assets_url().'js/file_s3_upload.js"></script>';    
            }
            else
            {
                $upload_js = '<script type="text/javascript" src="'.assets_url().'js/file_server_upload.js"></script>';
            }
        }
    ?>  
    <?php echo $upload_js */?>

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
  