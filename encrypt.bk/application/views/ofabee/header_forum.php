<?php
    $session           = $this->auth->get_current_user_session('user');
?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title><?php echo isset($title)?$title:config_item('site_name'); ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
 <?php if(!isset($meta_original_title)){ $meta_original_title = config_item('site_name');}?>
    <meta name="title" content="<?php echo isset($meta_title)?$meta_title:$meta_original_title; ?>">
    <meta name="description" content="<?php echo isset($meta_description)?$meta_description:config_item('meta_description'); ?>">
    <?php if(isset($page) && $page == 'coursedescription'){?>
    <meta property="og:url"           content="<?php echo $current_url; ?>" />
    <meta property="og:type"          content="website" />
    <meta property="og:title"         content="<?php echo $meta_original_title; ?>" />
    <meta property="og:description"   content="<?php echo $meta_description; ?>" />
    <meta property="og:image"         content="" />
  <?php }?>
    <link rel="icon" href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/images/favicon.ico">
    <title><?php echo isset($title)?$title:config_item('site_name'); ?></title>
    <style>
    .fixed {
      position: fixed;
      top:0; left:0;
      width: 100%; 
      z-index:10;
      background: rgba(46, 51, 56, 0.2)
    }
    .absolute{
        width: 100%;
        position: absolute;
        z-index:10;
        background: rgba(46, 51, 56, 0.2)
    }
    .sticky p {
        margin-top: 3px;
        font-size: 16px;
        color: #fe8000;
        text-align: center;
        font-family: 'Open Sans', sans-serif;
    }
    .go-live{
        background: #fe8000 none repeat scroll 0 0;
        border: 1px solid #fe8000;
        border-radius: 4px;
        color: #fff;
        padding: 2px 5px;
        cursor: pointer;
        font-size:15px;
    }
    </style>
<link rel="stylesheet" href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/bootstrap.css">
<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
<link rel="stylesheet" href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/custom_beta.css">
<link rel="stylesheet" href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/addon.css">
<?php if(isset($forum_style)){ ?>
  <link href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/forum/css/chrome-css.css" rel="stylesheet">
  <link href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/forum/css/fontello.css" rel="stylesheet">
  <link href="<?php echo assets_url() ?>css/redactor/css/redactor.css" rel="stylesheet">
  <link href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/forum/css/custom.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
  <script src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/forum/js/prefixfree.min.js"></script>
<?php } ?>
<script src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/js/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/js/underscore.js"></script>
<script src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/forum/js/bootstrap.min.js"></script>
    <link href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/bootstrap-multiselect.css" rel="stylesheet">
    <script src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/js/bootstrap-multiselect.js"></script>
    <script type="text/javascript">
        var base_url = '<?php echo site_url('/'); ?>';
        var admin_url='<?php echo admin_url();?>';
        $(document).on('change', '#basic', function(){
            var category_id   = $(this).find(':selected').val();
            var category_slug = $(this).find(':selected').data('link');
            
            window.location.href = base_url+""+category_slug;
        });
    </script>
    <?php if(isset($page) && $page == 'coursedescription'){?>
        <script src="https://apis.google.com/js/platform.js" async defer></script>
    <?php } ?>
    <?php $chat_details = get_support_chat(); ?>
    <?php //echo "<pre>";  print_r($chat_details); die;?>
    
    <?php if($chat_details['support_chat_script'] && $chat_details['support_chat_script']!="") { echo base64_decode($chat_details['support_chat_script']); } ?>

