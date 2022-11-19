<!-- SIDEBAR RIGHT SIDE FIXED --> <!-- START -->
<div class="right-wrap small-width base-cont-top-heading container-fluid pull-right">
    <h4> <?php echo lang('catalog_card_preview') ?></h4>
    <!--  Adding list group  --> <!-- START  -->

    <div class="box-style card-prieview">
        <div class="img-chng">
            <?php if(isset($c_image)){ ?>                
                <?php 
                $catalog_image = substr($c_image, 0, -4);
                $dimension     = '_300x160.jpg';
                $catalog_image = $catalog_image.$dimension;
                ?>
            <img id="catalog_image" src="<?php echo (($c_image == 'default.jpg')?default_catalog_path():catalog_path()).$catalog_image ?>" alt="<?php echo $c_title; ?>" />
            <?php } ?>
                <!--<form method="POST" enctype="multipart/form-data">-->
                <?php if(!isset($c_image)){ ?> 
                    <div class="img-icon-holder">
                        <i class="icon icon-board1"></i>
                    </div>
                <?php } ?>
                <input type="file" name="file" class="catalog-image-upload-btn" id="c_image" accept="image/*" title="No file chosen to upload">
                <button class="btn btn-green pos-abs"><?php echo lang('catalog_change_image') ?></button>
                <a href="#!." class="heart-icon">
                    <i class="icon icon-heart"></i>
                </a>
                <div class="ribbon-dark-green">
                    <?php echo lang('combo_ribbon') ?>
                </div>
            <!--</form>-->
        </div>
        <div class="img-content">
            <div class="ribbon-light-green">
                <b id="catalog_live_courses"><?php echo isset($c_course_count)?$c_course_count:''; ?></b> <?php echo lang('courses') ?>
            </div>

            <h4 id="c_live_title" class="pad-top18"><?php echo isset($c_title)?$c_title:lang('catalog_live_title_default'); ?></h4>
            <p>
                <i class="icon icon-star star-active"></i>
                <i class="icon icon-star star-active"></i>
                <i class="icon icon-star star-active"></i>
                <i class="icon icon-star"></i>
                <i class="icon icon-star"></i>
                (8060)
            </p>
            <span id="c_live_price" class="price-bold strike-txt"><?php echo isset($c_price)?$c_price:lang('catalog_live_price_default'); ?></span> <span id="c_live_discount" class="price"><?php echo isset($c_discount)?$c_discount:lang('catalog_live_discount_default'); ?></span>
        </div>
    </div>

    <!--  Adding list group  --> <!-- END  -->

    <?php /* ?><div class="note">
        <p><?php echo lang('note') ?>:</p>
        <p>
            Lorem ipsum is simply a dummy text. Lorem ipsum is simply a dummy text. 

        </p>
    </div><?php */ ?>

</div>
<!-- SIDEBAR RIGHT SIDE FIXED --> <!-- END -->
        

<script type="text/javascript">
    var catalog_id       = <?php echo $id; ?>;
</script>
