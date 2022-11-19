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
    <form method="post" id="cform" action="<?php echo site_url('admin/forum/create_forum'); ?>">
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
                    <h1 class="create-new-topic-head">Create new forum</h1>
                    <span class="create-new-input-holder">
                        <input type="rext" id="topic_title" placeholder="Add a title" value="<?php echo (isset($ftitle) ? $ftitle : ''); ?>" name="title" class="add-title-inputbox">
                    </span><!--input-holder-->
                </div><!--changed-container-for-forum-->
            </div><!--container-res-chnger-frorm-page-->
        </div><!--create-new-topic-->
    </section>
    
    <section>
        <div class="redactor-text-holder">
            <div class="container  container-res-chnger-frorm-page">
                <div class="changed-container-for-forum">
                    <textarea class="redactor-text-area" id="redactor" name="description" placeholder="Add description"><?php echo (isset($description) ? $description : ''); ?></textarea><!--redactor-text-area-->
                    <div class="redactor-btns redactor-btns-bottom clearfix">
                        <span class="error-field">
                            <span id="error_message" class="alert alert-danger col-md-12">
                                
                            </span>
                        </span>
                        <span class="redactor-orange-btn"><a href="javascript:void(0)" id="submit" class="redactor-orange-flat-btn">CREATE</a></span>
                        <span class="redactor-orange-btn"><a href="<?php echo site_url('admin/forum'); ?>" class="redactor-grey-flat-btn">CANCEL</a></span>
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
                    var description = $('#redactor').val();
                    if($('#topic_title').val() == '' && description == ''){
                        error_msg = 'Title field and description field is required.';
                    }else if(description == ''){
                        error_msg = 'Description field is required.';
                    }else if($('#topic_title').val() == ''){
                        error_msg = 'Title field is required.';
                    }
                    
                    if($('#topic_title').val() == '' || description == ''){
                        $('#error_message').html(error_msg).show();
                    }else{
                        if(description.length > 300){
                            $('#error_message').html('Description field limit is 200 character.').show();
                        }else{
                            $("#cform").submit();
                        }
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
                    pasteLinks : false,
                    pasteImages : false,
                    plugins: ['alignment', 'source'],
                    buttonsHide: ['link'],
                    callbacks: {
                        keyup: function(e)
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