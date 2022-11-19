<?php include_once "header_forum.php"; ?>
<script type="text/javascript">
    var admin_url = "<?php echo admin_url(); ?>";
</script>
<link rel="stylesheet" href="<?php echo assets_url(); ?>themes/<?php echo $this->config->item('theme') ?>/tinymce/autocomplete.css" />
<?php
$admin  = $this->auth->get_current_user_session('admin');
if(!isset($admin)){
    $admin = $this->auth->get_current_user_session('teacher');
    if(!isset($admin)){
        $admin = $this->auth->get_current_user_session('content_editor');
    }
}
?>
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
                <span class="topic-icon-left"><img class="topic-left-res-img" src="<?php echo assets_url().'themes/'.$this->config->item('theme'); ?>/img/forum-ico.svg"></span>
                <span class="topic-icon-text">Topic</span>
                <span class="forum-and-page header-page-details"></span>
            </div><!--change-size-of-bottom-container-->
        </div><!--container-->
    </div><!--view-un-answerd-strip-->
</section>
  
  
<section id="topicSectionMain">
    <div class="olp-post">
        <div class="container  container-res-chnger-frorm-page">
            <div class="changed-container-for-forum">
                <div class="olp-post-holder clearfix">
                    <span class="olp-post-image">
                        <img src="<?php echo (($topic->author_image == 'default.jpg') ? default_user_path() : user_path()) . $topic->author_image ?>" class="olp-prof-pic img-rounded">
                        <span class="olp-profile-name-small">   
                            <span class="olp-user-name"><?php echo $topic->author; ?></span>
                            <span class="olp-site-admin"><?php echo $topic->author_role; ?></span>
                            <span class="olp-posts-count">Posts: <?php echo $topic->author_post_count; ?></span>
                        </span><!--olp-profile-name-small-->  
                    </span><!--olp-post-image-->
                    
                    <span class="post-content">
                        <h3 class="olp-post-main-heading"><?php echo $topic->topic_name; ?></h3>
                        <p class="olp-post-para"><?php echo $topic->description; ?></p>
                        <span class="reply-for-cmt-post" id="<?php echo isset($admin['id'])?'mainTopicReply':'none' ?>"><a class="reply-link post-comment-common" href="<?php echo isset($admin['id'])?'javascript:void(0)':site_url('login'); ?>" >Reply</a></span>
                    </span><!--post-content-->
                    
                    <span class="cmt-post-time">
                        <span class="cmt-post-date"><?php echo $topic->created_date; ?></span>
                        <span class="cmt-post-current-time"><?php echo $topic->created_time; ?></span>
                    </span><!--cmt-post-time-->
                    <div class="olp-post-holder clearfix redactor-hidden" id="mainReply">
                        <textarea class="redactor-sub TinyMceEditor"></textarea>
                        <span class="redactor-bootom-btns clearfix">
                            <span class="cancel-post-btns">
                                <a href="javascript:void(0)" class="btn btn-post" id="mainTopicReplyBoxSend">Post</a>
                                <a href="javascript:void(0)" class="btn btn-cancel" id="mainTopicReplyBoxCancel">Cancel</a>
                            </span><!--cancel-post-btns-->
                         </span><!--redactor-bootom-btns-->
                    </div>
                </div><!--olp-post-holder-->
            </div><!--change-size-of-bottom-container-->
        </div><!--container-->
    </div><!--view-un-answerd-strip-->
</section>


  
<section>
    <div class="post-main-comment-wraper">
        <div class="container  container-res-chnger-frorm-page">
            <div class="changed-container-for-forum">            
                <ul class="post-comment-parent">

                </ul><!--post-comment-parent-->
                
            </div><!--changed-container-for-forum-->
        </div><!-- container-res-chnger-frorm-page-->    
    </div><!--post-main-comment-wraper-->
</section>


<section>
    <div class="pagination-strip pagination-strip-top-margin-reduce pagination-top-bottom-expander">
        <div class="container container-res-chnger-frorm-page">
            <div class="changed-container-for-forum links-pagination">
              
            </div><!--changed-container-for-forum-->
        </div><!--container-->
    </div><!--pagination-strip-->
</section>



    <!-- initialising the tag plugin using tokenize  -->
    <script type="text/javascript" src="<?php echo assets_url() ?>js/multi-select/jquery.tokenize.js"></script>
    <script src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme'); ?>/tinymce/tinymce.min.js"></script>
    <script src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme'); ?>/tinymce/plugin.js"></script>
    <!-- <script src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme'); ?>/js/ckeditor/ckeditor.js"></script> -->

<script type="text/javascript">
    var __site_url = '<?php echo admin_url(); ?>';
    var __themes_url = '<?php echo  assets_url().'themes/'.$this->config->item('theme') ?>';
    var __default_user_image_path = '<?php echo default_user_path(); ?>';
    var __user_image_path = '<?php echo user_path(); ?>';
    var __topic_comments = atob('<?php echo base64_encode(json_encode($posts)) ?>');
    var __topic_id = '<?php echo $topic->id; ?>';
    var __forum_id = '<?php echo $forum_id; ?>';
    var __num_pages = parseInt('<?php echo $pages; ?>');
    var __limit = '<?php echo $topic_comment_limit; ?>';
    var __total_comments = '<?php echo $total_posts; ?>';
    var __child_limit = '<?php echo $child_limit; ?>';
    var __user_id = '<?php echo isset($admin['id'])?base64_encode($admin['id']):base64_encode('0'); ?>';
    var __recieved_childs = [];
    var __childs_total = [];
    var __offset_child = [];
    var __mentions = atob('<?php echo base64_encode(json_encode($mentions)) ?>');
    var __report_pageNumber = '<?php echo $reportdata['pagenumber']; ?>';
    var __report_id = '<?php echo $reportdata['reportid']; ?>';
    var __report_parent = '<?php echo $reportdata['parentid']; ?>';
    var __current_page = 1;
    var __current_page_count = 0;
</script>

<script src="<?php echo assets_url() ?>/js/forum_reported.js"></script>