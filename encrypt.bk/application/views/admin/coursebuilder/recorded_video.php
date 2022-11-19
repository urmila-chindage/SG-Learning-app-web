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
    <div class='course-bulder-content-inner' id="recorded_video_form">
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
                        <a id="lecture_action_status_<?php echo $lecture['id'] ?>" class="btn <?php echo $status_button ?>"  data-toggle="modal" data-target="#active-lecture" onclick="changeLectureStatus('<?php echo $lecture['cl_status'] ?>', '<?php echo htmlentities($lecture['cl_lecture_name']) ?>', '<?php echo $lecture['id'] ?>', '<?php echo $lecture['cl_sent_mail_on_lecture_creation'] ?>')"><?php echo strtoupper(lang($status_label)) ?></a>
                        <a href="<?php echo admin_url('coursebuilder/home/'.$lecture['cl_course_id']) ?>"><button class="btn btn-red" id="back_button"><i class="icon icon-left"></i><?php echo lang('back') ?></button></a>
                    </div>
                </div>
                <!-- !.top Header -->

            <div class="col-sm-6 builder-left-inner"><!-- !.Left side bar section -->

    <!-- Form elements Left side :: lecture Documents-->

    <div class="builder-inner-from">


            <div class="form-group clearfix">
                <label><?php echo lang('lecture_title') ?> *:</label>
                <div id="lecture_status_wraper_<?php echo $lecture['id'] ?>" class="<?php echo $status_mark ?>-section"><i class="icon icon-ok-circled"></i><span class="ap_cont" id="lecture_status_text_<?php echo $lecture['id'] ?>"><?php echo ucfirst(lang($status_lang)) ?></span></div>
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
                <div class="col-sm-6">
                    <label class="mb10"><?php echo lang('access') ?>* :</label>
                    <br>
                    <label class="pad-top10">
                        <input type="radio" name="cl_limited" <?php echo (($lecture['cl_limited_access']==0)?$checked:''); ?> value="0"> <?php echo lang('unlimited') ?>
                    </label>
                    <label>
                        <input type="radio" name="cl_limited" <?php echo (($lecture['cl_limited_access'] > 0)?$checked:''); ?>  value="1" > <?php echo lang('limited') ?>
                    </label>
                </div>

                <div class="col-sm-6" id="cl_limited_access_wrapper" style="display:<?php echo ($lecture['cl_limited_access']>0)?'block':'none'; ?>">
                    <div class="form-group">
                        <label class="mb10"><?php echo lang('maximum_access_count') ?>* :</label>
                        <input type="text" class="form-control" id="cl_limited_access" maxlength="3" name="cl_limited_access" placeholder="Eg: 5" value="<?php echo $lecture['cl_limited_access'] ?>">
                    </div>
                </div>
            </div>
            <!-- !.Access and maximum access count -->

            
            <div class="form-group clearfix">
                <div class="checkbox"><label><input type="checkbox" id="cl_sent_mail_on_lecture_creation" value="1" <?php echo (($lecture['cl_sent_mail_on_lecture_creation'] == 1)?$checked:''); ?>><span class="ap_cont chk-box"><?php echo lang('send_mail_on_completed') ?>.</span></label></div>
            </div>

        <div class="pull-right">
            <input type="hidden" id="lecture_id" value="<?php echo $lecture['id'] ?>">
            <input type="hidden" id="section_id" value="<?php echo $lecture['cl_section_id'] ?>">
            <input type="hidden" id="course_id" value="<?php echo $lecture['cl_course_id'] ?>">
            <input type="hidden" id="recorded_url" value="<?php echo $lecture['cl_filename'] ?>">
            <button type="button" onclick="saveRecordedVideoLecture()" class="btn btn-green"><?php echo lang('save') ?></button>
            <button type="button" class="btn btn-red" data-dismiss="modal"><?php echo lang('cancel') ?></button>
        </div>

    </div>
    <!-- !.Form elements Left side :: lecture Documents-->


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
                <?php if($recorded_details['llr_type'] == '2'){ ?>
                    <iframe width="100%" height="480" src="<?php echo isset($lecture['cl_filename'])?(site_url('/live/play').'/'.$lecture['cl_filename']):'' ?>" frameborder="0" allowfullscreen></iframe>
                <?php }else if($recorded_details['llr_type'] == '1'){ ?>
                    <?php  $file_name      = $lecture['cl_filename'];
                        //$file_directory = substr($file_name, 1, -4 ).'/'.$file_name;
                        $file_directory = $file_name.'/'.'playlist.m3u8';
                        //$file_url = uploads_url('videos/'.config_item('acct_domain').'/'.$file_directory);
                        $file_url = 'http://138.201.198.106:1935/vod/'.$file_directory;
                        include_once 'live_video_player.php'; ?>
                <?php } ?>
            </div>
            
            
            
                                
             <!-- !.Preivew of  youtube content will show here -->
                

    </div><!-- right side bar section -->

    </div>

<script type="text/javascript" src="<?php echo assets_url() ?>js/recorded_video.js"></script>
<?php include_once 'lecture_footer.php'; ?>