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
<style>
    .list-recorded_videos optgroup{ text-indent: 15px;}
</style>
    <!-- Manin Iner container start -->
    <div class='course-bulder-content-inner builder-content-alt' >
                            <!-- top Header -->
                <div class="buldr-header inner-buldr-header clearfix">
                        <div class="pull-left">
                            <!-- Header left items -->
                            <div class="lecture-icon-big lecture-icon-align <?php echo $lecture_icons[$lecture['cl_lecture_type']]['parent'] ?>"><i class="icon <?php echo $lecture_icons[$lecture['cl_lecture_type']]['child'] ?>"></i></div>
                            <h3><?php echo $lecture['cl_lecture_name']; ?></h3>
                        </div>
                        <!-- !.Header left items -->
                        <div class="pull-right rite-side">
                            <!-- Header right side items with buttons -->
                            <a id="lecture_action_status_<?php echo $lecture['id'] ?>" class="btn <?php echo $status_button ?>"  data-toggle="modal" data-target="#active-lecture" onclick="changeLectureStatus('<?php echo $lecture['cl_status'] ?>', '<?php echo base64_encode(htmlentities($lecture['cl_lecture_name'])); ?>', '<?php echo $lecture['id'] ?>')"><?php echo strtoupper(lang($status_label)) ?></a>
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
                                <div class="form-group clearfix">
                                    <label><?php echo lang('lecture_title') ?> *:</label>
                                    <input type="text" placeholder="eg: Mathematical Calculations" maxlength="80" id="lecture_name" name="lecture_name" value="<?php echo isset($lecture['cl_lecture_name'])?htmlentities($lecture['cl_lecture_name']):''; ?>" class="form-control">
                                </div>

                                <!-- Description area -->
                                <div class="form-group clearfix" style="display: none;">
                                    <label><?php echo lang('description') ?> :</label>
                                    <textarea class="form-control" id="lecture_description" onkeyup="validateMaxLength(this.id)" maxlength="1000" name="lecture_description" ><?php echo isset($lecture['cl_lecture_description'])?$lecture['cl_lecture_description']:''; ?></textarea>
                                    <label class="pull-right" id="lecture_description_char_left"> <?php echo (1000 -strlen($lecture['cl_lecture_description'])).' '.lang('characters_left') ?></label>
                                </div>
                                <input type="hidden" id="recorder_video_list" value="<?php echo $lecture['cl_filename'] ?>" />
                                <?php //echo '<pre>'; print_r($recorded_list);die; ?>
                                <?php /* ?><div class="form-group clearfix">
                                    <label> Choose Recorded Video *:</label>
                                    <?php //echo '<br />'.$lecture['cl_filename'].'<br />'.$file['space_id'].'/'.$file['filename']; //echo (($lecture['cl_filename']= ($file['space_id'].'/'.$file['filename']))?'selected="selected"':'') ?>
                                    <select class="form-control list-recorded_videos" id="recorder_video_list">
                                        <option value="">Choose File</option>
                                        <?php if(!empty($recorded_list)): ?>
                                            <?php foreach($recorded_list as $date => $objects ): ?>
                                                <optgroup label="<?php echo $objects['label'] ?>">
                                                <?php if(!empty($objects['files'])): ?>
                                                    <?php foreach($objects['files'] as $file): ?>
                                                        <option <?php echo (($lecture['cl_filename']==$file['full_path'])?'selected="selected"':'') ?>  value="<?php echo $file['full_path'] ?>"><?php echo $file['filename'] ?></option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                                </optgroup>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>            
                                </div><?php */ ?>
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

                                
                                <div class="form-group clearfix" style="display: none;">
                                    <div class="checkbox"><label><input type="checkbox" id="cl_sent_mail_on_lecture_creation" value="1" <?php echo (($lecture['cl_sent_mail_on_lecture_creation'] == 1)?$checked:''); ?>><span class="ap_cont chk-box"><?php echo lang('send_mail_on_completed') ?>.</span></label></div>
                                </div>

                            <div class="text-right">
                                <input type="hidden" id="lecture_id" value="<?php echo $lecture['id'] ?>">
                                <input type="hidden" id="section_id" value="<?php echo $lecture['cl_section_id'] ?>">
                                <input type="hidden" id="course_id" value="<?php echo $lecture['cl_course_id'] ?>">
                                <input type="hidden" id="youtube_url" value="<?php echo $lecture['cl_filename'] ?>">
                                <button type="button" onclick="saveRecordedVideo()" class="btn btn-green"><?php echo lang('save') ?></button>
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
            <div class="buldr-header inner-buldr-header clearfix row">
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
                <?php 
                    $file_name      = $lecture['cl_filename'];
                    $file_url = cisco_path().$file_name;
                    include_once 'mp4_player.php';
                ?>
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
  
    var lecture_access_limit = '';
    function saveRecordedVideo()
    {
        var lecture_id                      = $('#lecture_id').val();
        var lecture_name                    = $('#lecture_name').val();
        var lecture_description             = $('#lecture_description').val();
        var recorder_video_id               = $('#recorder_video_list').val();
        var lecture_content                 = $('#cl_lecture_content').val();
        var sent_mail_on_lecture_creation   = $('#cl_sent_mail_on_lecture_creation').prop('checked');
            sent_mail_on_lecture_creation   = (sent_mail_on_lecture_creation==true)?'1':'0';
            lecture_access_limit            = $("input[type='radio'][name='cl_limited']:checked").val();

        var errorCount                      = 0;
        var errorMessage                    = '';
        //end

        //validation process
        if( lecture_name == '')
        {
            errorCount++;
            errorMessage += 'please enter lecture name <br />';
        }

        if( recorder_video_id == '')
        {
            errorCount++;
            errorMessage += 'please choose recorded video <br />';
        }


        if( lecture_description == '')
        {
            errorCount++;
            errorMessage += 'please enter lecture description<br />';
        }

        if( lecture_content == '')
        {
            errorCount++;
            errorMessage += 'please enter lecture content<br />';
        }

        if( lecture_access_limit > 0 )
        {
            lecture_access_limit = $('#cl_limited_access').val();
            if( lecture_access_limit == '')
            {
                errorCount++;
                errorMessage += 'please enter the access count<br />';
            }
        }
        cleanPopUpMessage();
        if(errorCount > 0 )
        {
            $('#html_form').prepend(renderPopUpMessage('error', errorMessage));
            scrollToTopOfPage();
            return false;
        }
        /*
        if(lecture_content!="")
        {
            errorCount++;
            //console.log(lecture_content);
            errorMessage += 'please select a valid image<br />';
        }*/
        $.ajax({
            url: admin_url+'coursebuilder/save_cisco_recorded_video_detail',
            type: "POST",
            data:{ "is_ajax":true, "lecture_id":lecture_id, 'cl_limited_access':lecture_access_limit, 'lecture_name':lecture_name, 'recorder_video_id':recorder_video_id},
            success: function(response) {
                var data  = $.parseJSON(response);
                if(data['error'] != "false")
                {                
                    $('#html_form').prepend(renderPopUpMessage('error', data['message']));
                    scrollToTopOfPage();
                }
                else
                {
                    location.reload();
                }
            }
        });
    }
    var lecture_access_limit = 0;
    $(document).ready(function(){
        lecture_access_limit = $("input[type='radio'][name='cl_limited']:checked").val();
        $("input[type='radio'][name='cl_limited']").click(function(){
            lecture_access_limit = $("input[type='radio'][name='cl_limited']:checked").val();
            if( lecture_access_limit == 0 )
            {
                $('#cl_limited_access_wrapper').hide();
            }
            else
            {
                $('#cl_limited_access_wrapper').show();
            }
        });
    });
</script>
<?php include_once 'lecture_footer.php'; ?>