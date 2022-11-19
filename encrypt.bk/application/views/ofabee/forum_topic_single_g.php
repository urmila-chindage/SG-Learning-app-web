<?php include_once "header.php"; ?>
    
        <script type="text/javascript">
            var admin_url = "<?php echo admin_url(); ?>";
        </script>
        <link rel="stylesheet" href="<?php echo assets_url(); ?>themes/<?php echo $this->config->item('theme') ?>/tinymce/autocomplete.css" />
        <!-- <link rel="stylesheet" href="<?php echo assets_url(); ?>css/redactor/css/redactor.css" />
        <link rel="stylesheet" href="<?php echo assets_url(); ?>css/redactor/css/alignment.css" />-->
        <!-- <link rel="stylesheet" href="<?php echo assets_url(); ?>themes/<?php echo $this->config->item('theme') ?>/css/jquery.mentionsInput.css" />  -->
        
   

<section>
        <div class="form-index-strip">
            <div class="container  container-res-chnger-frorm-page">
                <div class="changed-container-for-forum">
                    <?php echo $breadcrumb1; ?>
                </div><!--change-size-of-bottom-container-->
            </div><!--container-->
        </div><!--form-index-strip-->
    </section>
    
    <section>
     <div class="view-un-answerd-strip">
        <div class="container  container-res-chnger-frorm-page">
            <div class="changed-container-for-forum">
                <span class="topic-icon-left"><img class="topic-left-res-img" src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/forum/img/forum-ico.svg"></span>
                <span class="topic-icon-text">Topic</span>
                <span class="forum-and-page"><?php echo $total_posts.($total_posts<2 ? ' reply' : ' replies') ?> | page <strong><?php echo $current_page; ?></strong> of <strong><?php echo $pages; ?></strong> </span>
            </div><!--change-size-of-bottom-container-->
        </div><!--container-->
    </div><!--view-un-answerd-strip-->
  </section>
  
  
    <section>
     <div class="olp-post">
        <div class="container  container-res-chnger-frorm-page">
            <div class="changed-container-for-forum">
                <div class="olp-post-holder clearfix">
                    <span class="olp-post-image">
                        <img src="<?php echo (($topic->author_image == 'default.jpg')?default_user_path():user_path()) ?><?php echo $topic->author_image; ?>" class="olp-prof-pic img-rounded">
                          <span class="olp-profile-name-small"> 
                            <span class="olp-user-name"><?php echo $topic->author;?></span>
                            <span class="olp-site-admin"><?php echo $topic->author_role;?></span>
                            <span class="olp-posts-count">Posts: <?php echo $topic->author_post_count;?></span>
                          </span><!--olp-profile-name-small-->  
                      </span><!--olp-post-image-->
                    
                    <span class="post-content">
                        <h3 class="olp-post-main-heading"><?php echo $topic->topic_name;?></h3>
                        <p class="olp-post-para"><?php echo $topic->description;?></p>
                    </span><!--post-content-->
                    
                    <span class="cmt-post-time">
                        <?php echo $topic->created; ?>
                    </span><!--cmt-post-time-->
                </div><!--olp-post-holder-->
            </div><!--change-size-of-bottom-container-->
        </div><!--container-->
    </div><!--view-un-answerd-strip-->
  </section>
  
  <section>
    <div class="replies-logo-text">
        <div class="container  container-res-chnger-frorm-page">
            <div class="changed-container-for-forum">
                <span class="reply-and-text">
                    <img class="reply-parent-svg" src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/forum/img/reply.svg">
                    <label class="reply-text">Replies</label>
                </span><!--reply-and-text-->
            </div><!--changed-container-for-forum-->
        </div><!--container-res-chnger-frorm-page--> 
    </div><!--replies-logo-text-->
  </section>
  
  <section>
    <div class="post-main-comment-wraper">
        <div class="container  container-res-chnger-frorm-page">
            <div class="changed-container-for-forum">            
                <ul class="post-comment-parent">
                    
                    <?php foreach ($posts as $key => $value){ ?>
                        <li class="post-comment-main">
                            <div class="olp-post-holder clearfix">
                        <span class="olp-post-image">
                            <img src="<?php echo (($value->author_image == 'default.jpg')?default_user_path():user_path()) ?><?php echo $value->author_image; ?>" class="olp-prof-pic img-rounded">
                              <span class="olp-profile-name-small"> 
                                <span class="olp-user-name"><?php echo $value->author; ?></span>
                                <span class="olp-site-admin"><?php echo $value->author_role; ?></span>
                                <span class="olp-posts-count">Posts: <?php echo $value->author_post_count; ?></span>
                              </span><!--olp-profile-name-small-->  
                          </span><!--olp-post-image-->
                        
                        <span class="post-content post-content-comment">
                            <p class="olp-post-para"><?php echo $value->topic_comment; ?></p>
                             <span class="reply-for-cmt-post"><a class="reply-link post-comment-common" href="<?php echo site_url('login'); ?>" id="<?php echo 'r'.$value->id; ?>">Reply</a></span>
                        </span><!--post-content-->                   
                        <span class="cmt-post-time">
                            <?php echo $value->comment_created; ?>
                        </span><!--cmt-post-time-->                    
                    </div>
                        </li><!--post-comment-->
                        
                        <?php if(isset($value->child_post_count)&&$value->child_post_count>0){ ?>
                        <ul class="post-comment-child">
                        <li><span class="reply-and-text padd-adjust-top-btm">
                             <img class="reply-parent-svg reply-child-svg" src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/forum/img/reply-sub.svg">
                             <label class="reply-text-and-num"><?php echo $value->child_post_count.($value->child_post_count<2 ? ' Reply' : ' Replies') ?></label>
                             </span>
                         </li>
                            <?php foreach($value->child_posts as $cvalue){ ?>
                                <li class="post-comment-sub">
                                    <div class="olp-post-holder olp-subComment-Border clearfix">
                            <span class="olp-post-image">
                                <img src="<?php echo (($cvalue->author_image == 'default.jpg')?default_user_path():user_path()) ?><?php echo $cvalue->author_image; ?>" class="olp-prof-pic img-rounded">
                                  <span class="olp-profile-name-small"> 
                                    <span class="olp-user-name"><?php echo $cvalue->author; ?></span>
                                    <span class="olp-site-admin"><?php echo $cvalue->author_role; ?></span>
                                    <span class="olp-posts-count">Posts: <?php echo $cvalue->author_post_count; ?></span>
                                  </span><!--olp-profile-name-small-->  
                              </span><!--olp-post-image-->
                            
                            <span class="post-content post-content-comment">
                                <p class="olp-post-para"><?php echo $cvalue->topic_comment; ?></p> 
                            </span><!--post-content-->                   
                            <span class="cmt-post-time">
                                <?php echo $cvalue->comment_created; ?>
                            </span><!--cmt-post-time-->                    
                        </div>
                                    </li><!--post-comment-sub-->
                            <?php } ?>
                            <?php if($value->child_post_count>2){ ?>
                                <li id='<?php echo 't'.$value->id; ?>' class="post-comment-viw-more post-comment-common">
                                    <a href="javascript:void(0);" id="<?php echo 'l'.$value->id; ?>">View more replies</a>
                                    <img id="<?php echo 'a'.$value->id; ?>" src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/forum/img/ajax-loader.gif" class="ajax-loader hidden">
                                </li><!-- post-comment-sub-->
                            <?php } ?>
                        </ul>
                        <?php } ?>
                        
                    <?php } ?>
                    
                </ul><!--post-comment-parent-->
            </div><!--changed-container-for-forum-->
        </div><!-- container-res-chnger-frorm-page-->    
    </div><!--post-main-comment-wraper-->
  </section>

