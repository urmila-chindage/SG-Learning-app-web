<?php include_once 'header.php';?>

<!-- MAIN TAB --> <!-- STARTS -->
<script>
    $(document).ready(function(){
        /* Function defined in System JS for updating characters left */
        validateMaxLength('c_meta_description'); 
     });
</script>
<section class="courses-tab base-cont-top-heading">
    <h4> <?php echo isset($c_title)?$c_title:''; ?></h4>
    <ol class="nav nav-tabs offa-tab" style="padding-left: 40px;">
        <!-- active tab start -->
        <li>
            <a href="<?php echo admin_url('catalog_settings').'basics/'.$id ?>"> <?php echo lang('basic_tab') ?></a>
            <span class="active-arrow"></span>
        </li>
        <!-- active tab end -->
        <li>
            <a href="<?php echo admin_url('catalog_settings').'advanced/'.$id ?>"><?php echo lang('advanced_tab') ?></a>
            <span class="active-arrow"></span>
        </li>

        <li class="active">
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
                    <div class="form-horizontal">
                        <?php include_once('messages.php') ?>
                        <form class="form-horizontal" method="post" action="<?php echo admin_url('catalog_settings/seo/'.$id); ?>">
                            <div class="form-group">    
                                <div class="col-sm-12">
                                    <?php echo lang('catalog_friendly_url_lbl') ?>
                                    <div class="input-group">
                                        <span class="input-group-addon light-color" id="basic-addon1"><?php echo config_item('acct_domain') ?></span>
                                        <input type="text" class="form-control" value="<?php echo isset($c_slug)?$c_slug:''; ?>" name="c_slug" placeholder="<?php echo lang('catalog_friendly_url_ph') ?>" aria-describedby="basic-addon1">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group"> 
                                <div class="col-sm-12">
                                    <?php echo lang('catalog_meta_title_lbl') ?>
                                    <input type="text" name="c_meta" class="form-control" value="<?php echo isset($c_meta)?$c_meta:''; ?>" placeholder="<?php echo lang('catalog_meta_title_ph') ?>">
                                </div>
                            </div>

                            <div class="form-group"> 
                                <div class="col-sm-12">
                                    <?php echo lang('catalog_meta_description_lbl') ?>
                                    <textarea id="c_meta_description" maxlength="200" onkeyup="validateMaxLength('c_meta_description')" name="c_meta_description" placeholder="eg: Catalog SEO description" class="form-control" rows="3" ><?php echo isset($c_meta_description)?$c_meta_description:''; ?></textarea>
                                    <span id="c_meta_description_char_left" class="pull-right my-italic"></span>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-12">
                                    <input type="submit" class="pull-right btn btn-green marg10" value="<?php echo lang('save') ?>">
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
<?php include_once 'footer.php';?>