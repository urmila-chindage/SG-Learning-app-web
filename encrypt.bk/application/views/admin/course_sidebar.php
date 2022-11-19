<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */ ?>
<link href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/jquery.rateyo.min.css" rel="stylesheet">
<script type="text/javascript">
    var ratting   = '<?php echo $course_rating; ?>';
</script>
<?php 
    $has_s3 = $this->settings->setting('has_s3');
    if($has_s3["as_superadmin_value"] && $has_s3["as_siteadmin_value"]){
        $has_s3_flag = 1;
    }else{
        $has_s3_flag = 0;
    }
?>
<!-- SIDEBAR RIGHT SIDE FIXED --> <!-- START -->
<div class="right-wrap small-width base-cont-top-heading container-fluid pull-right custom-left-update">            
    <h4> <?php echo lang('course_card_preview') ?></h4>

    <!--  Adding list group  --> <!-- START  -->

    <div class="box-style card-prieview">
        <div class="img-chng">            
            <?php 
            $course_image = substr($cb_image, 0, -4);
            $dimension    = '_300x160.jpg';
            $course_image = $course_image.$dimension;
            ?>
            <img id="course_image" src="<?php echo (($cb_image == 'default.jpg')?default_course_path():course_path(array('course_id' => $id))).$course_image ?>" alt="<?php echo $cb_title; ?>" />
            <!--<form method="POST" enctype="multipart/form-data">-->
                <?php if(!isset($cb_image)){ ?> 
                    <div class="img-icon-holder">
                        <i class="icon icon-board1"></i>
                    </div>
                <?php } ?>
                <input type="file" name="file" class="course-image-upload-btn" id="cb_image" accept="image/*" title="No file chosen to upload">
                 <button class="btn btn-green pos-abs"><?php echo lang('course_change_image') ?></button>
                <a href="javascript:void(0)" class="heart-icon">
                    <i class="icon icon-heart"></i>
                </a>
            <!--</form>-->
        </div>
        <div class="img-content">
            <h4 id="cb_live_title"><?php echo isset($cb_title)?$cb_title:lang('course_live_title_default'); ?></h4>
            <?php if($this->auth->get_current_user_session('admin')): ?>
            <p id="cb_tutor">By: <a href="javascript:void(0);" data-toggle="modal" onclick="addTeacherToCourse()" data-target="#add-teacher" class="link-style add-teach"><?php echo lang('course_add_teacher') ?></a></p>
            <?php endif; ?>
            <div class="ratting"><div id="rateYo"></div><span><?php if($course_rating!='0') { echo $course_rating; } ?></span></div>
            <!--<p>
                <i class="icon icon-star rating-stars star-active"></i>
                <i class="icon icon-star rating-stars star-active"></i>
                <i class="icon icon-star rating-stars star-active"></i>
                <i class="icon icon-star rating-stars"></i>
                <i class="icon icon-star rating-stars"></i>
                (8060)
            </p>-->
            <span id="cb_live_price" style="display: none;" class="price-bold strike-txt"><?php if($cb_is_free == 1){ echo 'FREE'; }else{ ?>Rs <?php echo isset($cb_price)?$cb_price:lang('course_live_price_default'); ?></span><span id="cb_live_discount" class="price"><?php echo isset($cb_discount)?$cb_discount:lang('course_live_discount_default'); } ?></span>
        </div>
    </div>

    <!--  Adding list group  --> <!-- END  -->

    <?php /* ?><div class="note">
        <p><?php echo lang('course_card_note') ?></p>
        <p>
            Lorem ipsum is simply a dummy text. Lorem ipsum is simply a dummy text. 

        </p>
    </div><?php */ ?>

</div>
<!-- SIDEBAR RIGHT SIDE FIXED --> <!-- END -->
        

<script type="text/javascript">
    var course_id       = <?php echo $id; ?>;
</script>
<script type="text/javascript" src="<?php echo assets_url().'themes/'.$this->config->item('theme').'/js/jquery.rateyo.min.js'; ?>" ></script>
<script type="text/javascript">
$(document).ready(function(){
    $("#rateYo").rateYo({
        starWidth: "18px",
        rating: ratting,
        readOnly: true
    });
});
</script>


