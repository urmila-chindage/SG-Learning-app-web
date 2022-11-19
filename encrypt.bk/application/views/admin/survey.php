<?php include_once 'header.php'; ?>

<?php include_once "cms_tab.php"; ?>
<link rel="stylesheet" href="<?php echo assets_url() ?>css/datepicker.min.css">
<link rel="stylesheet" href="<?php echo assets_url() ?>css/timepicker.css">
<!-- ADDING REDACTOR PLUGIN INSIDE -->
<!-- ############################# --> <!-- START -->
    <link rel="stylesheet" href="<?php echo assets_url() ?>css/redactor/css/redactor.css" />
    <link rel="stylesheet" href="<?php echo assets_url() ?>css/redactor/css/alignment.css" />
<!-- ############################# --> <!-- END -->
<!-- MAIN TAB --> <!-- STARTS -->
<!--<section class="courses-tab base-cont-top-heading">
    <a href="<?php echo admin_url('survey') ?>"><h4> <?php echo lang('survey') ?> </h4></a>
    <ol class="nav nav-tabs offa-tab" style="padding-left: 40px;">
         active tab start 
        <li class="active">
            <a href="javascript:void(0)"> <?php echo lang('basic') ?></a>
            <span class="active-arrow"></span>
        </li>
    </ol>
</section>-->
<!-- MAIN TAB --> <!-- END -->

<section class="content-wrap base-cont-top-heading">

    <!-- LEFT CONTENT --> <!-- STARTS -->
    <!-- ===========================  -->

    <div class="left-wrap col-sm-12 pad0">

        <!-- Nav section inside this wrap  --> <!-- START -->
        <!-- =========================== -->

        <div class="container-fluid course-create-wrap">
            <div class="row-fluid course-cont">
                <div class="col-sm-12">
                    <div class="form-horizontal" id="page_form">
                        <?php include_once('messages.php');?>
                        <form class="form-horizontal" action="<?php echo admin_url('survey') ?>" method="POST">
                            <!-- Text Box  -->
                            <div class="form-group">

                                <div class="col-sm-12">
                                    <?php echo lang('survey_title') ?> * : 
                                    <input type="text" class="form-control" maxlength="80" placeholder="eg: Survey Title" name="s_title" id="s_title" value="<?php echo htmlentities($survey['s_title']) ?>" />
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <?php echo lang('survey_desc') ?> * : 
                                    <textarea class="form-control" onkeyup="validateMaxLength(this.id)" maxlength="200" id="s_description" value=""><?php echo htmlentities($survey['s_description']) ?></textarea>
                                   <label class="pull-right" id="s_description_char_left">200 characters left</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-4">
                                    <?php echo lang('start_date') ?> * : 
                                    <input type="text" placeholder="" id="start_date" class="form-control"  autocomplete="off" value="<?php echo htmlentities($survey['s_start_date']) ?>">
                                </div>
                                <div class="col-sm-4">
                                    <?php echo lang('end_date') ?> * : 
                                    <input type="text" placeholder="" id="end_date" class="form-control"  autocomplete="off" value="<?php echo htmlentities($survey['s_end_date']) ?>">
                                </div>
                            </div>
                            <!-- Text Box Addons  -->
                            <div class="form-group internal_page_wrapper">
                                <div class="col-sm-12">
                                    <?php echo lang('survey_body') ?> * :
                                    <textarea class="form-control" rows="10" name="s_content" id="s_content"><?php echo htmlentities($survey['s_html']) ?></textarea>
                                </div>
                            </div>

                            <!-- This is a test message  -->
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <input type="button" id="save_survey_details" class="pull-right btn btn-green marg10" value="SAVE">

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
<script src="<?php echo assets_url() ?>js/redactor/js/source.js"></script>
<script src="<?php echo assets_url() ?>js/redactor/js/alignment.js"></script>
<script src="<?php echo assets_url() ?>js/jquery.form-validator.min.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/datepicker.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/datepicker.en.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/jquery.timepicker.js"></script>
<script src="<?php echo assets_url() ?>js/survey_settings.js"></script>
<script>
    $(document).ready(function(){
        validateMaxLength('s_description');
        var today = new Date();
        $("#start_date").datepicker({
            language: 'en',
            minDate: today,
            dateFormat: 'dd-mm-yyyy',
            autoClose: true
        });
        $("#end_date").datepicker({
            language: 'en',
            minDate: today,
            dateFormat: 'dd-mm-yyyy',
            autoClose: true
        });
    });
</script>
<?php include_once 'footer.php'; ?>