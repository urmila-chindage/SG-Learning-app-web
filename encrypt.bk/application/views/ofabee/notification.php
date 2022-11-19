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
                <div class="col-xs-12 col-sm-12 col-md-12 notification-box">
                    <h3><?php echo $notification_content['n_title'] ?></h3>
                    <?php echo $notification_content['n_content'] ?>
                </div>        
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 mb30">
                    <div class="text-center">
                        <span class="noquestion-btn-wrap"><a href="<?php echo site_url('notification'); ?>" class="orange-flat-btn noquestion-btn">View all notifications</a></span>
                    </div>               
                </div>
            </div>
        </div>  <!--container-reduce-width-->
    </div><!--container altr-->       
    </div><!--all-challenges-->
</section>

<?php include('footer.php'); ?>