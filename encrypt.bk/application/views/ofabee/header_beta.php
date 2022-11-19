<?php
    $session  = $this->auth->get_current_user_session('user');
?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title><?php echo isset($title)?$title:config_item('site_name'); ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1">
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
<link rel="stylesheet" href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/bootstrap.min.css">
<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
<link href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/fontello.css" rel="stylesheet">
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


    <?php /* ?><link href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/bootstrap.css" rel="stylesheet">
    <link href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/font-awesome.min.css" rel="stylesheet">
    <link href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/bootstrap-select.css" rel="stylesheet">
    <link href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/owl.carousel.css" rel="stylesheet">
    <link href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/flexslider.css" rel="stylesheet">
    <link href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/starability-all.min.css" rel="stylesheet">
    <link href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/styles.css" rel="stylesheet"> <?php 
    <link href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/custom.css" rel="stylesheet">
    <link href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/toastr.min.css" rel="stylesheet">*/ ?>
<?php $login_session = $this->auth->get_current_user_session('user'); 
    if($login_session['us_role_id'] == '5'){
        $parent_dashboard = 'Parent';
    }
?>
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

<script src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/js/jquery.min.js"></script>

<script type="text/javascript">

    var base_url         = '<?php echo site_url('/'); ?>';
    var admin_url        = '<?php echo admin_url();?>';
    var current_category = "<?php if(isset($category_id)) {  echo $category_id; } else { echo '0'; }?>";
    var parent_dashboard = '<?php echo $parent_dashboard; ?>';
    var parent_keyword   = (parent_dashboard != '')?parent_dashboard:'';
    //$('#cat_text').html('');
    $(document).ready(function(){
        if(current_category!='0'){
            $('#category_heading').html($('#curr_category_'+current_category).text().charAt(0).toUpperCase() + $('#curr_category_'+current_category).text().slice(1)); 
            $('#cat_text').contents().first()[0].textContent = $('#curr_category_'+current_category).text().charAt(0).toUpperCase() + $('#curr_category_'+current_category).text().slice(1);
        }
    });
    $(document).on('click', '#basic li', function(){
        var category_id      = $(this).attr('id');
        var category_slug    = $(this).attr('data-link');
        window.location.href = base_url+""+category_slug;
    });

