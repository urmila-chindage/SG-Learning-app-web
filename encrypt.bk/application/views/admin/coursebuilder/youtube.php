<?php include_once 'lecture_header.php'; ?>
<link rel="stylesheet" href="<?php echo assets_url() ?>plyr/plyr.css">
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
    <div class='course-bulder-content-inner builder-content-alt' >

                        <!-- top Header -->
                   <div class="buldr-header inner-buldr-header clearfix">
                        <div class="pull-left">
                            <!-- Header left items -->
                            <div class="lecture-icon-big <?php echo $lecture_icons[$lecture['cl_lecture_type']]['parent'] ?>"><i class="icon <?php echo $lecture_icons[$lecture['cl_lecture_type']]['child'] ?>"></i></div>
                            <h3><?php echo $lecture['cl_lecture_name'] ?></h3>
                        </div>
                    <!-- !.Header left items -->
                    <div class="pull-right rite-side">
                        <!-- Header right side items with buttons -->
                        <?php
                            if (!empty($this->course_content_privilege)) {
                                if (in_array($this->privilege['edit'], $this->course_content_privilege)) {
    
                        ?>
                        <a id="lecture_action_status_<?php echo $lecture['id'] ?>" class="btn <?php echo $status_button ?>" onclick="changeLectureStatus('<?php echo $lecture['cl_status'] ?>', '<?php echo base64_encode(htmlentities($lecture['cl_lecture_name'])); ?>', '<?php echo $lecture['id'] ?>')"><?php echo strtoupper(lang($status_label)) ?></a>
                        <?php
                                }
                            }
                        ?>
                        <a href="<?php echo admin_url('coursebuilder/home/'.$lecture['cl_course_id']) ?>"><button class="btn btn-red" id="back_button"><i class="icon icon-left"></i><?php echo lang('back') ?></button></a>
                    </div>
                </div>
                <!-- !.top Header -->
   
            <div class="col-sm-6 builder-left-inner"><!-- !.Left side bar section -->
                <!-- accordion starts here -->
                <div class="panel-group" id="accordion">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <div class="panel-title">
                              <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
                                <h4 class="coursebuilder-settingstab-title">Basic Settings</h4>
                              </a>
                            </div>
                        </div>
                        <div id="collapseOne" class="panel-collapse collapse in">
                                    <div class="panel-body">
                                            <!-- Form elements Left side :: lecture Documents-->
                                        <div class="builder-inner-from" id="youtube_form">
                                        <div class="form-group clearfix" style="<?php echo ($course_details['cb_has_lecture_image'] == 1) ? "display:block" : "display:none" ?>">
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
                                                    src="<?php echo $lecture_image_url;?>">
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
                                        <div class="form-group clearfix">
                                            <label><?php echo ucfirst(lang('youtube')." / ".lang('vimeo')) ?> <?php echo strtoupper(lang('url')) ?> *:</label>
                                            <input type="text" placeholder="https://www.youtube.com/" value="<?php echo isset($lecture['cl_filename'])?htmlentities($lecture['cl_filename']):''; ?>" id="youtube_url" class="form-control">
                                        </div>
                                        <!-- Description area -->
                                        <div class="form-group clearfix">
                                            <label><?php echo lang('description') ?> :</label>
                                            <textarea class="form-control" id="lecture_description" onkeyup="validateMaxLength(this.id)" maxlength="1000" name="lecture_description" ><?php echo isset($lecture['cl_lecture_description'])?$lecture['cl_lecture_description']:''; ?></textarea>
                                            <label class="pull-right" id="lecture_description_char_left"> <?php echo (1000 -strlen($lecture['cl_lecture_description'])).' '.lang('characters_left') ?></label>
                                        </div>
                                        <!-- !.Description area -->
                                        <!-- Access and maximum access count -->
                                        
                                        <!-- !.Access and maximum access count -->

                                        
                                        <!-- <div class="form-group clearfix">
                                            <div class="checkbox"><label><input type="checkbox" id="cl_sent_mail_on_lecture_creation" value="1" <?php //echo (($lecture['cl_sent_mail_on_lecture_creation'] == 1)?$checked:''); ?>><span class="ap_cont chk-box"><?php //echo lang('send_mail_on_completed') ?>.</span></label></div>
                                        </div> -->

                                    <div class="text-right">
                                        <input type="hidden" id="lecture_id" value="<?php echo $lecture['id'] ?>">
                                        <input type="hidden" id="section_id" value="<?php echo $lecture['cl_section_id'] ?>">
                                        <input type="hidden" id="course_id" value="<?php echo $lecture['cl_course_id'] ?>">
                                        <?php
                                            if (!empty($this->course_content_privilege)) {
                                                if (in_array($this->privilege['edit'], $this->course_content_privilege)) {
    
                                        ?>
                                            <button id="save_btn" type="button" onclick="saveYoutubeLecture()" class="btn btn-green"><?php echo lang('save') ?></button>
                                        <?php
                                                }
                                            }
                                        ?>
                                    </div>

                                </div>
                                <!-- !.Form elements Left side :: lecture Documents-->
                            </div>
                        </div>
                        <?php include_once "access_restriction.php"; ?>
                        <?php include_once "support_files.php" ?>
                    </div>
                    
                </div>
                <!-- accordion ends here -->
