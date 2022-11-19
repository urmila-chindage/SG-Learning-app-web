<!DOCTYPE html>
<!-- head start-->
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <!-- <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no"> -->
    <title><?php echo isset($title)?$title:config_item('site_name'); ?></title>
    
    <?php $redactor_pages = array('user', 'course', 'faculties', 'institutes', 'groups', 'wishlist','environment', 'event'); ?>
    <?php if( in_array($this->router->fetch_class(), $redactor_pages)): ?>
    <link rel="stylesheet" type="text/css" href="<?php echo assets_url() ?>css/multi-select/jquery.tokenize.css">
    <!-- ADDING REDACTOR PLUGIN INSIDE -->
    <!-- ############################# --> <!-- START -->
        <link rel="stylesheet" href="<?php echo assets_url() ?>css/redactor/css/redactor.css" />
        <link rel="stylesheet" href="<?php echo assets_url() ?>css/redactor/css/alignment.css" />
    <!-- ############################# --> <!-- END -->
    <!-- ADDING REDACTOR PLUGIN INSIDE -->
    <?php endif;  ?>
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
    <link rel="icon" href="<?php echo $favicon; ?>"> <?php  ?>

    <!-- Customized bootstrap css library -->
    <link rel="stylesheet" type="text/css" href="<?php echo assets_url() ?>css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="<?php echo assets_url() ?>css/opensans.css">
    <link rel="stylesheet" type="text/css" href="<?php echo assets_url() ?>css/adminstyle.css">
    <link rel="stylesheet" href="<?php echo assets_url(); ?>themes/<?php echo $this->config->item('theme') ?>/tinymce/autocomplete.css" />
    <!-- Jquery library -->
    <link rel="stylesheet" type="text/css" href="<?php echo assets_url() ?>css/custom.css">
    <script>
        var admin_url   = '<?php echo admin_url() ?>';
        var uploads_url = '<?php echo uploads_url() ?>';
        var __controller = '<?php echo $this->router->fetch_class() ?>';
    </script>
    <script type="text/javascript" src="<?php echo assets_url() ?>js/language.js"></script>
    <script type="text/javascript" src="<?php echo assets_url() ?>js/jquery.min.js"></script>
    <script type="text/javascript" src="<?php echo assets_url() ?>js/system.js"></script>

    <style>
    .upload-file-name {
        background: rgb(255, 255, 255) none repeat scroll 0 0;
        border: 0 none;
        cursor: pointer;
        height: 100%;
        left: 0;
        margin: 0;
        padding: 8px 12px;
        position: absolute;
        width: calc(100% - 124px);
        z-index: 0;
    }
    .logo img{ width: auto ;}
    #popUpMessagePage{ text-align:center;margin-top:10px;}
    
</style>
</head>
<!-- head end-->

<!-- body start-->
<body>
    <!-- Top head start-->
    <?php 
    $admin  = $this->auth->get_current_user_session('admin');
    if($this->router->fetch_method() != 'import_questions' && $this->router->fetch_method() != 'enroll_users' && $this->router->fetch_method() != 'enroll_groups')
    {
        include_once 'head.php'; 
    }
    ?>
    <!-- Top head end-->

    <!-- Side Menu start-->
    <?php

    if($this->router->fetch_method() != 'import_questions' && $this->router->fetch_method() != 'enroll_users' && $this->router->fetch_method() != 'enroll_groups')
    {
        include_once "sidebar.php";
    }
    ?>
    <!-- Side Menu end-->

    <?php 
    $currency_live = '';
    $currency   = $this->settings->setting('currency');
    if(($currency['as_superadmin_value'] && $currency['as_siteadmin_value']) == 1){
        foreach( $currency['as_setting_value']['setting_value'] as $key=>$value )
        {
            $$key = $value;
            if($value == 1){
                $currency_live = $key;
            }
        }
    }
    ?>
    
    <!-- Manin Iner container start -->
        
    <div class="main-content <?php echo ((($this->router->fetch_class() == 'course' && $this->router->fetch_method() == 'discussion')||($this->router->fetch_class() == 'report' && $this->router->fetch_method() == 'index') || ($this->router->fetch_class() == 'coursebuilder' && $this->router->fetch_method() == 'report'))?'full-width':'')?> <?php echo (($this->router->fetch_class() == 'user' && $this->router->fetch_method() == 'profile')?'dashbrd-container profile-wrap':'course-container clearfix ') ?> " <?php if($this->router->fetch_method() == 'import_questions' || $this->router->fetch_method() == 'enroll_users' || $this->router->fetch_method() == 'enroll_groups'){ echo 'style="padding: 0px;"'; } ?>>
    
        <?php if(isset($breadcrumb) && !empty($breadcrumb)): ?>
        <!-- Bread crumb added inside this section -->
        <!-- Breadcrumb START-->
        <ol class="breadcrumb">
            <?php //echo "<pre>";print_r($breadcrumb); die;?>
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
        </ol>
        <!-- Breadcrumb END-->
        <?php endif; ?>

            <script>		
	        var __currency_symbol   = '<?php echo $currency_live ?>';		
	        if(__currency_symbol == 'INR'){		
	            var __currency = 'Rs';		
	        }		
	    </script>