</script>
<?php if(isset($page) && $page == 'coursedescription') { ?>

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

<?php //$header_categories = get_header_categories();

    if(!empty($category_id)){
            $category_pages = get_category_pages($category_id,0);
    }

   // $header_links = get_header_links(0);

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
    $GLOBALS['variable'] = '0';

    function header_tree($page_tree, $is_child = false)
    {
        $html = '';
        foreach($page_tree as $page) {
            $active = (1==$page['p_slug'])?'active':'';
            $url = (($page['p_goto_external_url'] == '1'))?$page['p_external_url']:site_url().'/'.$page['p_slug'];
            $target = ($page['p_new_window'] == '1')?'_blank':'';

            if($GLOBALS['variable']=='0')
            {
                $html.='<a class="dropdown-toggle" data-toggle="dropdown" href="'.$url.'" target="'.$target.'">'.$page['p_title'].'
                        <span class="menu-down">
                            <svg version="1.1" x="0px" y="0px" width="12px" height="10px" viewBox="0 0 21 17" enable-background="new 0 0 21 17" xml:space="preserve">
                                <g>
                                    <path fill="#333333" d="M16.2,4.2L10.5,10L4.8,4.2L3,6l7.5,7.5L18,6L16.2,4.2z"></path>
                                    <path fill="none" d="M-4.5-6.5h30v30h-30V-6.5z"></path>
                                </g>
                            </svg>
                        </span> 
                        </a>';
            }
            else
            {
                
                $html.= '<li><a class="'.$active.'" href="'.$url.'" target="'.$target.'">';
                $html.=$page['p_title'];
                $html.= '</a></li>';

            }

            $GLOBALS['variable'] = 1; 
            if(!empty($page['children']))
            {
                $html.= '<ul class="dropdown-menu top-submenu">';
                $html.= header_tree($page['children'], true);
                $html.= '</ul>';
            }
        }       
        return $html;
    }
    
?>

<?php
/*
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

<?php } 
*/
?>

<nav class="navbar navbar-default white-bg">
  <div class="container">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>                        
      </button>    
        <a href="<?php echo site_url(); ?>"><img src="<?php echo base_url().logo_path().$this->config->item('site_logo') ?>" class="img-responsive ptb10" width="180"></a>
    </div>
    <div class="collapse navbar-collapse" id="myNavbar">
        <ul class="nav navbar-nav navbar-right top-menu">
        
        <li class="dropdown active-topmenu topmenu" data-link="">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#" id="cat_text"><?php echo lang('categories') ?>
            <span class="menu-down">
                <svg version="1.1" x="0px" y="0px" width="12px" height="10px" viewBox="0 0 21 17" enable-background="new 0 0 21 17" xml:space="preserve">
                    <g>
                        <path fill="#F58700" d="M16.2,4.2L10.5,10L4.8,4.2L3,6l7.5,7.5L18,6L16.2,4.2z"></path>
                        <path fill="none" d="M-4.5-6.5h30v30h-30V-6.5z"></path>
                    </g>
                </svg>
            </span> 
            </a>
            <ul class="dropdown-menu top-submenu" id="basic">
                <?php if(!empty($header_categories)): ?>
                            <?php foreach ($header_categories as $category) : 
                                $selected = ($category['id']==(isset($category_id)?$category_id:'0'))?$category['ct_name']:'';
                            ?>
                            <li data-link="<?php echo $category['ct_slug']?>" id="<?php echo $category['id'] ?>"><a href="#" id="curr_category_<?php echo $category['id'] ?>"><?php echo $category['ct_name'] ?></a></li>
                            <?php endforeach; ?>
                <?php endif; ?>
            </ul>
          </li>

        <li class="dropdown topmenu">
            <?php //echo header_tree($header_links); ?>
        </li>

<!--          <li><a href="#">Sign In</a></li>
          <li><a href="#">Register</a></li>-->
          <?php if(isset($session['id'])){ ?>
            <li>
                <a href="<?php echo site_url('/dashboard'); ?>"><?php echo lang('my_dashboard') ?>
                </a>
            </li>

            <li class="hidden-xs dropdown topmenu"><a class="dropdown-toggle" data-toggle="dropdown" href="#"><img src="<?php echo (($session['us_image'] == 'default.jpg')?default_user_path():user_path()).$session['us_image'] ?>" width="36" height="36">
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
                  <li><a href="<?php echo site_url('/dashboard/#my_profile') ?>"><img src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/images/acnt.png" alt="img"><?php echo $session['us_name']; ?></a></li>
                  <li><a href="<?php echo site_url().'/'.'logout'?>"><img src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/images/logout.png" alt="img"><?php echo lang('logout');?></a></li>
              </ul>
            </li>          
          <?php } else { ?>
                    <li><a id='signin' href="<?php echo site_url('/login'); ?>"><?php echo lang('sign_in');?></a></li>
                    <li><a id='signup' href="<?php echo site_url('/register'); ?>"><?php echo lang('sign_up');?></a></li>
          <?php } ?>
        </ul>
     </div>   
  </div>
</nav>
<?php 
    $GLOBALS['category_tree_class'] = "nav navbar-nav category-nav";

    $GLOBALS['category_li_class']   = "dropdown";

    $GLOBALS['cat_variable']        = "0";

    $GLOBALS['class_child']         = '';

    function category_tree($page_tree)
    {
        $html = '<ul class="'.$GLOBALS['category_tree_class'].'">';
        
        $GLOBALS['category_tree_class'] = 'dropdown-menu sub-category';

        foreach($page_tree as $page) {

            $active = (1==$page['p_slug'])?'active':'';
            $url    = (($page['p_goto_external_url'] == '1'))?$page['p_external_url']:site_url().'/'.$page['p_slug'];
            $target = ($page['p_new_window'] == '1')?'_blank':'';

            if($page['p_parent_id']=='0')
            {
                //$GLOBALS['category_tree_class'] = "nav navbar-nav category-nav";

                $GLOBALS['category_li_class']   = "dropdown";

                $html.= '<li class="'.$GLOBALS['category_li_class'].'">';


                $html.= '<a href="'.$url.'" target="'.$target.'">'.$page['p_title'].' 
                        <span class="menu-down">
                            <svg version="1.1" x="0px" y="0px" width="12px" height="10px" viewBox="0 0 21 17" enable-background="new 0 0 21 17" xml:space="preserve">
                                <g>
                                    <path fill="#ffffff" d="M16.2,4.2L10.5,10L4.8,4.2L3,6l7.5,7.5L18,6L16.2,4.2z"></path>
                                    <path fill="none" d="M-4.5-6.5h30v30h-30V-6.5z"></path>
                                </g>
                            </svg>
                        </span>';                 
                 $html.='</a>';
            }
            else
            {
                if(!empty($page['children'])) { $GLOBALS['class_child'] = 'dropdown-submenu'; }

                if($GLOBALS['cat_variable']%2=='0')
                {
                    $html.='<li class="dropdown '.$GLOBALS['class_child'].'"><a class="dropdown-toggle" data-toggle="dropdown" href="'.$url.'" target="'.$target.'">'.$page['p_title'].'</a>';
                    $GLOBALS['class_child'] = '';
                }
                else
                {
                    $html.='<li class="kopie"><a href="'.$url.'" target="'.$target.'">'.$page['p_title'].'</a>';
                }
            }

            if(!empty($page['children']))
            {
                $GLOBALS['category_tree_class'] = 'dropdown-menu sub-category';
                $html.= category_tree($page['children']);
            }
            $html.= '</li>';  
        }
        $GLOBALS['cat_variable']++;
        $html.= '</ul>';
        return $html;
    }
    
?>
<?php
$avoid_nav_pages = array('teacher', 'index', 'material', 'assesment_report_item', 'report');
?>
<?php if(!in_array($this->router->fetch_class(), $avoid_nav_pages)): ?>
<?php //if(( ($this->router->fetch_class()!='teacher') && ($this->router->fetch_method()!='index') ) && ( ($this->router->fetch_class()!='material') && ($this->router->fetch_method()!='assesment_report_item') )) : ?>
<section id="nav-group">
<div class="nav-group">
    <div class="container">
        <div class="container-reduce-width">
            <h2 class="funda-head" id="category_heading"></h2>

                <nav class="navbar main-nav">
                        <?php if(!empty($category_id)): ?>

                            <?php 
                                if(!empty($category_pages)){
                                    echo category_tree($category_pages);
                                }
                            ?>

                        <?php endif; ?>
                </nav> 
        </div>
    </div>
</div>
</section>

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
