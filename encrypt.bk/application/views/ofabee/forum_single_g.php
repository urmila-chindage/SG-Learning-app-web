<?php include_once "header.php"; ?>

    <section>
        <div class="form-index-strip">
            <div class="container container-res-chnger-frorm-page">
                <div class="changed-container-for-forum">
                    <?php echo $breadcrumb1; ?>
                </div><!--change-size-of-bottom-container-->
            </div><!--container-->
        </div><!--form-index-strip-->
    </section>

    <section>
        <div class="first-forum-titlw-wraper">
            <div class="container container-res-chnger-frorm-page">
                <div class="changed-container-for-forum">
                    <h3 class="biography-text forum-title-margin"><?php echo $forum->forum_name;?></h3>
                    <div class="searchBox-and-btn">
                        <span class="serchbox-holder">
                            <input type="text" class="searchbox-inside" name="search" id="term" placeholder="Search this forum" onFocus="this.placeholder=''" onBlur="this.placeholder='Search this forum'" value="<?php echo isset($search_term)?$search_term:''; ?>">
                            <a href="javascript:void(0)" id="search" class="btn search-lense"><i class="icon-search"></i></a>
                        </span><!--serchbox-holder-->
                        <span class="searchbox-btn">
                            <a href="<?php echo site_url('login');?>" class="orange-flat-btn-for-search orange-flt-btn-search-height">Post a new topic</a>
                        </span><!--searchbox-btn--> 
                    </div><!--searchBox-and-btn-->
                </div><!--changed-container-for-forum-->
            </div><!--container-res-chnger-frorm-page-->
        </div><!--first-forum-titlw-wraper-->
    </section>
    
    <section>
        <div class="view-un-answerd-strip">
            <div class="container container-res-chnger-frorm-page">
                <div class="changed-container-for-forum">
                 <!-- <span class="dropdown-full-width">
                    <span class="sort-by">Sort by</span>
                    <span class="recent-posts">
                        <div class="dropdown dropdown-border-small">
                          <button class="btn btn-transperant dropdown-toggle" type="button" data-toggle="dropdown">Recent posts
                          <span class="btn-transperant-arrow">
                          <svg version="1.1" x="0px" y="0px" width="21px" height="17px" viewBox="0 0 21 17" enable-background="new 0 0 21 17" xml:space="preserve">
                            <g>
                                <g>
                                    <path fill="#4d4d4d" d="M16.2,4.2L10.5,10L4.8,4.2L3,6l7.5,7.5L18,6L16.2,4.2z"/>
                                    <path fill="none" d="M-4.5-6.5h30v30h-30V-6.5z"/>
                                </g>
                    </span>
                    </button>
                          <ul class="dropdown-menu dropdown-small">
                            <li><a href="#">First forum title</a></li>
                            <li><a href="#">First forum title</a></li>
                            <li><a href="#">First forum title</a></li>
                          </ul>
                        </div>
                    </span>
                    </span> -->
                    <span class="forum-and-page"><?php echo $total_topics.($total_topics<2 ? ' topic' : ' topics') ?> | page <strong><?php echo $current_page; ?></strong> of <strong><?php echo $pages; ?></strong> </span>
                </div><!--change-size-of-bottom-container-->
            </div><!--container-->
        </div><!--view-un-answerd-strip-->
    </section>
    
    <section>
        <div class="discussion-forum-wraper">
            <div class="container container-res-chnger-frorm-page">
                <div class="changed-container-for-forum">
                    <ul class="discussion-forum-parent">
                        <li class="discussion-header">
                            <span class="discussion-head-text">Topic</span>
                            <span class="topic-head-text">Replies</span>
                            <span class="topic-head-text">Views</span>
                            <span class="last-post-head-text">Last post</span>
                        </li><!--discussion-header-->
                        
                        <?php foreach ($topics as $key => $value){ ?>
                            
                            <li class="discussion-forum-white-lists">
                              <a href="<?php echo site_url('forum').'/'.$forum->forum_slug.'/'.$value->topic_slug;?>" class="discussion-link">
                                <img src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/forum/img/forum-ico.svg" class="forum-ion-res">
                                <span class="forum-title-wrap">
                                    <span class="forum-titile"><?php echo $value->topic_name;?></span>
                                    <span class="forum-des">by <span class="name-orange"><?php echo $value->author; ?></span><span class="forum-date-time"><?php echo $value->topic_created; ?></span></span>
                                </span><!--forum-title-wrap-->
                                 <span class="topic-xs">
                                    <span class="topic-form-text"><?php echo $value->count_posts ?><span class="hidden-lg hidden-md hidden-sm topic-post-sm">Topic</span></span>
                                    <span class="topic-form-text"><?php echo $value->views; ?><span class="hidden-lg hidden-md hidden-sm topic-post-sm">Posts</span></span>
                                  </span>   <!--topic-xs-->  
                                <span class="last-post-forum-text">
                                    <span class="by-name">by <span class="name-orange"><?php echo (isset($value->latest_post->author)) ? $value->latest_post->author : '' ?></span></span><!--by-name-->
                                    <span class="forum-date-time"><?php echo (isset($value->latest_post->comment_created)) ? $value->latest_post->comment_created : '' ?></span>
                                </span><!--last-post-forum-text-->
                               </a> 
                            </li><!--discussion-forum-white-lists"-->
                        <?php } ?>
                        
                        
                    </ul>
                </div><!--changed-container-for-forum-->
            </div><!--container-->
        </div><!--discussion-forum-->
    </section>

<section>
    <div class="pagination-strip">
        <div class="container container-res-chnger-frorm-page">
            <div class="changed-container-for-forum">
              <span class="pagination-prev-and-ul">
                <!-- <span class="pagination-prev"><a href="javascript:void(0);">Previous</a></span> -->
                <span class="pagination-wraper">
                    <ul class="pagination-black">
                        <?php foreach ($links as $link) {
                            echo "<li class='pagination-prev'>". $link."</li>";
                        } ?>
                    </ul>
                </span><!--pagination-wraper-->
               </span><!--pagination-prev-and-ul--> 
                    <span class="pagination-next-last">
                        <!-- <span class="pagination-prev pag-next"><a href="javascript:void(0);">Next</a></span> -->
                    </span><!--pagination-next-last-->
                <span class="forum-pagination-page"><?php echo $total_topics.($total_topics<2 ? ' topic' : ' topics') ?> | page <strong><?php echo $current_page; ?></strong> of <strong><?php echo $pages; ?></strong> </span>
            </div><!--changed-container-for-forum-->
        </div><!--container-->
    </div><!--pagination-strip-->
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
            $('#search').click(function() {
                    var url = "<?php echo site_url('forum/search').'/'.$forum->forum_slug.'/';?>";
                    var replaced = url.split(' ').join('+');
                    replaced += $('#term').val();
                    window.location = replaced;
                });
            function startTextToolbar()
            {
                $('#redactor, #redactor_invite').redactor({
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
<?php include_once "footer.php"; ?>