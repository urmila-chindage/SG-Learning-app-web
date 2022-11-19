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

    $has_s3 = $this->settings->setting('has_s3');

?>
    <!-- Manin Iner container start -->
    <div class='course-bulder-content-inner'>
                   <!-- top Header with drop down and action buttons -->
                   <div class="buldr-header inner-buldr-header clearfix">
                        <div class="pull-left">
                            <!-- Header left items -->
                            <div class="lecture-icon-big <?php echo $lecture_icons[$lecture['cl_lecture_type']]['parent'] ?>"><i class="icon <?php echo $lecture_icons[$lecture['cl_lecture_type']]['child'] ?>"></i></div>
                            <h3><?php echo $lecture['cl_lecture_name']; ?></h3>
                        </div>
                        <!-- !.Header left items -->
                        <div class="pull-right rite-side">
                            <!-- Header right side items with buttons -->
                            <a id="lecture_action_status_<?php echo $lecture['id'] ?>" class="btn <?php echo $status_button ?>" onclick="changeLectureStatus('<?php echo $lecture['cl_status'] ?>', '<?php echo base64_encode(htmlentities($lecture['cl_lecture_name'])); ?>', '<?php echo $lecture['id'] ?>')"><?php echo strtoupper(lang($status_label)) ?></a>
                            <a href="<?php echo admin_url('coursebuilder/home/'.$lecture['cl_course_id']) ?>"><button class="btn btn-red" id="back_button"><i class="icon icon-left"></i><?php echo lang('back') ?></button></a>
                        </div>
                    </div>
                    <!-- !.top Header with drop down and action buttons -->
                    
                <!-- accordion starts here -->
                <div class="col-sm-6 builder-left-inner">
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
                                            <!-- Form elements Left side :: lecture Documents-->
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
                                                        <!-- <div id="lecture_status_wraper_< ?php echo $lecture['id'] ?>" class="< ?php echo $status_mark ?>-section"><i class="icon icon-ok-circled"></i><span class="ap_cont" id="lecture_status_text_< ?php echo $lecture['id'] ?>">< ?php echo ucfirst(lang($status_lang)) ?></span></div> -->
                                                        <input type="text" maxlength="80" placeholder="eg: Mathematical Calculations" id="lecture_name" name="lecture_name" value="<?php echo isset($lecture['cl_lecture_name'])?htmlentities($lecture['cl_lecture_name']):''; ?>" class="form-control">
                                                </div>
                                                <!-- Description area -->
                                                <div class="form-group clearfix">
                                                    <label><?php echo lang('description') ?> :</label>
                                                    <textarea class="form-control" id="lecture_description" onkeyup="validateMaxLength(this.id)" maxlength="1000" name="lecture_description" ><?php echo isset($lecture['cl_lecture_description'])?$lecture['cl_lecture_description']:''; ?></textarea>
                                                        <label class="pull-right" id="lecture_description_char_left"> <?php echo (1000 -strlen($lecture['cl_lecture_description'])).' '.lang('characters_left') ?>
                                                        </label>
                                                </div>
                                                <!-- !.Description area -->
                                                <div class="form-group clearfix">
                                                        <label>Update Scorm Content :</label>
                                                         <div class="fle-upload">
                                                            <label class="fle-lbl">BROWSE</label>
                                                            <input accept=".zip" type="file" class="form-control upload" id="lecture_file_upload_manual" name="file">
                                                            <input value="" readonly="" class="form-control upload-file-name" id="upload_scorm" type="text">
                                                            <!-- <span id="searchclear">×</span> -->
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
                                                <div class="pull-right">
                                                    <input type="hidden" id="lecture_id" value="<?php echo $lecture['id'] ?>">
                                                    <input type="hidden" id="section_id" value="<?php echo $lecture['cl_section_id'] ?>">
                                                    <input type="hidden" id="course_id" value="<?php echo $lecture['cl_course_id'] ?>">
                                                    <?php ?>
                                                    <button type="button" onclick="<?php echo ($has_s3['as_superadmin_value'] && $has_s3['as_siteadmin_value'] )?'saveLectureDetail()':'saveLectureDetailScorm()'; ?>" class="btn btn-green"><?php echo lang('save') ?></button>
                                                </div>
                                            </div>
                                            <!-- !.Form elements Left side :: lecture Documents-->
                                        </div>
                                    </div>
                                </div>
                            </div>
                </div><!-- !.Left side bar section -->

                <div class="col-sm-6 builder-right-inner no-padding" style="overflow:hidden;"> <!-- right side bar section -->
                    <!-- Preivew of  youtube content will show here -->
                    <!-- <img src="images/ppt.jpg" class="default" alt="default" /> --> <!-- Default image if needed -->
                            <?php if( $lecture['cl_conversion_status'] < 3 || $lecture['cl_conversion_status'] == 4  ): ?>
                            <div class="preivew-area overflw-Y-scroll text-center">
                                    <div class="default-view-txt">
                                        <h3>Conversion in Progress !</h3>
                                        <p> File upload was successfully. Currently the file is under conversion. Preview will be displayed after conversion is completed</p>
                                    </div>
                                </div>    
                            <?php elseif($lecture['cl_conversion_status'] == 5): ?>
                            <div class="preivew-area overflw-Y-scroll text-center">
                                <div class="default-view-txt">
                                    <h3>Conversion Error !</h3>
                                    <p>The conversion for the file you uploaded completed with error. Please try again.</p>
                                </div>
                            </div>            
                            <?php else: ?>
                            <?php 
                                //echo $lecture['cl_filename']
                                $scorm_path = uploads_url().$lecture['cl_filename'];
                            ?>
                            <iframe class="scorm-preview" src="<?php echo $scorm_path ?>" width="100%"></iframe>
                            <?php endif; ?>
                </div>
                <!-- !.Preivew of  youtube content will show here -->
    </div>  <!-- !course-bulder-content-inner -->

    </div><!-- right side bar section -->

    </div>
  
<script type="text/javascript" src="<?php echo assets_url() ?>js/document_video.js"></script>
<?php 
    $upload_js = '<script type="text/javascript" src="'.assets_url().'js/file_server_upload.js"></script>';
    if( $has_s3['as_superadmin_value'] && $has_s3['as_siteadmin_value'] )
    {
        $upload_js = '<script type="text/javascript" src="'.assets_url().'js/file_s3_upload.js"></script>';    
    }
?>  
<?php echo $upload_js ?>
<script type="text/javascript">
var scormobj    = '';
var scorm_allowed       = ['zip'];
$(document).on('change', '#lecture_file_upload_manual', function(e){
    if ($(this).val()) {
        var fileName = $(this).val().split('/').pop().split('\\').pop();
        $('.file-descriptive').css('display','block');
        $('#attached_file_scorm').html('<label>'+lang('attached')+' : '+fileName+'</label>');
    }
    scormobj    = e.currentTarget.files;
    $('#upload_scorm').val(scormobj[0]['name']);
    var fileObj                     = new processFileName(scormobj[0]['name']);
        fileObj.uniqueFileName();        
    if(inArray(fileObj.fileExtension(), scorm_allowed) == false)
    {
        $('#upload_scorm').val('');
        lauch_common_message('Invalid File', 'This type of file is not allowed.');    
        return false;        
    }
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
  