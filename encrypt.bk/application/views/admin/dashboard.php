<html>
<!-- head start-->
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php echo isset($title)?$title:config_item('site_name'); ?></title>
    <link rel="stylesheet" type="text/css" href="<?php echo assets_url() ?>css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="<?php echo assets_url() ?>css/custom.css">
    <?php  ?><link rel="icon" href="<?php echo base_url('favicon.png') ?>"> <?php  ?>
</head>
<!-- head end-->

<!-- body start-->
<body>
    <?php
        $admin = $this->auth->get_current_user_session('admin');
    ?>
    <!-- Top head start-->
        <?php include_once 'head.php'; ?>
    <!-- Top head end-->

    <!-- Side Menu start-->
        <?php include_once "sidebar.php"; ?>
    <!-- Side Menu end-->


    <!-- Manin Iner container start -->
    <div class='dashbrd-container pos-top50 main-content'>
        <?php 
        /*echo '<pre>'; 
        print_r($admin['role_id']);
        print_r($this->accesspermission->get_permission(array('role_id' => $admin['role_id'])));
        die;*/ 
        ?>
        <span class="dash-wecl-ttle"><?php echo lang('welcome_back') ?>, <span><?php echo $admin['us_name'] ?>!</span><br/><?php echo lang('greetings') ?></span>
        <ul class="dash-ico-items">
            <?php foreach($module_menu as $key => $menu_obj): ?>
            <?php if(isset($menu_obj['dashboard'])): ?>
                <li>
                    <a href="<?php echo $menu_obj['link']?>" class="<?php echo $menu_obj['dashboard']['icon']?>" >
                        <i></i><span><?php echo $menu_obj['label']?></span>
                    </a>
                </li>
            <?php endif; ?>
            <?php endforeach; ?>
        </ul>
        
    </div>
    <!-- Manin Iner container end -->
    <?php include_once "common_modals.php" ?>
</body>
<!-- body end-->
<!-- Jquery library -->
<script type="text/javascript" src="<?php echo assets_url() ?>js/jquery.min.js"></script>
<!-- bootstrap library -->
<script type="text/javascript" src="<?php echo assets_url() ?>js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/system.js"></script>
</html>

<!-- custom layput js handling tooltip and hide show switch -->
<script type="text/javascript" src="<?php echo assets_url() ?>js/layout.min.js"></script>

