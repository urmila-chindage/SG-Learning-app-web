<script src="<?php echo assets_url('themes/'.config_item('theme')) ?>js/jquery_material.min.js"></script> 
<?php /* ?><script type="text/javascript" src="<?php echo assets_url('themes/'.config_item('theme')) ?>js/jquery-ui.min.js"></script> <?php */ ?>
<script src="<?php echo assets_url('themes/'.config_item('theme')) ?>js/jquery.min.1.11.1.js"></script>

<script>
    /*$(function () {
        $("#tabs").tabs();
    });*/
    var __controller         = '<?php echo $this->router->fetch_class() ?>';
    var __admin_url          = '<?php echo admin_url(); ?>';
    var __tab_id             = '<?php echo $tab_id ?>';
    var __site_url 			 = '<?php echo site_url() ?>';
    var __base_url 			 = '<?php echo base_url(); ?>';
</script>
<script src="<?php echo assets_url() ?>js/system.js"></script>
<script src="<?php echo assets_url('themes/'.config_item('theme')) ?>js/bootstrap.min.js" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo assets_url().'themes/'.$this->config->item('theme').'/js/jquery.rateyo.min.js'; ?>" ></script>
<script src="<?php echo assets_url('themes/'.config_item('theme')) ?>js/language.js" type="text/javascript"></script>
<script src="<?php echo assets_url('themes/'.config_item('theme')) ?>js/customscripts.js" type="text/javascript"></script>
<script src="<?php echo assets_url('themes/'.config_item('theme')) ?>js/overlay.js" type="text/javascript"></script>
<script src="<?php echo assets_url('themes/'.config_item('theme')) ?>js/material.js" type="text/javascript"></script>
<script src="<?php echo assets_url('themes/'.config_item('theme')) ?>js/material-ui.js" type="text/javascript"></script>

<script src="<?php echo assets_url() ?>js/redactor/js/redactor.js"></script>
<script src="<?php echo assets_url() ?>js/redactor/js/table.js"></script>
<script src="<?php echo assets_url() ?>js/redactor/js/source.js"></script>
<script src="<?php echo assets_url() ?>js/redactor/js/alignment.js"></script>
</body>
</html>