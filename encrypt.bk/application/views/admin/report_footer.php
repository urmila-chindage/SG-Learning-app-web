<!-- Modal pop up contents:: Delete Section popup-->
<div class="modal fade alert-modal-new" id="common_message" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-small" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="form-group">
                    <b id="common_message_header"></b>
                    <p class="m0" id="common_message_content"></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-red" data-dismiss="modal">Ok</button>
            </div>
        </div>
    </div>
</div>

</body>
<!-- body end-->
</html>

<!-- bootstrap library -->

<script>
    <?php $accepted_files = array('mp4', 'flv', 'avi', 'f4v', 'doc', 'docx', 'pdf', 'ppt', 'pptx', 'zip', 'odt', 'mp3'); ?>
    var __controller         = '<?php echo $this->router->fetch_class() ?>';
    var __allowed_files      = $.parseJSON('<?php echo json_encode($accepted_files) ?>');
</script>.
<script type="text/javascript" src="<?php echo assets_url() ?>js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/language.js"></script>
<script src="<?php echo assets_url() ?>js/app.js" type="text/javascript"></script>
<script src="<?php echo assets_url() ?>js/system.js" type="text/javascript"></script>
<script type="text/javascript">
    $(document).ready(function(){
        App.initEqualizrHeight(".builder-left-inner", ".builder-right-inner");
    });
</script>
