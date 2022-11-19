<!DOCTYPE html>
<html>
<!-- head start-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <title><?php echo lang('lecture') ?> <?php echo isset($title)?':: '.$title:config_item('site_name'); ?></title>
    <link rel="icon" href="<?php echo base_url('favicon.png') ?>">
    <!-- Customized bootstrap css library -->
    <link rel="stylesheet" type="text/css" href="<?php echo assets_url() ?>css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="<?php echo assets_url() ?>css/adminstyle.css">
    <link rel="stylesheet" type="text/css" href="<?php echo assets_url() ?>css/datepicker.min.css">
    <link rel="icon" href="<?php echo base_url('favicon.png') ?>">
    <!-- Jquery library -->
    <script type="text/javascript" src="<?php echo assets_url() ?>js/jquery.min.js"></script>
   <!-- <script type="text/javascript" src="<?php //echo assets_url() ?>js/parent-div-finder.js"></script> -->
     <script>
        var admin_url   = '<?php echo admin_url() ?>';
        var uploads_url = '<?php echo uploads_url() ?>';
        var __controller = '<?php echo $this->router->fetch_class() ?>';
    </script>
        <!-- <script type="text/javascript" src="<?php //echo assets_url() ?>js/language.js"></script> -->
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
</style>
</head>
<!-- head end-->
<!-- body start-->

<body>