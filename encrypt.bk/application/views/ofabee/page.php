<?php include('header.php'); ?>
<style>
.dynamic-page img{ max-width:100%;}
.dynamic-page td img {max-width: fit-content;}
</style>
<?php //echo '<pre>'; print_r($page_content);die; ?>
<link href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/styles.css" rel="stylesheet">
<section>
    <div class="dynamic-page pages-wrapper">
        <div class="container">
            <div class="container-reduce-width">
                <?php //include('sidebar.php') ?>
                        <?php if($page_content['p_parent_id']>0 || $page_content['p_category']>0): ?>
                            <h3 class="dynamic-heading"><?php echo $page_content['p_title'] ?></h3>
                        <?php endif; ?>
                        <div class="dynamic-content cms-content">
                        <?php echo $page_content['p_content'] ?>
                        </div>
            </div>
        </div>
    </div>
</section>

<?php include('footer.php'); ?>

