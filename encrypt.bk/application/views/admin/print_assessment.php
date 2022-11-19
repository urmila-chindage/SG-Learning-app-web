<!DOCTYPE html>
<html>
<!-- head start-->
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <title><?php echo isset($title)?$title:config_item('site_name'); ?></title>
    <link rel="icon" href="<?php echo base_url('favicon.png') ?>">
    <?php if($this->router->fetch_class() == 'user' || $this->router->fetch_class() == 'course'): ?>
    <link rel="stylesheet" type="text/css" href="<?php echo assets_url() ?>css/multi-select/jquery.tokenize.css">

    <!-- ADDING REDACTOR PLUGIN INSIDE -->
    <!-- ############################# --> <!-- START -->
        <link rel="stylesheet" href="<?php echo assets_url() ?>css/redactor/css/redactor.css" />
        <link rel="stylesheet" href="<?php echo assets_url() ?>css/redactor/css/alignment.css" />
    <!-- ############################# --> <!-- END -->
    <!-- ADDING REDACTOR PLUGIN INSIDE -->
    <?php endif;  ?>
    
     

    <!-- Customized bootstrap css library -->
    <link rel="stylesheet" type="text/css" href="<?php echo assets_url() ?>css/bootstrap.css">
    <!-- Jquery library -->
    <script type="text/javascript" src="<?php echo assets_url() ?>js/jquery.min.js"></script>
    <script>
        var admin_url   = '<?php echo admin_url() ?>';
        var uploads_url = '<?php echo uploads_url() ?>';
    </script>
</head>
<!-- head end-->

<!-- body start-->
<body>
    <!-- Top head start-->
    <!-- <header class="header">
        <a href="javascript:void(0)" class="logo"></a>
        <ul class="headr-menu-rite">
            <li><a href="javascript:void(0)" class="mail-icoset dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                <i class="icon icon-mail-alt"></i><span class="buble-not">12</span></a>
                <ul class="dropdown-menu" aria-labelledby="dLabel">
                    <li><a href=""><?php echo lang('email me')?></a></li>
                 </ul>
            </li>
            <li><a href="javascript:void(0)" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"class="notify-icoset dropdown-toggle">
                <i class="icon icon-bell"></i><span class="buble-not">12</span></a>
                 <ul class="dropdown-menu" aria-labelledby="dLabel">
                    <li><a href=""><?php echo lang('email me')?></a></li>
                 </ul>
            </li>
            <li><a href="javascript:void(0)" class=""><?php echo lang('admin')?></a></li>
        </ul>
    </header> -->
    <!-- Top head end-->

    <!-- Side Menu start-->
        <?php //include_once "sidebar.php"; ?>
    <!-- Side Menu end-->

    <!-- Manin Iner container start -->
        
    <div class="">
    <?php if(isset($breadcrumb) && !empty($breadcrumb)): ?>
        <!-- Bread crumb added inside this section -->
        <!-- Breadcrumb START-->
        <!-- <ol class="breadcrumb">
            <?php foreach($breadcrumb as $bcrumb): ?>
                <li class="<?php echo isset($bcrumb['active'])?$bcrumb['active']:'' ?>">
                    <?php if(isset($bcrumb['link']) && $bcrumb['link']!=''): ?>
                        <a href="<?php echo isset($bcrumb['link'])?$bcrumb['link']:'javascript:void(0)' ?>"><?php echo isset($bcrumb['icon'])?$bcrumb['icon']:'' ?> <?php echo isset($bcrumb['label'])?$bcrumb['label']:'' ?>
                    <?php else: ?>
                        <?php echo isset($bcrumb['label'])?$bcrumb['label']:'' ?>
                    <?php endif; ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ol> -->
        <!-- Breadcrumb END-->
    <?php endif; ?>

<script type="text/javascript">
    var lecture_id = '<?php echo $lecture_id; ?>';
    var user_id    = '<?php echo $user_id; ?>';
    var user_name  = '<?php echo $user["us_name"]; ?>';
    var next_id    = '<?php echo $next_id; ?>';
