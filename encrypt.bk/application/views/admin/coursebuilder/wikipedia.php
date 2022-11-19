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
    <div class='course-bulder-content-inner' id="wikipedia_form">

            <!-- top Header -->
            <div class="buldr-header inner-buldr-header clearfix row">
                        <div class="pull-left">
                            <!-- Header left items -->
                            <div class="lecture-icon-big <?php echo $lecture_icons[$lecture['cl_lecture_type']]['parent'] ?>"><i class="icon <?php echo $lecture_icons[$lecture['cl_lecture_type']]['child'] ?>"></i></div>
                            <h3><?php echo substr($lecture['cl_lecture_name'], 0, 13) ?>..</h3>
                        </div>
                <!-- !.Header left items -->
                <div class="pull-right rite-side">
                    <!-- Header right side items with buttons -->
                    <a id="lecture_action_status_<?php echo $lecture['id'] ?>" class="btn <?php echo $status_button ?>"  data-toggle="modal" data-target="#active-lecture" onclick="changeLectureStatus('<?php echo $lecture['cl_status'] ?>', '<?php echo htmlentities($lecture['cl_lecture_name']) ?>', '<?php echo $lecture['id'] ?>')"><?php echo strtoupper(lang($status_label)) ?></a>
                    <a href="<?php echo admin_url('coursebuilder/home/'.$lecture['cl_course_id']) ?>"><button class="btn btn-red" id="back_button"><i class="icon icon-left"></i><?php echo lang('back') ?></button></a>
                </div>
            </div>
            <!-- !.top Header -->
   
            <div class="col-sm-6 builder-left-inner"><!-- !.Left side bar section -->
    <!-- Form elements Left side :: lecture Documents-->

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
                <div class="builder-inner-from">
            <div class="form-group clearfix">
                <label><?php echo lang('lecture_title') ?> *:</label>
                <div id="lecture_status_wraper_<?php echo $lecture['id'] ?>" class="<?php echo $status_mark ?>-section"><i class="icon icon-ok-circled"></i><span class="ap_cont" id="lecture_status_text_<?php echo $lecture['id'] ?>"><?php echo ucfirst(lang($status_lang)) ?></span></div>
                <input type="text" maxlength="80" placeholder="eg: Mathematical Calculations" id="lecture_name" name="lecture_name" value="<?php echo isset($lecture['cl_lecture_name'])?htmlentities($lecture['cl_lecture_name']):''; ?>" class="form-control">
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

        <div class="text-right">
            <input type="hidden" id="lecture_id" value="<?php echo $lecture['id'] ?>">
            <button type="button" onclick="saveWikipediaLecture()" class="btn btn-green"><?php echo lang('save') ?></button>
        </div>

    </div>
    <!-- !.Form elements Left side :: lecture Documents-->
        </div>
      </div>
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
            <div class="preivew-area  text-left">

                <!-- heading and paragraghs of the html lecture begins here -->
               <div class="default-view-txt m0 mb10">
                   <?php echo base64_decode($lecture['cl_lecture_content']) ?>
                </div>
                <!-- !.heading and paragraghs of the html lecture begins here -->
            </div>
             <!-- !.Preivew of  youtube content will show here -->
                

    </div><!-- right side bar section -->

    </div>
  
<script type="text/javascript" src="<?php echo assets_url() ?>js/wikipedia.js"></script>
<?php include_once 'lecture_footer.php'; ?>