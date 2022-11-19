<?php //print_r($this->__access);die;?>
<?php include_once 'bundle_header.php';?>

<style>
.star-ratings-sprite {
    background: url('<?php echo assets_url() ?>img/star-rating-sprite.png') repeat-x;
    font-size: 0;
    height: 21px;
    line-height: 0;
    overflow: hidden;
    text-indent: -999em;
    width: 80px;
    display: inline-block;
}
.star-ratings-sprite-rating {
    background: url('<?php echo assets_url() ?>img/star-rating-sprite.png') 0 124% repeat-x;
    float: left;
    height: 21px;
    display: block;
}
.anouncement-holder{     
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.anouncement-holder .active-section{height: 20px;}
.anouncement-holder .Inactive-section{height: 20px;}
.anouncement-holder .btn-group.lecture-control{margin: 0px;}
.review-width-65{ width:65%;}
.anouncement-content .redactor-editor{
    padding: 10px 0px 0px 0px !important;
    word-break: break-word;
}
.anouncement-holder .icon-wrap-round.img{margin: 0;}
</style>

               
<section class="content-wrap cont-course-big nav-included content-wrap-align content-wrap-top review-wrapper"> 

    <div class="save-btn" id="reviewExportButton" style="display:none">
        <button class="pull-right btn btn-green" onclick="exportReviews();" style="margin: -28px 31px 0px 0px;">EXPORT</button>
    </div>
    
    <div class="left-wrap col-sm-12">
        <div class="row">
            <div class="col-sm-12 " id="review">
                <div class="col-sm-12 " id="reviewblock">
                    <?php /*?>
                    <!-- reviews static design-->
                    <div class="panel-group anouncement-pannel" id="an_id31" data-id="31">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <div class="anouncement-holder">
                                    <div class="review-width-65">
                                        <div class="media">
                                            <div class="media-left">                          
                                                <span class="icon-wrap-round img">                              
                                                    <img src="https://SGlearningapp.enfinlabs.com/uploads/SGlearningapp.enfinlabs.com/user/82.jpg">                          
                                                </span>                      
                                            </div>
                                            <div class="media-body reviewer-info">
                                                <span class="media-heading review-name">Anson</span>                          
                                                <p class="date">2019 Jun 24</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="section_status_wraper_31" class="Inactive-section">
                                        <span class="ap_cont section-main-18" id="section_status_text_31"><span class="warning-icon">!</span> Unpublished</span>
                                    </div>
                                    <div class="td-dropdown rTableCell">
                                        <div class="btn-group lecture-control">
                                            <span class="dropdown-tigger" data-toggle="dropdown" aria-expanded="false">                          <span class="label-text"><i class="icon icon-down-arrow"></i></span>                          <span class="tilder"></span>                      </span>                      
                                            <ul class="dropdown-menu pull-right" role="menu" id="review_menu_31">
                                                <li>  <a href="javascript:void(0)" onclick="changeReviewStatus(31,'1')">Publish</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="anouncement-content">
                                    <div id="an_31_des" class="redactor-editor">
                                        I've bought three courses so far: Modern Python Bootcamp and SQL Bootcamp by Colt Steele as well as Python for Data Science and Machine learning by Jose Portilla.
                                    </div>
                                    <div class="review-actions d-flex justify-between align-center hide-review-actions">
                                        <div class="star-ratings-sprite star-ratings-sprite-block">                  
                                            <span style="width:20%" class="star-ratings-sprite-rating"></span>              
                                        </div>
                                        <div class="publish-ignore">
                                            <label class="label label-success">Publish</label>
                                            <label class="label label-warning">Ignore</label>
                                        </div>
                                        <div class="text-right reply-btn-holder">
                                            <a class="reply-btn" href="#">
                                                <span><svg id="Layer_1" xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 77.025 1792 1571.686"><path fill="#57ba56" stroke="#57ba56" stroke-width="90" stroke-miterlimit="10" d="M1720.697,1099.628 c0,101.86-38.964,240.229-116.894,415.109c-1.841,4.296-5.063,11.659-9.664,22.091c-4.603,10.431-8.744,19.635-12.426,27.612 s-7.67,14.727-11.966,20.249c-7.363,10.432-15.953,15.647-25.771,15.647c-9.204,0-16.414-3.067-21.63-9.204 s-7.823-13.807-7.823-23.011c0-5.522,0.767-13.652,2.301-24.392c1.534-10.738,2.301-17.947,2.301-21.629 c3.067-41.726,4.603-79.463,4.603-113.212c0-61.976-5.369-117.508-16.107-166.597c-10.739-49.089-25.618-91.582-44.641-127.479 s-43.566-66.884-73.634-92.962c-30.067-26.079-62.435-47.402-97.104-63.97c-34.669-16.567-75.475-29.607-122.416-39.118 s-94.189-16.107-141.745-19.789c-47.555-3.681-101.399-5.522-161.534-5.522H660.372v235.627c0,15.954-5.829,29.76-17.488,41.419 c-11.659,11.659-25.465,17.488-41.419,17.488s-29.76-5.829-41.419-17.488L88.791,699.245 c-11.658-11.659-17.488-25.465-17.488-41.419s5.83-29.76,17.488-41.419l471.256-471.255c11.659-11.659,25.465-17.488,41.419-17.488 s29.76,5.83,41.419,17.488c11.659,11.658,17.488,25.465,17.488,41.419v235.627h206.174c437.506,0,705.963,123.643,805.369,370.93 C1704.437,875.352,1720.697,977.519,1720.697,1099.628z"></path></svg></span>
                                                Reply
                                            </a>
                                        </div>
                                    </div>

                                    <!-- Admin reply -->
                                    <div class="admin-reply-container">
                                        <!-- reply writer -->
                                        <div class="reply-writer">
                                            <div class="d-flex flex-row">
                                                <div class="media-left">                          
                                                    <span class="icon-wrap-round img">                              
                                                        <img src="https://SGlearningapp.enfinlabs.com/uploads/SGlearningapp.enfinlabs.com/user/82.jpg">                          
                                                    </span>                      
                                                </div>
                                                <div class="width100">
                                                    <span class="media-heading review-name">Ofabee Team</span>
                                                    <textarea placeholder="Write Your Reply" class="form-control" rows="5"></textarea>
                                                </div>
                                            </div>
                                            
                                            <div class="text-right reply-row">
                                                <label class="label label-warning">Cancel</label>
                                                <label class="label label-success">Post</label>
                                            </div>
                                        </div>
                                        <!-- reply preview -->
                                        <div class="admin-reply-preview">
                                            <div class="anouncement-holder">
                                                <div class="review-width-65">
                                                    <div class="media">
                                                        <div class="media-left">                          
                                                            <span class="icon-wrap-round img">                              
                                                            <img src="https://SGlearningapp.enfinlabs.com/uploads/SGlearningapp.enfinlabs.com/user/82.jpg">                          
                                                            </span>                      
                                                        </div>
                                                        <div class="media-body">
                                                            <span class="media-heading review-name">Ofabee Team</span>                          
                                                            <p>Thank you for your response, and please keep on subscribing cousrses</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <a class="edit-review"><i class="icon icon-pencil"></i> Edit</a>
                                            </div>
                                        </div>
                                        <!-- reply preview ends -->
                                        <!-- reply writer ends -->
                                    </div>
                                    <!-- Admin reply ends -->

                                </div>

                                <?php ?>

                            </div>
                        </div>
                    </div>
                    <!-- reviews ends -->
                    <?php */?>
                </div>
            </div>

        </div>
    </div>
</section>
<?php $no_content_js = true; ?>
<script src="<?php echo assets_url() ?>js/jquery.timeago.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/jquery.slimscroll.min.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/multi-select/jquery.tokenize.js"></script>
<?php include_once 'training_footer.php';?>
<script>
    $(document).ready(function () {
        loadReviewssAdmin();
    })
</script>


<script>
    var __limit         = '<?php echo $limit ?>';
    var __bundle_id      = '<?php echo $bundleId; ?>';
    var __defaultpath   = '<?php echo default_user_path(); ?>';
    var __userpath      = '<?php echo user_path(); ?>';
    var __privilege     = JSON.parse('<?php echo json_encode($this->__access); ?>');
    var __userPrivilege = JSON.parse('<?php echo json_encode($this->review_permission); ?>');
    var __admin_name    = '<?php  echo $admin['us_name'] ?>';
    var __assets_url    = '<?php echo assets_url();?>';
    //var __bundleId     = '<?php echo $bundleId;?>';
    //console.log(__privilege, __userPrivilege);
    //console.log(jQuery.inArray( 1, __userPrivilege ));
$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip();   
});
</script>
<script src="<?php echo assets_url() ?>js/bundle_reviews.js"></script>