<?php
    $session  = $this->auth->get_current_user_session('user');
?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title><?php echo isset($title)?$title:config_item('site_name'); ?></title>
<link rel="icon" href="<?php echo base_url('favicon.png') ?>">
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
    <link rel="stylesheet" href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/discussion-customized.css">
    <link rel="stylesheet" href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/custom_beta.css">
    <link rel="stylesheet" href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/addon.css">
    <link href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/font-awesome.min.css" rel="stylesheet">
    <link href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/bootstrap-select.css" rel="stylesheet">
    <link href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/owl.carousel.css" rel="stylesheet">
    <link href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/flexslider.css" rel="stylesheet">
    <link href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/starability-all.min.css" rel="stylesheet">
    <link href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/toastr.min.css" rel="stylesheet">
    <link href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/bootstrap-multiselect.css" rel="stylesheet">
    <link href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/fontello.css" rel="stylesheet">
    <link href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/forum/css/chrome-css.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/bootstrap-tokenfield.css">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">

    <?php /* ?><link href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/bootstrap.css" rel="stylesheet">
    <link href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/font-awesome.min.css" rel="stylesheet">
    <link href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/bootstrap-select.css" rel="stylesheet">
    <link href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/owl.carousel.css" rel="stylesheet">
    <link href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/flexslider.css" rel="stylesheet">
    <link href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/starability-all.min.css" rel="stylesheet">
    <link href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/styles.css" rel="stylesheet"> <?php 
    <link href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/custom.css" rel="stylesheet">
    <link href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/toastr.min.css" rel="stylesheet">*/ ?>

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


<?php if(isset($page) && $page == 'coursedescription') { ?>

    <script src="https://apis.google.com/js/platform.js" async defer></script>
    <?php } ?>
    <?php $chat_details = get_support_chat(); ?>
    <?php //echo "<pre>";  print_r($chat_details); die;?>
    <?php if($chat_details['support_chat_script'] && $chat_details['support_chat_script']!="") { echo base64_decode($chat_details['support_chat_script']); } ?>

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
                $html.='<a class="dropdown-toggle" '.((!$url)?'data-toggle="dropdown"':'').'  href="'.$url.'" target="'.$target.'">'.$page['p_title'].'
                        <span class="menu-down">';
                if(!empty($page['children']))
                {
                    $html.='    <svg version="1.1" x="0px" y="0px" width="12px" height="10px" viewBox="0 0 21 17" enable-background="new 0 0 21 17" xml:space="preserve">
                                    <g>
                                        <path fill="#333333" d="M16.2,4.2L10.5,10L4.8,4.2L3,6l7.5,7.5L18,6L16.2,4.2z"></path>
                                        <path fill="none" d="M-4.5-6.5h30v30h-30V-6.5z"></path>
                                    </g>
                                </svg>';
                    
                }
                $html.='</span> 
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

<!--          <li><a href="#">Sign In</a></li>
          <li><a href="#">Register</a></li>-->
          <?php 
            $admin = $this->auth->get_current_user_session('admin');
            $faculty_management_link = '';
            if(!$admin)
            {
                $faculty_management_link    = '';
                $admin                      = $this->auth->get_current_user_session('teacher');
                if($admin)
                {
                    $faculty_management_link = '<li><a href="'.admin_url('profile').'">Profile</a></li>';
                    $teacher_session_object = $admin;
                }
                else
                {
                    $faculty_management_link = '<li><a href="'.admin_url('profile').'">Profile</a></li>';
                    $admin   = $this->auth->get_current_user_session('content_editor');
                }
            }
            else
            {
                $faculty_management_link = '<li><a href="'.admin_url('profile').'">Profile</a></li>';
                $faculty_management_link .= '<li><a href="'.admin_url('environment').'">'.lang('settings').'</a></li>';
                if($admin['id'] == 2 )
                {
                    $faculty_management_link .= '<li><a href="'.admin_url('faculties').'">'.lang('faculty_management').'</a></li>';                    
                }
            }
        ?>
                <li><a href="<?php echo site_url('logout') ?>"><img src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/images/logout.png" alt="img"><?php echo lang('logout');?></a></li>
              </ul>
            </li>          
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
                        <span class="menu-down">';
                if(!empty($page['children']))
                {
                $html.= '   <svg version="1.1" x="0px" y="0px" width="12px" height="10px" viewBox="0 0 21 17" enable-background="new 0 0 21 17" xml:space="preserve">
                                <g>
                                    <path fill="#ffffff" d="M16.2,4.2L10.5,10L4.8,4.2L3,6l7.5,7.5L18,6L16.2,4.2z"></path>
                                    <path fill="none" d="M-4.5-6.5h30v30h-30V-6.5z"></path>
                                </g>
                            </svg>';
                }
                $html.= '</span>';                 
                $html.='</a>';
            }
            else
            {
                if(!empty($page['children'])) { $GLOBALS['class_child'] = 'dropdown-submenu'; }

                if($GLOBALS['cat_variable']%2=='0')
                {
                    $html.='<li class="dropdown '.$GLOBALS['class_child'].'"><a class="dropdown-toggle" '.((!$url)?'data-toggle="dropdown"':'').' href="'.$url.'" target="'.$target.'">'.$page['p_title'].'</a>';
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
<?php $explicit_nav_class  = array('teachers', 'report', 'homepage'); ?>
<?php $explicit_nav_method = array('assesment_report_item', 'challenge_zone_report_item', 'user_generated_test_report_item'); ?>
<?php if( !in_array($this->router->fetch_method(), $explicit_nav_method) && !in_array($this->router->fetch_class(), $explicit_nav_class)) : ?>
    <?php if(isset($title) || !empty($category_pages)): ?>
    <section id="nav-group">
    <div class="nav-group">
        <div class="container">
            <div class="container-reduce-width">
                <h2 class="funda-head" id="category_heading"><?php echo isset($title)?$title:''; ?></h2>

                            <?php if(!empty($category_id)): ?>
                                <nav class="navbar main-nav">
                                            <?php 
                                                if(!empty($category_pages)){
                                                    echo category_tree($category_pages);
                                                }
                                            ?>
                                </nav> 
                            <?php endif; ?>


            </div>
        </div>
    </div>
    </section>
    <?php endif; ?>
<?php endif; ?>

<!-- <section> -->
<!-- Trigger the modal with a button -->
<!-- <button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#myModal">Open Modal</button> -->

<!-- Modal -->
<!-- <div id="myModal" class="modal fade ofabee-modal" role="dialog">
  <div class="modal-dialog"> -->

    <!-- Modal content-->

    <!-- <div class="modal-content ofabee-modal-content">
      <div class="modal-header ofabee-modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title ofabee-modal-title">Modal Header</h4>
      </div>
      <div class="modal-body ofabee-modal-body">
        <textarea class="ofabee-textarea" placeholder="Type your text here"></textarea>
      </div>
      <div class="modal-footer ofabee-modal-footer">
        <button type="button" class="btn ofabee-dark" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn ofabee-orange">Submit</button>
      </div>
    </div>

  </div>
</div>
</section> -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
