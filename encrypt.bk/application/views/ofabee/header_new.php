<?php
$session = (empty($session))?$this->auth->get_current_user_session('user'):$session;
$explicit_class_step = array('test');
$explicit_method_step = array('complete_profile');
if (in_array($this->router->fetch_method(), $explicit_method_step)) {
    
} else {
    if ($this->router->fetch_method() != 'step' && (isset($session['us_profile_completed']) && $session['us_profile_completed'] != 1) /* &&in_array($session['id'],$inclusion) */) {
        redirect('/dashboard/step/1');
    }
}
if (isset($this->session->userdata['hide_live'])) {
    $hide_live = $this->session->userdata['hide_live'];
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no">
    <meta name="name" content="content">
    <link rel="icon" href="<?php echo base_url('favicon.png'); ?>" type="image/x-icon"/>
    <link rel="shortcut icon" type="image/png" href="<?php echo base_url('favicon.png'); ?>"/>
    <?php
    if (!isset($meta_original_title)) 
    {
        $meta_original_title = config_item('site_name');
    }
    ?>
    <title><?php echo isset($title) ? $title : config_item('site_name'); ?></title>
    <meta name="title" content="<?php echo isset($meta_title) ? $meta_title : $meta_original_title; ?>">
    <meta name="description" content="<?php echo isset($meta_description) ? $meta_description : config_item('meta_description'); ?>">
    <?php
    $logo              = $this->config->item('site_logo');
    $logo              = ($logo == 'default.png') ? base_url('uploads/site/logo/default.png') : logo_path() . $logo;
    ?>
    <meta property="og:image" content="<?php echo $logo; ?>" />
    <meta property="og:image:width" content="400" />
    <meta property="og:image:height" content="300" />
    <link rel="stylesheet" href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme');?>/homepage/css/plyr.css">

    <?php
    /*
    <link rel="stylesheet" href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme');?>/homepage/css/style.css">
	<link rel="stylesheet" href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme');?>/homepage/css/bootstrap.min.css">
	<link rel="stylesheet" href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme');?>/homepage/css/owl.carousel.css">
    <link rel="stylesheet" href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme');?>/homepage/css/owl.theme.default.min.css">
    */
    ?>

    <link rel="stylesheet" href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme');?>/homepage/css/homepage.css">
    
    <?php
    /*
	<script src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme');?>/homepage/js/jquery.min.js"></script>
	<script src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme');?>/homepage/js/bootstrap.min.js"></script>
	<script src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme');?>/homepage/js/custom.js"></script>
	<script src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme');?>/homepage/js/owl.carousel.js"></script>
    */
    ?>
   	<script src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/homepage/js/homepage.js"></script>
       
    <script src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme');?>/homepage/js/plyr.js"></script>
    <script type="text/javascript">
        var base_url            = '<?php echo site_url('/'); ?>';
        var admin_url           = '<?php echo admin_url(); ?>';
        var assets_url          = '<?php echo assets_url(); ?>';
        var current_category    = "<?php echo isset($category_id) ? $category_id : 0; ?>";
        $(document).ready(function () {
            if (current_category != '0') {
                $('#category_heading').html($('#curr_category_' + current_category).text().charAt(0).toUpperCase() + $('#curr_category_' + current_category).text().slice(1));
            }
        });
        $(document).on('click', '#basic li', function () {
            var category_id         = $(this).attr('id');
            var category_slug       = $(this).attr('data-link');
            window.location.href    = base_url + category_slug;
        });

    </script>
</head>
<body>

	<!-- banner -->
	<section class="section-banner">
		<!-- navigation -->
		<nav class="navbar navbar-default">
	        <!-- Brand and toggle get grouped for better mobile display -->
	        <div class="navbar-header">
	            <button type="button" data-target="#navbarCollapse" data-toggle="collapse" class="navbar-toggle">
	                <span class="sr-only">Toggle navigation</span>
	                <span class="icon-bar"></span>
	                <span class="icon-bar"></span>
	                <span class="icon-bar"></span>
	            </button>
	           <a class="navbar-brand" href="<?php echo site_url(); ?>"><img src="<?php echo $logo; ?>" alt="SGlearningapp" class="img-responsive logo"></a>
	        </div>
	        <!-- Collection of nav links, forms, and other content for toggling -->
	        <div id="navbarCollapse" class="collapse navbar-collapse">
            <ul class="nav navbar-nav navbar-right top-menu">
                        <?php if (isset($session['id'])): ?>
                            <li class="menu-visible-xs avatar-mobile-view dropdown topmenu">
                                <a id="my_profile_name_header" href="<?php echo site_url('/dashboard/profile') ?>">
                                    <div style="display: flex;">
                                        <img id="my_profile_image_header" class="img-circle" src="<?php echo (($session['us_image'] == 'default.jpg') ? default_user_path() : user_path()) . $session['us_image'] ?>" width="40" height="40">
                                        <div class="mobile-profile-info">
                                            <span class="user-profile-name">Hi, <?php echo $session['us_name']; ?></span>
                                            <span class="wishes">Welcome Back</span>
                                        </div>
                                    </div>
                                    <span class="glyphicon glyphicon-menu-right profile-menu-arrow"></span>
                                </a>
                            </li>
                        <?php endif; ?>
                        <li>
                            <a href="<?php echo site_url('/course/listing'); ?>"><?php echo lang('explore_course') ?>
                            </a>
                        </li>
                        <?php if (isset($session['id'])) : ?>
                            <li>
                                <a href="<?php echo site_url('/dashboard'); ?>">Dashboard
                                </a>
                            </li>
                            <li   class="menu-visible-xs">
                                <a href="<?php echo site_url('dashboard/courses'); ?>">My Subscriptions
                                </a>
                            </li>
                            <li class="visible-xs"><a href="<?php echo site_url() . 'logout' ?>"><img class="logout-icon" src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/images/logout.png" alt="img"><?php echo lang('logout'); ?></a></li>
                            <?php if ($session): ?>
                                <?php
                                    $site_notification = array();
                                    $unseen_msg_count  = isset($site_notification['unseen']) ? sizeof($site_notification['unseen']) : 0;
                                ?>
                                <li  class="hidden-xs ">
                                    <a onclick="msgRedirect()">
                                    <img id="message_icon_image" style="border-radius: 0px;" src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/images/Notification_Icon_Inactive.png" width="22" <?php echo (($unseen_msg_count > 0) ? 'class="ml15"' : '') ?>><?php echo (($unseen_msg_count > 0) ? '<span class="badge-orange" id="site_message_count_wrapper"><span id="site_message_count">' . $unseen_msg_count . '</span></span>' : '') ?>
                                        <span class="badge-orange" id="message_count_wrapper" style="display:none;">
                                            <span id="message_count" style="display: inline-block;min-width: 15px;text-align: center;"></span>
                                        </span>
                                    </a>
                                </li>
                                <?php /*
                                <li onclick="getNotifications()" id="notification_main"  class="hidden-xs"><a href="javascript:void(0)"><img id="notification_icon_image" src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/images/Notification_Icon_Inactive.svg" width="36" height="36" class="ml15"><span class="badge-orange" id="site_notification_count_wrapper" style="display:none;"><span id="site_notification_count"></span></span></a>
                                    <ul style="display:none" id="notifications_ul" class="dropdown-menu top-submenu user-notifications">
                                        <li><h3>Notifications</h3></li>
                                        <li id="notifications_area">
                                            <ul class="my-notifications no-overflow">
                                                <div class="empty-notifications"><img src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/images/No_Notification_illustration.svg" width="100" height="100">
                                                <p>No notifications to show</p>
                                                </div>
                                            </ul>
                                        </li>
                                    </ul>          
                                </li>
                                <?php */ ?>
                            <?php endif; ?>
                            <li class="hidden-xs dropdown topmenu">
                                <a class="dropdown-toggle" data-toggle="dropdown" href="javascript:void(0)"><img id="my_profile_image_header" src="<?php echo (($session['us_image'] == 'default.jpg') ? default_user_path() : user_path()) . $session['us_image'] ?>" width="36" height="36">
                                    <span class="menu-down">
                                        <svg version="1.1" x="0px" y="0px" width="12px" height="10px" viewBox="0 0 21 17" enable-background="new 0 0 21 17" xml:space="preserve">
                                        <g>
                                        <path fill="#333333" d="M16.2,4.2L10.5,10L4.8,4.2L3,6l7.5,7.5L18,6L16.2,4.2z"></path>
                                        <path fill="none" d="M-4.5-6.5h30v30h-30V-6.5z"></path>
                                        </g>
                                        </svg>
                                    </span>
                                </a>
                                <ul class="dropdown-menu top-submenu user-submenu profile-dropdown">
                                    <li><a id="my_profile_name_header" href="<?php echo site_url('/dashboard/profile') ?>">
                                        <span class="user-profile-name"><?php echo $session['us_name']; ?></span></a></li>
                                    <li><a  id="logout" href="<?php echo site_url() . 'logout' ?>">
                                        <?php echo lang('logout'); ?></a></li>
                                </ul>
                            </li>
                        <?php else: ?>
                            <li><a id='signin' href="<?php echo site_url('/login'); ?>"><?php echo lang('sign_in'); ?></a></li>
                            <li><a id='signup' href="<?php echo site_url('/register'); ?>"><?php echo lang('sign_up'); ?></a></li>
                        <?php endif; ?>
                    </ul>  
	        </div>
	    </nav>
        
        <div class="banner-wrapper">
            <?php if(isset($banner) && $banner!="") { ?>
            <img class="sdpk-banner" src="<?php echo banner_path().$banner ?>" alt="SGlearningapp">
            <?php } ?>

            <div class="play-btn" onclick="playVideo()">
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" viewBox="0 0 58 58" style="enable-background:new 0 0 58 58;" xml:space="preserve" width="512px" height="512px" class="" style="width: 50px;
                height: auto;"><g><circle style="fill:#FF3F00" cx="29" cy="29" r="29" data-original="#EBBA16" class="active-path" data-old_color="#FF4700"/><g>
                <polygon style="fill:#F9F5F5" points="44,29 22,44 22,29.273 22,14 " data-original="#FFFFFF" class="" data-old_color="#F4EAEA"/>
                <path style="fill:#F9F5F5" d="M22,45c-0.16,0-0.321-0.038-0.467-0.116C21.205,44.711,21,44.371,21,44V14 c0-0.371,0.205-0.711,0.533-0.884c0.328-0.174,0.724-0.15,1.031,0.058l22,15C44.836,28.36,45,28.669,45,29s-0.164,0.64-0.437,0.826 l-22,15C22.394,44.941,22.197,45,22,45z M23,15.893v26.215L42.225,29L23,15.893z" data-original="#FFFFFF" class="" data-old_color="#F4EAEA"/>
                </g></g></svg>
            </div>
        </div>


	</section>
	<!-- banner section ends -->
