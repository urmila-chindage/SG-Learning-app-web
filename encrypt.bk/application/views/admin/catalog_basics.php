<?php include_once 'header.php';?>
<script>
    $(document).ready(function(){
        /* Function defined in System JS for updating characters left */
        validateMaxLength('c_description'); 
     });
</script>
<!-- MAIN TAB --> <!-- STARTS -->
<section class="courses-tab base-cont-top-heading">
    <h4><?php echo isset($c_title)?$c_title:''; ?></h4>
    <ol class="nav nav-tabs offa-tab" style="padding-left: 40px;">
        <!-- active tab start -->
        <li class="active">
            <a href="<?php echo admin_url('catalog_settings').'basics/'.$id ?>"> <?php echo lang('basic_tab') ?></a>
            <span class="active-arrow"></span>
        </li>
        <!-- active tab end -->
        <li>
            <a href="<?php echo admin_url('catalog_settings').'advanced/'.$id ?>"><?php echo lang('advanced_tab') ?></a>
            <span class="active-arrow"></span>
        </li>
        <li>
            <a href="<?php echo admin_url('catalog_settings').'seo/'.$id ?>"><?php echo lang('seo_tab') ?></a>
            <span class="active-arrow"></span>
        </li>
    </ol>
</section>
<!-- MAIN TAB --> <!-- END -->

<?php include_once 'catalog_sidebar.php';?>        
        
