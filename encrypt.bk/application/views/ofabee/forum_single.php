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
                        <span class="serchbox-holder serchbox-holder-modified">
                            <input type="text" class="searchbox-inside searchbox-inside-alter" name="search" id="term" placeholder="Search this forum" onFocus="this.placeholder=''" onBlur="this.placeholder='Search this forum'" value="<?php echo isset($search_term)?$search_term:''; ?>">
                            <a href="javascript:void(0)" id="search" class="btn search-lense search-lense-modified"><i class="icon-search"></i></a>
                        </span><!--serchbox-holder-->
                        <span class="searchbox-btn">
                            <a href="<?php echo site_url('forum').'/'.$forum->forum_slug.'/create_forum_topic';?>" class="orange-flat-btn-for-search orange-flt-btn-search-height">Post a new topic</a>
                        </span><!--searchbox-btn--> 
                    </div><!--searchBox-and-btn-->
                </div><!--changed-container-for-forum-->
            </div><!--container-res-chnger-frorm-page-->
        </div><!--first-forum-titlw-wraper-->
    </section>
    
    <section>
        <div class="view-un-answerd-strip">
            <div class="container container-res-chnger-frorm-page">
                <div class="changed-container-for-forum forum-active-link">
                 <span class="dropdown-full-width">
                    <span class="sort-by">Sort by</span>
                    <span class="recent-posts">
                        <div class="dropdown dropdown-border-small">
                          <button class="btn btn-transperant dropdown-toggle" type="button" data-toggle="dropdown"><span id="filter_label"></span>
                          <span class="btn-transperant-arrow">
                          <svg version="1.1" x="0px" y="0px" width="21px" height="17px" viewBox="0 0 21 17" enable-background="new 0 0 21 17" xml:space="preserve">
                            <g>
                                <g>
                                    <path fill="#4d4d4d" d="M16.2,4.2L10.5,10L4.8,4.2L3,6l7.5,7.5L18,6L16.2,4.2z"/>
                                    <path fill="none" d="M-4.5-6.5h30v30h-30V-6.5z"/>
                                </g>
                    </span>
                    </button>
                          <ul class="dropdown-menu dropdown-small dropdown-values">
                                <li><a href="javascript:void(0)" onclick="filter_post(0)">Recent Posts</a></li>
                                <li><a href="javascript:void(0)" onclick="filter_post(1)">Most Viewed</a></li>
                                <li><a href="javascript:void(0)" onclick="filter_post(2)">Most Replied</a></li>
                          </ul>
                        </div><!--dropdown-->
                    </span><!--recent-posts-->
                    </span><!--dropdown-full-width-->
                    <span class="forum-and-page"></span>
                </div><!--change-size-of-bottom-container-->
            </div><!--container-->
        </div><!--view-un-answerd-strip-->
    </section>
    
    <section>
        <div class="discussion-forum-wraper">
            <div class="container container-res-chnger-frorm-page">
                <div class="changed-container-for-forum">
                    <ul class="discussion-forum-parent">

                    </ul>
                </div><!--changed-container-for-forum-->
            </div><!--container-->
        </div><!--discussion-forum-->
    </section>

<section>
    <div class="pagination-strip">
        <div class="container container-res-chnger-frorm-page">
            <div class="changed-container-for-forum links-pagination">
              
            </div><!--changed-container-for-forum-->
        </div><!--container-->
    </div><!--pagination-strip-->
</section>



<script type="text/javascript">
    var __forum_topics = atob('<?php echo base64_encode(json_encode($topics)) ?>');
    var __themes_url = '<?php echo  assets_url().'themes/'.$this->config->item('theme') ?>';
    var __num_pages = parseInt('<?php echo $pages; ?>');
    var __total_forums = parseInt('<?php echo $total_topics; ?>');
    var __site_url = '<?php echo site_url(); ?>';
    var __limit = '<?php echo $forum_topic_limit; ?>';
    var __listing_type = 0;
    var __keyword = '';
    var __num_pages_search = __num_pages;
    var __forum_id = '<?php echo $forum->id; ?>';
</script>
<script src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/js/forum_topic.js"></script>
<?php include_once "footer.php"; ?>