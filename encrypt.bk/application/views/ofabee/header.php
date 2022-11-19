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
$chat_details = get_support_chat();
$support_chat_enabled = isset($chat_details['support_chat_script']) && $chat_details['support_chat_script'] != "";
$information_bars     = information_bar_data();
/*$current_page         = parse_url($_SERVER['REQUEST_URI'])['path'];
if($current_page == '/login' || $current_page == '/register')
{
    $information_bars = '';
}
else
{
    $information_bars     = information_bar_data();
}*/
?>
<!DOCTYPE HTML>
<html>
    <head>
        
        <meta charset="utf-8">
        <title><?php echo isset($title) ? $title : config_item('site_name'); ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
        <?php

            if(file_exists(favicon_upload_path().'/favicon.png'))
            {
                $favicon = base_url(favicon_upload_path().'favicon.png?v='.rand());
            }
            else
            {
                $favicon = base_url('favicon.png').'?v='.rand();
            }
        ?>
        <link rel="icon" href="<?php echo $favicon;  ?>" type="image/x-icon"/>
        <link rel="shortcut icon" type="image/png" href="<?php echo $favicon; ?>"/>
        <?php
        if (!isset($meta_original_title)) 
        {
            $meta_original_title = config_item('site_name');
        }
        ?>
        <meta name="title" content="<?php echo isset($meta_title) ? $meta_title : $meta_original_title; ?>">
        <meta name="description" content="<?php echo isset($meta_description) ? $meta_description : config_item('meta_description'); ?>">
        <?php
        $logo              = $this->config->item('site_logo');
        $logo              = ($logo == 'default.png') ? base_url('uploads/site/logo/default.png') : logo_path() . $logo;
        ?>
        <meta property="og:image" content="<?php echo $logo; ?>" />
        <meta property="og:image:width" content="400" />
        <meta property="og:image:height" content="300" />
        
        <?php /* ?>
        <link rel="apple-touch-icon" sizes="180x180" href="<?php echo assets_url() ?>pwa/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="<?php echo assets_url() ?>pwa/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="<?php echo assets_url() ?>pwa/favicon-16x16.png">
        <link rel="manifest" href="<?php echo assets_url() ?>pwa/manifest.json">
        <link rel="mask-icon" href="<?php echo assets_url() ?>pwa/safari-pinned-tab.png" color="#262255">
        <meta name="theme-color" content="#262255">
        <?php */ ?>

        <?php ?>
        <!-- <link href="<?php // echo assets_url() ?>themes/<?php // echo $this->config->item('theme') ?>/css/bootstrap.min.css" rel="stylesheet">
        <link href="<?php // echo assets_url() ?>themes/<?php // echo $this->config->item('theme') ?>/css/opensans.css" rel="stylesheet">
        <link href="<?php // echo assets_url() ?>themes/<?php // echo $this->config->item('theme') ?>/css/custom_beta.css" rel="stylesheet">
        <link href="<?php // echo assets_url() ?>themes/<?php // echo $this->config->item('theme') ?>/css/addon.css" rel="stylesheet">
        <link href="<?php // echo assets_url() ?>themes/<?php // echo $this->config->item('theme') ?>/css/font-awesome.min.css" rel="stylesheet">
        <link href="<?php // echo assets_url() ?>themes/<?php // echo $this->config->item('theme') ?>/css/fontello.css" rel="stylesheet">
        <link href="<?php // echo assets_url() ?>themes/<?php // echo $this->config->item('theme') ?>/forum/css/chrome-css.css" rel="stylesheet"> -->

        <?php ?>
        
        <?php  ?>
        <link href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/sdpk.css<?php echo config_item('code_version')?>" rel="stylesheet">
        <?php  ?>

        <style>
            @media (max-width: 768px){ 
                .nav-header-holder .container{padding-right: 15px !important;padding-left: 15px !important;}
            }
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
            .count_info-xs{
                margin: 0 10px;
                background: #e77a14;
                width: auto;
                height: 20px;
                display: inline-block;
                border-radius: 5px;
                padding: 0 4px;
                color: #fff;
                font-size: 13px;
                text-align: center;
                line-height: 20px;
            }
            .top-notification-slider .item{
              
                width: calc(100% - 60px);
                opacity: 1;
                transition: .6s ease opacity;
                word-break: break-word;
            }
            .top-notification-slider .item p {
                margin: 0!important;
                color: #fff;
                font-size: 14px;
                font-weight: 500;
            }
            .preview_purpose{
                word-break: break-word;
            }
        </style>
        
        <!-- <script src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/js/jquery.min.js<?php echo config_item('code_version')?>"></script>
        <script src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/js/course_card.j<?php echo config_item('code_version')?>s"></script>
        <script src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/js/bootstrap.min.js<?php echo config_item('code_version')?>"></script>
        <script src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/js/ie10-viewport-bug-workaround.js<?php echo config_item('code_version')?>"></script> -->
        

        <script src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/js/scripts/sdpk.js<?php echo config_item('code_version')?>"></script>
        <script type="text/javascript">
            var base_url            = '<?php echo site_url('/'); ?>';
            var admin_url           = '<?php echo admin_url(); ?>';
            var assets_url          = '<?php echo assets_url(); ?>';
            var current_category    = "<?php echo isset($category_id) ? $category_id : 0; ?>";
            var notificatinBarType  = '1';
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
    <script> var showHidInformationBar = false;  </script>
    <!-- Top notitfication -->
    <?php $email_notification_status = "inactive";
    $email_notification_style = "display:none";
    if( !empty($_SESSION['user']) && $_SESSION['user']['us_role_id'] == '2' && !empty($session['us_email']) )
    {
        $email_notification_status = ($_SESSION['user']['us_email_verified'] == '0' ) ? "active" : "inactive";
        $email_notification_style = ($_SESSION['user']['us_email_verified'] == '0' ) ? "display:block" : "display:none";
       ?>
       <input type="hidden" id="check_email_verification_bar" value="<?php echo $_SESSION['user']['us_email_verified'] == '0' ? "1" : "0" ;  ?>">
       <?php
    } 
    ?>
    <!-- Top navigation bar -->
    <!-- < ?php if(!empty($_SESSION['user']) && $_SESSION['user']['us_email_verified'] == '0'){ ?> -->
    <div id="information_bar_verify" style="<?php echo $email_notification_style; ?>">
        <div class="carousel-inner top-notification-slider text-center" style="display: flex;">
            <div class="item  <?php echo $email_notification_status; ?>">
                <p class="notification_test">Hey! it seems you haven't verified you email Id​. <a href="javascript:void(0)" id="verify_user" style="color:red" >Click here </a> to get verification mail.</p> 
            </div>
        </div>
    </div>
    <!-- < ?php }else{ ?> -->
    <?php  if(isset($information_bars) ): ?> 
        <div id="information_bar" class="carousel slide" data-ride="carousel" style="display:none">
            <!-- Wrapper for slides -->
            <div class="carousel-inner top-notification-slider text-center">
                <?php $actives = 'active'; $bar_count = 0; foreach($information_bars as $information_bar): if($information_bar['n_notification_bar_type'] == 2) :?>
                <div class="item  <?php echo $actives; $actives = ''; $bar_count++; ?>">
                    <?php echo $information_bar['n_content']; ?> 
                </div>
               
                <?php  endif; endforeach;?>
                <span class="close">×</span>
            </div>
        </div>
        <script> showHidInformationBar = ('<?php echo $bar_count; ?>' > '0')?true:false;  </script>
    <?php endif; ?> 

    
        <!-- Top notitfication ends -->
        <?php if(!isset($_GET['privacy'])): ?>
        <nav class="navbar white-bg nav-header-holder">
            <div class="container container-altr">
                <div class="nav-bg-overlay"></div>
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a href="<?php echo site_url(); ?>" class="logo-holder"><img src="<?php echo $logo."?v=".rand(); ?>" class="logo-image-header"></a>
                </div>
                <div class="collapse navbar-collapse" id="myNavbar">
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
                        <?php else: ?>
                            
                            <li><a id='signin' href="<?php echo site_url('/login'); ?>"><?php echo lang('sign_in'); ?></a></li>
                            <li><a id='signup' href="<?php echo site_url('/register'); ?>"><?php echo lang('sign_up'); ?></a></li>
                        <?php endif; ?>

                        <!-- category menu -->
                        <?php $page_categories = page_categories(); ?>
                        <?php if(isset($page_categories)): //print_r($page_categories);?>
                            <li class="dropdown topmenu">
                                <a class="dropdown-toggle" data-toggle="dropdown" href="javascript:void(0)">
                                    Categories
                                    <span class="menu-down">
                                        <svg version="1.1" x="0px" y="0px" width="12px" height="10px" viewBox="0 0 21 17" enable-background="new 0 0 21 17" xml:space="preserve">
                                        <g>
                                        <path fill="#333333" d="M16.2,4.2L10.5,10L4.8,4.2L3,6l7.5,7.5L18,6L16.2,4.2z"></path>
                                        <path fill="none" d="M-4.5-6.5h30v30h-30V-6.5z"></path>
                                        </g>
                                        </svg>
                                    </span>
                                </a>
                                <ul class="dropdown-menu top-submenu user-submenu category_menu_dropdown">
                                    <?php foreach($page_categories as $category): ?>
                                        <li class="<?php echo (isset($_GET['categoryids']) && $category['id'] == $_GET['categoryids']) ? 'active-category' : ''; ?>"><a href="<?php echo site_url('course/listing?categoryids='.$category['id']);?>"><?php echo ucfirst($category['ct_name']);?></a></li>
                                    <?php endforeach; ?>
                                </ul>
                            </li>
                        <?php endif;?>
                        <!-- category menu ends -->

                        <li>
                            <a href="<?php echo site_url('/course/listing'); ?>"><?php echo lang('explore_course') ?>
                            </a>
                        </li>
                        
                        <?php if (isset($session['id'])) : ?>
                            <li>
                                <a href="<?php echo site_url('/dashboard'); ?>">Dashboard
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo site_url('dashboard/courses'); ?>">My Subscriptions
                                </a>
                            </li>
                            <!--<li class="menu-visible-xs">
                                <a href="<?php //echo site_url('dashboard/my_bundles'); ?>">My Bundles</a>
                            </li>-->
                            <li class="menu-visible-xs">
                                <a onclick="notificationRedirect()">
                                    <span>Notifications</span>
                                    <span class="count_info-xs" id="mobilenotify" style="display:none"></span>
                                </a>
                            </li>
                            <li class="menu-visible-xs">
                                <a onclick="msgRedirect()">
                                    <span>Messages</span>
                                    <span class="count_info-xs" id="mobilemessage" style="display:none"></span>
                                </a>
                            </li>
                        <?php endif;?>


                        <?php $header_pages = menu_pages(array('type' => 'header')); $area_selected = 'aria-selected="true"';?>
                            <!--mobile page menu starts-->
                            <?php if(isset($header_pages['parent']) && $header_pages['parent']){ ?>
                            <?php foreach($header_pages['parent'] as $parent): ?>  
                            <?php 
                                $parent_link= (($parent['mm_connected_as_external'] == '1' ) || ($parent['mm_item_connected_slug'] != '') ? true : false );
                                if($parent_link || isset($parent['child']) && count($parent['child']) > 0)
                                { 
                                    $page_url   = site_url($parent['mm_item_connected_slug']);
                                    $attributes = ((($parent['mm_new_window'])) ? 'target="_blank"':'');
                                    $page_url   = (($parent['mm_connected_as_external'] == '1' ) ? $parent['mm_external_url']: $page_url);
                                    $page_url   =  $page_url;
                                    $active_li  = (($this->uri->segment(2) == $parent['pageid']) && ($this->uri->segment(0) == 'page') ? 'class="active menu-scroll-left"':'');
                            ?>
                        <li class="menu-visible-xs">
                        
                            <a <?php echo $active_li ?> href="<?php echo $page_url ?>" <?php echo $attributes ?> <?php echo $area_selected ?> class="dropdown-toggle" <?php if(!$parent_link): ?> data-toggle="dropdown" href="javascript:void(0)"<?php endif; ?> aria-expanded="true">
                                <?php echo $parent['mm_name'];?>
                                <?php if(isset($parent['child']) && count($parent['child']) > 0):?>
                                    <span class="menu-down">
                                        <svg version="1.1" x="0px" y="0px" width="12px" height="10px" viewBox="0 0 21 17" enable-background="new 0 0 21 17" xml:space="preserve">
                                        <g>
                                        <path fill="#333333" d="M16.2,4.2L10.5,10L4.8,4.2L3,6l7.5,7.5L18,6L16.2,4.2z"></path>
                                        <path fill="none" d="M-4.5-6.5h30v30h-30V-6.5z"></path>
                                        </g>
                                        </svg>
                                    </span>
                                <?php endif;?>
                            </a>
                            
                            <?php if(isset($parent['child']) && count($parent['child']) > 0):?>
                                <ul class="dropdown-menu" style="border: 0;">
                                    <?php foreach($parent['child'] as $child): ?>
                                        <?php
                                            $page_url   = site_url($child['mm_item_connected_slug']);
                                            $showLinkC  = (($child['mm_connected_as_external'] == '1' ) ? $child['mm_external_url']: $child['mm_item_connected_slug']);
                                            $attributes = ((($child['mm_new_window'])) ? 'target="_blank"':'');
                                            $page_url   = (($child['mm_connected_as_external'] == '1' ) ? $child['mm_external_url']: $page_url);
                                            $page_url   =  $page_url;
                                            $active_li  = (($this->uri->segment(2) == $child['pageid']) && ($this->uri->segment(0) == 'page') ? 'class="active menu-scroll-left"':'');
                                        ?>
                                        <?php if(!empty($showLinkC)):?>
                                            <li>
                                                <a <?php echo $active_li ?> href="<?php echo $page_url ?>" <?php echo $attributes ?> <?php echo $area_selected ?> ><?php echo $child['mm_name'];?></a>
                                            </li>
                                        <?php endif;?>
                                    <?php endforeach; ?>
                                </ul>
                        <?php endif;?>

                        </li>
                        <?php $area_selected = ''; ?>
                    <?php } endforeach; } ?>

                        <!--mobile page menu ends -->

                            <?php /* if(isset($header_pages['parents']) && !empty($header_pages['parents'])): ?>
                                <?php foreach($header_pages as $header_page): ?>
                                    <?php 
                                        $page_url   = site_url($header_page['mm_item_connected_slug']);
                                        $attributes = ((!empty($header_page['mm_new_window'])) ? 'target="_blank"':'');
                                        $page_url   = (!empty($header_page['mm_connected_as_external']) && $header_page['mm_connected_as_external'] == '1') ? $header_page['mm_external_url']:$page_url;
                                    ?>
                                    <li class="menu-visible-xs">
                                        <a href="<?php echo $page_url; ?>" <?php echo $attributes;?>><?php echo $header_page['mm_name'];?></a>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; */?>


                            <?php if (isset($session['id'])): ?>
                            <!-- <li class="menu-visible-xs">
                                <a target="_blank" href="https://SGlearningapphelpdesk.freshdesk.com/support/tickets/new">
                                    <span>Support</span>
                                </a>
                            </li>
                            <li class="menu-visible-xs">
                                <a target="_blank" href="https://SGlearningapphelpdesk.freshdesk.com/support/home">
                                    <span>FAQ</span>
                                </a>
                            </li> -->
                            <?php if($support_chat_enabled): ?>
                                <li class="menu-visible-xs">
                                    <a href="javascript:void(0)" onclick="toggleFreshChatWidget()">Support Chat</a>
                                </li>
                            <?php endif; ?>
                            <li class="visible-xs">
                                <a href="<?php echo site_url() . 'logout' ?>">
                                    <img class="logout-icon" src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/images/logout.png" alt="img"><?php echo lang('logout'); ?>
                                </a>
                            </li>
                            <?php
                                $site_notification = array();
                                $unseen_msg_count  = isset($site_notification['unseen']) ? sizeof($site_notification['unseen']) : 0;
                            ?>
                            <li class="message-icon hidden-xs ">
                                <a onclick="msgRedirect()">
                                <img id="message_icon_image" style="border-radius: 0px;" src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/images/Notification_Icon_Inactive.png" width="22" <?php echo (($unseen_msg_count > 0) ? 'class="ml15"' : '') ?>><?php echo (($unseen_msg_count > 0) ? '<span class="badge-orange" id="site_message_count_wrapper"><span id="site_message_count">' . $unseen_msg_count . '</span></span>' : '') ?>
                                   <?php //if($unseen_msg_count > 0){  ?>
                                    <span class="badge-orange" id="message_count_wrapper" style="display: none">
                                        <span id="message_count" style="display: inline-block;min-width: 15px;text-align: center;"></span>
                                    </span>
                                   <?php //} ?>
                                </a>
                            </li>
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
                                    <li><a href="<?php echo site_url() . 'logout' ?>">
                                        <?php echo lang('logout'); ?></a></li>
                                </ul>
                            </li>
                        <?php else: ?>
                            <?php /*<li><a id='signin' href="<?php echo site_url('/login'); ?>"><?php echo lang('sign_in'); ?></a></li>
                            <li><a id='signup' href="<?php echo site_url('/register'); ?>"><?php echo lang('sign_up'); ?></a></li>*/?>
                            <!-- <li class="menu-visible-xs">
                                <a href="https://SGlearningapphelpdesk.freshdesk.com/support/home" target="_blank">FAQ</a>
                            </li> -->
                            <?php if($support_chat_enabled): ?>
                                <li class="menu-visible-xs">
                                    <a href="javascript:void(0)" onclick="toggleFreshChatWidget()">Support Chat</a>
                                </li>
                            <?php endif; ?>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>
        <?php endif; ?>

        <?php $explicit_force_class  = array('course'); ?>
        <?php $explicit_force_method = array('generate_test_view'); ?>
        <?php $explicit_nav_class    = array('teachers', 'report', 'homepage', 'material', 'course'); ?>
        <?php $explicit_nav_method   = array('assesment_report_item', 'challenge_zone_report_item', 'user_generated_test_report_item'); ?>
        <?php if ((!in_array($this->router->fetch_method(), $explicit_nav_method) && !in_array($this->router->fetch_class(), $explicit_nav_class)) || (in_array($this->router->fetch_class(), $explicit_force_class) && in_array($this->router->fetch_method(), $explicit_force_method))): ?>
    <?php if (isset($title) || !empty($category_pages)): ?>
                <!-- <section id="nav-group">
                    <div class="nav-group">
                        <div class="container">
                            <div class="container-reduce-width">
                                <h2 class="funda-head" id="category_heading"><?php echo isset($title) ? $title : ''; ?></h2>

                                <?php if (!empty($category_id)): ?>
                                    <?php if (!empty($category_pages)): ?>
                                        <nav class="navbar main-nav">

                                            <?php echo category_tree($category_pages); ?>

                                        </nav>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </section> -->
            <?php endif; ?>
        <?php endif; ?>

        <?php

        function get_time($datetime) {
            $now  = new DateTime;
            $ago  = new DateTime($datetime);
            $diff = $now->diff($ago);

            $diff->w = floor($diff->d / 7);
            $diff->d -= $diff->w * 7;

            if ($diff->y >= 1) {
                return 'On ' . date('d-M-Y', strtotime($datetime)) . ' at ' . date('h:i A', strtotime($datetime));
            } else {
                if ($diff->m >= 1) {
                    return 'On ' . date('d-M-Y', strtotime($datetime)) . ' at ' . date('h:i A', strtotime($datetime));
                } else {
                    if ($diff->d >= 1 || $diff->h >= 12) {
                        return 'On ' . date('d-M-Y', strtotime($datetime)) . ' at ' . date('h:i A', strtotime($datetime));
                    } else {
                        if ($diff->h >= 1) {
                            return 'Today at ' . date('h:i A', strtotime($datetime));
                        } else {
                            if ($diff->i > 1 && $diff->i < 30) {
                                return 'Few minutes ago...';
                            } else {
                                if ($diff->s < 60) {
                                    return 'Few seconds ago...';
                                } else {
                                    return 'Just now...';
                                }
                            }
                        }
                    }
                }
            }
        }
        ?>

<script>
const __siteUrl = "<?php echo site_url(); ?>";
var infoCookieName      = 'notifications';
var infoPopUpCookieName = 'notificationsPopUp';
var loggedUserid   = "<?php echo $this->auth->get_current_user_session('user')['id'] ?>";

function getCookie(cname) {
  var name = cname + "=";
  var decodedCookie = decodeURIComponent(document.cookie);
  var ca = decodedCookie.split(';');
  for(var i = 0; i <ca.length; i++) {
    var c = ca[i];
    while (c.charAt(0) == ' ') {
      c = c.substring(1);
    }
    if (c.indexOf(name) == 0) {
      return c.substring(name.length, c.length);
    }
  }
  return "";
}
// function getCookie(name) {
    
//         var dc      = document.cookie;
//         var prefix  = name + "=";
//         var begin   = dc.indexOf("; " + prefix);
//         if (begin == -1) {
//             begin   = dc.indexOf(prefix);
//             if (begin != 0) return null;
//         }
//         else
//         {
//             begin += 2;
//             var end = document.cookie.indexOf(";", begin);
//             if (end == -1) {
//             end = dc.length;
//             }
//         }
//         console.log(unescape(dc.substring(begin + prefix.length, end))); ;
//         return decodeURI(dc.substring(begin + prefix.length, end));
//     } 

if(loggedUserid != '') {
    infoCookieName          = loggedUserid+'notifications';
    infoPopUpCookieName     = loggedUserid+'notificationsPopUp';
}
$(document).ready(function(){
   
   
    $(".navbar-toggle").click(function(){
        $(".navbar-collapse").addClass("navslide");
        $(".nav-bg-overlay").css('opacity','1');
        $(".nav-bg-overlay").css('visibility','visible');
        $("body").css('overflow-y','hidden');
    });

    $(".nav-bg-overlay").click(function(){
        $(this).css('opacity','0');
        $(this).css('visibility','hidden');
        $("body").css('overflow-y','auto');
        $(".navbar-collapse").removeClass("navslide");
    });
    
    if( getCookie(infoPopUpCookieName) == '1' ) {
       
        $('#information-modal').modal('hide');
    } else {
        if( showHidInformationPopUp ) {
            setTimeout(function() {
                    $('#information-modal').modal('show');
                }, 3000);
        }
    }
console.log(( getCookie(infoCookieName) == '1' ));
    if( getCookie(infoCookieName) == '1' ) {
        
        $('.top-notification-slider').css("display", "none");
    } else {
    
        if( $( window ).width() > 768 ) {
            $('.top-notification-slider').css("display", "flex");
            var email_verify_check = $('#check_email_verification_bar').val();
            if(showHidInformationBar && (email_verify_check != '1')){$('#information_bar').show();}
        } 
    }
    
    
    // $('.top-notification-slider').css("display", "flex");
    // if(getCookie(infoCookieName) == '1'){  
    // }
    // //eraseCookie(infoCookieName);
});

function notificationRedirect(){

    window.location = __siteUrl+'dashboard/notify_notification';
}
<?php if($support_chat_enabled): ?>
function toggleFreshChatWidget() {
    window.fcWidget.open();window.fcWidget.show();
    $('.nav-bg-overlay').css('opacity','0');
    $('.nav-bg-overlay').css('visibility','hidden');
    $("body").css('overflow-y','auto');
    $(".navbar-collapse").removeClass("navslide");
}
<?php endif; ?>
// < ?php if(false): ?>
// // top notification slider
// var __slides                        = document.querySelectorAll('#top_notification_slider .slide');
// var __currentSlide                  = 0;
// var __slideInterval                 = setInterval(nextSlide,3000);
// __slides[__currentSlide].className  = 'slide showing';
// function nextSlide(){
//     __slides[__currentSlide].className = 'slide';
//     __currentSlide = (__currentSlide+1)%__slides.length;
//     __slides[__currentSlide].className = 'slide showing';
// }
// $('#top_notification_slider').on('mouseover',function(){
//    clearInterval(__slideInterval);
// });
// $('#top_notification_slider').on('mouseleave',function(){
//    __slideInterval = setInterval(nextSlide,3000);
// });

$(document).on('click','#information_bar .close',function() {
    $('.top-notification-slider').css("display", "none");
    setcookie(infoCookieName, '1', (60*24));
});

$(document).on('click','#information-modal .info-close,#information-modal .close',function() {
    //$('.top-notification-slider').css("display", "none");
    setcookie(infoPopUpCookieName, '1', (60*24));
});


// // top notification slider ends
// < ?php endif; ?>

</script>
 <?php if(!isset($_GET['privacy'])): ?>
<section class="">
    <?php include_once "page_menu.php"; ?>
</section>
<?php endif; ?>