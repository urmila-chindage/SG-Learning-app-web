<?php include_once 'coursebuilder/lecture_header.php';
//print_r($test);
//die();
?>
<!-- ADDING REDACTOR PLUGIN INSIDE -->
<!-- ############################# --> <!-- START -->
    <link rel="stylesheet" href="<?php echo assets_url() ?>css/redactor/css/redactor.css" />
    <link rel="stylesheet" href="<?php echo assets_url() ?>css/redactor/css/alignment.css" />

<!-- ############################# --> <!-- END -->
<link rel="stylesheet" href="<?php echo assets_url() ?>css/datepicker.min.css">
<link rel="stylesheet" href="<?php echo assets_url() ?>css/timepicker.css">

<link href="<?php echo assets_url() ?>css/bootstrap-tagsinput.css" rel="stylesheet" type="text/css" />
<!--Tag input js-->
<script src="<?php echo assets_url() ?>js/bootstrap-tagsinput.js" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/datepicker.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/datepicker.en.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/jquery.timepicker.js"></script>
<!-- MAIN TAB --> <!-- STARTS -->
<?php include_once('test_header.php'); ?>
<div class="right-wrap small-width base-cont-top-heading container-fluid pull-right rightwrap-top-update">
    <br/>
    <div class="list-group test-listings">
      <a href="javascript:void(0)" class="list-group-item active">
        <span class="font15">Instructions</span>
      </a>
      <a href="javascript:void(0)" class="list-group-item link-style">
          <span class="green-span"><i class="icon icon-ok-circled"></i></span>          
          <span class="listing-text">For unlimited attempts mark <b>Number of Attempts</b> as <b>0</b>.</span></a>
      <a href="javascript:void(0)" class="list-group-item link-style">
          <span class="green-span"><i class="icon icon-ok-circled"></i></span> 
          <span class="listing-text"><b>Total question</b> and <b>Total marks</b> can be changed in STEP: 3 as per requirement.</span></a>
    </div>
    <!--  Adding list group  --> <!-- END  -->
</div>

