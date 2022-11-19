<html>
<!-- head start-->
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php echo isset($title)?$title:config_item('site_name'); ?></title>
    <link rel="icon" href="<?php echo base_url('favicon.png') ?>">
    <link rel="icon" href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/images/favicon.ico">
    <!-- Customized bootstrap css library -->
    <link rel="stylesheet" type="text/css" href="<?php echo assets_url() ?>css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="<?php echo assets_url() ?>css/custom.css">
</head>
<!-- head end-->

<!-- body start-->
<body>
    <!-- Top head start-->
        <?php include_once 'head.php'; ?>
    <!-- Top head end-->

    <!-- Side Menu start-->
        <?php include_once "sidebar_teacher.php"; ?>
    <!-- Side Menu end-->


    <!-- Manin Iner container start -->
    <div class='dashbrd-container pos-top50 main-content'>
        <?php 
        
     $now           = time(); // or your date as well
     $expiry_time   = strtotime(config_item('acct_expiry_date'));
     $datediff      = $now - $expiry_time;
     $expiry_days   = abs(floor($datediff/(60*60*24)));
         

?>
        <?php if($expiry_days <= 20 ): ?>
            <div class="dash-expry">
                <?php 
                    switch ($expiry_days)
                    {
                         case ($expiry_days <= 0):
                            echo sprintf(lang('trial_expired'), $expiry_days).' <a href="#!.">Upgrade</a>';                             
                             break;
                         case ($expiry_days > 0):
                            echo sprintf(lang('trial_expires'), $expiry_days).' <a href="#!.">Upgrade</a>';                             
                             break;
                    }
                ?>
                
            </div>
        <?php endif; ?>
        <?php $admin = $this->session->userdata('teacher');?>
        <h3 class="dash-wecl-ttle"><?php echo lang('welcome_back') ?>, <span><?php echo $admin['us_name'] ?>!</span><br/><?php //echo lang('greetings') ?></h3>
        <ul class="dash-ico-items">
            <li><a href="<?php echo admin_url('course') ?>" class="dash-mc" ><i></i><span>Courses</span></a></li>
            <!-- <li><a href="<?php echo admin_url('user') ?>" class="dash-mu" ><i></i><span>Users</span></a></li> -->
            <li><a href="<?php echo admin_url('coursebuilder/report') ?>" class="dash-r" ><i></i><span>Reports</span></a></li>
            <?php /* ?><li><a href="javascript:void(0)" class="dash-mf" ><i></i><span>Manage Faculties</span></a></li>
            <li><a href="javascript:void(0)" class="dash-s" ><i></i><span>Settings</span></a></li>
            <li><a href="<?php echo admin_url('page') ?>" class="dash-cms" ><i></i><span>CMS</span></a></li><?php  */?>
        </ul>
        <div class="dash-chart-wrap">
        </div>
    </div>
    <div class='dashbrd-container pos-top50 main-content'>
        <div class="padder pull-left">
            <div class="col-sm-6 text-left dashboard-liveclass">
                <div class="col-sm-12 no-padding">
                     <h4 class="course-head">UP COMMING LIVE CLASSES</h4>
                    <div class="list-style-wrap container-fluid">
                        
                        <?php if(isset($live_classes) && !empty($live_classes)): ?>
                        <?php foreach($live_classes as $live_class): ?>
                            <div class="list-style-div row">
                                <div class="col-sm-8 ellipsis-hidden">
                                    <div class="ellipsis-style">                                        
                                        <span class="date-wdth-fxd"><?php echo event_day($live_class['ll_date']); ?></span><span class="italic-span">: <?php echo $live_class['ll_time'] ?> - <?php echo $live_class['live_lecture_name']; ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-4 pad0">                                
                                    <a id="start_or_stop_live_btn" class="pull-right btn <?php echo (isset($live_class['ll_is_online']) && $live_class['ll_is_online'])?'btn-red':'btn-light-green' ?>" onclick="startOrStopLive('<?php echo $live_class['live_lecture_id'] ?>','<?php echo $live_class['ll_course_id']?>', '<?php echo (isset($live_class['ll_is_online']) && $live_class['ll_is_online'])?'0':'1' ?>');" href="javascript:void(0)" ><?php echo lang((isset($live_class['ll_is_online']) && $live_class['ll_is_online'])?'stop':'start') ?></a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                 </div>            
            </div>
            
            <div class="col-sm-6 text-left dashboard-discussions">
                <!-- <div class="row heading-right">
                    <div class="col-sm-12">
                        <h4>LATEST DISCUSSIONS</h4>
                    </div>
                </div> -->

                <div class="row right-cont-style">
                    <div class="col-sm-12">
                        <?php if(isset($course_comments) && !empty($course_comments)): ?>
                        <?php foreach($course_comments as $comment): ?>
                        <div class="right-group-wrap clearfix">
                            <span class="box-style"></span>
                            <span class="user-date">
                                <span class="user"><?php echo $comment['us_name'] ?></span><br>
                                <?php 
                                    $today              = date('F d, Y');
                                    $today_date_time    = date('F d, Y h:i a');
                                    $date               = date('F d, Y h:i a', strtotime($comment['created_date']));
                                    $fetch_date         = date('F d, Y', strtotime($comment['created_date']));
                                    $yesterday          = date('F d, Y',strtotime("-1 days"));
                                    //die($today.'//'.$date);
                                    if($today==$fetch_date)
                                    {
                                        $time           = date('h:i a', strtotime($comment['created_date']));
                                        $date           = 'Today'.' '.$time;
                                    }
                                    if($yesterday==$fetch_date)
                                    {
                                        $time   = date('h:i a', strtotime($comment['created_date']));
                                        $date   = 'Yesterday'.' '.$time;
                                    }
                                ?>
                                <span class="date"><?php echo $date;?></span>
                            </span>
                            <div class="content-text">
                                <?php echo (strlen($comment['comment'])>150)?(substr($comment['comment'], 0, 147).'...'):$comment['comment']; ?>
                                <?php $comment_id = ($comment['parent_id']>0)?$comment['parent_id']:$comment['id']; ?>
                                <a href="<?php echo admin_url().'course/discussion/'.$comment['course_id'].'/'.$comment_id; ?>" class="link-style pull-right">Reply</a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Manin Iner container end -->
    <div class="modal fade active-popup" id="active-lecture" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog modal-small" role="document">
                <div class="modal-content">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                    <div class="modal-body">
                        <div class="form-group">
                            <b id="header_text"></b>
                            <p class="m0">Are you sure?.</p>
                            <p id="popup_message"></p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-green" data-dismiss="modal">CANCEL</button>
                        <button type="button" class="btn btn-red" id="change_status_section" >CONTINUE</button>
                    </div>
                </div>
            </div>
        </div>
