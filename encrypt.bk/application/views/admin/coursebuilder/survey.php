
<link rel="stylesheet" type="text/css" href="<?php echo assets_url() ?>font/Chanakya/fonts.css">
<?php include_once 'lecture_header.php'; ?>
<link rel="stylesheet" type="text/css" href="<?php echo assets_url() ?>css/survey.css">

    <!-- Manin Iner container start -->
    <div class='course-bulder-content-inner'>
   
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

    <div class="col-sm-6 builder-left-inner"><!-- !.Left side bar section -->
        <!-- accordion starts -->
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
                            <div class="builder-inner-from" id="survey_form">
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
                                    <label> Survey Name *:</label>
                                    <input type="text" maxlength="80" placeholder="eg: Survey on Course" id="lecture_name" name="lecture_name" value="<?php echo isset($lecture['cl_lecture_name'])?htmlentities($lecture['cl_lecture_name']):''; ?>" class="form-control">
                                </div>

                                <!-- Description area -->
                                <div class="form-group clearfix">
                                    <label> Survey Description:</label>
                                    <textarea class="form-control" id="lecture_description" onkeyup="validateMaxLength(this.id)" maxlength="1000" name="lecture_description" ><?php echo isset($lecture['cl_lecture_description'])?$lecture['cl_lecture_description']:''; ?></textarea>
                                    <label class="pull-right" id="lecture_description_char_left"> 
                                        <?php echo (1000 -strlen($lecture['cl_lecture_description'])).' '.lang('characters_left') ?></label>
                                </div>
                                <!-- !.Description area -->
                                <?php
                                    $survey_tutor = false;
                                    if(!empty($lecture['survey'])) {
                                        if(isset($lecture['survey']['s_tutor_id']) && $lecture['survey']['s_tutor_id'] != 0) {
                                            $survey_tutor = true;
                                        }
                                    }
                                ?>
                                <?php  $selected = 'selected="selected"'; ?>
                                <div class="form-group clearfix">
                                    <div class="col-sm-6 no-padding">
                                        <label>Survey Type *</label><br />
                                        <label class="pad-top10">
                                            <input type="radio" class="" name="survey_type" id="survey_regular" value="regular" <?php echo ($survey_tutor)?'':'checked' ?>> Regular
                                        </label>
                                        <label class="pad-top10" style="padding-left: 15px;">
                                            <input type="radio" class="" name="survey_type" id="survey_tutor" value="tutor" <?php echo ($survey_tutor)?'checked':'' ?>> Tutor Feedback
                                        </label>
                                    </div>
                                    <div class="col-sm-6 pad-top10" id="tutor_select" <?php echo ($survey_tutor)?'':'style="visibility:hidden;line-height: 21px;"' ?>>                                   
                                        <?php
                                        if($lecture['survey']['s_response_received'] == 0):
                                            ?>
                                            <select class="form-control" id="lecture_tutor">
                                                <option value="0">Choose Tutor</option>
                                                <?php if(!empty($tutors)):?>
                                                    <?php foreach($tutors as $tutor):?>
                                                        <option value="<?php echo $tutor['id'] ?>" <?php echo ($lecture['survey']['s_tutor_id'] == $tutor['id'])? $selected:'' ?>><?php echo $tutor['us_name'] ?></option>
                                                    <?php  endforeach; ?>
                                                <?php endif;?>
                                            </select>
                                            <?php
                                        else:
                                            ?>
                                            <?php if(!empty($tutors)):
                                                foreach($tutors as $tutor):
                                                if($lecture['survey']['s_tutor_id'] == $tutor['id']): ?>
                                                    <br/><label>Tutor :&nbsp; </label><label> <?php echo $tutor['us_name'] ?> </label>
                                                <?php endif;
                                                endforeach; 
                                            endif;?>
                                            <?php
                                        endif;
                                        ?>
                                        
                                        
                                    </div>
                                </div>

                                <div class="text-right">
                                    <input type="hidden" id="lecture_id" value="<?php echo $lecture['id'] ?>">
                                    <input type="hidden" id="course_id" value="<?php echo $lecture['cl_course_id'] ?>">
                                    <button type="button" onclick="saveSurveyLecture()" class="btn btn-green"><?php echo lang('save') ?></button>
                                </div>
                        </div>
                      </div>
                    </div>
                    <?php include_once "access_restriction.php" ?>
                </div>
        </div>
        <!-- accordion ends -->
    </div>  <!-- !.Left side bar section -->

               <!-- top Header -->
        <div class="buldr-header inner-buldr-header clearfix">
            <div class="pull-left">
                <div class="lecture-icon-big <?php echo $lecture_icons[$lecture['cl_lecture_type']]['parent'] ?>" style="padding-top: 7px;">
                    <i class="icon <?php echo $lecture_icons[$lecture['cl_lecture_type']]['child'] ?>"></i>
                </div>
                <h3 id="lecture_name_title"><?php echo $lecture['cl_lecture_name']; ?></h3>
            </div>
            <div class="pull-right rite-side">
                <a id="lecture_action_status_<?php echo $lecture['id'] ?>" class="btn <?php echo $status_button ?>"   onclick="changeLectureStatus('<?php echo $lecture['cl_status'] ?>', '<?php echo base64_encode(htmlentities($lecture['cl_lecture_name'])); ?>', '<?php echo $lecture['id'] ?>')"><?php echo strtoupper(lang($status_label)) ?></a>
                <a href="<?php echo admin_url('coursebuilder/home/'.$lecture['cl_course_id']) ?>"><button class="btn btn-red" id="back_button"><i class="icon icon-left"></i><?php echo lang('back') ?></button></a>
            </div>
        </div>
        <!-- !.top Header --> 


    <div class="col-sm-6 builder-right-inner"> <!-- right side bar section -->
        
        <!-- Preivew of  test content will show here -->
        <div class="preivew-area test-content">
        <?php 
            $question_type  = array( '1' => 'Radio', '2' => 'Checkbox', '3' => 'Text', '4' => 'Range', '5' => 'Dropdown');            
        ?>

            <!-- survey starts here -->
            <div class="survey-row">
				<div class="survey-title-info text-center">
					<h2 id="survey_title">PREVIEW - <?php echo $lecture['cl_lecture_name']; ?></h2>
                    <?php
                    $param  = array('survey_id' => $lecture['survey']['survey_id'], 'lecture_id'=>$lecture['id']);
                    ?>
                    <?php 
                  
                    $module = $this->accesspermission->get_permission(array('role_id' => $this->__loggedInUser['role_id'], 'module' => 'report'));  
                    if(in_array(1, $module))
                    {  
                    ?>
					<a class="report-response" href="<?php echo admin_url() ?>report/survey_report/<?php echo base64_encode(json_encode($param)) ?>">View students responses</a>
                    <?php } ?>
                    </div>
            </div>
            <div class="survey-wrapper">
			</div>

                <div class="survey-option-row">
                <div class="form-group col-md-12 col-sm-12 clearfix">
                        <button type="" class="add-survey-btn btn btn-default add-new-question" onclick="addQuestion()" id="add_question">ADD QUESTION</button>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- right side bar section -->

