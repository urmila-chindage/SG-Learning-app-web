<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><?php echo $title ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" href="<?php echo base_url('favicon.png'); ?>" type="image/x-icon"/>
        <link rel="stylesheet" href="<?php echo theme_url() ?>css/bootstrap.css" media="all" />		
        <link rel="stylesheet" href="<?php echo theme_url() ?>css/reset.css" media="all" />
                
        <link rel="stylesheet" href="<?php echo theme_url() ?>css/style.css" media="all" />
        <link rel="stylesheet" href="<?php echo theme_url() ?>css/custom.css" media="all" />
        <link rel="stylesheet" href="<?php echo theme_url() ?>css/fontello.css" media="all" />
        <link rel="stylesheet" href="<?php echo theme_url() ?>css/material.css" media="all" />
        <link rel="stylesheet" href="<?php echo assets_url() ?>css/redactor/css/redactor.css" />		
        <link rel="stylesheet" href="<?php echo assets_url() ?>css/redactor/css/alignment.css" />
        <link rel="stylesheet" href="<?php echo theme_url() ?>css/redactor-styles.css" media="all" />
        <link rel="icon"       href="<?php echo theme_url() ?>/images/favicon.ico?v=2.2">
            
        <script src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/js/jquery.min.js"></script>
        <script>
            var site_url    = '<?php echo site_url() ?>/';
            var theme_url   = '<?php echo theme_url() ?>';		
            var admin_url   = '<?php echo admin_url() ?>';
            var uploads_url = '<?php echo uploads_url() ?>';
            var __course_id = '<?php echo $id; ?>';
            
            var __course_preview_time = '<?php echo $user_preview_time; ?>';		
            var __course_slug = '<?php echo $course['cb_slug']; ?>';		
            var hid_val     =  '0';		
            var __check_user_rating = '<?php echo $check_user_rating ?>';		
            var __check_lecture_completed = '<?php echo $check_lecture_completed ?>';
            var __user_review = '<?php echo (isset($course_review) || $course_review != '')?$course_review:''; ?>';	
        </script>		
        <link href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/jquery.rateyo.min.css" rel="stylesheet">		
        <script type="text/javascript">		
            var ratting   = '<?php echo $course_rating; ?>';
        </script>
    </head>
    <body>
        <div class="fixedheader">
            <div class="ts-md-7 ts-sm-6 ts-xs-6">
                <a href="<?php echo site_url('course/dashboard/'.$id) ?>" class="orangeicon icon-left-open topbutton mob-hide"><span class="backto">Back to Course</span></a>
                <span class="separator"></span>
                <h4 class="testtitle"><?php echo $title ?></h4>

            </div>
            <div class="ts-md-5 ts-sm-6 ts-xs-6 sbt">

                <ul class="topmenu topmenu-move-left">
                    <?php /* ?><li class="mail"><a href="#"><span class="icon-mail"></span><span class="icon-angle-down"></span></a>
                        <ul class="dropdown_mail">
                            <li><a href="#">Submenu 1</a></li>
                            <li><a href="#">Submenu 2</a></li>
                        </ul>
                    </li>
                    <li class="bell"><a href="#"><span class="icon-bell"></span><span class="icon-angle-down"></span></a>
                        <ul class="dropdown_bell">
                            <li><a href="#">notification 1</a></li>
                            <li><a href="#">notification 2</a></li>
                        </ul>        
                    </li><?php */ ?>
                    <li class="avatar">
                        <?php         
                            $user       = $this->auth->get_current_user_session('user');
                            $user_image = (($user['us_image'] == 'default.jpg')?default_user_path():user_path()).$user['us_image'];
                        ?>
                        <a href="javascript:void(0)"><img  src="<?php echo $user_image ?>" width="22" height="22" class="rounded nptb" /><span class="icon-angle-down downarrow"></span></a>
                        <ul class="dropdown_avatar drop-list">
                            <?php /* ?><li><a href="#">settings</a></li><?php */?>
                            <li><a href="<?php echo site_url('logout') ?>">Logout</a></li>
                        </ul>          
                    </li>
                </ul>
                <a href="<?php echo site_url('dashboard') ?>" class="cr topbutton mob-hide"><span class="cr backto course-title-left">course library<span class="orangeicon icon-right-open"></span></span></a>
                <a href="#" id="popup-rating">		
                    <div class="ratting course-rating">
                        <div id="rateYo"></div>		
                        <span class="course-rating-count"><?php echo ($course_rating!='0')?$course_rating:'Rate it!'; ?></span>		
                    </div>		
                </a>


            </div>
        </div>