</div>  <!-- !.Left side bar section -->


    <div class="col-sm-6 builder-right-inner"> <!-- right side bar section -->

       <!-- top Header with drop down and action buttons -->
            <div class="buldr-header inner-buldr-header clearfix">
                    <div class="pull-left">
                        <!-- Header left items -->
                        <h3 class="right-top-header">Preview</h3>
                        <!-- File name -->
                        <?php //<span class="right-file-name">File name.mp4</span> ?>
                        <!-- !.File name -->
                    </div>
                <!-- !.Header left items -->

                <!-- !.Header right items -->
                <?php /*
                <div class="pull-right rite-side">
                    <span class="download-txt">Downloadable</span>
                    <!-- Custom check box with css styles -->
                    <section class="model-check">
                        <div class="cust-checkbox">
                            <input type="checkbox" />
                            <label></label>
                        </div>
                    </section><!-- !.Custom check box with css styles -->
                </div> */ ?>
            </div>
            <!-- !.top Header with drop down and action buttons --> 
            
            <!-- Preivew of  youtube content will show here -->
            <div class="preivew-area  text-center">
                <video id="player" controls height="480">
                </video>
            </div>
             <!-- !.Preivew of  youtube content will show here -->
                

    </div><!-- right side bar section -->

    </div>
<script type="text/javascript">
    $(document).ready(function() {
        $("#cl_limited_access").keydown(function (e) {
            // Allow: backspace, delete, tab, escape, enter and .
            if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
                 // Allow: Ctrl+A, Command+A
                (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) || 
                 // Allow: home, end, left, right, down, up
                (e.keyCode >= 35 && e.keyCode <= 40)) {
                     // let it happen, don't do anything
                     return;
            }
            // Ensure that it is a number and stop the keypress
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                e.preventDefault();
            }
        });
    });
</script>
<script src="<?php echo assets_url() ?>plyr/plyr.js"></script>
<script type="text/javascript">
    var __file='';
    var videoFile=<?php echo json_encode($lecture['cl_filename']) ?>;
    
    function getId(url) {
        // var regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
        var regExp = /^.*(vimeo\.com|youtu\.be|www\.youtube\.com)\/([\w\/-]+)([^#\&\?]*).*/;
        var match = url.match(regExp);
        
        if (match) {
            match =  match[2].split('/');
            if (url.indexOf('vimeo.com') !== -1) {
                return match[1];
            }else{
                return match[1];
            }
        } else {
            return 'error';
        }
    }

    var videoId = getId(videoFile);

    const player = new Plyr('#player');
    if(videoId){
        if (videoFile.indexOf('vimeo.com') !== -1) {
            var provider = "vimeo";
        }else{
            var provider = "youtube";
        }
        player.source = {
            type: 'video',
            sources: [{
                src: videoId, // From the YouTube video link
                provider: provider,
            },],
        };
    }
    

    /*
    YouTube video link
    https://www.youtube.com/watch?v=aqz-KE-bpKQ
    */
</script>
<?php include_once 'lecture_footer.php'; ?>
<script type="text/javascript" src="<?php echo assets_url() ?>js/youtube.js"></script>

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
  