</script>
<section class="">
    <!-- <div class="col-sm-6 ">
        <a href="<?php echo admin_url().'coursebuilder/report/'.$lecture_id; ?>" class="btn btn-red bck-btn selected"><i class="icon icon-left"></i> BACK<ripples></ripples></a> <h2 class="dsp-inline bold-heading"><?php echo strtoupper($course['cb_title']); ?> - <?php echo strtoupper($lecture['cl_lecture_name']); ?></h2>
    </div> -->
    <div class="col-sm-6 user-slide">
        <!-- <div class="pull-right dsp-inline">
            <a href="<?php 
        if($next_id != ''){
        echo admin_url().'coursebuilder/evaluate_assessment/'.$lecture_id.'/'.$next_id;
        }
        else{
            echo '#';
        }
             ?>">
                <h2 class="font14 font-bold nxt">
                    NEXT <i class="icon font-bold icon-right-open-big"></i>
                </h2>
            </a>
        </div> -->
        <div class="pull-right dsp-inline pad-top5 font15">
            <a href="<?php echo admin_url().'user/profile/'.$user['id']; ?>">
                <span class="icon-wrap-round sm-img img">
                    <img src="<?php echo (($user['us_image'] == 'default.jpg')?default_user_path():  user_path()).$user['us_image']; ?>">
                </span>

                <span class="line-h36"><?php echo $user['us_name']; ?></span>
            </a>
        </div>
        <!-- <div class="pull-right dsp-inline">
            <a href="<?php 
                if($prev_id != ''){
                echo admin_url().'coursebuilder/evaluate_assessment/'.$lecture_id.'/'.$prev_id;
                }
                else{
                    echo '#';
                }
            ?>">
                <h2 class="font14 font-bold prev">
                    <i class="icon font-bold  icon-left-open-big"></i>PREV
                </h2>
            </a>
        </div> -->
    </div>
    
    <div class="container-fluid">
        <div class="col-sm-12 table-data-bdr marg-top10">
            <div class="col-sm-12">
                <div class="rTable width-100p">
                    <div class="rTableRow">
                        <div class="rTableCell"><?php
                            $dt = new DateTime($user['aa_attempted_date']);
                            echo strtoupper($dt->format('M d Y'));
                            
                            $duration = $user['aa_duration']/60;
                            $user_in_mins = substr($duration, 0, 4);
                        ?></div>
                        <div class="rTableCell font-green"><?php echo $user['correct']; ?> Correct</div>
                        <div class="rTableCell font-red"><?php echo $user['incorrect']; ?> Wrong</div>
                        <div class="rTableCell font-lgt-grey"><?php echo $user['count_not_tried']; ?> Not Tried</div>
                        <div class="rTableCell font-green"><?php echo round($user['success_percent'], 2); ?>% success</div>
                        <div class="rTableCell"><?php echo $user_in_mins; ?> min</div>
                        <!-- <div class="rTableCell pad-vert"><a class="btn btn-green selected pull-right mrgin-rightM30 small-font" onclick="printreport()" >PRINT<ripples></ripples></a></div> -->
                    </div>

                </div>
            </div>
        </div>
    </div>
