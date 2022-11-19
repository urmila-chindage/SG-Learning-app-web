<?php include_once "header.php"; ?>  

    <script type="text/javascript">
        var admin_url = <?php echo admin_url(); ?>
    </script>
    <link rel="stylesheet" href="<?php echo assets_url(); ?>css/redactor/css/redactor.css" />
    <link rel="stylesheet" href="<?php echo assets_url(); ?>css/redactor/css/alignment.css" />


        <section class="content-wrap small-width zero-level-top">
            
            <!-- Nav section inside this wrap  --> <!-- START -->
            <h2 style="padding-top: 5px">Create a forum Topic</h2>
            <?php if (validation_errors()) : ?>
                <div class="col-md-12">
                    <div class="alert alert-danger" role="alert">
                        <?= validation_errors() ?>
                    </div>
                </div>
            <?php endif; ?>
            <?php if (isset($error)) : ?>
                <div class="col-md-12">
                    <div class="alert alert-danger" role="alert">
                        <?= $error ?>
                    </div>
                </div>
            <?php endif; ?>
            <div class="col-md-12">
                <?= form_open() ?>
                    <div class="form-group">
                        <label for="description">Your Comment</label>
                        <textarea maxlength="200" rows="6" class="form-control" id="comment" name="comment" placeholder="Enter short description for the new forum (max 200 characters)"><?php if(isset($content)){ echo $content;} ?></textarea>
                    </div>
                    <div class="form-group">
                        <input type="submit" class="btn btn-default" value="Create forum">
                    </div>
                </form>
            </div>
            
        </section>

<!-- Basic All Javascript -->
    <script src="<?php echo assets_url() ?>js/user.js"></script>
    <!-- initialising the tag plugin using tokenize  -->
    <script type="text/javascript" src="<?php echo assets_url() ?>js/multi-select/jquery.tokenize.js"></script>
    <!-- END -->
    <!-- ADDING REDACTOR PLUGIN INSIDE -->
    <!-- ############################# --> <!-- START -->
        <!-- JS -->
        <script src="<?php echo assets_url() ?>js/redactor/js/redactor.js"></script>
        <script src="<?php echo assets_url() ?>js/redactor/js/table.js"></script>
        <script src="<?php echo assets_url() ?>js/redactor/js/source.js"></script>
        <script src="<?php echo assets_url() ?>js/redactor/js/alignment.js"></script>
        <script>
            $(function()
            {
                startTextToolbar();
            });

            function startTextToolbar()
            {
                $('#redactor, #redactor_invite,#comment').redactor({
                    imageUpload: admin_url+'configuration/redactore_image_upload',
                    plugins: ['table', 'alignment', 'source'],
                    callbacks: {
                        imageUploadError: function(json, xhr)
                        {
                             alert('Please select a valid image');
                             return false;
                        }
                    }   
                });
            }
        </script>
    <!-- ############################# -->
<!-- END -->
 <?php include_once 'footer.php'; ?>