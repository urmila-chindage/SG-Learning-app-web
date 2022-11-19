<?php include('header.php');?>
<style media="screen">
    .notification-col{
        background: #fff;
        padding: 25px 20px;
        margin: 5px 0px;
    }
    .notification-wrapper{
        padding: 25px 0;
        float: right;
    }
    .notification-wrapper a{text-decoration:none;}
@media (max-width:780px){
    .notification-box{
        min-height: auto;
        padding: 15px 15px 15px 65px;
        margin: 5px 0px;
        border-right: none;
        border-top: solid 1px #dadada;
        border-left: none;
        border-bottom: none;
    }
    .notification-box a{font-size: 16px;}
    .notification-col.active-notification{background-color: #eaeaea;}
    .notification-col{
        background: #fff;
        padding: 10px 20px;
        margin: 2px 0px;
    }
    .notification-wrapper{
        padding: 5px 0;
        float: right;
        height: calc(100vh - 150px);
        overflow-y: auto;
    }
}
</style>


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
                <div class="notification-wrapper">
                    <?php
                        foreach($notifications as $key => $notify){
                        ?> 
                            <div class="col-sm-12 col-xs-12 col-md-12 notification-col <?php echo ($notify['seen']!=0)?' active-notification':'';?> " id="<?php echo $key ?>" data-link="<?php echo $notify['link'];?>">
                                <a onclick="markRead('<?php echo $key ?>')" href="javascript:void(0)" class="notification general-notification">
                                    <span class="noti-text"><?php echo $notify['message'];?><span class="notification-time"><?php echo $notify['time'];?></span></span>
                                </a>
                            </div>
                        <?php
                        }
                    ?>
                </div>       
            </div>
            
        </div>  <!--container-reduce-width-->
    </div><!--container altr-->       
    </div><!--all-challenges-->
</section>

<?php include('footer.php'); ?>
<script>
function markRead(message){
    $.ajax({
            type: "POST",
            url: '<?php echo site_url('dashboard/read_notification'); ?>',
            data : {notification : message},
            success: function (response) {
                var data = $.parseJSON(response);
                //console.log(data);
                if(data['success']){
                    $(message).removeClass('readed');
                    var link = $('#'+message).attr('data-link');
                    window.location = siteUrl+link;
                }
            }
        });
}
</script>