<html lang="en">
<!-- Head start -->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php if(!isset($meta_original_title)){ $meta_original_title = config_item('site_name');}?>
    <meta name="title" content="<?php echo isset($meta_title)?$meta_title:$meta_original_title; ?>">
    <meta name="description" content="<?php echo isset($meta_description)?$meta_description:''; ?>">
    <?php if(isset($page) && $page == 'coursedescription'){?>
    <meta property="og:url"           content="<?php echo $current_url; ?>" />
    <meta property="og:type"          content="website" />
    <meta property="og:title"         content="<?php echo $meta_original_title; ?>" />
    <meta property="og:description"   content="<?php echo $meta_description; ?>" />
    <meta property="og:image"         content="" />
    <?php }?>
    <link rel="icon" href="images/favicon.ico">
    <title><?php echo isset($title)?$title:config_item('site_name'); ?></title>
    
    <!-- Bootstrap core CSS -->
    <link href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/bootstrap.css" rel="stylesheet">
    <link href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/font-awesome.min.css" rel="stylesheet">
    <link href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/bootstrap-select.css" rel="stylesheet">
    <link href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/owl.carousel.css" rel="stylesheet">
    <link href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/flexslider.css" rel="stylesheet">
    <link href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/starability-all.min.css" rel="stylesheet">
    <link href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/styles.css" rel="stylesheet">
    <link href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/custom.css" rel="stylesheet">
    <!--[if lt IE 9]>
    <script src="js/ie8-responsive-file-warning.js"></script>
    <![endif]-->
    <script src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/js/ie-emulation-modes-warning.js"></script>
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    
    <script src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/js/jquery.min.js"></script>
    <script type="text/javascript">
        var base_url = '<?php echo site_url('/'); ?>';
        $(document).on('change', '#basic', function(){
            var category_slug = $(this).find(':selected').data('link');
            window.location.href = base_url+""+category_slug;
        });
    </script>
    <?php if(isset($page) && $page == 'coursedescription'){?>
        <script src="https://apis.google.com/js/platform.js" async defer></script>
    <?php } ?>
</head>

<body>
    <?php if(isset($page) && $page == 'coursedescription'){?>
    <div id="fb-root"></div>
    <div id="fb-root"></div>
    <script>(function(d, s, id) {
      var js, fjs = d.getElementsByTagName(s)[0];
      if (d.getElementById(id)) return;
      js = d.createElement(s); js.id = id;
      js.src = "//connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v2.7";
      fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));</script>
    <?php } ?>

    <?php $header_categories = get_header_categories();
    /*if(!empty($category_content)){
        $category_id; 
        $category_pages = get_category_pages($category_id);    
    }*/
    if(!empty($_SESSION['course_id'])){
        $category_pages = get_category_pages($_SESSION['course_id']);    
    }
    $challenge_zone = array();
    if(isset($category_id)){
        if($category_id){
            $challenge_zone = get_challenge_zone($category_id);
        }
    }
    ?>
    

    <?php 
    function page_tree($page_tree)
    {
        $html = '<ul>';
        foreach($page_tree as $page) {
            $active = (1==$page['p_slug'])?'active':'';
            $html.= '<li class="new"><a class="'.$active.'" href="'.$page['p_slug'].'">';
            $html.=$page['p_title'];
            $html.= '</a>';
            if(!empty($page['children']))
            {
                $html.= page_tree($page['children']);
            }
            $html.= '</li>';    
        }
        $html.= '</ul>';
        return $html;
    }
    
    ?>
    <?php if(!empty($_SESSION['course_id'])): ?>
    <!--- linkbar ------>
    <div class="linkbar">
        <div class="container">
            <ul class="sub_menus">
                <?php foreach ($category_pages as $category_page) : ?>
                    <li>
                        <a class="<?php echo ($this->uri->segment(1)==$category_page['p_slug'])?'active':''?> " href="<?php echo $category_page['p_slug'] ?>">
                            <?php echo $category_page['p_title'] ?>
                        </a>
                        <?php 
                        //$GLOBALS['id'] = $category_page['id'];
                        $sub_pages = get_subpages($category_page['id']);
                        //echo "<pre>";print_r($sub_pages);die;
                        if(!empty($sub_pages)){
                           echo page_tree($sub_pages);
                        }
                        ?>
                    </li>
                    
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <!--- linkbar end------>
    <?php endif; ?>
<div class="container darkbg">
    <div class="row">
        <div class="col-xs-12 col-sm-12 spacer">
            <div class="col-xs-12 col-sm-12">
            
            <span class="maintitle"><?php echo strtoupper($current_category['ct_name']); ?> - <?php echo strtoupper($cz_title); ?></span></div>
        </div>
    </div>
    <div class="row borderrow">
        <div class="col-xs-12 col-sm-12">
            <div class="row lh">
            <div class="col-xs-6 col-sm-6 sdfw">
                <div class="col-xs-3 col-sm-3">
                    <?php
                    $dt = new DateTime($user['uga_attempted_date']);
                    echo strtoupper($dt->format('M d Y'));
                    ?>
                </div>
                <div class="col-xs-3 col-sm-3 eval_greentext"><?php echo $user['correct']; ?>  correct</div>
                <div class="col-xs-3 col-sm-3 eval_redtext"><?php echo $user['incorrect']; ?> wrong</div>
                <div class="col-xs-3 col-sm-3 eval_lightgreytext"><?php echo $user['count_not_tried']; ?> not tried</div>
            </div>
            <div class="col-xs-6 col-sm-6 sdfw">
                <div class="col-xs-4 col-sm-4 eval_greentext"><?php echo round($user['percentage'], 2); ?>% success</div>
                <div class="col-xs-4 col-sm-4"><?php echo $user['uga_duration']; ?> min</div>
                
            </div>
            </div>
        </div>
    </div>