<section>
    <div class="pagination-strip pagination-strip-top-margin-reduce">
        <div class="container container-res-chnger-frorm-page">
            <div class="changed-container-for-forum">
              <span class="pagination-prev-and-ul">
                <span class="pagination-wraper">
                    <ul class="pagination-black">
                        <?php foreach ($links as $link) {
                            echo "<li>". $link."</li>";
                        } ?>
                    </ul>
                </span><!--pagination-wraper-->
               </span><!--pagination-prev-and-ul--> 
                    <span class="pagination-next-last">
                    </span><!--pagination-next-last-->
                <span class="forum-pagination-page"><?php echo $total_posts.($total_posts<2 ? ' reply' : ' replies') ?> | page <strong><?php echo $current_page; ?></strong> of <strong><?php echo $pages; ?></strong> </span>
            </div><!--changed-container-for-forum-->
        </div><!--container-->
    </div><!--pagination-strip-->
</section>

<section>
    <div class="text-center">
        <span class="searchbox-btn searchbox-btn-altr">
           <a href="<?php echo site_url('login'); ?>" class="orange-flat-btn-for-search orange-flt-btn-search-height orange-flt-btn-search-height-altr">Post new reply</a>
        </span>
    </div><!--text-center-->
</section>


    <!-- initialising the tag plugin using tokenize  -->
    <script type="text/javascript" src="<?php echo assets_url() ?>js/multi-select/jquery.tokenize.js"></script>
    <script src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme'); ?>/tinymce/tinymce.min.js"></script>
    <script src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme'); ?>/tinymce/plugin.js"></script>
    <!-- <script src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme'); ?>/js/ckeditor/ckeditor.js"></script> -->
    
    

    <!-- END -->
    <!-- ADDING REDACTOR PLUGIN INSIDE -->
    <!-- ############################# --> <!-- START -->
        <!-- JS -->
        <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.6.1/ckeditor.js"></script> -->
        <!-- <script src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/js/underscore.js"></script> -->
        <!-- <script src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/js/jquery.mentionsInput.js"></script> -->
        <!-- <script src="<?php echo assets_url() ?>js/redactor/js/redactor.js"></script>
        <script src="<?php echo assets_url() ?>js/redactor/js/table.js"></script>
        <script src="<?php echo assets_url() ?>js/redactor/js/source.js"></script>
        <script src="<?php echo assets_url() ?>js/redactor/js/alignment.js"></script> -->
        <script>
            tinyMCE.init({
                paste_data_images: true,
                mode : "exact",
                selector: "<?php foreach ($posts as $value){ echo "#tiny".$value->id.","; } ?>#editor1",
                plugins : 'mention',
                mentions: {
                    source: <?php echo $json_participants; ?>
                },
                init_instance_callback : function(ed) {
                 //   QUnit.start();
                }
            });
            $(".redactor-hidden").hide();
            $("#post").on("click",function(e) {
                $('#editor1').html(tinyMCE.activeEditor.getContent());
            });
        </script>
        <script>
            /*
            editor_id = $("#editor1").attr('id');
            tinymce.get(editor_id).hide();
            $('#editor1').hide();
            */
            var __user_id = '<?php echo $login['id']; ?>';
            var __topic_id = '<?php echo $topic->id; ?>';
            $( document ).ready(function() {
                var c_id = '';
                var comment_id = '';
                var comments_id = '';
                var post_id = new Object();
                var limit = 2;
                var offset = new Object();
                var load_more = '';

                $('.post-comment-common').on( "click", function(){
                  
                   if(this){
                    var lm = $(this).attr("id");
                   }else{
                    var lm = 0;
                   }
                    var txtid = lm;
                    c_id = '';
                    for (i = 0; i < txtid.length; i++)
                    {
                        if ("" + parseInt(txtid[i]) != "NaN")
                            c_id = c_id + txtid[i];
                    }
                    if(lm[0]=='t'){
                        $('#a'+c_id).removeClass('hidden');
                        if($(this).data('counter')){
                                $(this).data('counter',$(this).data('counter')+limit)
                            }else{
                                $(this).data('counter',limit);
                            }
                            comments_id = '#t'+c_id;
                            load_more = '#l'+c_id;
                            $.post("<?php echo site_url('forum/get_ajax_comment'); ?>",{
                                parent : c_id,
                                offset : $(this).data('counter'),
                                limit : limit
                                }, function(args) {
                                if(args){
                                    obj = JSON.parse(args);
                                    $('#a'+c_id).addClass('hidden');
                                    if(jQuery.isEmptyObject(obj)){
                                        //display text and disable load button if nothing to load
                                        $(load_more).hide();
                                    }else{
                                        $.each(obj, function(key,value) {
                                            $(comments_id).before('<li class="post-comment-sub"><div class="olp-post-holder olp-subComment-Border clearfix"><span class="olp-post-image"><img src="<?php echo user_path(); ?>'+value.author_image+'" class="olp-prof-pic img-rounded"><span class="olp-profile-name-small"> <span class="olp-user-name">'+value.author+'</span><span class="olp-site-admin">'+value.author_role+'</span><span class="olp-posts-count">Posts: '+value.author_post_count+'</span></span><!--olp-profile-name-small--></span><!--olp-post-image--><span class="post-content post-content-comment"><p class="olp-post-para">'+value.topic_comment+'</p></span><!--post-content--><span class="cmt-post-time">'+value.comment_created+'</span><!--cmt-post-time--></div></li>');
                                        });

                                        if(obj.length < 2){
                                            $(load_more).hide();
                                        } 
                                    }
                                }else{
                                    $(load_more).hide();
                                    $('#a'+c_id).addClass('hidden');
                                }

                            });
                    }else if(lm[0]=='r'||lm[0]=='h'){
                        $('#ti'+c_id).slideToggle("slow");
                        tinyMCE.activeEditor.setContent('');
                    }else if(lm[0]=='b'){
                        comment_id = c_id;
                        comments_id = '#t'+c_id;
                        textarea_id = '#'+c_id;
                        var pling = $(tinyMCE.activeEditor.getContent());
                        var IDs = [];
                        pling.find("span").each(function(){ IDs.push(this); });
                        for(var i=0;i<IDs.length;i++){
                            IDs[i] = $(IDs[i]).attr('data-internalid');
                        }
                        IDs = jQuery.unique(IDs);
                        //alert(JSON.stringify(IDs));
                        if(tinyMCE.activeEditor.getContent()==''){
                            $(textarea_id).focus();
                        }else{
                            $.post("<?php echo site_url('forum/ajax_comment'); ?>",{
                            topic_id : __topic_id,
                            user_id : __user_id,
                            mention_ids : IDs,
                            comment : tinyMCE.activeEditor.getContent(),
                            parent_id : comment_id,
                            url : $(location).attr('href')
                            }, function(data) {
                            if (data){
                            location.reload();
                            tinyMCE.activeEditor.setContent('');
                            $('#ti'+c_id).slideToggle("slow");
                            }else{
                              
                            }
                            });
                        }
                    }

                });
            });
            

        </script>
<?php include_once "footer.php"; ?>