</body>
<!-- body end-->
<?php 
function event_day($live_event_date)
{
    $current_date    = date('Y-m-d');
    $total_days      =  round(abs(strtotime($current_date)-strtotime($live_event_date))/86400);
    switch ($total_days) {
        case 0:
            $day = lang('today');
        break;
        case 1:
            $day = lang('tommorrow');
        break;
        default:
            $day = $live_event_date;
        break;
    }
    return $day;
}
?>
</html>
<!-- Jquery library -->
<script type="text/javascript" src="<?php echo assets_url() ?>js/jquery.min.js"></script>

<!-- bootstrap library -->
<script type="text/javascript" src="<?php echo assets_url() ?>js/bootstrap.min.js"></script>
<!-- custom layput js handling tooltip and hide show switch -->
<script type="text/javascript" src="<?php echo assets_url() ?>js/layout.min.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/language.js"></script>
<script>
var admin_url    = '<?php echo admin_url(); ?>';
var site_url     = '<?php echo site_url(); ?>';
var myWindow;
var __controller = '<?php echo $this->router->fetch_class() ?>';


function startOrStopLive(live_id, course_id, make_online)
{
    if(make_online == '0'){
        closeLive();
    }else{
        popLive(live_id);
    }
    $.ajax({
        url: admin_url+'coursebuilder/configure_live',
        type: "POST",
        data:{"is_ajax":true, "live_id":live_id,"course_id":course_id,"make_online":make_online},
        success: function(response) {
            var data                = $.parseJSON(response);
           
            if(data['already_live'] > 0 )
            {
                $('#active-lecture').modal();
                $('#header_text').html(lang('other_lecture_on_live'));
                $('#popup_message').html(lang('message_to_disable_live'));
                $('#change_status_section').unbind();
                $('#change_status_section').html(lang('start'));
                $('#change_status_section').click({"live_id": live_id, "course_id": course_id, "make_online": make_online}, startOrStopLiveInput);    
            }
            else
            {
                var currently_live = (make_online==1)?0:1;
                var current_lang   = (make_online==1)?'stop':'start';
                if(make_online == 1)
                {
                    $('#start_or_stop_live_btn').addClass('btn-red').removeClass('btn-green');;
                }
                else
                {
                    $('#start_or_stop_live_btn').addClass('btn-green').removeClass('btn-red');
                }
                $('#start_or_stop_live_btn').removeAttr('onclick');
                $('#start_or_stop_live_btn').unbind();
                $('#start_or_stop_live_btn').html(lang(current_lang));
                $('#start_or_stop_live_btn').click({"live_id": live_id, "course_id": course_id, "make_online": currently_live}, startOrStopLiveInput);    
                $('#active-lecture').modal('hide');
                
                //location.reload();

            }
            
        }
    });    
}