</div>
    <script>
        var __lecture_id    = '<?php echo $lecture_id ?>';
        var __course_id     = '<?php echo $lecture['cl_course_id'] ?>';
        var __survey_id     = '<?php echo $lecture['survey']['survey_id']; ?>';
        var __courseName    = atob('<?php echo base64_encode($lecture['course_name']) ?>');
        var __lectureName   = atob('<?php echo base64_encode($lecture['cl_lecture_name']) ?>');
        var __tutors        = '<?php echo json_encode($tutors); ?>';
        
        $(document).ready(function(){
            
            $('#survey_tutor, #survey_regular').on('change', function(){
                if ($('#survey_tutor').is(':checked')) {
                    $("#tutor_select").css('visibility', 'visible');
                    var tutorHtml = '';
                        tutorHtml += '<select class="form-control" id="lecture_tutor">';
                        tutorHtml += '<option value="0">Choose Tutor</option>';
                        if(Object.keys(__tutors).length>0){
                            var tutors = $.parseJSON(__tutors);
                            for(var i=0;i<tutors.length;i++){
                                tutorHtml += '<option value="'+tutors[i]['id']+'">'+tutors[i]['us_name']+'</option>';
                            }

                        }
                        tutorHtml += '</select>';
                        $("#tutor_select").html(tutorHtml);
                } else {
                    $('#lecture_tutor').val('0');
                    $("#tutor_select").css('visibility', 'hidden');
                }
            });
        });
        var __questions     = atob('<?php echo base64_encode(json_encode($lecture['questions'])); ?>');
    </script>
<script src="<?php echo assets_url() ?>js/jquery.form-validator.min.js" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/survey.js"></script>
<?php include_once 'lecture_footer.php'; ?>
<?php $error    = $this->session->flashdata('error');  ?>
<?php if($error): ?>
<script type="text/javascript">
    var messageObject = {
        'body':'<?php echo $error ?>',
        'button_yes':'OK',
        'prevent_button_no': true
    };
    callback_danger_modal(messageObject);
</script>
<?php endif; ?>

<?php $message    = $this->session->flashdata('message');  ?>
<?php if($message): ?>
<script type="text/javascript">
    var messageObject = {
        'body':'<?php echo $message ?>',
        'button_yes':'OK',
        'prevent_button_no': true
    };
    callback_success_modal(messageObject);
</script>
<?php endif; ?>


<style>
body {
    /* Set "my-sec-counter" to 0 */
    counter-reset: survey-question-counter;
}

div.question-counter::before {
    /* Increment "my-sec-counter" by 1 */
    counter-increment: survey-question-counter;
    content: "Q." counter(survey-question-counter);
}
</style>
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
  