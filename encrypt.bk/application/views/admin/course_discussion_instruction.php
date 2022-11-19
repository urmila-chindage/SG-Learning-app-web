<?php include_once 'training_header.php';?>
<link rel="stylesheet" href="<?php echo assets_url() ?>css/redactor/css/redactor.css" />
<link rel="stylesheet" href="<?php echo assets_url() ?>css/redactor/css/alignment.css" />
<section class="content-wrap cont-course-big top-spacing content-wrap-align">
    <div class="left-wrap col-sm-12 pad0">
        <div class="container-fluid course-create-wrap course-settings">
            <div class="row-fluid course-cont">
                <div class="col-sm-12">
                    <div class="form-horizontal">
                        <form class="form-horizontal" name="Form" id="discussion_instruction_form" enctype="multipart/form-data" method="post" action="<?php echo admin_url('course/discussion_instruction/' . $id); ?>">
                            <div class="form-group">
                                <div class="col-sm-12">
                                    Discussion Instruction*
                                   <textarea style="display: none;" name="cb_discussion_instruction" id="cb_discussion_instruction" maxlength="1000"  class="form-control" rows="3" ><?php echo isset($cb_discussion_instruction) ? $cb_discussion_instruction : $default_instruction; ?></textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <input type="button" onclick="validateAndSubmitDiscussionInstruction()" class="pull-right btn btn-green marg10" value="<?php echo lang('save') ?>">
                                </div>
                            </div>
                            <input type="hidden" name="savenext" id="savenextform" value="0">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php include_once 'footer.php';?>
<script src="<?php echo assets_url() ?>js/redactor/js/redactor.js"></script>
<script src="<?php echo assets_url() ?>js/redactor/js/table.js"></script>
<script src="<?php echo assets_url() ?>js/redactor/js/alignment.js"></script>
<script>
$(document).ready(function(){
    $('#cb_discussion_instruction').redactor({
        pasteLinks: false,
        buttons: ['format', 'bold', 'italic', 'ul', 'ol','image', 'file'],
        maxHeight: '250px',
        maxHeight: '250px',
        imageUpload: admin_url + 'configuration/redactore_image_upload',
        plugins: ['table', 'alignment', 'source'],
        callbacks: {
            imageUploadError: function(json, xhr) {
                var erorFileMsg = "This file type is not allowed. upload a valid image.";
                $('#discussion_instruction_form').prepend(renderPopUpMessage('error', erorFileMsg));
                scrollToTopOfPage();
                return false;
            }
        }
    });
});

function validateAndSubmitDiscussionInstruction() {
    var errorCount = 0;
    var errorMessage = '';
    if ( $("#cb_discussion_instruction").val() == '' ) {
        errorCount++;
        errorMessage += 'Enter Course Discussion Instruction <br />';
    }
    if( errorCount == 0 ) {
         $('#discussion_instruction_form').submit();
    } else {
        $('#discussion_instruction_form').prepend(renderPopUpMessage('error', errorMessage));
        return false;
    }
}
</script>