function popLive(live_id) {
    myWindow= window.open(site_url+'/live/join/'+live_id, "_blank");
    myWindow.focus();
}

function closeLive() {
  if(myWindow)
   myWindow.close();
}

function startOrStopLiveInput(param)
{
    startOrStopLiveConfirmed(param.data.live_id, param.data.course_id, param.data.make_online);
}

function startOrStopLiveConfirmed(live_id,course_id,make_online)
{
    $.ajax({
        url: admin_url+'coursebuilder/configure_live_confirmed',
        type: "POST",
        data:{"is_ajax":true, "live_id":live_id, "course_id":course_id, "make_online":make_online},
        success: function(response) {
            var data           = $.parseJSON(response);
            var currently_live = (make_online==1)?0:1;
            var current_lang   = (make_online==1)?'stop':'start';
                if(make_online == 1)
                {
                    $('#start_or_stop_live_btn').addClass('btn-red').removeClass('btn-green');;
                }
                else
                {
                    $('#start_or_stop_live_btn').addClass('btn-green').removeClass('btn-red');
                }
            $('#start_or_stop_live_btn').removeAttr('onclick');
            $('#start_or_stop_live_btn').unbind();
            $('#start_or_stop_live_btn').html(lang(current_lang));
            $('#start_or_stop_live_btn').click({"live_id": live_id, "course_id": course_id, "make_online": currently_live}, startOrStopLiveInput);    
            $('#active-lecture').modal('hide');
            
        }
    });  
}
</script>
<?php /* ?>
<!-- dashboard js -->
<script type="text/javascript" src="<?php echo assets_url() ?>js/Chart.js"></script>

<!-- Circle Progress Bar -->
<script type="text/javascript" src="<?php echo assets_url() ?>js/circle-bar/jquery.circle-diagram.js"></script>


<script type="text/javascript" src="<?php echo assets_url() ?>js/dashboard.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $('#diagram-id-1').circleDiagram({
            "percent": "56%",
            "size": "150",
            "borderWidth": "8",
            "bgFill": "#F3F3F3",
            "frFill": "#F15F06",
            "textSize": "31",
            "textColor": "#585858"
        }); 

        $('#active_course').circleDiagram({
            "percent": "<?php echo $active_course ?>%",
            "size": "150",
            "borderWidth": "8",
            "bgFill": "#F3F3F3",
            "frFill": "#75B40A",
            "textSize": "31",
            "textColor": "#585858"
        });

        $('#active_user').circleDiagram({
            "percent": "<?php echo $active_user ?>%",
            "size": "150",
            "borderWidth": "8",
            "bgFill": "#F3F3F3",
            "frFill": "#BA1712",
            "textSize": "31",
            "textColor": "#585858"
        }); 

        $('#diagram-id-4').circleDiagram({
            "percent": "80%",
            "size": "150",
            "borderWidth": "8",
            "bgFill": "#F3F3F3",
            "frFill": "#75BB04",
            "textSize": "31",
            "textColor": "#585858"
        }); 

    });
</script><?php */ ?>



