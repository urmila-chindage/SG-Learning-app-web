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
        <div class="view-un-answerd-strip">
            <div class="container container-res-chnger-frorm-page">
                <div class="changed-container-for-forum">
                    <!-- <span class="view-unans-text"><a class="view-in-answerd-link" href="javascript:void(0)">View unanswerd posts</a></span>
                    <span class="view-active-text"><a class="view-in-answerd-link" href="javascript:void(0)">View active topics</a></span> -->
                    <span class="forum-and-page"><?php echo $total_forum; ?> forums | page <strong><?php echo $current_page; ?></strong> of <strong><?php echo $pages; ?></strong> </span>
                </div>
            </div>
        </div>
    </section>
    
    <section>
        <div class="discussion-forum-wraper">
            <div class="container container-res-chnger-frorm-page">
                <div class="changed-container-for-forum">
                    <ul class="discussion-forum-parent">
                        <li class="discussion-header">
                            <span class="discussion-head-text">Dicussion forums</span>
                            <span class="topic-head-text">Topics</span>
                            <span class="topic-head-text">Posts</span>
                            <span class="last-post-head-text">Last post</span>
                        </li><!--discussion-header-->
                        
                        <?php foreach ($forums as $key => $value){ ?>

                            <li class="discussion-forum-white-lists">
                              <a href="<?php echo site_url('forum').'/'.$value->forum_slug;?>" class="discussion-link">
                                <img src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/forum/img/forum-ico.svg" class="forum-ion-res">
                                <span class="forum-title-wrap forum-title-wrap-alter-index">
                                    <span class="forum-titile"><?php echo $value->forum_name;?></span>
                                    <span class="forum-des forum-des-for-index"><?php echo $value->forum_description; ?></span>
                                </span><!--forum-title-wrap-->
                                 <span class="topic-xs">
                                    <span class="topic-form-text"><?php echo $value->count_topics; ?><span class="hidden-lg hidden-md hidden-sm topic-post-sm"><?php echo ($value->count_topics>1 ? 'Topics' : 'Topic'); ?></span></span>
                                    <span class="topic-form-text"><?php echo $value->count_posts ?><span class="hidden-lg hidden-md hidden-sm topic-post-sm"><?php echo ($value->count_posts>1 ? 'Posts' : 'Post'); ?></span></span>
                                  </span>   <!--topic-xs-->  
                                <span class="last-post-forum-text">
                                    <span class="by-name">by <span class="name-orange"><?php echo $value->latest_topic->author; ?></span></span><!--by-name-->
                                    <span class="forum-date-time"><?php echo $value->latest_topic->topic_created ?></span>
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
                <span class="forum-pagination-page"><?php echo $total_forum; ?> forums | page <strong><?php echo $current_page; ?></strong> of <strong><?php echo $pages; ?></strong> </span>
            </div><!--changed-container-for-forum-->
        </div><!--container-->
    </div><!--pagination-strip-->
</section>


<?php include_once "footer.php"; ?>