<section class="content-wrap small-width base-cont-top-heading">
    <!-- LEFT CONTENT --> <!-- STARTS -->
    <!-- ===========================  -->

    <div class="left-wrap col-sm-12 pad0">
        <!-- Nav section inside this wrap  --> <!-- START -->
        <!-- =========================== -->

        <div class="container-fluid course-create-wrap">
            <div class="row-fluid course-cont">
                <div class="col-sm-12">
                    <div class="form-horizontal" id="catalog_show_message">
                        <?php include_once('messages.php') ?>
                        <form class="form-horizontal" id="catalog_basics" method="post" action="<?php echo admin_url('catalog_settings/basics/'.$id); ?>">
                            <div class="form-group">
                                <div class="col-sm-12" id="catalog_settings_<?php echo $id ?>">
                                    <?php $c_status_class   = ($c_status)?'label-warning':'label-success'; ?>
                                    <?php $c_status         = ($c_status)?'deactivate':'activate'; ?> 
                                    <?php $c_action = $c_status; ?>
                                    <a href="javascript:void(0);" data-toggle="modal" onclick="changeCatalogStatus('<?php echo $id ?>', '<?php echo lang($c_action).' '.$c_title.' '.  lang('catalog') ?>', '<?php echo lang('change_status_message').' '.lang($c_action) ?>')" data-target="#publish-course" class="btn pull-right label <?php echo $c_status_class ?>"><?php echo strtoupper(lang($c_status)) ?></a>
                                </div>
                            </div>
                            <!-- Text Box  -->
                            <div class="form-group">

                                <div class="col-sm-12">
                                    <?php echo  lang('catalog_title_lbl') ?>
                                    <input type="text" name="c_title" maxlength="50" id="c_title" value="<?php echo isset($c_title)?$c_title:''; ?>" class="form-control" data-validation="required" data-validation-error-msg-required="<?php echo lang('title_required_error') ?>" placeholder="<?php echo lang('catalog_title_ph') ?>" />
                                </div>
                            </div>

                            <!-- Text Box Addons  -->
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <?php echo  lang('catalog_description_lbl') ?>
                                    <textarea class="form-control" maxlength="1000" onkeyup="validateMaxLength('c_description')" name="c_description" id="c_description" rows="3" data-validation="required" data-validation-error-msg-required="<?php echo lang('description_required_error') ?>" ><?php echo isset($c_description)?$c_description:'' ?></textarea>
                                    <span class="pull-right my-italic" id="c_description_char_left"></span>
                                </div>
                            </div>

                            <!-- Select Box  -->
                            <div class="form-group">

                                <div class="col-sm-4">
                                    <?php echo  lang('catalog_position_lbl') ?>
                                    <input type="text" class="form-control" onkeypress="return preventAlphabets(event)" maxlength="5" data-validation="number" data-validation-allowing="range[1;1999999999999500]" data-validation-error-msg-number="<?php echo lang('enter_range_error') ?>" name="c_position" value="<?php echo isset($c_position)?$c_position:''; ?>" placeholder="<?php echo lang('catalog_position_ph') ?>">
                                </div>

                                <div class="col-sm-8">
                                    <?php echo  lang('catalog_category_lbl') ?>
                                    <input type="text" class="form-control" maxlength="50" name="c_category" id="c_category" data-validation="required" data-validation-error-msg-required="<?php echo lang('category_required_error') ?>" autocomplete="off" value="<?php echo isset($c_category)?$c_category:''; ?>" placeholder="Category name">
                                    <ul class="auto-search-lister" id="listing_category" style="display: none;">
                                    </ul>
                                </div>

                            </div>

                            <!-- Manage Course grid  -->
                            <!-- =====================  --> <!-- START  -->


                            <div class="row pad-top15">
                                <div class="col-sm-12">
                                    <h4 class="dsp-inline"><?php echo  lang('manage_catalog_courses_lbl') ?></h4>
                                    <a href="javascript:void(0)" onclick="addCoursesToCatalog()" data-toggle="modal" data-target="#add-catalog" class="btn btn-green pull-right"><?php echo  lang('add_catalog_course') ?></a>
                                </div>
                            </div>
                            <input type='hidden' name='c_courses' data-validation="required" data-validation-error-msg-required="Select one course" id='get_course_id' value='<?php echo (isset($c_courses)?json_encode(explode(',',$c_courses)):''); ?>' />
                            <div class="row">
                                <div class="col-sm-12 course-cont-wrap catalog-table"> 
                                    <div class="table mar-ver0 course-cont rTable" id="course_assigned_catalog" name="catalog_course_list" style="">
                                    </div>
                                </div>

                            </div>

                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-sm-12 font16">
                                        <span class="pull-right"><?php echo  lang('total_catalog_worth') ?><span class="green-font font-bold" id="course_assigned_total"></span></span>
                                    </div>
                                </div>
                            </div>
                            <!-- =====================  --> <!-- END  -->
                            <!-- Manage Course grid  -->


                            <!-- This is a test message  -->
                            <div class="form-group">    
                                <div class="col-sm-4 pad-top15">
                                    <label class="control-label">
                                        <input type="checkbox" name="c_is_free" id="c_is_free"value="1" <?php $checked = "checked"; echo ($c_is_free == 1)?$checked:'' ;?> >
                                        <?php echo  lang('catalog_free_lbl') ?>
                                    </label>
                                </div>
                                <div class="col-sm-4" id="catalog_price_val">
                                    <?php echo  lang('catalog_price_lbl') ?>
                                    <div class="input-group">
                                        <input type="text" class="form-control" maxlength="10" name="c_price" id="c_price" value="<?php echo isset($c_price)?$c_price:''; ?>" placeholder="<?php echo lang('catalog_price_ph') ?>" aria-describedby="basic-addon1" data-validation="number">
                                        <span class="input-group-addon" id="basic-addon1"><?php echo  lang('indian_rupees') ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-4" id="catalog_discount_val">
                                    <?php echo  lang('catalog_discount_lbl') ?>
                                    <div class="input-group">
                                        <input type="text" class="form-control" maxlength="10" name="c_discount" id="c_discount" value="<?php echo ((isset($c_discount) && $c_discount > 0)?$c_discount:'') ?>" placeholder="<?php echo lang('catalog_discount_ph') ?>" aria-describedby="basic-addon2" data-validation="number" data-validation-optional="true">
                                        <span class="input-group-addon" id="basic-addon2"><?php echo  lang('indian_rupees') ?></span>
                                    </div>
                                </div>
                            </div>


                            <div class="form-group">
                                <div class="col-sm-12">
                                    <input type="button" id="catalog_save_button" class="pull-right btn btn-green marg10" value="<?php echo  lang('save') ?>">

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


<script type="text/javascript" src="<?php echo assets_url() ?>js/catalog_settings.js"></script>
<script type="text/javascript">
    var catalog_id = <?php echo $id; ?>;
    var catalog_course = <?php echo (isset($c_courses)?json_encode(explode(',',$c_courses)):'0'); ?>
</script>  
<?php include_once 'footer.php';?>