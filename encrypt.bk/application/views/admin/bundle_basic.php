<?php  
$courses = json_decode($c_courses,true);
?>
<?php include_once 'bundle_header.php'; ?>
<!-- ############################# --> <!-- START -->
<link rel="stylesheet" href="<?php echo assets_url() ?>css/redactor/css/redactor.css" />
<link rel="stylesheet" href="<?php echo assets_url() ?>css/redactor/css/alignment.css" />

<!-- ############################# --> <!-- END -->
<link rel="stylesheet" href="<?php echo assets_url() ?>css/datepicker.min.css">
<link rel="stylesheet" href="<?php echo assets_url() ?>css/bootstrap-multiselect.css">
<link rel="stylesheet" href="<?php echo assets_url() ?>css/jquery-ui.css">
   <body>

   <script src="<?php echo assets_url() ?>js/bootstrap-tagsinput.js" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/datepicker.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/datepicker.en.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/bootstrap-multiselect.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/jquery-ui.min.js"></script>
      <!-- Manin Iner container start -->
      <div class="main-content-  course-container clearfix  ">
         <section class="content-wrap cont-course-big top-spacing content-wrap-align">
            <!-- LEFT CONTENT --> <!-- STARTS -->
            <!-- ===========================  -->
            <div class="left-wrap col-sm-12 pad0">
               <!-- Nav section inside this wrap  --> <!-- START -->
               <!-- =========================== -->




               <div class="container-fluid course-create-wrap course-settings">
                  <div class="row-fluid course-cont">
                    <form class="form-horizontal" name="Form" onsubmit="return validateForm();" id="save_course_basics" enctype="multipart/form-data" method="post" action="<?php echo admin_url('bundle/save_param/' . $id); ?>">
                     <div class="col-sm-12">
                        <div class="form-horizontal manage-bundle-wrapper" id="course_form">

                              <!-- Text Box  -->
                              <div class="form-group">
                                 <div class="col-sm-3">
                                    <div class="upload-prieview course-image-preview">
                                       <div class="img-chng">
                                          <div class="settings-logo">
                                             <img id="site_logo" data-id="<?php echo $c_image; ?>" class="img-responsive" src="<?php echo (($c_image == 'default.jpg') ? default_catalog_path() : catalog_path(array('bundle_id' => $id))) . $c_image; ?>">
                                          </div>
                                          <input name="c_image" class="fileinput logo-image-upload-btn" id="site_logo_btn" accept="image/*" type="file">
                                          <button class="btn btn-green pos-abs">
                                             CHANGE IMAGE
                                             <ripples></ripples> 
                                          </button>
                                       </div>
                                       <span> File Size : 740px x 452px</span>
                                    </div>
                                 </div>
                                 <div class="col-sm-9">
                                    <div class="course-settings-title edit1">
                                       <!-- <span>Bundle Title </span> -->
                                       <input type="text" maxlength="50" name="c_title" class="form-control bundle-title-input" id="c_title" value="<?php echo $c_title; ?>" placeholder="C++ and Java programming combo course">
                                       <div class="bundle-title-preview"><?php echo $c_title; ?></div>
                                       <span class="bundle-title-edit">Edit</span>
                                    </div>
                                    <?php if(!empty($categories)): 
                                       $category_length       = 0;
                                       $more_category_number  = 0; ?>
                                       <div class="category-list d-flex">
                                          <?php foreach ($categories as $category): 
                                                if( in_array( $category['id'], $c_category ) ):
                                                    $category_length += strlen($category['ct_name']);
                                                    ?>
                                                    <input type="hidden" value="<?php echo $category['id']  ?>" name="c_category[]">
                                                    <?php if( $category_length <= 110 ): ?>
                                                        <div class="category-item">
                                                        <span><?php echo $category['ct_name'] ?></span>
                                                        <span id="<?php echo $category['id'] ?>" class="category-rmv">&times;</span>
                                                        </div>
                                                    <?php else: 
                                                        $more_category_number++;
                                                    endif; ?>
                                                <?php endif; ?> 
                                          <?php endforeach;?>
                                          <?php if( !empty( $more_category_number )): ?>
                                             <div onClick="categoryMOdalShow()" class="more-category-item">
                                                <span>+ <span class="category_more_number"><?php echo $more_category_number; ?></span> more</span>
                                             </div>
                                          <?php endif; ?>
                                          <div onClick="categoryMOdalShow()"gr class="add-category-item">
                                             <span>Add Category</span>
                                          </div>
                                       </div>   

                                    <?php else: ?>
                                       <a class="text-blue pad-top10" target="_blank" href="<?php echo admin_url('question_manager') ?>">Click here to create a category</a>
                                    <?php endif; ?>
                                 </div>
                              </div>
                              <!-- Catalog starts here -->

                              <!-- Manage bundle starts here -->
                              <div class="manage-bundle">
                                  <!-- manage bundle tab -->
                                  <ul class="nav nav-tabs">
                                    <li class="active"><a data-toggle="tab" href="#bundle-items">Bundle items (<span id="bundle_items_count"><?php echo count($courses); ?></span>)</a></li>
                                    <li><a data-toggle="tab" href="#bundle-desc"><?php echo lang('bundle_description_label') ?></a></li>
                                    <li class="pull-right">
                                    <a href="<?php echo admin_url().'bundle/enroll_course/'.$id; ?>" class="add-item-btn">
                                        <?php echo lang('add_bundle_course_item'); ?></a>
                                        </li>
                                  </ul>
                                  <div class="tab-content scrollable-content">
                                    <div id="bundle-items" class="tab-pane fade in active">
                                      <div class="catalog-table">
                                        <!-- ========== -->
                                          <?php if(!empty( $courses )) foreach($courses as $course){  ?>
                                          <div class="bundle-item-row d-flex flex-row justify-between align-center" data-name="<?php echo $course['course_name'] ?>" id="course_bundle_<?php echo $course['id'];?>">
                                             <div class="d-flex align-center">
                                                <div class="cap-icon">
                                                   <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" viewBox="0 0 512 408" style="enable-background:new 0 0 512 408;width: 22px;height: 22px;fill: #64277d;margin-bottom: -7px;" xml:space="preserve"><g><g><path d="M503.7,122.6l-241-121c-4.2-2.1-9.2-2.1-13.5,0l-241,121C3.2,125.1,0,130.3,0,136c0,5.7,3.2,10.9,8.3,13.4L30,160.2v127.4    C12.5,293.8,0,310.4,0,330v61c0,8.3,6.7,15,15,15h60c8.3,0,15-6.7,15-15v-61c0-19.6-12.5-36.2-30-42.4V175.2l31,15.4V256    c0,29.5,18.2,56.7,51.2,76.5c30.6,18.4,71,28.5,113.8,28.5c89.3,0,165-44.6,165-105v-65.4l82.7-41.2    C514.7,143.9,514.8,128.1,503.7,122.6z M60,376H30v-46c0-8.2,6.7-14.9,14.9-15c0,0,0.1,0,0.1,0s0.1,0,0.1,0    c8.2,0,14.9,6.8,14.9,15V376z M391,256c0,40.7-61.8,75-135,75s-135-34.3-135-75v-50.5l128.3,63.9c2.1,1,4.4,1.6,6.7,1.6    s4.6-0.5,6.7-1.6L391,205.5V256z M256,239.2c-11.2-5.6-198.7-98.9-207.5-103.3L256,31.8l207.5,104.2    C454.3,140.5,266.8,233.9,256,239.2z"></path></g></g></svg>
                                                </div>
                                                <div  class="bundle-name"><a href="<?php echo admin_url().'course/basic/'.$course['id'] ?>"><?php echo $course['course_name'] ?></a> </div>
                                             </div>
                                             <?php
                                                $course_status          = 'Private';
                                                $course_class           = 'warning';
                                                if(isset($course['status']) && $course['status'] == '1'){
                                                    $course_status      = 'Public';
                                                    $course_class       = 'success';
                                                }
                                             ?>
                                             <div class="status-holder d-flex justify-between align-center"> 
                                             <div class="rTableCell pad0 cours-fix width70">            
                                                <label style="font-size: 11px !important" class="pull-right label label-<?php echo $course_class;?> label-xs" id=""><?php echo $course_status;?></label>        
                                             </div>
                                                <div title="Remove" onclick="deleteCourse(<?php echo $course['id'];?>,<?php echo $id;?>)" class="remove-bundle-item">&times;</div>
                                             </div>
                                          </div>
                                          <?php } ?>
                                        <!-- =============== -->
                                      </div>
                                    </div>
                                    <div id="bundle-desc" class="tab-pane fade">
                                        <div class="form-group">
                                           <div class="col-sm-12">
                                              <div class="redactor-box redactor-blur redactor-styles-on redactor-toolbar-on" dir="ltr">
                                                 <!-- <div class="redactor-toolbar-wrapper" style="">
                                                    <div class="redactor-toolbar" style=""><a class="re-button re-html re-button-icon" data-re-name="html" href="https://SGlearningapp.enfinlabs.com/admin/bundle/basic/23/#" alt="HTML" rel="html" role="button" aria-label="HTML" tabindex="-1" data-re-icon="true"><i class="re-icon-html"></i></a><a class="re-button re-format re-button-icon" data-re-name="format" data-dropdown="true" href="https://SGlearningapp.enfinlabs.com/admin/bundle/basic/23/#" alt="Format" rel="format" role="button" aria-label="Format" tabindex="-1" data-re-icon="true"><i class="re-icon-format"></i></a><a class="re-button re-bold re-button-icon" data-re-name="bold" href="https://SGlearningapp.enfinlabs.com/admin/bundle/basic/23/#" alt="Bold" rel="bold" role="button" aria-label="Bold" tabindex="-1" data-re-icon="true"><i class="re-icon-bold"></i></a><a class="re-button re-italic re-button-icon" data-re-name="italic" href="https://SGlearningapp.enfinlabs.com/admin/bundle/basic/23/#" alt="Italic" rel="italic" role="button" aria-label="Italic" tabindex="-1" data-re-icon="true"><i class="re-icon-italic"></i></a><a class="re-button re-deleted re-button-icon" data-re-name="deleted" href="https://SGlearningapp.enfinlabs.com/admin/bundle/basic/23/#" alt="Strikethrough" rel="deleted" role="button" aria-label="Strikethrough" tabindex="-1" data-re-icon="true"><i class="re-icon-deleted"></i></a><a class="re-button re-lists re-button-icon" data-re-name="lists" data-dropdown="true" href="https://SGlearningapp.enfinlabs.com/admin/bundle/basic/23/#" alt="Lists" rel="lists" role="button" aria-label="Lists" tabindex="-1" data-re-icon="true"><i class="re-icon-lists"></i></a><a class="re-button re-image re-button-icon" data-re-name="image" href="https://SGlearningapp.enfinlabs.com/admin/bundle/basic/23/#" alt="Image" rel="image" role="button" aria-label="Image" tabindex="-1" data-re-icon="true"><i class="re-icon-image"></i></a><a class="re-button re-table re-button-icon" data-re-name="table" href="https://SGlearningapp.enfinlabs.com/admin/bundle/basic/23/#" alt="Table" rel="table" role="button" aria-label="Table" tabindex="-1" data-re-icon="true" data-dropdown="true"><i class="re-icon-table"></i></a><a class="re-button re-link re-button-icon" data-re-name="link" data-dropdown="true" href="https://SGlearningapp.enfinlabs.com/admin/bundle/basic/23/#" alt="Link" rel="link" role="button" aria-label="Link" tabindex="-1" data-re-icon="true"><i class="re-icon-link"></i></a><a class="re-button re-alignment re-button-icon" data-re-name="alignment" href="https://SGlearningapp.enfinlabs.com/admin/bundle/basic/23/#" alt="Align" rel="alignment" role="button" aria-label="Align" tabindex="-1" data-re-icon="true" data-dropdown="true"><i class="re-icon-alignment"></i></a></div>
                                                 </div> -->
                                                 <!-- <span class="redactor-voice-label" id="redactor-voice-0" aria-hidden="false">Rich text editor</span> -->
                                                 <!-- <div class="redactor-styles redactor-in redactor-in-0" dir="ltr" aria-labelledby="redactor-voice-0" role="presentation" style="max-height: 250px;" contenteditable="true">
                                                    <p>qwerty</p>
                                                 </div> -->
                                                 <textarea name="c_description" id="c_description" maxlength="1000" class="form-control redactor-source" rows="3" data-redactor-uuid="0" style="display: none;"><?php echo isset($c_description) ? $c_description : ''; ?></textarea>
                                                 <ul dir="ltr" class="redactor-statusbar"></ul>
                                              </div>
                                           </div>
                                          </div>
                                    </div>
                                  </div>
                                  <!-- manage bundle tab ends -->
                              </div>
                              <!-- Manage bundle ends here -->

                              <!-- Bundle validity starts -->
                              <div class="bundle-validity">
                                 <div class="strong-title"> <?php echo lang('bundle_access_lbl') ?> :</div>
                                 <div class="validity-holder">
                                    <div class="col-md-6 pad0 d-flex flex-row">
                                        <label class="d-flex flex-row">
                                          <input type="radio" name="c_access_validity"  <?php $checked = "checked";echo ($c_access_validity == 0) ? $checked : '';?> id="c_access_unlimited" value="0" checked>
                                          <span style="margin:0px 17px"><?php echo lang('bundle_access_unlimited') ?></span></label>
                                        <label class="d-flex flex-row">
                                          <input type="radio" name="c_access_validity"  <?php $checked = "checked";echo ($c_access_validity == 1) ? $checked : '';?> id="c_access_limited" value="1">
                                          <span style="margin:0px 15px"><?php echo lang('bundle_access_limited') ?></span>
                                        </label class="d-flex flex-row">
                                        <label class="d-flex flex-row">
                                          <input type="radio" name="c_access_validity"  <?php $checked = "checked";echo ($c_access_validity == 2) ? $checked : '';?> id="c_access_limited_by_date" value="2">
                                          <span style="margin:0px 15px"><?php echo lang('bundle_access_limited_by_date') ?></span>
                                        </label>
                                    </div>
                                    <div class="col-md-6 pad0" >
                                       <div class="validityby-days input-group" id="bundle_validity" style="display: none;">
                                          <input type="text" class="form-control c_validity" name="c_validity" value="<?php echo isset($c_validity)&& ($c_validity!=0)? $c_validity : '' ?>" placeholder="eg: 180" aria-describedby="basic-addon1" id="byday">
                                          <span class="input-group-addon" id="basic-addon1">Days</span>
                                       </div>
                                       <div class="validityby-date" id="bundle_validity_date" style="display: none;">
                                       <input type="text" style="background-color:white" readonly name="c_validity_date" placeholder="To Date" class="form-control" id="by_date" autocomplete="off" value="<?php echo ($c_validity_date=="0000-00-00" || $c_validity_date ==  '') ? '':date("d-m-Y", strtotime($c_validity_date)) ?>">
                                      </div>
                                    </div>
                                  </div>
                                 <div class="clearfix"></div>
                              </div>
                              <!-- Bundle validity ends -->

                              <!-- Bundle pricing starts  -->
                              <div class="bundle-pricing" >
                                <div id="course_price_setup">
                                  <div class="col-md-6 pricing-column-left">


                                  <div class="form-group">
                                <div class="col-sm-6">
                                    <!-- <br/> -->
                                    <span><?php echo lang('review_and_rating') ?></span>
                                    <br>
                                    <label class="control-label">
                                        <input type="radio" name="c_rating_enabled" value="0" <?php $checked = "checked";
                                            echo (isset($c_rating_enabled) && $c_rating_enabled == 0) ? $checked : '';?>  id="c_rating_enabled" checked>
                                        <span>Disabled</span>
                                    </label>
                                    <?php /* */?>
                                    <label>
                                        <input type="radio" name="c_rating_enabled" <?php $checked = "checked";
                                            echo (isset($c_rating_enabled) && $c_rating_enabled == 1) ? $checked : '';?> value="1"  id="c_rating_enabled">
                                        <span>Enabled</span>
                                    </label>
                                </div>
                            </div>
                            <hr>




                                    <div class="d-flex flex-row pricing-option">
                                         <div class="strong-title">Pricing :</div>
                                        <label class="d-flex flex-row" style="margin-left:17px">
                                          <input type="radio" name="c_is_free" checked="" id ="c_is_paid" class="c_is_free" value="0">
                                          <span style="margin:0px 17px">Paid</span>
                                        </label>
                                        <label class="d-flex flex-row">
                                          <input type="radio" name="c_is_free" <?php $checked = "checked";echo ($c_is_free == 1) ? $checked : '';?> value="1" class="c_is_free">
                                          <span style="margin:0px 17px">Free</span>
                                        </label>
                                    </div>
                                    <div style="<?php $styles = "display:none;";echo ($c_is_free == 1) ? $styles : '';?>" class="course_price_div">
                                        <div class="d-flex">
                                        <div class="col-md-6 pad0">
                                            <div class="strong-title">Price</div>
                                            <div class="pad-top10">
                                                <input type="text" onkeypress="return isNumber(event)" name="c_price" placeholder="Bundle price" class="form-control" id="c_price" autocomplete="off"  value="<?php echo isset($c_price) ?$c_price : '0' ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="strong-title">Discount Price :</div>
                                            <div class="pad-top10">
                                                <input type="text" onkeypress="return isNumber(event, this)" name="c_discount" placeholder="Bundle discount price" class="form-control" id="c_discount" autocomplete="off" value="<?php echo isset($c_discount) ?$c_discount : '0' ?>">
                                            </div>
                                        </div>
                                        </div>
                                        <div class="d-flex flex-row tax-column">
                                        <div class="strong-title">Tax :</div>
                                        <label class="d-flex" style="margin-left:17px">
                                            <input type="radio" name="c_tax_method" value="0" id="c_tax_inclusive" <?php $checked = "checked"; echo ($c_tax_method == '0') ? $checked : '';?>>
                                            <span style="margin:0px 17px">Inclusive of tax</span>
                                        </label>
                                        <label class="d-flex">
                                            <input type="radio" name="c_tax_method"  value="1" id="c_tax_exclusive" <?php $checked = "checked";echo ($c_tax_method == 1) ? $checked : '';?>>
                                            <span style="margin:0px 17px">Exclusive of tax</span>
                                        </label>
                                        </div>
                                    </div>
                                  </div>
                                 <?php 
                                    $course_price       = ( $c_discount != 0 ) ? $c_discount : $c_price;
                                    $sgst_price         = ( $sgst / 100 ) * $course_price;
                                    $cgst_price         = ( $cgst / 100 ) * $course_price;
                                    $total_course_price = $course_price + $sgst_price + $cgst_price;
                                 ?>
                                  <div style="<?php $styles = "display:none;";echo ($c_is_free == 1) ? $styles : '';?>" class="course_price_div">
                                    <div class="col-md-6 pricing-column-right">
                                        <div class="strong-title">Total Price</div>
                                        <div id="tax-table" class="table-holder">
                                        <table class="billing-table">
                                            <tbody>
                                                <tr>
                                                <td>Bundle Price</td>
                                                <td class="text-right">
                                                    <span style="font-family: Roboto, sans-serif;">₹</span>
                                                    <span id="course_price"><?php echo $course_price; ?></span> 
                                                </td>
                                                        
                                                </tr>
                                                <tr>
                                                <td>CGST(6%)</td>
                                                <td class="text-right"> 
                                                    <span style="font-family: Roboto, sans-serif;">₹</span>
                                                    <span id="cgst_price"><?php echo round($sgst_price,2); ?></span>
                                                </td>
                                                </tr>
                                                <tr>
                                                <td>SGST(6%)</td>
                                                <td class="text-right">
                                                    <span style="font-family: Roboto, sans-serif;">₹</span>
                                                    <span id="sgst_price"><?php echo round($cgst_price,2); ?></span>
                                                </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <div class="d-flex justify-between bundle-total-row">
                                            <div><b>Total</b></div>
                                            <div>
                                            <b>
                                                <span style="font-family: Roboto, sans-serif;">₹</span>
                                                <span id="total_course_price"><?php echo round($total_course_price,2); ?></span>
                                            </b>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                  </div>
                                </div>
                                <div class="clearfix"></div>
                              </div>
                              <!-- Bundle pricing ends -->

                        </div>
                     </div>
                     <div class="form-group">
                        <div class="col-sm-12" style="margin-bottom: 15px;">
                           <input type="submit" id="" class="pull-right btn btn-green marg10" value="<?php echo lang('save') ?>">
                           <ripples></ripples>
                           </input>
                        </div>
                     </div>
                </form>
                  </div>
               </div>




               
            </div>
         </section>
      </div>
      



      <!-- Basic All Javascript -->
      <!-- bootstrap library -->
      <script>
        function categoryMOdalShow(){
            $('#select_Category').modal('show');
            $('#search_results input[type="checkbox"]').prop('checked',false);
            $.each(catObj.categoryTemp, function(index,categoryId){
                $('#check-'+categoryId).prop('checked',true);
                //console.log(categoryId);
            });
        }

      </script>
      <script type="text/javascript">
         $(document).ready(function(){
             initToolTip();
         });
         function initToolTip()
         {
             $('[data-toggle="tooltip"]').tooltip({
                 trigger : 'hover'
             });
         }
      </script>

      <script>
        $(document).ready(function() {
            checkMessageCount();
            App.init();
        });
      </script>
     
