
<?php  
$courses = json_decode($c_courses,true);
?>
<?php include_once 'bundle_header.php';?>

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
.scrollable-content{
    overflow: auto;
    max-height: 250px;
}
::-webkit-scrollbar {width: 6px !important;}
::-webkit-scrollbar-track {background: #f1f1f1 !important;}
::-webkit-scrollbar-thumb {background: #ccc !important;}
::-webkit-scrollbar-thumb:hover {background: #555 !important;}

.courseBundleRow .delte{display:none;}
.delte{color:#bd5a5a;}
.courseBundleRow:hover .delte{display:block;}
.bundle-course-title{
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.bundle-course-title span{
    color: #096cbf;
    font-size: 18px;
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

                        <form class="form-horizontal" name="Form" onsubmit="return validateForm();" id="save_course_basics" enctype="multipart/form-data" method="post" action="<?php echo admin_url('bundle/save_param/' . $id); ?>">
                            <!-- Text Box  -->
                            <div class="form-group">
                                <div class="col-sm-5">
                                    Bundle Image
                                    <div class="upload-prieview course-image-preview">
                                        <div class="img-chng">
                                            <div class="settings-logo">
                                            
                                                <img id="site_logo" data-id="<?php echo $c_image; ?>" class="img-responsive" src="<?php echo (($c_image == 'default.jpg') ? default_catalog_path() : catalog_path(array('bundle_id' => $id))) . $c_image; ?>">
                                            </div>
                                            <input name="c_image" class="fileinput logo-image-upload-btn" id="site_logo_btn" accept="image/*" type="file">
                                            <button class="btn btn-green pos-abs">CHANGE IMAGE</button>
                                        </div>
                                        <span class="bundle-image-info"> File Sizes : 740px x 452px</span>
                                    </div>
                                </div>
                                <div class="col-sm-7">
                                    <div class="course-settings-title">
                                        <span><?php echo lang('bundle_title_label') ?> </span>
                                        <input type="text" maxlength="50" name="c_title" class="form-control" id="c_title"  value="<?php echo isset($c_title) ? $c_title : ''; ?>" placeholder="Programming Pack..." />
                                    </div>
                                    <div class="course-settings-title">
                                        <span>Bundle Category * :</span>
                                        <div class="">
                                            <?php if(!empty($categories)): ?>
                                               
                                                <select id="listing_category" name="c_category[]" class="multiselect" multiple="multiple">
                                                    <?php foreach ($categories as $category): ?>
                                                    
                                                        <option value="<?php echo $category['id']; ?>" <?php echo in_array($category['id'], $c_category) ? 'selected' : ''; ?>><?php echo $category['ct_name'] ?></option>
                                                    <?php endforeach;?>
                                                </select>
                                            <?php else: ?>
                                                <a class="text-blue pad-top10" target="_blank" href="<?php echo admin_url('question_manager') ?>">Click here to create a category</a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <a href="<?php echo admin_url().'bundle/enroll_course/'.$id; ?>" class="btn btn-green selected">
                                        <?php echo lang('add_bundle_course'); ?></a>
                                    </div>
                                    
                                </div>
                            </div>

                           
                            <?php
                            if(!empty($courses)){
                            ?>
                             <!-- Catalog starts here -->
                            
                            <div class="row pad-top15" id="course_bundle_wrapper">
                                <div class="col-sm-12 bundle-course-title">
                                    <h4 class="dsp-inline">
                                        <?php echo lang('bundle_manage_course'); ?> 
                                    </h4>
                                    <span id="course_count_bundle">
                                            <?php
                                                $course_count = count($courses);
                                                if($course_count > 0){
                                                    $course_cout_display = ($course_count == 1)?'item':'items';
                                                    echo '('.$course_count.' '.$course_cout_display.')';
                                                }else{
                                                    echo '( No Items )'; 
                                                } 
                                            ?>
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12 course-cont-wrap catalog-table scrollable-content">
                                    <div class="table mar-ver0 course-cont rTable" id="course_assigned_catalog" name="catalog_course_list" style="">
                                        <?php 
                                        
                                        foreach($courses as $course){   
                                        ?>
                                        <div class="rTableRow courseBundleRow" data-name="<?php echo $course['course_name'];?>" id="course_bundle_<?php echo $course['id'];?>">
                                            <div class="rTableCell">
                                                <span class="icon-wrap-round">
                                                    <i class="icon icon-graduation-cap"></i>        
                                                </span>        
                                                <a href="<?php echo admin_url().'course/basic/'.$course['id'] ?>"><?php echo $course['course_name'] ?></a>    
                                            </div>
                                            <?php
                                                $course_status          = 'Private';
                                                if($course['status'] == 1){
                                                    $course_status  = 'Public';
                                                }
                                            ?>
                                            <div class="rTableCell">
                                                <span><?php echo $course_status; ?></span>
                                            </div>
                                            <div class="rTableCell" onclick="deleteCourse(<?php echo $course['id'];?>,<?php echo $id;?>)">  <i class="icon icon-cancel-1 delte"></i>    </div>
                                        </div>
                                        <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <?php }?>
                            <!-- Catalog ends here -->

                            <!-- Text Box Addons  -->
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <?php echo lang('bundle_description_label') ?> * :
                                    <textarea name="c_description" id="c_description" maxlength="1000"  class="form-control" rows="3" ><?php echo isset($c_description) ? $c_description : ''; ?></textarea>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-6">
                                    <?php echo lang('bundle_access_lbl') ?> <br/>
                                    <label class="pad-top10">
                                        <input type="radio" name="c_access_validity"  <?php $checked = "checked";echo ($c_access_validity == 0) ? $checked : '';?> id="c_access_unlimited" checked  value="0" >
                                        <?php echo lang('bundle_access_unlimited') ?>
                                    </label>
                                    <label>
                                        <input type="radio" name="c_access_validity" <?php $checked = "checked";echo ($c_access_validity == 1) ? $checked : '';?> id="c_access_limited" value="1" >
                                        <?php echo lang('bundle_access_limited') ?>
                                    </label>
                                    <label>
                                        <input type="radio" name="c_access_validity" <?php $checked = "checked";echo ($c_access_validity == 2) ? $checked : '';?> id="c_access_limited_by_date"
                                        value="2" >
                                        <?php echo lang('bundle_access_limited_by_date') ?>
                                    </label>

                                </div>
                                <div class="col-sm-6" id="bundle_validity">
                                    <?php echo lang('bundle_validity_lbl') ?>
                                    <div class="">

                                        <div class="input-group">
                                            <input type="text" class="form-control c_validity" name="c_validity" value="<?php echo isset($c_validity)&& ($c_validity!=0)? $c_validity : '' ?>" placeholder="eg: 180" aria-describedby="basic-addon1" id="byday">
                                            <span class="input-group-addon" id="basic-addon1">Days</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-6" id="bundle_validity_date">
                                    <?php echo lang('bundle_validity_lbl') ?>
                                    <div class="">
                                        <input type="text" style="background-color:white" readonly name="c_validity_date" placeholder="To Date" class="form-control" id="by_date" autocomplete="off" value="<?php echo ($c_validity_date=="0000-00-00" || $c_validity_date ==  '') ? '':date("d-m-Y", strtotime($c_validity_date)) ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-4">
                                    <label class="control-label">
                                        <input type="checkbox" name="c_is_free" value="1" <?php $checked = "checked";echo ($c_is_free == 1) ? $checked : '';?>  id="c_is_free" >
                                        <span>Free Bundle</span>
                                    </label>
                                </div>
                            </div>
                            <div id="course_price_setup" style="<?php $styles = "display:none;";echo ($c_is_free == 1) ? $styles : '';?>">
                                <div class="form-group">
                                    <div class="col-sm-4"  >
                                        <span>Bundle Price  * :</span>
                                        <br>
                                        <div class="pad-top10">
                                            <input type="text" onkeypress="return isNumber(event)"  name="c_price" placeholder="Bundle price" class="form-control" id="c_price" autocomplete="off" value="<?php echo isset($c_price) ?$c_price : '0' ?>">
                                        </div>
                                    </div>
                                    <div class="col-sm-4"  >
                                        <span>Bundle Discount Price :</span>
                                        <br>
                                        <div class="pad-top10">
                                            <input type="text" onkeypress="return isNumber(event)"  name="c_discount" placeholder="Bundle discount price" class="form-control" id="c_discount" autocomplete="off" value="<?php echo isset($c_discount) ?$c_discount : '0' ?>">
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <span>Tax :</span>
                                        <br>
                                        <label class="control-label">
                                            <input type="radio" name="c_tax_method" value="0" <?php $checked = "checked"; echo ($c_tax_method == 0) ? $checked : '';?>  id="c_tax_inclusive" checked>
                                            <span>Inclusive of tax</span>
                                        </label>
                                        <label>
                                            <input type="radio" name="c_tax_method" <?php $checked = "checked";echo ($c_tax_method == 1) ? $checked : '';?> value="1"  id="c_tax_exclusive">
                                            <span>Exclusive of tax</span>
                                        </label>
                                    </div>

                                </div>
                                <?php 
                                $course_price       = ( $c_discount != 0 ) ? $c_discount : $c_price;
                                $sgst_price         = ( $sgst / 100 ) * $course_price;
                                $cgst_price         = ( $cgst / 100 ) * $course_price;
                                $total_course_price = $course_price + $sgst_price + $cgst_price;
                                ?>
                                <div id="tax-table" class="form-group table-holder" >
                                    <table class="billing-table">
                                        <tr>
                                            <td>Bundle Price</td>
                                            <td class="text-right"><span id="course_price"><?php echo $course_price; ?></span> &#8377; </td>
                                        </tr>
                                        <tr>
                                            <td>CGST(<?php echo $cgst; ?>%)</td>
                                            <td class="text-right"><span id="cgst_price"><?php echo round($sgst_price,2); ?></span> &#8377; </td>
                                        </tr>
                                        <tr>
                                            <td>SGST(<?php echo $sgst; ?>%)</td>
                                            <td class="text-right"><span id="sgst_price"><?php echo round($cgst_price,2); ?></span> &#8377; </td>
                                        </tr>
                                        <tr>
                                            <td><b>Total</b></td>
                                            <td class="text-right"><b><span id="total_course_price"><?php echo round($total_course_price,2); ?></span> &#8377; </b></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <input type="submit" id="course_save_button" class="pull-right btn btn-green marg10" value="<?php echo lang('save') ?>">

                                </div>
                            </div>
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
<script type="text/javascript">
    var __cgst          = Number('<?php echo $cgst; ?>');
    var __sgst          = Number('<?php echo $sgst; ?>');
    var __course_price  = Number(($("#c_discount").val()!=0)?$("#c_discount").val():$("#c_price").val());
    $( document ).ready(function() {
        tax_price_table();
    });
    $("#c_is_free").click(function()
    {
        if ($('#c_is_free').is(':checked')) 
        {
            $("#course_price_setup").hide();
        }
        else 
        {
            $("#course_price_setup").show();
        }
    });

    $('input[type=radio][name=c_tax_method]').change(function() {
        tax_price_table();
    });

    $("#c_price").keyup(function() {
            tax_price_table();
    });

    $("#c_discount").keyup(function() {
        if($('input[name=c_tax_method]:checked').val()=='1')
        {
           if(($("#c_discount").val()!='') || ($("#c_discount").val()!='0'))
           {
                if((Number($("#c_discount").val()))>(Number($("#c_price").val())))
                {
                    $("#c_discount").val('0');
                    tax_price_table();
                    errorMessage='Discount price should be less than course price.';
                    $('#course_form').prepend(renderPopUpMessage('error', errorMessage));
                    goTo();
                    return false;
                }
                
           }
           tax_price_table();
        }
    });

    function tax_price_table()
    {
        if((Number($("#c_discount").val()))>(Number($("#c_price").val())))
        {
            $("#c_discount").val('0');
            tax_price_table();
            errorMessage='Discount price should be less than course price.';
            $('#course_form').prepend(renderPopUpMessage('error', errorMessage));
            goTo();
            return false;
        }
        else
        {
            __course_price  = Number(($("#c_discount").val()!=0)?$("#c_discount").val():$("#c_price").val());
            if(Number($("#c_discount").val())>Number($("#c_price").val()))
            {
                __course_price  =  Number($("#c_price").val());
            }
            var sgst_price         = (__sgst / 100) * __course_price;
            var cgst_price         = (__cgst / 100) * __course_price;
            if($('#c_tax_inclusive').is(':checked')) 
            {
                __course_price     = __course_price -(sgst_price+cgst_price);
                var total_course_price = __course_price+sgst_price+cgst_price;
            } 
            else if($('#c_tax_exclusive').is(':checked')) 
            {
                var total_course_price = __course_price+sgst_price+cgst_price;
            }
            $("#course_price").html(__course_price.toFixed(2));
            $("#sgst_price").html(sgst_price.toFixed(2));
            $("#cgst_price").html(cgst_price.toFixed(2));
            $("#total_course_price").html(total_course_price.toFixed(2));
            $("#tax-table").show();
        }
    }



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
    
    function validateForm()
    {
        
        var categoryList    = $('#listing_category').val();
        var previewImage    = $('#site_logo').attr('data-id');
        var description     = $('#c_description').val();
        var accessDays      = $('#byday').val();
        var accessDate      = $('#by_date').val();
        var errorCount      = 0;
        var errorMessage    = '';

        if(previewImage =='default.jpg'){

            var previewImageCheck = $('#site_logo_btn').val();
            if(previewImageCheck == ''){

                errorMessage += 'Please select a Bundle Image<br />';
                errorCount++;
            }
        }
        if(description == ''){
            errorMessage += 'Please enter description<br />';
            errorCount++;
        }

        if(categoryList ==''){
            errorMessage += 'Please select a category<br />';
            errorCount++;
        }

        if(!$('#c_is_free').is(':checked')) 
        {
           if(($("#c_price").val()=='') || ($("#c_price").val()=='0')){
                errorMessage += 'Please enter the bundle price<br />';
                errorCount++;
           }

           if(($("#c_discount").val()!='') || ($("#c_discount").val()!='0')){
                if(Number($("#c_discount").val())>Number($("#c_price").val()))
                {
                    errorMessage += 'Discount price should be less than bundle price.<br />';
                    errorCount++;
                   
                }
                
           }

        }
        
        if($('#c_access_limited').is(':checked')){
            var accessDays      = $('#byday').val();
            if(accessDays == 0||accessDays == null||accessDays == ''||accessDays == 'undefined'){
                errorMessage   += 'Please enter validity days<br>';
                errorCount++;
            }
            if(accessDays!=null||accessDays==''||accessDays=='undefined'){
                
                if(accessDays.length > 3||accessDays.length < 1){
                    errorMessage += 'Validity days should be within 1000days';
                    errorCount++;
                }
            }
        }
        if ($('#c_access_limited_by_date').is(':checked')) {
            if(accessDate == null||accessDate == ''||accessDate == 'undefined'){
                errorMessage       += 'Please select a validity date';
                errorCount++;
            }
            if(accessDate != null||accessDate == ''||accessDate == 'undefined'){
                var accessDateTemp  = accessDate;
                myDate              = accessDate.split("-");
                var newDate         = myDate[1]+"/"+myDate[0]+"/"+myDate[2];
                var CurrentDate     = new Date();
                CurrentDate.setHours(0,0,0,0);
                accessDate          = parseInt((new Date(newDate).getTime() / 1000).toFixed(0));
                CurrentDate         = parseInt((new Date(CurrentDate).getTime() / 1000).toFixed(0));
                if(accessDate < CurrentDate && __byDateDefault != accessDateTemp){
                    errorMessage    += 'Please enter a valid validity date';
                    errorCount++;
                } 
            }
    }
       
       if(errorCount > 0){
            $('#course_form').prepend(renderPopUpMessage('error', errorMessage));
            goTo();
            return false;
       }else{
           return true;
       }
    }
    function goTo(){
        $([document.documentElement, document.body]).animate({
        scrollTop: $("body").offset().top
        }, 500);
    }
    var url = window.location.search;
        url = url.replace("?", ''); // remove the ?
        //console.log(url);
        if(url=='success'){
            var messageObject = {
                'body': 'Courses enrolled to Bundle',
                'button_yes': 'OK',
            };
            callback_success_modal(messageObject);
            var link = window.location.protocol + "//" + window.location.host + window.location.pathname;
            window.history.pushState({
                path: link
            }, '', link);
        }
        $(document).ready(function(){
            $('#c_description').redactor({
            //toolbarFixedTopOffset: course_top_offset,
                maxHeight: '250px',
                imageUpload: admin_url + 'configuration/redactore_image_upload',
                plugins: ['table', 'alignment', 'source'],
                callbacks: {
                    imageUploadError: function(json, xhr) {
                        var erorFileMsg = "This file type is not allowed. upload a valid image.";
                        $('#course_form').prepend(renderPopUpMessage('error', erorFileMsg));
                        scrollToTopOfPage();
                        return false;
                    }
                }
            });

            $(document).on('click', '#c_is_free', function() {
                if ($(this).is(':checked')) {
                    // $("#course_price_val").hide();
                    $("#course_price_setup").hide();
                    // $("#course_discount_val").hide();
                } else {
                    // $("#course_price_val").show();
                    // $("#course_discount_val").show();
                    $("#course_price_setup").show();
                    
                }
            });
        });
        
        //Live Price update
        var cb_price = $('#cb_price'),
            cb_live_price = $('#cb_live_price');

        //Live Discount update
        var cb_discount = $('#cb_discount'),
            cb_live_discount = $('#cb_live_discount');

        function deleteCourse(course_id,bundle_id){

            var courseName      = $('#course_bundle_'+course_id).attr('data-name');
            var message         = 'Are you sure to delete course named '+courseName+' from this bundle? ';
            var messageObject   = {
                'body': message,
                'button_yes': 'DELETE',
                'button_no': 'CANCEL',
                'continue_params': {
                    'bundle_id': bundle_id,
                    'course_id':course_id
                },
            };
            callback_warning_modal(messageObject, deleteCourseConfirmed);
        }
        function deleteCourseConfirmed(params){
            var bundle_id   = params.data.bundle_id;
            var course_id   = params.data.course_id;
            $.ajax({
                url: admin_url+'bundle/delete_course',
                type: "POST",
                data:{"course_id":course_id, "is_ajax":true,"bundle_id":bundle_id},
                success: function(response) {
                    
                    var data  = $.parseJSON(response);
                    //console.log(data);
                    if(data['error'] == false)
                    {
                        $('#course_bundle_'+course_id).remove();
                        cacheClear();
                        var messageObject = {
                            'body': data['message'],
                            'button_yes': 'OK',
                        };
                        callback_success_modal(messageObject);
                    } else {
                        var messageObject = {
                            'body': data['message'],
                            'button_yes': 'OK',
                        };
                        callback_danger_modal(messageObject);
                    }
                }
            });
        }
        
        function cacheClear(){

            var courseLength = $('.courseBundleRow:visible').length;
            
            if(courseLength <= 0){
                $('#course_bundle_wrapper').hide();
            }else{
                var course_count_display    = (courseLength == 1)?'item':'items';
                var renderCourse            = '('+courseLength +' '+ course_count_display+')';
                $('#course_count_bundle').text(renderCourse);
            }
        }
       
        //Live Price update
    var c_price = $('#c_price'),
        c_live_price = $('#c_live_price');

    //Live Discount updateaction="<?php echo admin_url('bundle/save_param/' . $id); ?>"
    var c_discount = $('#c_discount'),
        c_live_discount = $('#c_live_discount');


    function preventAlphabetsPercentage(evt) {
        var charCode = (evt.which) ? evt.which : event.keyCode;
        //alert(charCode);
        if ((charCode != 46 && charCode > 31 &&
                (charCode < 48 || charCode > 57)))
            return false;

        return true;
    } 

    $(document).ready(function() {
        readerObj='';
        $('.message_container').delay(3000).fadeOut();
        $("#c_description").trigger("keyup");
        $("#c_meta_description").trigger("keyup");
        if ($("#c_is_free").is(":checked")) {
            $("#c_price").attr('data-validation-optional', 'true');
        }

        c_discount.trigger("keyup");

        $("#site_logo_btn").change(function() {
            readImageData(this); //Call image read and render function
        });


    });

    function readImageData(imgData) {
        if (imgData.files && imgData.files[0]) {
            var readerObj = new FileReader();

            readerObj.onload = function(element) {
                $('#site_logo').attr('src', element.target.result);
                
                var img = new Image;

                img.onload = function() {
                    if(img.width < 739 || img.height < 417){
                        lauch_common_message('Image Size', 'The image you have choosen is too small and cannot be uploaded.');
                        $('#site_logo').attr('src',__course_loaded_img);
                        $('#site_logo_btn').val('');
                        return false;
                    }
                };

                img.src = element.target.result;
            }
            readerObj.readAsDataURL(imgData.files[0]);
        }
    }
    if ($('#c_access_unlimited').is(':checked')) {
        $('#bundle_validity').hide();
        $('#bundle_validity_date').hide();
    } else if ($('#c_access_limited').is(':checked')) {
        $('#bundle_validity').show();
        $('#bundle_validity_date').hide();
    } else {
        $('#bundle_validity_date').show();
        $('#bundle_validity').hide();
    }

    $('#c_access_unlimited').click(function() {
        if ($('#c_access_unlimited').is(':checked')) {
            $('#bundle_validity').hide();
            $('#bundle_validity_date').hide();
        }
    });

    $('#c_access_limited').click(function() {
        $('#bundle_validity_date').hide();
        if ($('#c_access_limited').is(':checked')) {
            $('#bundle_validity').show();
        }
    });

    $('#c_access_limited_by_date').click(function() {
        $('#bundle_validity').hide();
        if ($('#c_access_limited_by_date').is(':checked')) {
            $('#bundle_validity_date').show();
        }
    });

 
</script>