<?php 
$cnt = 1;
foreach($user['assessment_report'] as $report)
{ 
?>
    <?php 
    if($report['q_type'] == 1){
    ?>
<div class="questionrow"> <!--start of question row-->
    <div class="row">

        <div class="col-xs-12 col-sm-12">
            <div class="col-xs-10 col-sm-10 spacer">
                <b>Question: <?php echo $cnt; ?></b> <span class="mainquestion">SINGLE CHOICE</span>
            </div>
            <div class="col-xs-2 col-sm-2 spacer">
                <i class="redicon icon-cancel pull-right"></i>
            </div>
            <div class="col-xs-12 col-sm-12 spacer"><?php echo $report['q_question']; ?></div>
                <div class="col-xs-12 col-sm-12 spacer">
                <?php 
                    $chr  = 65; 
                    for($i =0; $i < count($report['qo_options_value']);$i = $i + 1) 
                    {
                ?>
                    <div class="col-xs-6 col-sm-6 spacer usdfw"><?php echo chr($chr++).'. '.$report['qo_options_value'][$i]['qo_options'].','; ?></div>
                <?php
                    }
                ?>
                </div>
        </div>
    
        </div>
        <div class="row darkborderrow">
            <div class="col-xs-12 col-sm-12">
                <div class="col-xs-4 col-sm-4 usnp"><span class="eval_blacktext">Correct Answers: 
                <?php 
                    if(isset($report['correct_answer'])){
                        echo $report['correct_answer'];
                    } 
                ?>
                </span></div>
                <div class="col-xs-4 col-sm-4 usnp"><span class="<?php echo ($report['correct'] == 1?'rightmarked':'wrongmarked'); ?>">Marked Answers: 
                <?php 
                    if(isset($report['user_answer'])){
                        echo $report['user_answer'];
                    } 
                ?>
                </span></div>
                <div class="col-xs-4 col-sm-4 deskright usnp"><span class="<?php echo ($report['correct'] == 1?'positivemark':'negativemark'); ?>">Mark <?php echo ($report['correct'] == 1?'+':'-');echo $report['q_positive_mark']; ?></span></div>
            </div>
        </div>
</div>   
    <?php
    }
    else if($report['q_type'] == 2){
    ?>
<div class="questionrow"> <!--start of question row-->
    <div class="row">
        <div class="col-xs-12 col-sm-12">
            <div class="col-xs-10 col-sm-10 spacer">
                <b>Question: <?php echo $cnt; ?></b> <span class="mainquestion">MULTIPLE CHOICE</span>
            </div>
            <div class="col-xs-2 col-sm-2 spacer">
                <i class="greenicon icon-ok pull-right"></i>
            </div>
            <div class="col-xs-12 col-sm-12 spacer"><?php echo $report['q_question']; ?></div>
                <div class="col-xs-12 col-sm-12 spacer">
                    <?php 
                    $chr  = 65; 
                    for($i =0; $i < count($report['qo_options_value']);$i = $i + 1) 
                    {
                    ?>
                    <div class="col-xs-6 col-sm-6 spacer usdfw"><?php echo chr($chr++).'. '.$report['qo_options_value'][$i]['qo_options'].','; ?></div>
                    <?php
                        }
                    ?>
                </div>
                            
                
            </div>
    
        </div>
        <div class="row darkborderrow">
            <div class="col-xs-12 col-sm-12">
                <div class="col-xs-4 col-sm-4 usnp"><span class="eval_blacktext">Correct Answers: <?php 
                    if(isset($report['correct_answer'])){
                        echo $report['correct_answer'];
                    } 
                ?></span></div>
                <div class="col-xs-4 col-sm-4 usnp"><span class="<?php echo ($report['correct'] == 1?'rightmarked':'wrongmarked'); ?>">Marked Answers: <?php 
                    if(isset($report['user_answer'])){
                        echo $report['user_answer'];
                    } 
                ?></span></div>
                <div class="col-xs-4 col-sm-4 deskright usnp"><span class="<?php echo ($report['correct'] == 1?'positivemark':'negativemark'); ?>">Mark <?php echo ($report['correct'] == 1?'+':'-');echo $report['q_positive_mark']; ?></span></div>
            </div>
        </div>
</div>   <!--end of question row-->
    <?php
    }
    else if($report['q_type'] == 3){
    ?>
<div class="questionrow"> <!--start of explanatory question row-->
    <div class="row">
        <div class="col-xs-12 col-sm-12">
            <div class="col-xs-12 col-sm-12 spacer">
                <b>Question: <?php echo $cnt; ?></b> <span class="mainquestion">EXPLANATORY</span>
            </div>
            <div class="col-xs-12 col-sm-12 spacer"><?php echo $report['q_question']; ?></div>
                <div class="col-xs-12 col-sm-12 spacer">
                    <div class="t_whitebox"><?php echo $report['ugar_answer']; ?></div>
                </div>
                            
                
            </div>
    
        </div>
        <div class="row darkborderrow">
            <div class="col-xs-12 col-sm-12">
                <div class="col-xs-4 col-sm-4 usnp"><span class="eval_blacktext"></span></div>
                <div class="col-xs-4 col-sm-4 usnp"><span class="rightmarked"></span></div>
                <div class="col-xs-4 col-sm-4 deskright usnp"><span class="positivemark">Mark <?php echo $report['ugar_mark']; ?></span></div>
        </div>
    </div>
</div>   <!--end of explanatory question row-->
    <?php
    }
    ?>
<?php
 $cnt++;
} 
 ?>
</div>