<?php 
$cnt = 1;
foreach($user['assessment_report'] as $report)
{ 
?>
    <div class="container-fluid">
        <div class="col-sm-12 question-cont marg-top10">
            <?php if($report['q_type'] != ''){ ?>
            <div class="col-xs-6 font15 font-bold">
                Question: <?php echo $cnt; ?> <span class="single-pad-left">
                 
                 <?php 
                    if($report['q_type'] == 1){
                        echo 'SINGLE CHOICE';
                    }
                    else if($report['q_type'] == 2){
                        echo 'MULTI CHOICE';
                    }
                    else if($report['q_type'] == 3){
                        echo 'EXPLANATORY';
                    }
                 ?>
                 </span>
            </div>
            <?php } ?>
    <?php if($report['q_type'] == 1 || $report['q_type'] == 2) { ?>
            <div class="col-xs-6 font15 font-bold">
            <?php if($report['correct'] == 1){ ?>
                <i class="font-green icon icon-ok pull-right"></i>
            <?php }else if($report['correct'] == 0){ ?>
                <i class="font-red pull-right icon icon-cancel-1"></i>
            <?php } ?>
            </div>
            <div class="col-sm-12 quest-descr">
                <?php echo $report['q_question']; ?>
                <div class="rTable choice-question width-100p">
                    <?php 
                    $chr  = 65; 
                    for($i =0; $i < count($report['options']);$i = $i + 1) 
                    { 
                    ?>
                    <?php if($i % 2 == 0){ ?>
                    <div class="rTableRow">
                    <?php } ?>
                        <div class="rTableCell">
                            <?php echo chr($chr++).'. '.$report['options'][$i]['qo_options']; ?>
                        </div>
                        
                    <?php if($i % 2 != 0){ ?>
                    </div>
                    <?php 
                    } }
                    ?>
                </div>
            </div>
    <?php } else if($report['q_type'] == 3){ ?>
            <div class="col-sm-12 quest-descr">
                <?php echo $report['q_question']; ?>
                <div class="choice-question width-100p">
                    <label><?php echo $report['ar_answer']; ?></label>
                </div>
            </div>
    <?php } ?>

        </div>

        <?php if($report['q_type'] == 1 || $report['q_type'] == 2){ ?>
        <div class="col-sm-12 result-sec table-data-bdr dark marg-top10">
            <div class="col-sm-12">
                <div class="rTable width-100p">
                    <div class="rTableRow font-bold">
                        <div class="rTableCell">Correct Answers: 
                        <?php 
                        if(isset($report['correct_answer'])){
                            echo $report['correct_answer'];
                        } 
                        ?></div>
                        <?php if($report['correct'] == 1){ ?>
                        <div class="rTableCell font-green">Marked Answers: 
                        <?php
                        if(isset($report['user_answer'])){
                            echo $report['user_answer'];
                        } 
                        ?>
                        </div>
                        <div class="rTableCell pad-vert"><span class = "pull-right font-green">Mark +<?php echo $report['q_positive_mark']; ?></span></div>
                        <?php }
                            else if($report['correct'] == 0){
                        ?>
                        <div class="rTableCell font-red">Marked Answers: 
                        <?php
                        if(isset($report['user_answer'])){
                            echo $report['user_answer'];
                        } 
                        ?>
                        </div>
                        <div class="rTableCell pad-vert"><span class = "pull-right font-red">Mark -<?php echo $report['q_positive_mark']; ?></span></div>
                        <?php
                            }
                            else if($report['correct'] == 2){
                        ?>
                        <div class="rTableCell font-lgt-grey">Not Tried</div>
                        <div class="rTableCell pad-vert"></div>
                        <?php
                            }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <?php } else if($report['q_type'] == 3){ ?>
        <div class="col-sm-12 result-sec explan-qst table-data-bdr dark marg-top10">
            <div class="col-sm-12">
                <div class="rTable width-100p">
                    <div class="rTableRow font-bold">
                        <div class="rTableCell"></div>
                        <div class="rTableCell pad-vert">
                            <span class="pull-right">Mark <?php echo $report['ar_mark']; ?>
                                
                            </span>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <?php } ?>
    </div>
<?php
 $cnt++;
 } 
 ?>
 <!-- <div class="container-fluid marg-bot10 pad-top30">
    <div class="col-sm-12">
        <a class="btn btn-green selected pull-right" onclick="savecontinue()" >SAVE &amp; CONTINUE<ripples></ripples></a>
        <a class="btn btn-green selected pull-right" onclick="saveexit()" >SAVE &amp; EXIT<ripples></ripples></a>
    </div>
</div> -->
</section>

