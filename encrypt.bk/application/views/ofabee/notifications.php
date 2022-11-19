<?php include('header.php'); ?>

<section id="nav-group">
<div class="nav-group">
    <div class="container container-altr">
        <div class="container-reduce-width">
            <h2 class="funda-head"><?php echo lang('notifications');?></h2>
        </div>
    </div>
</div>
</section>

<section>
<div class="all-notifications">
    <div class="container container-altr">
        <div class="container-reduce-width">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <ul class="all-notification-list">
                    <?php foreach ($notifications as $notification) : ?>
                        <?php $is_window = ($notification['n_new_window']=="" || $notification['n_new_window']=="0")?'':'_blank' ?>
                        <li>
                            <span class="notification-desc"><?php echo isset($notification['n_title'])?$notification['n_title']:''; ?></span>
                            <span class="notification-link"><a target="<?php echo $is_window ?>" href="<?php echo site_url().$notification['n_slug'] ?>" class="name-orange">View Details</a></span> 
                        </li>
                    <?php endforeach; ?>                                                                         
                    </ul>
                </div>        
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12"> 
                    <div class="pagination-strip mb15">
                        <div class="container-res-chnger-frorm-page">
                            <div class="full-width">
                              <span class="pagination-prev-and-ul">
                                <span class="pagination-wraper">
                                    <ul class="pagination-black">
                                        <?php foreach ($links as $link) {
                                              echo "<li>". $link."</li>";
                                        } ?>
                                    </ul>
                                </span><!--pagination-wraper-->
                               </span><!--pagination-prev-and-ul--> 
                                    
                                    </span><!--pagination-next-last-->
                               
                            </div><!--changed-container-for-forum-->
                        </div><!--container-->
                    </div>                
                </div>
           </div>
        </div>  <!--container-reduce-width-->
    </div><!--container altr-->       
    </div><!--all-challenges-->
</section>

<?php include('footer.php'); ?>