<?php include_once 'header.php'; ?>

<!-- MAIN TAB --> <!-- STARTS -->
<section class="courses-tab base-cont-top-heading">
    <a href="<?php echo admin_url('notification') ?>"><h4> <?php echo htmlentities($n_title) ?> </h4></a>
    <ol class="nav nav-tabs offa-tab" style="padding-left: 40px;">
        <!-- active tab start -->
        <li>
            <a href="<?php echo admin_url('notification/basics/' . $id) ?>"> <?php echo lang('basic') ?></a>
            <span class="active-arrow"></span>
        </li>
        <!-- active tab end -->
        <li class="active">
            <a href="javascript:void(0)"> <?php echo lang('seo') ?></a>
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
                        <form class="form-horizontal" action="<?php echo admin_url('notification/seo/'.$id) ?>" method="POST">
                            <!-- Text Box  -->
                            <div class="form-group">    
                                <div class="col-sm-12">
                                    <?php echo lang('friendly_url') ?>:
                                    <div class="input-group">
                                        <span id="basic-addon1" class="input-group-addon light-color"><?php echo base_url() ?></span>
                                        <input type="text" id="n_slug" name="n_slug" aria-describedby="basic-addon1" placeholder="notification name" class="form-control" value="<?php echo $n_slug ?>">
                                    </div>
                                </div>
                            </div>

                            <!-- Text Box Addons  -->
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <?php echo lang('notification_meta') ?>  : 
                                    <input type="text" id="n_meta" name="n_meta" aria-describedby="basic-addon1" placeholder="eg: Notification Meta Title" class="form-control" value="<?php echo $n_meta ?>">
                                </div>
                            </div>
                            
                            
                            <!-- Text Box Addons  -->
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <?php echo lang('notification_seo') ?>  : 
                                    <textarea class="form-control"  onkeyup="validateMaxLength(this.id)" maxlength="1000" placeholder="eg: Notification SEO description" rows="3" name="n_seo_title" id="n_seo_title" ><?php echo $n_seo_title ?></textarea>
                                    <span class="pull-right my-italic" id="n_seo_title_char_left">  <?php echo intval(1000-strlen($n_seo_title)) ?> <?php echo lang('charectors_left') ?></span>
                                </div>
                            </div>



                            <!-- This is a test message  -->
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <input type="submit" class="pull-right btn btn-green marg10" value="SAVE">

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
<script src="<?php echo assets_url() ?>js/notification_seo_settings.js"></script>
<?php include_once 'footer.php'; ?>