<!-- JS -->
<?php include_once 'footer.php';?>
<script src="<?php echo assets_url() ?>js/redactor/js/redactor.js"></script>
<script src="<?php echo assets_url() ?>js/redactor/js/table.js"></script>
<script src="<?php echo assets_url() ?>js/redactor/js/alignment.js"></script>
<?php  
    $saved_category_ids  = array('cat' => $c_category); 
    $saved_category_ids  = json_encode($saved_category_ids);
?>
<script>
var Category = function(){
    this.categoryTemp    = [];
    this.categoryPerman  = [];
    this.totalCategories = 0;
}
var catObj             = new Category();
var savedCategoryIds   = JSON.parse('<?php echo $saved_category_ids; ?>');
catObj.categoryPerman  = savedCategoryIds.cat;
if(catObj.categoryPerman.includes("")){
    let index          = catObj.categoryPerman.indexOf("");
    catObj.categoryPerman.splice(index,1);
}
catObj.categoryTemp    = catObj.categoryPerman;
var allCategories      = JSON.parse('<?php echo json_encode($categories); ?>');
catObj.totalCategories = allCategories.length;
</script>




<script type="text/javascript">
    var __cgst          = Number('<?php echo $cgst; ?>');
    var __sgst          = Number('<?php echo $sgst; ?>');
    var __course_price  = Number(($("#c_discount").val()!=0)?$("#c_discount").val():$("#c_price").val());
    $( document ).ready(function() {
        tax_price_table();
    });
    $(".c_is_free").click(function()
    {
        var c_is_free = "";
        $('.c_is_free').each(function () {
            if( this.checked == true ){
                c_is_free = $(this).val();
            }
        });
        if( c_is_free == "1" )
        {
            $(".course_price_div").hide();
        }
        else
        {
            $(".course_price_div").show();
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
                    errorMessage='Discount price should be less than bundle price.';
                    $('#course_form').prepend(renderPopUpMessage('error', errorMessage));
                    goTo();
                    return false;
                }
                
           }
        }
        tax_price_table();
    });

    function tax_price_table()
    {
        if((Number($("#c_discount").val()))>(Number($("#c_price").val())))
        {
            $("#c_discount").val('0');
            // tax_price_table();
            errorMessage='Discount price should be less than course price.';
            $('#course_form').prepend(renderPopUpMessage('error', errorMessage));
            goTo();
            return false;
        }
        else
        {
            __course_price  = Number(($("#c_discount").val() > 0)?$("#c_discount").val():$("#c_price").val());
            if(Number($("#c_discount").val())>Number($("#c_price").val()))
            {
                __course_price  =  Number($("#c_price").val());
            }
       
            var sgst_price         = (__sgst / 100) * __course_price;
            var cgst_price         = (__cgst / 100) * __course_price;
            
            if($('#c_tax_inclusive').is(':checked')) 
            {
                __course_price         = (__course_price/(100+__sgst+__cgst))*100;
                var sgst_price         = __course_price *  (__sgst/100);
                var cgst_price         = (__cgst / 100) * __course_price;
                var total_course_price = __course_price+sgst_price+cgst_price;
            } 
            else if($('#c_tax_exclusive').is(':checked')) 
            {
                __course_price          = __course_price;
                var total_course_price  = __course_price + (sgst_price+cgst_price);
            }
           
            $("#course_price").html(__course_price.toFixed(2));
            $("#sgst_price").html(parseFloat(sgst_price).toFixed(2));
            $("#cgst_price").html(parseFloat(cgst_price).toFixed(2));
            // $("#sgst_price").html(sgst_price.toString().substring(0, sgst_price.toString().indexOf(".") + 3));
            // $("#cgst_price").html(cgst_price.toString().substring(0, cgst_price.toString().indexOf(".") + 3));
            $("#total_course_price").html(total_course_price.toFixed(2));
            $("#tax-table").show();
        }
    }



    function isNumber(evt, input ="") {
        evt             = (evt) ? evt : window.event;
        var charCode    = (evt.which) ? evt.which : evt.keyCode;
        // if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        //     return false;
        // }
        if ((evt.which != 46 || input % 1 != 0) && (evt.which < 48 || evt.which > 57)) {
            evt.preventDefault();
            return false;
        }
        
        return true;
    }
    
    function validateForm()
    {
        
        var categoryList    = $('#listing_category').val();
        //var categoryCount   = $('.select_Category:checkbox:checked').length;
        var categoryCount   = catObj.categoryPerman.length;
        var previewImage    = $('#site_logo').attr('data-id');
        var description     = $('#c_description').val();
        var accessDays      = $('#byday').val();
        var accessDate      = $('#by_date').val();
        var c_title         = $('#c_title').val().trim();
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

        if(c_title == ''){
            errorMessage += 'Please enter Bundle Title<br />';
            errorCount++;
        }else{
                $.ajax({
                    url: '<?php echo site_url('admin/bundle/ajax_name_check')?>',
                    global: false,
                    type: 'POST',
                    data: { b_id : '<?php echo $id; ?>', cb_title : c_title },
                    async: false, //blocks window close
                    success: function(data) { 
                        var res = jQuery.parseJSON(data);
                        if(res.error){
                            errorMessage += `The bundle title <b>${res.c_title}</b> is already in use<br />`;
                            errorCount++;
                        }
                    }
                });
            
        }
        
        if(categoryCount < 1){
            errorMessage += 'Please select atleast one category<br />';
            errorCount++;
        }

        if($('#c_is_paid').is(':checked')) 
        {
            if(($("#c_price").val()=='') || ($("#c_price").val() <= 0)){
                errorMessage += 'Bundle price should be greater than zero<br />';
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
                
                if(accessDays < 1 || accessDays > 999 /*accessDays.length > 3||accessDays.length < 1*/){
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
        if($('#c_discount').val()!=''||$('#c_discount').val()!='0')
        {
            var discount = $('#c_discount').val();
            if( parseInt(discount) > $('#c_price').val() ) {
                errorMessage    += 'Bundle Discount price should be less than Bundle price ';
                errorCount++;
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
        console.log(url);
        if(url=='success'){
            var messageObject = {
                'body': 'Items enrolled to Bundle',
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
                maxHeight  : '250px',
                imageUpload: admin_url + 'configuration/redactore_image_upload',
                plugins    : ['table', 'alignment', 'source'],
                callbacks  : {
                    imageUploadError: function(json, xhr) {
                        var erorFileMsg = "This file type is not allowed. upload a valid image.";
                        $('#course_form').prepend(renderPopUpMessage('error', erorFileMsg));
                        scrollToTopOfPage();
                        return false;
                    }
                }
            });

            // $(document).on('click', '.c_is_free', function() {
                // alert("Dd");
                // if ($(this).is(':checked')) {
                //     // $("#course_price_val").hide();
                //     $(".course_price_div").hide();
                //     // $("#course_discount_val").hide();
                // } else {
                //     // $("#course_price_val").show();
                //     // $("#course_discount_val").show();
                //     $(".course_price_div").css('display','block');
                //     $(".course_price_div").show();
                    
                // }
            // });
        });
        
        //Live Price update
        var cb_price = $('#cb_price'),
            cb_live_price = $('#cb_live_price');

        //Live Discount update
        var cb_discount = $('#cb_discount'),
            cb_live_discount = $('#cb_live_discount');

        function deleteCourse(course_id,bundle_id){
            var courseName      = $('#course_bundle_'+course_id).attr('data-name');
            var message         = 'Are you sure to delete item named '+courseName+' from this bundle? ';
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
                    console.log(data);
                    if(data['error'] == false)
                    {
                        $('#course_bundle_'+course_id).remove();
                        var bundle_count = $('#bundle_items_count').html();
                        $('#bundle_items_count').html(bundle_count-1);
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

    //Live Discount update
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
        if ($(".c_is_free").is(":checked")) {
            $("#c_price").attr('data-validation-optional', 'true');
        }

        c_discount.trigger("keyup");

        $("#site_logo_btn").change(function() {
            readImageData(this); //Call image read and render function
        });


    });
    var __course_loaded_img = '<?php echo base_url();?>uploads/default/catalog/default.jpg';
    function readImageData(imgData) {
        if (imgData.files && imgData.files[0]) {
            var readerObj = new FileReader();
            
            readerObj.onload = function(element) {
                $('#site_logo').attr('src', element.target.result);
                $('#site_logo').attr('data-id', 'true');
            //console.log(readerObj.result.split(';')[0].split('/')[1]);
            var validImageTypes = ['gif', 'jpeg', 'png', 'jpg'];
            if (!validImageTypes.includes(readerObj.result.split(';')[0].split('/')[1])) {
                $('#site_logo').attr('src',__course_loaded_img);
                $('#site_logo').attr('data-id', 'default.jpg');
                lauch_common_message('Image Size', 'The image you have choosen is not supported! please choose a valid file.');
                $('#site_logo_btn').val('');
                return false;
            }
                
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
    $(document).on('click','.category-rmv',function(){
        var categoryId = $(this).attr('id');
        if(catObj.categoryPerman.includes(categoryId)){
            $('input.select_all_categories:checkbox').prop('checked',false);
            let index = catObj.categoryPerman.indexOf(categoryId);
            if(index > -1){
                catObj.categoryTemp.splice(index,1);
            }
            catObj.categoryTemp = catObj.categoryPerman;    
        }

        $('.select_Category:checkbox:checked').each(function () {
            if( $(this).attr('id') == categoryId){
                $(this).prop('checked', false);
            }
        });
        addCategory();
    });
    // $('#listing_category').multiselect({
    //         includeSelectAllOption: ($('#listing_category option').length>1),
    //         buttonWidth:'100%',
    //         numberDisplayed:6
    //     });
        // $('#listing_language').multiselect({
        //     includeSelectAllOption: ($('#listing_language option').length>1),
        //     buttonWidth:'100%',
        //     numberDisplayed:6
        // });
       

</script>


      
    
    <script>
        $('.bundle-title-edit').on('click',function(){
            $(".edit1").toggleClass("edit");
        })
    </script>






<!-- modals -->

<div class="modal fade" data-backdrop="static" data-keyboard="false" id="select_Category" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-small" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                    <h4 class="modal-title" id="course_group_create">Add Category</h4>
                </div>
                <div class="modal-body ">

                    <div class="inside-box pos-rel pad-top50" id="users_new_group_wrapper">

                        <!-- Nav section inside this wrap  --> <!-- START -->
                        <!-- =========================== -->
                        <div class="container-fluid nav-content pos-abslt width-100p nav-js-height">

                            <div class="row">
                                <div class="rTable content-nav-tbl normal-tbl" 
                                style="border-bottom: 1px solid #ccc;">
                                    <div class="rTableRow">
                                        <div class="rTableCell">
                                            <a href="javascript:void(0)" class="select-all-style">
                                                <label> 
                                                    <input value="1" class="select_all_categories" type="checkbox">  Select All
                                                </label>
                                                <span id="reflectCount"></span>
                                            </a>
                                        </div>

                                        <div class="rTableCell">
                                            <div class="input-group">
                                                <input type="text" class="form-control srch_txt" id="user_keyword" placeholder="Search Category">
                                                <span id="searchclear" style="">×</span>
                                                <a class="input-group-addon" id="user_keyword_btn">
                                                    <i class="icon icon-search"> </i>
                                                </a>
                                            </div>
                                        </div>
                                        
                                    </div>
                                    
                                </div>

                                <!-- multiselect -->
                                <span id="search_results">
                                <?php foreach ($categories as $category): ?>
                                    <div class="checkbox-wrap users-to-add-in-new-group" id="">    
                                        <span class="chk-box">        
                                            <label class="font14">            
                                                <input type="checkbox" <?php if( in_array( $category['id'], $c_category ) ){ echo "checked"; } ?> name="c_category[]" id ="check-<?php echo $category['id']; ?>" value="<?php echo $category['ct_name']."---".$category['id']; ?>" class="select_Category"> 
                                                <span class="student-name" title="<?php echo $category['ct_name']; ?> "><?php echo $category['ct_name']; ?></span>       
                                            </label>    
                                        </span>    
                                        <span class="email-label pull-right"></span>
                                    </div>
                                <?php endforeach;?>
                                </span>
                                <!-- multiselect ends -->

                            </div>
                        </div>
                        <!-- Nav section inside this wrap  --> <!-- END -->
                        <!-- =========================== -->

                    </div>
                </div>
                <div class="modal-footer">
                    <a type="button" class="btn btn-red " data-dismiss="modal" >CANCEL</a>
                    <span onclick="addCategory()" id="add_category" type="button" class="btn btn-green">ATTACH</span>
                </div>
            </div>
        </div>
    </div>

    <script>

    // $('#add_category').click(function(){
    //     console.log('clicked');
    //     catObj.categoryPerman           = catObj.categoryTemp;
    //     addCategory();
    // });
     
     function addCategory(){
        
        var category_length             = 0;
        var category_more_list          = 0;
        var category_list_html          = "";
        catObj.categoryPerman           = catObj.categoryTemp;
        $.each(catObj.categoryPerman, function( index, categoryId ) {
            var category            = $('#check-'+categoryId).val();
            var category_list           = [];
            if(category != undefined && category.length > 0 && category.includes("---")){
                category_list           = category.split("---");
            } else {
                $.each(allCategories, function(index, category){
                    if(category.id == categoryId){
                        category_list[0] = category.ct_name;
                        category_list[1] = category.id;
                        return false;
                    }
                });
            }
            
            category_length             = category_length + parseInt(category_list[0].length);
            
            category_list_html         += '<input type="hidden" value="'+category_list[1]+'" name="c_category[]">';
            if( category_length <= 110){
            category_list_html         += '<div class="category-item">';
            category_list_html         += '<span>'+category_list[0]+'</span>';
            category_list_html         += '<span id="'+category_list[1]+'" class="category-rmv">&times;</span></div>';
            }else{
            category_more_list          = category_more_list + 1;
            }
            
        });
        if( category_more_list ){
            category_list_html         += '<div onClick="categoryMOdalShow()" class="more-category-item">';
            category_list_html         += '<span>+<span class="category_more_number">'+ category_more_list +'<span>more</span></div>';
        }
        category_list_html             += '<div onClick="categoryMOdalShow()" class="add-category-item">';
        category_list_html             += ' <span>Add Category</span>';
        category_list_html             += ' </div>';
        $('.category-list').html(category_list_html);
        $('#select_Category').modal('hide');

    }

    $(document).on('change','.select_all_categories',function(){
        if( $(this).prop('checked') ){
            $('input.select_Category:checkbox').prop('checked','true');
            $('input.select_Category:checkbox').each(function(){
                let categoryId   = $(this).attr('id');
                let categoryList = categoryId.split('-');
                let id           = categoryList[1];
                if(!catObj.categoryTemp.includes(id)){
                    catObj.categoryTemp.push(id);
                }
                
            });
            //console.log(catObj.categoryTemp);
        }else{
            $('input.select_Category:checkbox').prop('checked',false);
            catObj.categoryTemp = [];
            //console.log(catObj.categoryTemp);
        }
    });
    $(document).on('change','.select_Category',function(){
        let categoryId   = $(this).attr('id');
        //console.log(categoryId);
        let categoryList = categoryId.split('-');
        let id           = categoryList[1];
        //console.log(catObj.categoryTemp);
        let checked      = 0;
        let unselected_values = [];
        if($(this).prop('checked') == true) {
            checked      = 1;
            $('input.select_Category:checkbox:not(:checked)').each(function () {
                unselected_values.push($(this).val());
            });
            if( jQuery.isEmptyObject(unselected_values) ){
                $('input.select_all_categories:checkbox').prop('checked','true');
            }
            //console.log('checked');
        } else {
            checked      = 0;
            $('input.select_all_categories:checkbox').prop('checked',false);
            //console.log('unchecked');
        }
        if(catObj.categoryTemp.includes(id)){
            //console.log('exist');
            if(checked === 0){
                $('input.select_all_categories:checkbox').prop('checked',false);
                let index = catObj.categoryTemp.indexOf(id);
                if(index > -1){
                    catObj.categoryTemp.splice(index,1);
                }
                
            } 
        } else {
            if(checked === 1){
                catObj.categoryTemp.push(id);
            }
        }
        //console.log("change"+catObj.categoryTemp);
    });
    
    // $(document).on('change','.select_Category',function(){
    //     var unselected_values = [];
    //     if( $(this).prop('checked') == false ){
    //         $('input.select_all_categories:checkbox').prop('checked',false);
    //     }else{
    //         $('input.select_Category:checkbox:not(:checked)').each(function () {
    //             unselected_values.push($(this).val());
    //         });
    //         if( jQuery.isEmptyObject(unselected_values) ){
    //             $('input.select_all_categories:checkbox').prop('checked','true');
    //         }
    //     }
    // });

    $(document).on('click', '#user_keyword_btn', function(){
        var user_keyword = $('#user_keyword').val();
        if(user_keyword.match(/^ \s+ $/))
        {
            lauch_common_message('KEYWORD MISSING', 'Please enter a valid input to search.');
            $('#user_keyword').val('');
        } 
        else
        {
            searchUser($('#user_keyword').val().trim());
        }
   });

   function searchUser( userKeyword )
   {
        //var selected_categories = <?php //echo json_encode($c_category); ?>;
        userKeyword             = typeof userKeyword != 'undefined' ? userKeyword : '';
        var search_html         = "";
        $.ajax({
            url: admin_url+'bundle/categories',
            type: "POST",
            data:{"is_ajax":true, "keyword":userKeyword},
            success: function(response) {
                var data        = $.parseJSON(response);
                if(data.length > 0 )
                {
                    $.each(data, function( index, category ) {
                        if(jQuery.inArray( category.id, catObj.categoryTemp ) >= 0){
                            var checked = "checked";
                        }else{
                            var checked = "";
                        }
                        search_html += '<div class="checkbox-wrap users-to-add-in-new-group" id=""> ';
                        search_html += '<span class="chk-box">';
                        search_html += '<label class="font14">';
                        search_html += '<input type="checkbox"  name="c_category[]" '+checked+' id="check-'+category.id+'" value="'+category.ct_name+'---'+category.id+'" class="select_Category">';
                        search_html += '<span class="student-name" title="'+category.ct_name+'">'+category.ct_name+'</span>';
                        search_html += '</label></span><span class="email-label pull-right"></span></div>';
                    });
                    $('#search_results').html(search_html);
                }else{
                    search_html = "";   
                    search_html += '<div class="checkbox-wrap users-to-add-in-new-group" id=""> ';
                    search_html += 'No category found';
                    search_html += '</div>';
                    $('#search_results').html(search_html);
                }
                let flag = 1;
                let counter = 0;
                $('input.select_Category:checkbox').each(function(){
                    counter++;
                    if($(this).prop('checked') == false ){
                        flag = 0;
                    }
                });
                if(flag === 1 && counter > 0){
                    $('.select_all_categories').prop('checked',true);
                } else {
                    $('.select_all_categories').prop('checked',false);
                }
            }
        });
   }
   $('#searchclear').on('click',function(){
        searchUser( keyword = "" );
        if(catObj.categoryTemp.length == catObj.totalCategories) {
            $('.select_all_categories').prop('checked',true);    
        } else{
            $('.select_all_categories').prop('checked',false);
        }
   });

  $( function() {
        $( "#by_date" ).datepicker({
            language: 'en',
            minDate: 'today',
            dateFormat: 'dd-mm-yy',
            autoClose: true
        });
  });
    </script>

