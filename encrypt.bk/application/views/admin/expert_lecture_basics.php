<?php include_once 'header.php'; ?>
<?php 
$GLOBALS['id']        = $id;
$GLOBALS['selected']  = 'selected="selected"';
$GLOBALS['checked']   = 'checked="checked"';
?>
<!-- MAIN TAB --> <!-- STARTS -->
<section class="courses-tab base-cont-top-heading">
    <a href="<?php echo admin_url('expert_lectures') ?>"><h4> <?php echo htmlentities($el_title) ?> </h4></a>
    <ol class="nav nav-tabs offa-tab" style="padding-left: 40px;">
        <!-- active tab start -->
        <li class="active">
            <a href="javascript:void(0)"> <?php echo lang('basic') ?></a>
            <span class="active-arrow"></span>
        </li>
    </ol>
</section>
<!-- MAIN TAB --> <!-- END -->

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
                        <form class="form-horizontal" id="expert_form" action="<?php echo admin_url('expert_lectures/basics/'.$id) ?>" method="POST">
                            <!-- Text Box  -->
                            <div class="form-group">

                                <div class="col-sm-12">
                                    <?php echo lang('expert_lecture_title') ?> * : 
                                    <input type="text" maxlength="80" class="form-control" data-validation="required" data-validation-error-msg-required="<?php echo lang('expert_name_required') ?>" placeholder="eg: Mathematical Calculations" name="el_title" id="el_title" value="<?php echo htmlentities($el_title) ?>" />
                                </div>
                            </div>
                            
                            <!-- Text Box Addons  -->
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <?php echo lang('expert_lecture_url') ?> * : 
                                    <input type="text" data-validation="required url" data-validation-error-msg-required="<?php echo lang('youtube_required_error') ?>" onBlur="youtube_parser()" class="form-control" placeholder="Eg: https://www.youtube.com/embed/SAsdfQWEhi5" name="el_url" id="el_url" value="<?php echo isset($el_url)?$el_url:''; ?>" />

                                    <input type="hidden" id="youtube_video_image" value="<?php echo isset($el_image)?$el_image:'' ?>" name="el_image" />
                                    <input type="hidden" id="youtube_video_thumbnail" value="<?php echo isset($el_thumbnail)?$el_thumbnail:'' ?>" name="el_thumbnail" />
                                </div>
                            </div>

                            <!-- This is a test message  -->
                            <div class="form-group">
                                <div class="col-sm-4">
                                    <?php echo lang('status') ?>:
                                    <select class="form-control" name="el_status" id="el_status"> 
                                        <option value="1" <?php echo ($el_status == 1)?$GLOBALS['selected']:'';  ?>><?php echo lang('active') ?></option>
                                        <option value="0" <?php echo ($el_status == 0)?$GLOBALS['selected']:'';  ?>><?php echo lang('inactive') ?></option>
                                    </select>
                                </div>
                            </div>

                            <!-- This is a test message  -->
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <input type="button" id="expert_lecture_submit" class="pull-right btn btn-green marg10" value="<?php echo lang('save') ?>">

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
<script src="<?php echo assets_url() ?>js/expert_lecture_settings.js"></script>
<?php include_once 'footer.php'; ?>