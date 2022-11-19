<?php 
   
?>

    <!-- ADDING REDACTOR PLUGIN INSIDE -->
    <!-- ############################# --> <!-- START -->
        <link rel="stylesheet" href="<?php echo assets_url() ?>css/redactor/css/redactor.css" />
        <link rel="stylesheet" href="<?php echo assets_url() ?>css/redactor/css/alignment.css" />
    <!-- ############################# --> <!-- END -->
    <!-- ADDING REDACTOR PLUGIN INSIDE -->

    <div class="col-sm-6 builder-left-inner" id="challenge_form"><!-- !.Left side bar section -->

                <!-- top Header with drop down and action buttons -->
                <div class="buldr-header inner-buldr-header clearfix row">
                        <div class="pull-left">
                            <!-- Header left items -->
                            <div class="lecture-icon-big text-green"><i class="icon icon-beaker"></i></div>
                            <h3><?php echo substr($cz_title, 0, 13) ?>..</h3>
                            <!-- Drop down -->
                            <div class="btn-group section-control">
                                <span class="dropdown-tigger" data-toggle="dropdown">
                                <span class="label-text">
                                  <i class="icon icon-down-arrow"></i>
                                </span>
                                <span class="tilder"></span>
                                </span>
                                <ul class="dropdown-menu pull-right" role="menu">
                                    <li>
                                        <a href="#" data-target="#deleteSection" onclick="deleteChallengeZone('<?php echo base64_encode($cz_title) ?>', '<?php echo $id ?>')" data-toggle="modal"><?php echo lang('delete') ?></a>
                                    </li>
                                </ul>
                            </div>
                            <!-- !.Drop down -->
                        </div>
                    <!-- !.Header left items -->
                    <div class="pull-right rite-side">
                        <!-- Header right side items with buttons -->
                    <a href="<?php echo admin_url('challenge_zone/') ?>"><button class="btn btn-red" id="back_button"><i class="icon icon-left"></i><?php echo lang('back') ?></button></a>
                    </div>
                </div>
                <!-- !.top Header with drop down and action buttons -->

        <!-- Form elements Left side :: lecture Documents-->

        <div class="builder-inner-from challenge_zone_form" id="challenge_message"> 
                <div class="form-group clearfix">
                    <label><?php echo lang('challenge_title') ?> *:</label>
                    
                <input type="text" maxlength="80" placeholder="eg: Mathematical Calculations" id="challenge_name" name="cz_title" value="<?php echo htmlentities(isset($cz_title)?$cz_title:''); ?>" class="form-control">
                </div>

                 <!-- Lecture Content area -->
                <div class="form-group clearfix">
                    <label><?php echo lang('instructions') ?> *:</label>
                    <textarea class="form-control h235" id="challenge_instruction" onkeyup="validateMaxLength(this.id)" maxlength="1000" name="cz_instructions" ><?php echo isset($cz_instructions)?$cz_instructions:''; ?></textarea>
                    <?php /*?><label class="pull-right" id="challenge_instruction_char_left"> 
                        <?php 
                        $intruction_char_length = isset($cz_instructions)?strlen($cz_instructions):0;
                        ?>
                        <?php echo (1000 - $intruction_char_length).' '.lang('characters_left') ?>
                    </label><?php  */ ?>
                </div>
                <!-- !.Lecture Content area -->

                <!-- Access and maximum access count -->
                <div class="form-group clearfix row correct-lbl">
                    <div class="add-selectn col-sm-9 alignment-order">
                        <label><?php echo lang('category') ?> *:</label>
                        <select class="form-control" id="challenge_zone_category" name="cz_category">
                            <option value="0"><?php echo lang('select_category') ?></option>
                            <?php foreach($categories as $category): ?>
                                <option <?php if($cz_category == $category['id']) echo "selected"; ?> value="<?php echo $category['id'] ?>"><?php echo $category['ct_name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <label class=""><?php echo lang('duration') ?>* :</label>
                        <div class="input-group">
                            <input type="text" class="form-control" maxlength="3" id="challenge_duration" onkeypress="return preventAlphabets(event)" name="cz_duration"  value="<?php echo isset($cz_duration)?$cz_duration:''; ?>" placeholder="Eg: 160">
                            <div class="input-group-addon">MIN</div>
                         </div>
                    </div>
                </div>
                
                <div class="form-group clearfix row correct-lbl">
                    <div class="col-sm-3">
                        <label><?php echo lang('start_date') ?> *:</label>
                        <input type="text" name="start_date" placeholder="" value="<?php echo isset($cz_start_date)?$cz_start_date:'' ?>" id="challenge_start_date" class="form-control" >
                    </div>
                    <div class="col-sm-3">
                        <label><?php echo lang('start_time') ?> *:</label>
                        <div class="input-group" id="challenge_start_time_div">
                            <input type="text" name="start_time" value="<?php echo isset($cz_start_time)?$cz_start_time:'' ?>" class="form-control" id="challenge_start_time" placeholder="10.45">
                            <div class="input-group-addon" id="challenge_start_time_session"><?php echo isset($cz_stime_session)?$cz_stime_session:'AM' ?> </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <label><?php echo lang('end_date') ?> *:</label>
                        <input type="text" name="end_date" value="<?php echo isset($cz_end_date)?$cz_end_date:'' ?>" placeholder="" id="challenge_end_date" class="form-control" >
                    </div>
                    <div class="col-sm-3">
                        <label><?php echo lang('end_time') ?> *:</label>
                        <div class="input-group" id="challenge_end_time_div">
                            <input type="text" name="end_time" value="<?php echo isset($cz_end_time)?$cz_end_time:'' ?>" class="form-control" id="challenge_end_time" placeholder="10.45">
                            <div class="input-group-addon" id="challenge_end_time_session"><?php echo isset($cz_etime_session)?$cz_etime_session:'AM' ?> </div>
                        </div>
                    </div>
                </div>
                
                <div class="form-group clearfix row correct-lbl">
                    <div class="checkbox">
                        <label>
                            <input id="cz_show_categories" name="cz_show_categories" value="1" <?php echo (isset($cz_show_categories) && $cz_show_categories == '1')?'checked="checked"':'' ?> type="checkbox">
                            <span class="ap_cont chk-box">Show Categories</span><br><small>(If you select this option, categories tab will display in assessment page)</small>
                        </label>
                    </div>
                </div>
                <!-- !.Access and maximum access count -->

            <div class="pull-right">
                <input type="hidden" id="challenge_id" value="<?php echo $challenge_id ?>">
                <button type="button" onclick="saveChallengeZone()" class="btn btn-green"><?php echo lang('save') ?></button>
                <a href="<?php echo admin_url('challenge_zone/basics/'.$challenge_id) ?>" class="btn btn-red" data-dismiss="modal"><?php echo lang('cancel') ?></a>
            </div>

        </div>
        <!-- !.Form elements Left side :: lecture Documents-->


    </div>  <!-- !.Left side bar section -->

        <!-- JS -->
<script src="<?php echo assets_url() ?>js/redactor/js/redactor.js"></script>
<script src="<?php echo assets_url() ?>js/redactor/js/table.js"></script>
<script src="<?php echo assets_url() ?>js/redactor/js/source.js"></script>
<script src="<?php echo assets_url() ?>js/redactor/js/alignment.js"></script>
<script src="<?php echo assets_url() ?>js/redactor/js/limiter.js"></script>
<script src="<?php echo assets_url() ?>js/jquery.form-validator.min.js" type="text/javascript"></script>
<script>

var challenge_id = <?php echo $challenge_id ?>;
$(document).ready(function(e) {
    $('#redactor, #challenge_instruction').redactor({
        minHeight: 250,
        maxHeight: 250,
        imageUpload: admin_url+'configuration/redactore_image_upload',
        plugins: ['table', 'alignment', 'source'],
        callbacks: {
            imageUploadError: function(json, xhr)
            {
                 var erorFileMsg = "This file type is not allowed. upload a valid image.";
                 $('#challenge_form').prepend(renderPopUpMessage('error', erorFileMsg));
                 scrollToTopOfPage();
                 return false;
            }
        }   
    });
    
    $('#explanation, #question, .question-text-input, #directions').redactor({
        minHeight: 250,
        maxHeight: 250,
        limiter:20,
        imageUpload: admin_url+'configuration/redactore_image_upload',
        plugins: [], 
       buttonsHide: ['format', 'italic','deleted', 'lists','link','line'],
        callbacks: {
            imageUploadError: function(json, xhr)
            {
                 var erorFileMsg = "This file type is not allowed. upload a valid image.";
                 $('#challenge_form').prepend(renderPopUpMessage('error', erorFileMsg));
                 scrollToTopOfPage();
                 return false;
            }
        }   
    });
});

$(document).click(function(){
    $("#toolbar_wrapper").hide();
});
$("#toolbar_wrapper").click(function(e){
    e.stopPropagation();
  $("#toolbar_wrapper").css('display','block');
});
$(".redactor").click(function(e){
  e.stopPropagation();
    $("#toolbar_wrapper").css('display','block');
});

  // Restrict presentation length

</script>