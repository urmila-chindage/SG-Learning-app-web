<?php include_once 'header.php';?>

<!-- MAIN TAB --> <!-- STARTS -->
<section class="courses-tab base-cont-top-heading">
    <h4> <?php echo isset($c_title)?$c_title:''; ?> </h4>
    <ol class="nav nav-tabs offa-tab" style="padding-left: 40px;">
        <!-- active tab start -->
        <li>
            <a href="<?php echo admin_url('catalog_settings').'basics/'.$id ?>"> <?php echo lang('basic_tab') ?></a>
            <span class="active-arrow"></span>
        </li>
        <!-- active tab end -->
        <li class="active">
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
                    <div class="form-horizontal" id="catalog_div_youtube">
                        <form class="form-horizontal" id="catalog_form_youtube"  method="post" action="<?php echo admin_url('catalog_settings/advanced/'.$id); ?>">

                            <!-- Text Box Addons  -->
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <p> <?php echo lang('youtube_text') ?> </p>
                                    <div class="video-player" id="myCode">
                                        <iframe width="389" height="265" name="c_promo" src="<?php echo isset($c_promo)?generate_youtube_url($c_promo):''; ?>" frameborder="0" allowfullscreen></iframe>
                                    </div>

                                </div>
                            </div>

                            <!-- Text Box  -->
                            <div class="form-group">

                                <div class="col-sm-12">
                                    <p><?php echo lang('youtube_url_lbl') ?> </p>
                                    <input id="myUrl" type="text" data-validation="required url" data-validation-error-msg-required="<?php echo lang('youtube_required_error') ?>" class="form-control" value="<?php echo isset($c_promo)?$c_promo:''; ?>" name="c_promo" placeholder="<?php echo lang('youtube_url_ph') ?>">
                                </div>

                            </div>

                            <!-- This is a test message  -->
                            <div class="form-group">    
                                <div class="col-sm-12">
                                    <input type="submit" id="save_catalog_youtube" class="pull-right btn btn-green marg10" value="<?php echo lang('save')?>">
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
<?php 
 function generate_youtube_url($url=false)
{
    $pattern = 
        '%^# Match any youtube URL
        (?:https?://)?  # Optional scheme. Either http or https
        (?:www\.)?      # Optional www subdomain
        (?:             # Group host alternatives
          youtu\.be/    # Either youtu.be,
        | youtube\.com  # or youtube.com
          (?:           # Group path alternatives
            /embed/     # Either /embed/
          | /v/         # or /v/
          | /watch\?v=  # or /watch\?v=
          )             # End path alternatives.
        )               # End host alternatives.
        ([\w-]{10,12})  # Allow 10-12 for 11 char youtube id.
        $%x'
        ;
    $result = preg_match($pattern, $url, $matches);
    if ($result) {
        //return $matches[1];
        return 'https://www.youtube.com/embed/'.$matches[1];
    }
    return false;
}
 ?>  
<script type="text/javascript" src="<?php echo assets_url() ?>js/catalog_settings.js"></script>
<?php include_once 'footer.php';?>