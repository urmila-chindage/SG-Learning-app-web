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
    <script type="text/javascript" src="<?php echo assets_url() ?>js/system.js"></script>
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
                            <div class="builder-inner-from" id="descriptive_wrapper_form">
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
                                        <label><?php echo lang('descriptive_test_title') ?> *:</label>
                                    <input type="text" maxlength="80" placeholder="eg: Mathematical Calculations" id="test_title" name="test_title" value="<?php echo isset($lecture['cl_lecture_name'])?htmlentities($lecture['cl_lecture_name']):''; ?>" class="form-control">
                                    </div>
                                    
                                    <!-- Description area -->
                                    <div class="form-group clearfix">
                                        <label><?php echo lang('descriptive_test_description') ?> *:</label>
                                        <textarea class="form-control" id="test_description" onkeyup="validateMaxLength(this.id)" maxlength="1000" name="lecture_description" ><?php echo isset($lecture['cl_lecture_description'])?$lecture['cl_lecture_description']:''; ?></textarea>
                                        <label class="pull-right" id="test_description_char_left"> 
                                            <?php echo (1000 -strlen($lecture['cl_lecture_description'])).' '.lang('characters_left') ?></label>
                                    </div>
                            
                                
                                    <div class="form-group clearfix">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div>
                                                    <label>Submission Date *:</label>
                                                    <input type="text" placeholder="dd-mm-yyyy" class="form-control" id="descriptive_submission_date" value="<?php echo isset($test_details['dt_last_date'])?date("d-m-Y", strtotime($test_details['dt_last_date'])):''; ?>" autocomplete="off" readonly="" style="background: #fff;"/>
                                                   
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label><?php echo lang('total_mark') ?> *:</label>
                                                    <input type="number"  oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="4" onkeypress="return isNumber(event)" placeholder="eg: 100" min="1" value="<?php echo isset($test_details['dt_total_mark'])?$test_details['dt_total_mark']:''; ?>" max="999" id="total_mark" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <label>Words Limit:</label>
                                                <input type="number"  oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="8"  onkeypress="return isNumber(event)"  min="1" placeholder="eg: 1000" id="descriptive_words_limit" value="<?php echo isset($test_details['dt_words_limit'])?$test_details['dt_words_limit']:''; ?>"  id="descriptive_words_limit" class="form-control">
                                            </div>
                                        </div>
                                     </div>

                                  

                                    <div class="text-right">
                                        <input type="hidden" id="lecture_id" value="<?php echo $lecture['id'] ?>">
                                        <input type="hidden" id="course_id" value="<?php echo $lecture['cl_course_id'] ?>">
                                        <button type="button" onclick="saveDescriptiveTest()" class="btn btn-green"><?php echo lang('save') ?></button>
                                    </div>

                            </div>
                            <!-- !.Form elements Left side :: lecture Documents-->
                        </div>
                    </div>
                    <?php include_once "access_restriction.php" ?>
                    <?php include_once "support_files.php" ?>
                </div>
            </div>
            <!-- accordion ends here -->
    <script>
    var courseid             = $('#course_id').val();   
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
  