<section class="content-wrap small-width base-cont-top-heading content-top-update">
    <!-- LEFT CONTENT --> <!-- STARTS -->

    <!-- ===========================  -->
    <div class="left-wrap col-sm-12 pad0">
        <!-- Nav section inside this wrap  --> <!-- START -->
        <!-- =========================== -->
        <div class="container-fluid course-create-wrap">
            <div class="row-fluid course-cont">
                <div class="col-sm-12">
                    <div class="form-horizontal" id="course_form"> 
                        <form class="form-horizontal" id="save_test_basics" method="post" action="<?php echo admin_url('test_manager/test_basics'.'/'.base64_encode($test['id'])); ?>" enctype="multipart/form-data">
                            <div class="each-steps" id="step-one">
                            <div class="form-group" style="<?php echo ($course_details['cb_has_lecture_image'] == 1) ? "display:block" : "display:none" ?>">
                            <div class="col-sm-8" >
                            <?php echo lang('lecture_image') ?> :
                                        <div class="section-create-wrapper text-center">
                                            <div class="section-card-container">
                                                <div class="section-card">
                                                <?php $version = rand(); ?>

                                                <?php $lecture_image = explode('?', $test['cl_lecture_image']); 
                                                    
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
                                                        $lecture_image_url = ($test['cl_lecture_image'] && file_exists(course_lecture_upload_path_document(
                                                        array('course_id' => $test['course_id'])). $lecture_image[0])) ? course_lecture_image_path(
                                                        array('course_id' => $test['course_id'])) . $test['cl_lecture_image'] : default_course_path()."default-lecture.jpg";
                                                    }            
                                    ?>

                                                    <img id="lecture_image_add" data-id="<?php ?>" image_name="<?php echo (isset($test['cl_lecture_image']))  ?  $test['id'] : "default-lecture.jpg"; ?>" class="img-responsive" 
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
                            </div>
                                <div class="form-group">
                                    <div class="col-sm-8">
                                        Test Name* :
                                        <input type="text"  maxlength="50" name="test_name" class="form-control" id="test_name" value="<?php echo $test['cl_lecture_name']; ?>" placeholder="Test Name" />
                                    </div>

                                    <div class="col-sm-4">
                                        Duration (In Min.)* :
                                        <div class="input-group">
                                            <input type="text" name="test_duration" onkeypress="return isNumber(event)" id="test_duration" class="form-control" value="<?php echo $test['a_duration']; ?>" placeholder="Eg: 30" aria-describedby="basic-addon1" data-validation="number">
                                            <span class="input-group-addon" id="basic-addon1">MIN</span>
                                        </div>
                                    </div>

                                    <?php /* ?>
                                    <div class="col-sm-6">
                                        Select Category* :
                                        <select id="test_category" name="test_category" class="form-control">
                                            <option value="0">Select Category* :</option>
                                        <?php foreach($categories as $category): ?>
                                            <option <?php echo $test['a_category']==$category['id']?'selected="selected"':''; ?> value="<?php echo $category['id']; ?>"><?php echo $category['ct_name']; ?></option>
                                        <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <?php */ ?>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-4">
                                        Total Marks :
                                        <input type="text" name="test_mark" onkeypress="return isNumber(event)" maxlength="3" <?php if($test['a_mark']!=0){ ?> value="<?php echo $test['a_mark']; ?>" <?php } else { ?>placeholder="Eg : 100"<?php } ?>  class="form-control" id="test_mark"  />
                                    </div>
                                    <div class="col-sm-4">
                                        Number of Attempts* (0 for unlimited) :
                                        <input type="number" min="0" name="cl_limited_access" oninput="this.value = Math.abs(this.value)" maxlength="4"  step="1"
                                        value="<?php echo $test['cl_limited_access']; ?>" class="form-control" id="cl_limited_access" placeholder="Eg : 5" />
                                       
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-12">
                                         Default Instruction* :
                                        <?php $current_language     = isset($active_lang)?$active_lang:1; ?>
                                        <div id="text_area_holder" style="border:1px solid #c7c9ca;">

                                            <textarea id="a_instruction" name="a_instruction"><?php $test['a_instructions'] = json_decode($test['a_instructions'],true); echo isset($test['a_instructions'][$current_language])?($test['a_instructions'][$current_language]):$default_instruction; ?></textarea>
                                            <input type="hidden" name="active_language" value="<?php echo $current_language; ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <input onclick="saveNext();" type="button" id="saveNext_button" class="pull-right btn btn-green marg10" data-div="step-two" value="SAVE & NEXT">
                                    <input onclick="save();" type="button" id="save_button" class="pull-right btn btn-green marg10" data-div="step-two" value="SAVE">
                                </div>
                            </div>
                            <input type="hidden" name="savenext" id="savenextform" value="0">
                            <input type="hidden" name="submitted" id="submittedform" value="0">
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- =========================== -->
        <!-- Nav section inside this wrap  --> <!-- END -->
    </div>
    <!-- ==========================  -->
    <!--  LEFT CONTENT--> <!-- ENDS -->
</section>

<!-- JS -->
<script type="text/javascript">
    var __languages         = '<?php echo json_encode($languages); ?>';
    var __tot_mark          = '<?php echo $total_mark; ?>';
</script>
<script src="<?php echo assets_url() ?>js/redactor/js/redactor.js"></script>
<script src="<?php echo assets_url() ?>js/redactor/js/table.js"></script>
<script src="<?php echo assets_url() ?>js/redactor/js/alignment.js"></script>
<script src="<?php echo assets_url() ?>js/test_basics.js"></script>

<?php include_once 'footer.php';?>
<script>
var __course_loaded_img  = $('#lecture_image_add').attr('src');
var cb_has_lecture_image =  '<?php echo $course_details['cb_has_lecture_image'] ?>';

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