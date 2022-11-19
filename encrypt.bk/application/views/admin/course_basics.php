<?php include_once 'training_header.php';?>

<!-- ############################# --> <!-- START -->
<link rel="stylesheet" href="<?php echo assets_url() ?>css/redactor/css/redactor.css" />
<link rel="stylesheet" href="<?php echo assets_url() ?>css/redactor/css/alignment.css" />

<!-- ############################# --> <!-- END -->
<link rel="stylesheet" href="<?php echo assets_url() ?>css/datepicker.min.css">
<link rel="stylesheet" href="<?php echo assets_url() ?>css/bootstrap-multiselect.css">
<link rel="stylesheet" href="<?php echo assets_url() ?>css/jquery-ui.css">


<style>
.billing-table td{padding:10px 20px;border:1px solid #ccc;}
.table-holder{padding: 0 15px;}
.close-activity-btn {
    font-size: 26px;
    font-weight: 800;
    color: #f70000;
    cursor: pointer;
    user-select: none;
}
.padding-bottom-12 {
    padding-bottom: 12px;
}
</style>
<!--Tag input js-->
<script src="<?php echo assets_url() ?>js/bootstrap-tagsinput.js" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/datepicker.js"></script>
<!-- <script type="text/javascript" src="<?php echo assets_url() ?>js/datepicker.en.js"></script> -->
<script type="text/javascript" src="<?php echo assets_url() ?>js/bootstrap-multiselect.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/jquery-ui.min.js"></script>

<section class="content-wrap cont-course-big top-spacing content-wrap-align">
    <!-- LEFT CONTENT --> <!-- STARTS -->
    <!-- ===========================  -->
    <div class="left-wrap col-sm-12 pad0">
        <!-- Nav section inside this wrap  --> <!-- START -->
        <!-- =========================== -->
        <div class="container-fluid course-create-wrap course-settings">

            <div class="row-fluid course-cont">
                <div class="col-sm-12">
                    <div class="form-horizontal" id="course_form">
                        <?php include_once 'messages.php'?>

                        <form class="form-horizontal" name="Form" onsubmit="return validateForm();" id="save_course_basics" enctype="multipart/form-data" method="post" action="<?php echo admin_url('course_settings/basics/' . $id); ?>">
                            <!-- Text Box  -->
                            <div class="form-group">
                                <div class="col-sm-5">
                                    Course Image
                                    <div class="upload-prieview course-image-preview">
                                        <div class="course-card-placeholder">
                                            <div class="settings-logo" style="height: 200px;">
                                                <img id="site_logo" data-id="<?php echo $cb_image; ?>" class="img-responsive" src="<?php echo (($cb_image == 'default.jpg') ? default_course_path() : course_path(array('course_id' => $id))) . $cb_image; ?>">
                                            </div>
                                            <input name="cb_image" class="fileinput logo-image-upload-btn" id="site_logo_btn" accept="image/*" type="file">
                                            <button class="btn btn-green pos-abs">CHANGE IMAGE</button>
                                        </div>
                                    </div>
                                    <span>Allowed Minimum File Size : 740px x 452px</span>
                                </div>
                                <div class="col-sm-7">
                                    <div class="course-settings-title">
                                        <span><?php echo lang('course_title_lbl') ?> </span>
                                        <input type="text" maxlength="50" name="cb_title" class="form-control" id="cb_title"  value="<?php echo isset($cb_title) ? $cb_title : ''; ?>" placeholder="<?php echo lang('course_tilte_ph') ?>" />
                                    </div>
                                    <div class="course-settings-title">
                                        <span>Course Category * :</span>
                                        <div class="">
                                            <?php if(!empty($categories)): ?>
                                                <select id="listing_category" name="cb_category[]" class="multiselect" multiple="multiple">
                                                    <?php foreach ($categories as $category): ?>
                                                        <option value="<?php echo $category['id']; ?>" <?php echo in_array($category['id'], $cb_category) ? 'selected' : ''; ?>><?php echo $category['ct_name'] ?></option>
                                                    <?php endforeach;?>
                                                </select>
                                            <?php else: ?>
                                                <a class="text-blue pad-top10" target="_blank" href="<?php echo admin_url('question_manager') ?>">Click here to create a category</a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="course-settings-title">
                                        <span>Course Language :</span>
                                        <div class="">
                                            <!-- multiselect: -->
                                            <select id="listing_language" name="cb_language[]" class="multiselect" multiple="multiple">
                                                <?php foreach ($languages as $language): ?>
                                                    <option value="<?php echo $language['id']; ?>" <?php echo in_array($language['id'], $cb_language) ? 'selected' : ''; ?>><?php echo $language['cl_lang_name'] ?></option>
                                                <?php endforeach;?>
                                            </select>
                                            <!-- multiselect: ends -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                           
                            <div class="form-group clearfix " >
                                <div class="col-sm-12">  <?php echo lang('course_what_you_will_get') ?>
                                    <div class="col-sm-12 " id="what_u_get_wrapper">
                                        <?php if(isset($cb_what_u_get) && !empty($cb_what_u_get)): foreach($cb_what_u_get as $key=>$cb_what):?>
                                            <div class="row activities-row" data-id="<?php echo $key?>" id="cb_what_u_get_row_data<?php echo $key?>"> 
                                                <div class="clearfix"></div> 
                                                <div class="col-sm-11 padding-bottom-12"> 
                                                    <input class="form-control " name="cb_what_u_get[]" value="<?php echo trim($cb_what);?>"> 
                                                </div> 
                                                <div class="col-sm-1"> 
                                                    <span onclick="removeActivity('cb_what_u_get_row_data<?php echo $key?>', '<?php echo $key?>')" class="close-activity-btn">×</span> 
                                                </div>
                                            </div>
                                        <?php endforeach; endif;?>
                                    </div>
                                    <div class="col-sm-12 text-center add-rule-btn" onclick="generateActivity({'id':'what_u_get_wrapper','name':'cb_what_u_get'})">
                                        <a href="javascript:void(0)" class="add-new-activity-btn" >Add New </a>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group clearfix " >
                                <div class="col-sm-12">  <?php echo lang('requirements') ?>
                                    <div class="col-sm-12 " id="requirements">
                                        <?php if(isset($cb_requirements) && !empty($cb_requirements)): foreach($cb_requirements as $key=>$cb_requirements):?>
                                            <div class="row activities-row" data-id="<?php echo $key?>" id="cb_requirements_row_data<?php echo $key?>"> 
                                                <div class="clearfix"></div> 
                                                <div class="col-sm-11 padding-bottom-12"> 
                                                    <input class="form-control " name="cb_requirements[]" value="<?php echo trim($cb_requirements);?>"> 
                                                </div> 
                                                <div class="col-sm-1"> 
                                                    <span onclick="removeActivity('cb_requirements_row_data<?php echo $key?>', '<?php echo $key?>')" class="close-activity-btn">×</span> 
                                                </div>
                                            </div>
                                        <?php endforeach; endif;?>
                                    </div>
                                    <div class="col-sm-12 text-center add-rule-btn" onclick="generateActivity({'id':'requirements','name':'cb_requirements'})">
                                        <a href="javascript:void(0)" class="add-new-activity-btn" >Add New </a>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Text Box Addons  -->
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <?php echo lang('course_short_description_lbl') ?>
                                    <textarea name="cb_short_description" id="cb_short_description" maxlength="1000"  class="form-control" rows="3" ><?php echo isset($cb_short_description) ? $cb_short_description : ''; ?></textarea>

                                </div>
                            </div>
                            <!-- Text Box Addons  -->
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <?php echo lang('course_description_lbl') ?>
                                    <textarea name="cb_description" id="cb_description" maxlength="1000"  class="form-control" rows="3" ><?php echo isset($cb_description) ? $cb_description : ''; ?></textarea>

                                </div>
                            </div>

                            <!-- This is a test message  -->
                            <div class="form-group">
                                <div class="col-sm-6">
                                    <?php echo lang('course_access_lbl') ?> <br/>
                                    <label class="pad-top10">
                                        <input type="radio" name="cb_access_validity"  <?php $checked = "checked";
echo ($cb_access_validity == 0) ? $checked : '';?> id="cb_access_unlimited" checked  value="0" >
                                        <?php echo lang('course_access_unlimited') ?>
                                    </label>
                                    <label>
                                        <input type="radio" name="cb_access_validity" <?php $checked = "checked";
echo ($cb_access_validity == 1) ? $checked : '';?> id="cb_access_limited" value="1" >
                                        <?php echo lang('course_access_limited') ?>
                                    </label>
                                    <label>
                                        <input type="radio" name="cb_access_validity" <?php $checked = "checked";
echo ($cb_access_validity == 2) ? $checked : '';?> id="cb_access_limited_by_date"
                                        value="2" >
                                        <?php echo lang('course_access_limited_by_date') ?>
                                    </label>

                                </div>
                                <div class="col-sm-6" id="course_validity">
                                    <?php echo lang('course_validity_lbl') ?>
                                    <div class="">

                                        <div class="input-group">
                                            <input type="text" onkeypress="return preventAlphabets(event)" min="1" class="form-control cb_validity" name="cb_validity" value="<?php echo isset($cb_validity) && $cb_validity!='0' ? $cb_validity : '' ?>" placeholder="eg: 180" aria-describedby="basic-addon1" id="byday">
                                            <span class="input-group-addon" id="basic-addon1">Days</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-6" id="course_validity_date">
                                    <?php echo lang('course_validity_lbl') ?>
                                    <div class="">
                                        <input type="text" style="background-color:white" readonly name="cb_validity_date" placeholder="To Date" class="form-control" id="by_date" autocomplete="off" value="<?php echo ($cb_validity_date=="0000-00-00" || $cb_validity_date ==  '') ? '':date("d-m-Y", strtotime($cb_validity_date)) ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-6">

                                    <span>Student Self Enrollment to Course:</span>
                                    <br>
                                    <label class="pad-top10">
                                        <input type="radio" name="cb_has_self_enroll" id="self_enroll_no" value="0" class="self_enroll" <?php echo ($cb_has_self_enroll == '0') ? 'checked' : ''; ?>  >
                                        Disabled
                                    </label>
                                    <label class="pad-top10">
                                        <input type="radio" name="cb_has_self_enroll" id="self_enroll_yes" value="1" class="self_enroll"  <?php echo ($cb_has_self_enroll == '1') ? 'checked' : ''; ?>  >
                                        Enabled
                                    </label>
                                    <!-- <label class="text-blue pad-top10">Self enrollment needs admin approval to access course</label> -->
                                </div>
                                <div class="col-sm-6" id="course_self_date_block" style="display:none">

                                    <span>Close Student Self Enrollment on :</span>
                                    <br>
                                    <div class="pad-top10">
                                        <input type="text" readonly name="cb_self_enroll_date" placeholder="Closes on" class="form-control custom-date-picker" id="close_date" autocomplete="off" value="<?php echo isset($cb_self_enroll_date) ?date("d-m-Y", strtotime($cb_self_enroll_date)) : '' ?>">
                                    </div>

                                </div>
                            </div>


                             <div class="form-group">
                                <div class="col-sm-6">
                                    <!-- <?php echo lang('course_certificate_settings') ?><br/> -->
                                    <span>Course Certificate:</span>
                                    <br>
                                    <label class="control-label">
                                        <input type="radio" name="cb_has_certificate" value="0" <?php $checked = "checked";
echo ($cb_has_certificate == 0) ? $checked : '';?>  id="cb_certificate_enable" checked>
                                        <!-- <?php echo lang('course_certificate_lbl') ?> -->
                                        <span>Disabled</span>
                                    </label>
                                    <?php /* */?>
                                    <label>
                                        <input type="radio" name="cb_has_certificate" <?php $checked = "checked";
echo ($cb_has_certificate == 1) ? $checked : '';?> value="1"  id="cb_certificate_enable">
                                        <!-- <?php echo lang('course_certificate_lbl') ?> -->
                                        <span>Enabled</span>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-6">
                                    <!-- <br/> -->
                                    <span><?php echo lang('review_and_rating') ?></span>
                                    <br>
                                    <label class="control-label">
                                        <input type="radio" name="cb_has_rating" value="0" <?php $checked = "checked";
                                            echo (isset($cb_has_rating) && $cb_has_rating == 0) ? $checked : '';?>  id="cb_has_rating" checked>
                                        <!-- <?php echo lang('course_certificate_lbl') ?> -->
                                        <span>Disabled</span>
                                    </label>
                                    <?php /* */?>
                                    <label>
                                        <input type="radio" name="cb_has_rating" <?php $checked = "checked";
                                            echo (isset($cb_has_rating) && $cb_has_rating == 1) ? $checked : '';?> value="1"  id="cb_has_rating">
                                        <!-- <?php echo lang('course_certificate_lbl') ?> -->
                                        <span>Enabled</span>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-6">
                                    <!-- <br/> -->
                                    <span>Total Enrolled : </span>
                                    <br>
                                    <label class="control-label">
                                        <input type="radio" name="cb_has_show_total_enrolled" value="0" <?php $checked = "checked";
echo (isset($cb_has_show_total_enrolled) && $cb_has_show_total_enrolled == 0) ? $checked : '';?>  id="cb_has_show_total_enrolled" checked>
                                        <!-- <?php echo lang('course_certificate_lbl') ?> -->
                                        <span>Disabled</span>
                                    </label>
                                    <?php /* */?>
                                    <label>
                                        <input type="radio" name="cb_has_show_total_enrolled" <?php $checked = "checked";
echo (isset($cb_has_show_total_enrolled) && $cb_has_show_total_enrolled == 1) ? $checked : '';?> value="1"  id="cb_has_show_total_enrolled">
                                        <!-- <?php echo lang('course_certificate_lbl') ?> -->
                                        <span>Enabled</span>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-6">
                                    <!-- <br/> -->
                                    <span>Lecture image : </span>
                                    <br>
                                    <label class="control-label">
                                        <input type="radio" name="cb_has_lecture_image" value="0" <?php $checked = "checked";
echo (isset($cb_has_lecture_image) && $cb_has_lecture_image == 0) ? $checked : '';?>  id="cb_has_lecture_image" checked>
                                        <!-- < ?php echo lang('course_certificate_lbl') ?> -->
                                        <span>Disabled</span>
                                    </label>
                                    <?php /* */?>
                                    <label>
                                        <input type="radio" name="cb_has_lecture_image" <?php $checked = "checked";
echo (isset($cb_has_lecture_image) && $cb_has_lecture_image == 1) ? $checked : '';?> value="1"  id="cb_has_lecture_image">
                                        <!-- < ?php echo lang('course_certificate_lbl') ?> -->
                                        <span>Enabled</span>
                                    </label>
                                </div>
                            </div>


                            <div class="form-group">
                                <div class="col-sm-4" id="course_self_date_block" >
                                    <span>Total Hours of Videos :</span>
                                    <br>
                                        <div class="pad-top10">
                                            <input type="text" onkeypress="return isNumber(event)"  name="cb_total_video_hours" placeholder="video Hours" class="form-control" id="cb_total_video_hours" autocomplete="off" value="<?php echo isset($cb_total_video_hours) ?$cb_total_video_hours : '0' ?>">
                                        </div>
                                </div>
                            </div>

                             <div class="form-group">
                                <div class="col-sm-4" id="course_self_date_block" >
                                        <label class="control-label">
                                                <input type="checkbox" name="cb_is_free" value="1" <?php $checked = "checked";
        echo ($cb_is_free == 1) ? $checked : '';?>  id="cb_is_free" >
                                                <span>Free Course</span>
                                            </label>
                                    </div>
                                    
                            </div>
                            <div id="course_price_setup" style="<?php $styles = "display:none;";
    echo ($cb_is_free == 1) ? $styles : '';?>">
                            <div class="form-group">
                                <div class="col-sm-3">
                                    <span>Course Preview:</span>
                                    <br>
                                    <label class="control-label">
                                        <input type="radio" class="cb_preview" name="cb_preview" <?php echo ($cb_preview == 0) ? 'checked' : ''; ?> value="0" id="cb_preview_no">

                                        <span>Disabled</span>

                                    </label>
                                    <label class="control-label">
                                        <input type="radio" class="cb_preview" name="cb_preview" <?php echo ($cb_preview == 1) ? 'checked' : ''; ?> value="1" id="cb_preview_yes">

                                        <span>Enabled</span>
                                    </label>

                                </div>
                                <div class="col-sm-3" id="course_preview_time" style="display:none">
                                    <span>Allowed preview time :</span>
                                    <br>
                                    <label class="pad-top10">
                                        <div class="input-group">
                                             <input type="text" pattern="\d*" min="1" max="99" maxlength="2" step="any" class="form-control" id="course_preview_time_text" name="cb_preview_time" value="<?php
echo isset($cb_preview_time) ? ($cb_preview_time/60) : '' ?>" placeholder="15" >
                                            <span class="input-group-addon" id="basic-addon1">mins</span>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-4" id="course_self_date_block" >
                                        <span>Course Price :</span>
                                        <br>
                                        <div class="pad-top10">
                                            <input type="text" onkeypress="return isNumber(event)"  name="cb_price" placeholder="Course price" class="form-control" id="cb_price" autocomplete="off" value="<?php echo isset($cb_price) ?$cb_price : '0' ?>">
                                        </div>
                                    </div>
                                    <div class="col-sm-4" id="course_self_date_block" >
                                        <span>Discount Course Price :</span>
                                        <br>
                                        <div class="pad-top10">
                                            <input type="text" onkeypress="return isNumber(event)"  name="cb_discount" placeholder="Course discount price" class="form-control" id="cb_discount_price" autocomplete="off" value="<?php echo isset($cb_discount) ?$cb_discount : '0' ?>">
                                        </div>

                                    </div>
                                    <div class="col-sm-4">
                                        <span>Tax :</span>
                                        <br>
                                        <label class="control-label">
                                            <input type="radio" name="cb_tax_method" value="0" <?php $checked = "checked";
    echo ($cb_tax_method == 0) ? $checked : '';?>  id="cb_tax_inclusive" checked>
                                            <span>Inclusive of tax</span>
                                        </label>
                                        <label>
                                            <input type="radio" name="cb_tax_method" <?php $checked = "checked";
    echo ($cb_tax_method == 1) ? $checked : '';?> value="1"  id="cb_tax_exclusive">
                                            <span>Exclusive of tax</span>
                                        </label>
                                </div>

                            </div>
                                <?php 
                                $course_price       = ($cb_discount!=0)?$cb_discount:$cb_price;
                                $sgst_price         = ($sgst / 100) * $course_price;
                                $cgst_price         = ($cgst / 100) * $course_price;
                                $total_course_price = $course_price+$sgst_price+$cgst_price;
                                ?>
                                <div id="tax-table" class="form-group table-holder">
                                    <table class="billing-table">
                                        <tr>
                                            <td>Course Price</td>
                                            <td class="text-right"><span id="course_price"><?php echo $course_price; ?></span> Rs</td>
                                        </tr>
                                        <tr>
                                            <td>CGST(<?php echo $cgst; ?>%)</td>
                                            <td class="text-right"><span id="cgst_price"><?php echo round($sgst_price,2); ?></span> Rs</td>
                                        </tr>
                                        <tr>
                                            <td>SGST(<?php echo $sgst; ?>%)</td>
                                            <td class="text-right"><span id="sgst_price"><?php echo round($cgst_price,2); ?></span> Rs</td>
                                        </tr>
                                        <tr>
                                            <td><b>Total</b></td>
                                            <td class="text-right"><b><span id="total_course_price"><?php echo round($total_course_price,2); ?></span> Rs</b></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                   
                          

                            <div class="form-group">
                                <div class="col-sm-12">
                                    <!-- <input type="submit" id="course_savenext_button" class="pull-right btn btn-green marg10" value="SAVE & NEXT"> -->
                                    <a href="javascript:void('0')" id="course_savenext_button" class="pull-right btn btn-green marg10">SAVE & NEXT</a>
                                    <!-- <input type="submit" id="course_save_button" class="pull-right btn btn-green marg10" value="<?php //echo lang('save') ?>"> -->
                                    <a href="javascript:void('0')" id="course_save_button" class="pull-right btn btn-green marg10"><?php echo lang('save') ?></a>

                                </div>
                            </div>
                            <input type="hidden" name="savenext" id="savenextform" value="0">
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

<script type="text/javascript">
    var __byDateDefault = '';
    var __closeDateDefault = '';
    var __course_loaded_img = '';
    var __course_id = '<?php echo $id;?>';

    $(document).ready(function() {
        tax_price_table();
        __course_loaded_img = $('#site_logo').attr('src');
        __byDateDefault = $('#by_date').val();
        __closeDateDefault = $('#close_date').val();
        $('#listing_category').multiselect({
            includeSelectAllOption: ($('#listing_category option').length>1),
            buttonWidth:'100%',
            numberDisplayed:6
        });
        $('#listing_language').multiselect({
            includeSelectAllOption: ($('#listing_language option').length>1),
            buttonWidth:'100%',
            numberDisplayed:6
        });

        setTimeout(function(){
            $('.message_container').remove();
        }, 3000);
    });
    $( function() {
        $( "#by_date,#close_date" ).datepicker({
            language: 'en',
            minDate: 'today',
            dateFormat: 'dd-mm-yy',
            autoClose: true
        });
    });
   
</script>

<!-- JS -->
<?php include_once 'footer.php';?>
<script src="<?php echo assets_url() ?>js/redactor/js/redactor.js"></script>
<script src="<?php echo assets_url() ?>js/redactor/js/table.js"></script>
<script src="<?php echo assets_url() ?>js/redactor/js/alignment.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/course_settings.js"></script>
<script type="text/javascript">
    var __cgst          = Number('<?php echo $cgst; ?>');
    var __sgst          = Number('<?php echo $sgst; ?>');
    var __course_price  = Number(($("#cb_discount_price").val()!=0)?$("#cb_discount_price").val():$("#cb_price").val());
    var __activityCount = Number('<?php echo 0; /*count($restriction_activities)*/ ?>')+1;
    var __lectureOptionHtml     = '';
    $("#cb_is_free").click(function()
    {
        if ($('#cb_is_free').is(':checked')) 
        {
            $("#course_price_setup").hide();
        }
        else 
        {
            $("#course_price_setup").show();
        }
    });

    $('input[type=radio][name=cb_tax_method]').change(function() {
        tax_price_table();
    });

    $("#cb_price").keyup(function() 
    {
            tax_price_table();
    });

    $("#cb_discount_price").keyup(function() {
        if(($("#cb_discount_price").val()!='') || ($("#cb_discount_price").val()!='0'))
        {
            if((Number($("#cb_discount_price").val()))>=(Number($("#cb_price").val())))
            {
                $("#cb_discount_price").val('0');
                tax_price_table();
                errorMessage='Discount price should be less than course price.';
                $('#course_form').prepend(renderPopUpMessage('error', errorMessage));
                goTo();
                return false;
            }
            
        }
        tax_price_table();

    });

    function isNumber(evt) {
        evt = (evt) ? evt : window.event;
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        // if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        //     return false;
        // }
        if ((evt.which != 46 || $(this).val().indexOf('.') != -1) && (evt.which < 48 || evt.which > 57)) {
            evt.preventDefault();
            return false;
        }
        return true;
    }
 
    function tax_price_table()
    {
        __course_price  = Number(($("#cb_discount_price").val()!=0)?$("#cb_discount_price").val():$("#cb_price").val());
        if(Number($("#cb_discount_price").val())>Number($("#cb_price").val()))
        {
            __course_price  =  Number($("#cb_price").val());
        }
        if($('input[name=cb_tax_method]:checked').val()=='0')
        {
            __course_price         = (__course_price/(100+__sgst+__cgst))*100;
            var sgst_price         = __course_price *  (__sgst/100);
            var cgst_price         = (__cgst / 100) * __course_price;
            var total_course_price = __course_price+sgst_price+cgst_price;
        }
        else  if($('input[name=cb_tax_method]:checked').val()=='1')
        {
            var sgst_price         = (__sgst / 100) * __course_price;
            var cgst_price         = (__cgst / 100) * __course_price;
            var total_course_price = __course_price+sgst_price+cgst_price;
        }
        $("#course_price").html(__course_price.toFixed(2));
        $("#sgst_price").html(sgst_price.toFixed(2));
        $("#cgst_price").html(cgst_price.toFixed(2));
        $("#total_course_price").html(total_course_price.toFixed(2));
        $("#tax-table").show();

    }
    var error = 0;
    function validateForm()
    {
        var a = $('#listing_category').val();
        var GivenDate = $('#close_date').val();
        var accessDays = $('#byday').val();
        var accessDate = $('#by_date').val();
        var previewTime = $('#course_preview_time_text').val().trim();
        var previewImage = $('#site_logo').attr('data-id');
        var cb_short_description = $('#cb_short_description').val().trim();
        
        if(previewImage=='default.jpg'){
            //var previewImageCheck = $('#site_logo_btn').val();
            //if(previewImageCheck==''){
                errorMessage='Please select a Course Image';
                
                $('#course_form').prepend(renderPopUpMessage('error', errorMessage));
                if(error){
                    location.reload();
                }
                error++;
                goTo();
                return false;
            //}
        }
        if(a==''){
            errorMessage='Please select a category';
            $('#course_form').prepend(renderPopUpMessage('error', errorMessage));
            goTo();
            return false;
        }

        if(stripHtmlTags(cb_short_description) ==''){
            errorMessage='Please enter the course short description';
            $('#course_form').prepend(renderPopUpMessage('error', errorMessage));
            goTo();
            return false;
        }

        
        if(!$('#cb_is_free').is(':checked')) 
        {
           if(($("#cb_price").val()=='') || ($("#cb_price").val()=='0')){
                errorMessage='Please enter the course price';
                $('#course_form').prepend(renderPopUpMessage('error', errorMessage));
                goTo();
                return false;
           }

           if(($("#cb_discount_price").val()!='') || ($("#cb_discount_price").val()!='0')){
                if(Number($("#cb_discount_price").val())>=Number($("#cb_price").val()))
                {
                    errorMessage='Discount price should be less than course price.';
                    $('#course_form').prepend(renderPopUpMessage('error', errorMessage));
                    goTo();
                    return false;
                }
                
           }

        }

        if ($('#self_enroll_yes').is(':checked')) {
            if(GivenDate==null||GivenDate==''||GivenDate=='undefined'){
                errorMessage='Please select an enrollment date';
                $('#course_form').prepend(renderPopUpMessage('error', errorMessage));
                goTo();
                return false;
            }
            else if(GivenDate!=null||GivenDate!=''||GivenDate!='undefined'){
               
                var GivenDateTemp = GivenDate;
                myDate=GivenDate.split("-");
                var newDate=myDate[1]+"/"+myDate[0]+"/"+myDate[2];
                var CurrentDate = new Date();
                CurrentDate.setHours(0,0,0,0);
                GivenDate = parseInt((new Date(newDate).getTime() / 1000).toFixed(0));
                CurrentDate = parseInt((new Date(CurrentDate).getTime() / 1000).toFixed(0));
                if(GivenDate < CurrentDate && __closeDateDefault != GivenDateTemp ){
                    errorMessage='Please select a valid enrollment date';
                    $('#course_form').prepend(renderPopUpMessage('error', errorMessage));
                    goTo();
                    return false;
                }
            }

            if($('#cb_access_limited_by_date').is(':checked')) 
            {
               
                var validity_date       = $("#by_date").val();
                var self_enroll_date    = $("#close_date").val();
                if(validity_date==null||validity_date==''||validity_date=='undefined'){
                    errorMessage='Please select the course validate date';
                    $('#course_form').prepend(renderPopUpMessage('error', errorMessage));
                    goTo();
                    return false;
                }
                if(self_enroll_date==null||self_enroll_date==''||self_enroll_date=='undefined'){
                    errorMessage='Please select an enrollment date';
                    $('#course_form').prepend(renderPopUpMessage('error', errorMessage));
                    goTo();
                    return false;
                }
                var split_validity_date = validity_date.split("-");
                var split_enroll_date   = self_enroll_date.split("-");
                var new_validity_date   = split_validity_date[1]+"/"+split_validity_date[0]+"/"+split_validity_date[2];
                var new_enroll_date     = split_enroll_date[1]+"/"+split_enroll_date[0]+"/"+split_enroll_date[2];
                var validityDate        = parseInt((new Date(new_validity_date).getTime() / 1000).toFixed(0));
                var enrollDate          = parseInt((new Date(new_enroll_date).getTime() / 1000).toFixed(0));
                if(validityDate < enrollDate)
                {
                    errorMessage='Course validity date should be greater than self enrollment date.';
                    $('#course_form').prepend(renderPopUpMessage('error', errorMessage));
                    goTo();
                    return false;
                }
               
            } 
        }
        if($('#cb_access_limited').is(':checked')){

            var accessDays = $('#byday').val().trim();
            if(accessDays==null||accessDays==''||accessDays=='undefined'){
                errorMessage='Please enter validity days';
                $('#course_form').prepend(renderPopUpMessage('error', errorMessage));
                goTo();
                return false;
            }
            if(accessDays!=null||accessDays==''||accessDays=='undefined'){
                
                if(accessDays < 1 || accessDays > 999 ){
                    errorMessage='Validity days should be within 1000days';
                    $('#course_form').prepend(renderPopUpMessage('error', errorMessage));
                    goTo();
                    return false;
                }
            }

        }
        if ($('#cb_access_limited_by_date').is(':checked')) {
            if(accessDate==null||accessDate==''||accessDate=='undefined'){
                errorMessage='Please select a validity date';
                $('#course_form').prepend(renderPopUpMessage('error', errorMessage));
                goTo();
                return false;
            }
            if(accessDate!=null||accessDate==''||accessDate=='undefined'){
                var accessDateTemp = accessDate;
                myDate=accessDate.split("-");
                var newDate=myDate[1]+"/"+myDate[0]+"/"+myDate[2];
                var CurrentDate = new Date();
                CurrentDate.setHours(0,0,0,0);
                accessDate = parseInt((new Date(newDate).getTime() / 1000).toFixed(0));
                CurrentDate = parseInt((new Date(CurrentDate).getTime() / 1000).toFixed(0));
                if(accessDate < CurrentDate && __byDateDefault != accessDateTemp){
                    errorMessage='Please enter a valid validity date';
                    $('#course_form').prepend(renderPopUpMessage('error', errorMessage));
                    goTo();
                    return false;
                } 
            }
        }
        
        if ($('#cb_preview_yes').is(':checked') && !$('#cb_is_free').is(':checked')) {
            if(previewTime==null||previewTime==''||previewTime=='undefined'){
                errorMessage='Please enter preview time';
                $('#course_form').prepend(renderPopUpMessage('error', errorMessage));
                goTo();
                return false;
            }
            else if(previewTime!=null||previewTime!=''||previewTime!='undefined'){
                var patt = new RegExp("^[0-9]+$");
                var res = patt.test(previewTime);
                if(!res || previewTime == '0'){
                    errorMessage='Please enter a valid preview time';
                    $('#course_form').prepend(renderPopUpMessage('error', errorMessage));
                    goTo();
                    return false;
                }
            }
        }
       
    }
    function goTo(){
        $([document.documentElement, document.body]).animate({
        scrollTop: $("body").offset().top
        }, 500);
    }

    var renderingActivity = false;
    function generateActivity(param) {
        if(renderingActivity == true) {
            return false;
        }
        renderingActivity = true;
        var activityHtml  = '';
            activityHtml += '<div class="row activities-row" data-id="'+__activityCount+'" id="'+param['name']+'_row_'+__activityCount+'">';
            activityHtml += ' <div class="clearfix"></div>';
            activityHtml += ' <div class="col-sm-11 padding-bottom-12">';
            activityHtml += ' <input class="form-control " name="'+param['name']+'[]">';
            activityHtml += ' </div>';
       
            activityHtml += ' <div class="col-sm-1">';
            activityHtml += ' <span onclick="removeActivity(\''+param['name']+'_row_'+__activityCount+'\', \''+__activityCount+'\')" class="close-activity-btn">×</span>';
            activityHtml += ' </div>';
            activityHtml += '</div>';
            __activityCount++;
            $('#'+param['id']).append(activityHtml);
            renderingActivity = false;
    }
    function removeActivity(activityId, activityRow) {
        var messageObject = {
        'body':'Are you sure to remove this field?',
        'button_yes':'REMOVE',
        'button_no':'CANCEL',
        'continue_params':{'activityId':activityId},
        };
        callback_warning_modal(messageObject, removeActivityConfirmed);
        // var param = {};
        // param['data'] = {'activityId':activityId};
        // removeActivityConfirmed(param);
    }

    function removeActivityConfirmed(param) {
        $('#'+param.data.activityId).remove();
        $('#common_message_advanced').modal('hide');
        var activities = $('.activities-row');
        if(activities.length < 1){
            __activityCount = 1;
        }
    }
</script>