<?php if(isset($forum_style)){ ?>
  <script src="<?php echo assets_url() ?>js/redactor/js/redactor.js"></script>
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
    
    //echo "<pre>";print_r($header_categories);
    /*if(!empty($category_content)){
        $category_id; 
        $category_pages = get_category_pages($category_id);    
    }*/
    if(!empty($category_id)){
        $category_pages = get_category_pages($category_id,0);

        //echo "<pre>";print_r($category_pages);    
    }
    
    $header_links = get_header_links(0);
    
    $challenge_zone = array();
    if(isset($category_id)){
        if($category_id){
            $challenge_zone = get_challenge_zone($category_id);
        }
    }
    ?>
    
    <?php 
    
    $login_status_class = isset($session['id'])?'float_l':'';
    $GLOBALS['header_tree_class'] = "nav navbar-nav header-menu-tree $login_status_class";
    $GLOBALS['current_user_id'] = $session['id'];
    
    /*$GLOBALS['common_header_buttons'] = "<li><a id='signin' href='".site_url('/login')."'>".lang('sign_in')."</a></li>
                                    <li><a id='signup' href='".site_url('/register')."'>".lang('sign_up')."</a></li>";*/
    function header_tree($page_tree, $is_child = false)
    {
        
        $html = '<ul class="'.$GLOBALS['header_tree_class'].'">';
        $GLOBALS['header_tree_class'] = '';
        //$html = '<li class="offer_course_category"><a href="'.$_SESSION['course']['course_slug'].'">'.$_SESSION['course']['course_name'].'</a></li>';
        //echo "<pre>"; print_r($page_tree); die;
        foreach($page_tree as $page) {
            $active = (1==$page['p_slug'])?'active':'';
            $url = (($page['p_goto_external_url'] == '1'))?$page['p_external_url']:site_url().'/'.$page['p_slug'];
            $target = ($page['p_new_window'] == '1')?'_blank':'';
            $html.= '<li><a class="'.$active.'" href="'.$url.'" target="'.$target.'">';
            $html.=$page['p_title'];
            $html.= '</a>';
            if(!empty($page['children']))
            {
                $html.= header_tree($page['children'], true);
            }
            $html.= '</li>'; 
        }
        /*if(!isset($GLOBALS['current_user_id'])){
            if(!$is_child){
                $html.= $GLOBALS['common_header_buttons'];
            }
        }*/
        
        $html.= '</ul>';        
        return $html;
    }
    
    ?>
    
    <?php
        $data_lectures    = get_online_lectures();
        if($data_lectures && $this->router->fetch_class()!='live') {    ?>
            <div class="sticky absolute">
                <?php 
                    foreach($data_lectures as $data_lecture)
                    { ?>
                        <div class="container">
                            <p>LIVE CLASS FOR <?php echo strtoupper($data_lecture['cl_lecture_name']);?> IS GOING ON <a class="go-live" href="<?php echo site_url('/live') ?>">JOIN LIVE</a></p>
                        </div>
               <?php } ?>
            </div>
    <?php } ?>


<nav class="navbar white-bg">
  <div class="container">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>                        
      </button>    
      <img src="<?php echo base_url().logo_path().$this->config->item('site_logo') ?>" class="img-responsive ptb10" width="180">
    </div>
	<div class="collapse navbar-collapse" id="myNavbar">
        <ul class="nav navbar-nav navbar-right top-menu">
        
                <li class="dropdown active-topmenu topmenu">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#">Categories
            <span class="menu-down">
                <svg version="1.1" x="0px" y="0px" width="12px" height="10px" viewBox="0 0 21 17" enable-background="new 0 0 21 17" xml:space="preserve">
                    <g>
                        <path fill="#F58700" d="M16.2,4.2L10.5,10L4.8,4.2L3,6l7.5,7.5L18,6L16.2,4.2z"></path>
                        <path fill="none" d="M-4.5-6.5h30v30h-30V-6.5z"></path>
                    </g>
                </svg>
            </span> 
            </a>
            <ul class="dropdown-menu top-submenu">
              <li><a href="#">Page 1-1</a></li>
              <li><a href="#">Page 1-2</a></li>
              <li><a href="#">Page 1-3</a></li>
            </ul>
          </li>
        
        <li class="dropdown topmenu">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#">Current Affairs
            <span class="menu-down">
                <svg version="1.1" x="0px" y="0px" width="12px" height="10px" viewBox="0 0 21 17" enable-background="new 0 0 21 17" xml:space="preserve">
                    <g>
                        <path fill="#333333" d="M16.2,4.2L10.5,10L4.8,4.2L3,6l7.5,7.5L18,6L16.2,4.2z"></path>
                        <path fill="none" d="M-4.5-6.5h30v30h-30V-6.5z"></path>
                    </g>
                </svg>
            </span> 
            </a>
            <ul class="dropdown-menu top-submenu">
              <li><a href="#">Page 1-1</a></li>
              <li><a href="#">Page 1-2</a></li>
              <li><a href="#">Page 1-3</a></li>
            </ul>
          </li>
<!--          <li><a href="#">Sign In</a></li>
          <li><a href="#">Register</a></li>-->
          <li><a href="#">My Dashboard</a></li>
          <li class="hidden-xs"><a href="#"><img src="<?php echo (($session['us_image'] == 'default.jpg')?default_user_path():user_path()).$session['us_image'] ?>" width="36" height="36"></a></li>  
        </ul>

     </div>   
  </div>
</nav>
<?php 
    $GLOBALS['category_tree_class'] = "category_tree";

    function category_tree($page_tree)
    {
       // $i=0;
       // $text = ($i==0)?'test':'';
        $html = '<ul class="'.$GLOBALS['category_tree_class'].'">';
        $GLOBALS['category_tree_class'] = '';
        //$html = '<li class="offer_course_category"><a href="'.$_SESSION['course']['course_slug'].'">'.$_SESSION['course']['course_name'].'</a></li>';
        foreach($page_tree as $page) {
            $active = (1==$page['p_slug'])?'active':'';
            $url = (($page['p_goto_external_url'] == '1'))?$page['p_external_url']:site_url().'/'.$page['p_slug'];
            $target = ($page['p_new_window'] == '1')?'_blank':'';
            $html.= '<li class="new"><a class="'.$active.'" href="'.$url.'" target="'.$target.'">';
            $html.=$page['p_title'];
            $html.= '</a>';
            if(!empty($page['children']))
            {
                $html.= category_tree($page['children']);
            }
            $html.= '</li>';
            
        }
        $html.= '</ul>';
        return $html;
    }
    
    ?>
    <?php if(!empty($category_id)): ?>
    <!--- linkbar ------>
    <div class="linkbar">
        <div class="container sub_menus">
            <?php 
                if(!empty($category_pages)){
                    echo category_tree($category_pages);
                }
            ?>
            <?php /*?><ul class="sub_menus">
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
            </ul><?php */?>
        </div>
    </div>
    <!--- linkbar end------>
    <?php endif; ?>
<script>
$(window).scroll(function(){
  var sticky = $('.sticky'),
      scroll = $(window).scrollTop();

  if (scroll >= 1) {
    sticky.removeClass('absolute');
    sticky.addClass('fixed');
    }
  else {
    sticky.removeClass('fixed');
    sticky.addClass('absolute');
    }
});

$(document).ready(function(){
    function checkCookieStatus(){
        var x = readCookie('surveycookie');
        var y = readCookie('surveytakencookie');
        //console.log(x+'//'+y);
        //console.log(today+'//'+start_date+'//'+end_date+'//'+this_controller);
        if(today >= start_date && today <= end_date && this_controller!='survey') {

            if (!x && !y) {
                setTimeout(function(){  var surveyModal = $("#survey_modal").modal({backdrop: "static", keyboard: false});  }, 1000);
            }
        }
    }

    setInterval(function(){ checkCookieStatus(); }, 20000);
});
</script>