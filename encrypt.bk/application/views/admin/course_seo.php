
<?php include_once 'training_header.php'; ?>

<?php $teacher  = $this->auth->is_logged_in(false, false, 'teacher'); ?>
<link rel="stylesheet" href="<?php echo assets_url() ?>css/datepicker.min.css">
<script type="text/javascript" src="<?php echo assets_url() ?>js/datepicker.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/datepicker.en.js"></script>


<section class="content-wrap cont-course-big top-spacing content-wrap-align">
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
                        <form class="form-horizontal" method="post" action="<?php echo admin_url('course_settings/seo/'.$id); ?>">

                            <div class="form-group">    
                                <div class="col-sm-12">
                                    <?php echo lang('course_friendly_url_lbl') ?>
                                    <div class="input-group">
                                        <span class="input-group-addon light-color" id="basic-addon1"><?php echo config_item('acct_domain') ?></span>
                                        <input type="text" class="form-control" name="cb_slug" value="<?php echo isset($cb_slug)?$cb_slug:'' ?>" placeholder="<?php echo lang('course_friendly_url_ph') ?>" aria-describedby="basic-addon1">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group"> 
                                <div class="col-sm-12">
                                    <?php echo lang('course_meta_title_lbl') ?>
                                    <input type="text" class="form-control" name="cb_meta" value="<?php echo isset($cb_meta)?$cb_meta:'' ?>" placeholder="<?php echo lang('course_meta_title_ph') ?>">
                                </div>
                            </div>

                            <div class="form-group"> 
                                <div class="col-sm-12">
                                    <?php echo lang('course_meta_description_lbl') ?>
                                    <textarea class="form-control" id="cb_meta_description" maxlength="200" placeholder="eg: Course Meta description" rows="3" name="cb_meta_description" ><?php echo isset($cb_meta_description)?$cb_meta_description:'' ?></textarea>
                                    <span id="cb_meta_description_remain" class="pull-right my-italic"></span>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-12">
                                    <input type="hidden"  name="cb_title" value="<?php echo $cb_title ?>">
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
        <!-- JS -->
<script src="<?php echo assets_url() ?>js/redactor/js/redactor.js"></script>
<script src="<?php echo assets_url() ?>js/redactor/js/table.js"></script>
<script src="<?php echo assets_url() ?>js/redactor/js/alignment.js"></script>

<script type="text/javascript" src="<?php echo assets_url() ?>js/course_settings.js"></script>
<?php include_once 'footer.php';?>