<?php include_once "header_forum.php"; ?> 
<link href="<?php echo assets_url() ?>css/redactor/css/redactor.css" rel="stylesheet">
<link href="<?php echo assets_url() ?>css/redactor/css/alignment.css" rel="stylesheet">
    <script type="text/javascript">
        var admin_url = "<?php echo admin_url(); ?>";
    </script>
        
    <section>
        <div class="form-index-strip">
            <div class="container container-res-chnger-frorm-page">
                <div class="changed-container-for-forum">
                    <?php echo $breadcrumb1; ?>
                </div><!--change-size-of-bottom-container-->
            </div><!--container-->
        </div><!--form-index-strip-->
    </section>
    <form method="post" id="cform">
    <section>
        <div class="create-new-topic">
            <div class="container  container-res-chnger-frorm-page">
                <div class="changed-container-for-forum">
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
                    <h1 class="create-new-topic-head">Create new topic</h1>
                    <span class="create-new-input-holder">
                        <input type="rext" id="topic_title" placeholder="Add a title" value="<?php echo (isset($title) ? $title : ''); ?>" name="title" class="add-title-inputbox">
                    </span><!--input-holder-->
                </div><!--changed-container-for-forum-->
            </div><!--container-res-chnger-frorm-page-->
        </div><!--create-new-topic-->
    </section>
    <?php
        $uri_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri_segments = explode('/', $uri_path);

    ?>
    <section>
        <div class="redactor-text-holder">
            <div class="container  container-res-chnger-frorm-page">
                <div class="changed-container-for-forum">
                    <textarea class="redactor-text-area" id="redactor" name="content" placeholder="Topic description"></textarea><!--redactor-text-area-->
                    <div class="redactor-btns redactor-btns-bottom clearfix">
                        <span class="error-field">
                            <span id="error_message" class="alert alert-danger col-md-12">
                                
                            </span>
                        </span>
                        <span class="redactor-orange-btn"><a href="javascript:void(0)" id="submit" class="redactor-orange-flat-btn">Post</a></span>
                        <span class="redactor-orange-btn"><a href="<?php echo site_url('admin/forum').'/'.$uri_segments[3]; ?>" class="redactor-grey-flat-btn">Cancel</a></span>
                    </div><!--redactor-btns-->
                </div><!--changed-container-for-forum-->
            </div>  <!--container-res-chnger-frorm-page--> 
        </div><!--redactor-text-holder-->
    </section>
    </form>
<!-- Basic All Javascript -->
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
                $('#error_message').hide();
                var error_msg = '';
                startTextToolbar();
                $("#submit").on("click",function(e) {
                    error_msg = '';
                    if($('#topic_title').val() == '' && $('#redactor').val() == ''){
                        error_msg = 'Title field and description field is required.';
                    }else if($('#redactor').val() == ''){
                        error_msg = 'Description field is required.';
                    }else if($('#topic_title').val() == ''){
                        error_msg = 'Title field is required.';
                    }
                    
                    if($('#topic_title').val() == '' || $('#redactor').val() == ''){
                        $('#error_message').html(error_msg).show();
                    }else{
                        $("#cform").submit();
                    }
                });

                $('#topic_title').click(function(){
                    $('#error_message').html('').hide();
                });
            });

            function startTextToolbar()
            {
                $('#redactor').redactor({
                    minHeight: 300,
                    imageUpload: admin_url+'configuration/redactore_image_upload',
                    plugins: ['table', 'alignment', 'source'],
                    callbacks: {
                        imageUploadError: function(json, xhr)
                        {
                             alert('Please select a valid image');
                             return false;
                        },keyup: function(e)
                        {
                            $('#error_message').html('').hide();
                        }
                    }   
                });
                $('#redactor').redactor('code.set', '<?php echo (isset($content) ? $content : ''); ?>');
            }
        </script>
    <!-- ############################# -->
<!-- END -->