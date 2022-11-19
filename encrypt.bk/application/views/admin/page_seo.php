<?php include_once 'header.php'; ?>
<style>
section.base-cont-top-heading.courses-tab { height: 47px; }
.courses-tab ol.nav li { border-bottom: unset !important; }
</style>
<section class="courses-tab base-cont-top-heading">
    <ol class="nav nav-tabs offa-tab">
        <li>
            <a href="<?php echo admin_url('page/basics/' . $id) ?>"> <?php echo lang('basic') ?></a>
            <span class="active-arrow"></span>
        </li>
        <li class="active">
            <a href="javascript:void(0)"> <?php echo lang('seo') ?></a>
            <span class="active-arrow"></span>
        </li>
    </ol>
</section>
<section class="content-wrap small-width base-cont-top-heading">
    <div class="left-wrap col-sm-12 pad0">
        <div class="container-fluid course-create-wrap">
            <div class="row-fluid course-cont">
                <div class="col-sm-12">
                    <div class="form-horizontal">
                        <?php include_once('messages.php') ?>
                        <form class="form-horizontal" action="<?php echo admin_url('page/seo/'.$id) ?>" method="POST">
                            <div class="form-group">    
                                <div class="col-sm-12">
                                    <?php echo lang('friendly_url') ?>:
                                    <div class="input-group">
                                        <span id="basic-addon1" class="input-group-addon light-color"><?php echo base_url() ?></span>
                                        <input type="text" id="p_slug" name="p_slug" aria-describedby="basic-addon1" placeholder="page name" class="form-control" value="<?php echo $p_slug ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <?php echo lang('page_meta') ?>  : 
                                    <input type="text" id="p_meta" name="p_meta" aria-describedby="basic-addon1" placeholder="eg: Page Meta title" class="form-control" value="<?php echo $p_meta ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <?php echo lang('page_seo') ?>  : 
                                    <textarea class="form-control"  onkeyup="validateMaxLength(this.id)" maxlength="1000" placeholder="eg: Page SEO description"  rows="3" name="p_seo_title" id="p_seo_title" ><?php echo $p_seo_title ?></textarea>
                                    <?php
                                        $remaining_charectors = intval(1000-strlen(str_replace("\n", '', $p_seo_title)));
                                        $remaining_charectors = ($remaining_charectors > 0)?$remaining_charectors:0;
                                    ?>
                                    <span class="pull-right my-italic" id="p_seo_title_char_left">  <?php echo $remaining_charectors.' '.lang('charectors_left')?></span>
                                </div>
                            </div>
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
    </div>
</section>
<script src="<?php echo assets_url() ?>js/page_seo_settings.js"></script>
<?php include_once 'footer.php'; ?>