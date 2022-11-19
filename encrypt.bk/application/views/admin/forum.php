<?php include_once "header_forum.php"; ?>
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
                <div class="changed-container-for-forum clearfix">
                    <a href="<?php echo admin_url('forum/create_forum'); ?>" class="orange-flat-btn-for-admin-forum">Create Forum</a>
                </div>
                <div class="changed-container-for-forum forum-active-link">
                    <span class="dropdown-full-width">
                        <span class="sort-by">Show by</span>
                        <span class="recent-posts">
                            <div class="dropdown dropdown-border-small">
                                <button class="btn btn-transperant dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"><span id="filterLabel"></span>
                                    <span class="btn-transperant-arrow">
                                        <svg version="1.1" x="0px" y="0px" width="21px" height="17px" viewBox="0 0 21 17" enable-background="new 0 0 21 17" xml:space="preserve">
                                        <g>
                                            <g>
                                                <path fill="#4d4d4d" d="M16.2,4.2L10.5,10L4.8,4.2L3,6l7.5,7.5L18,6L16.2,4.2z"></path>
                                                <path fill="none" d="M-4.5-6.5h30v30h-30V-6.5z"></path>
                                            </g>
                                        </g></svg>
                                    </span>
                                </button>
                                <ul class="dropdown-menu dropdown-small dropdown-filter">
                                    <li><a href="javascript:void(0)" onclick="set_order_type(2)">Active Topics</a></li>
                                    <li><a href="javascript:void(0)" onclick="set_order_type(1)">Unanswered first</a></li>
                                </ul>
                            </div>
                            <!--dropdown-->
                        </span>
                        <!--recent-posts-->
                    </span>

                </div><!--change-size-of-bottom-container-->
            </div><!--container-->
        </div><!--view-un-answerd-strip-->
    </section>

    <section>
        <div class="discussion-forum-wraper">
            <div class="container container-res-chnger-frorm-page">
                <div class="changed-container-for-forum">
                    <ul class="discussion-forum-parent">
                        //Forums with header will come here.
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


<div class="modal fade" id="DeleteModal" role="dialog">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-close-wrap clearfix">
                <h3 class="delet-post-head">Delete Forum</h3>
                <button type="button" class="close close-modified" data-dismiss="modal">
                            &times;
                        </button>
            </div>
            <!-- modal-close-wrap -->
            <div class="common-modal-content-wraper remove-top-padd">
                <div class="row">
                    <div class="col-md-3 col-sm-3 col-xs-12">
                            <img class="oops-face" src="<?php echo assets_url('themes').$this->config->item('theme') ?>/img/Oops_Face_icon.svg" alt="">
                    </div>
                    <!--columns-->
                    <div class="col-md-9 col-sm-9 col-xs-12">
                        <span class="oops-content">
                            This Forum will be deleted and you won't be able to find it anymore. This action cannot be undone.
                        </span>
                            <!--oops-content-->
                            <div class="modal-footer-btn text-right modal-btn-wrap-responsive">
                                <a href="javascript:void(0)" data-dismiss="modal" class="btn modalGrey-btn margin-right">Cancel</a>
                                <a href="javascript:void(0)" class="btn modalorange-btn no-right-margin" id="delete_main" onclick="">Delete</a>
                            </div>
                    </div>
                        <!--columns-->
                </div>
                    <!--row-->

            </div>
            <!-- common-modal-content-wraper -->
        </div>

    </div>
</div>


<script type="text/javascript">
    var __forums = atob('<?php echo base64_encode(json_encode($forums)) ?>');
    var __themes_url = '<?php echo  assets_url().'themes/'.$this->config->item('theme') ?>';
    var __num_pages = parseInt('<?php echo $pages; ?>');
    var __total_forums = parseInt('<?php echo $total_forum; ?>');
    var __site_url = '<?php echo admin_url(); ?>';
    var __limit = '<?php echo $forum_limit; ?>';
    var __listing_type = 0;
    var __current_page = 1;
    var __current_page_count = 0;
</script>
<script src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/js/toastr.min.js"></script>
<script src="<?php echo assets_url() ?>/js/forum_index.js"></script>