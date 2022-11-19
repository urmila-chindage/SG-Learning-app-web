
<!DOCTYPE html>
<html>
    <!-- head start-->
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
        <title><?php echo isset($title) ? $title : config_item('site_name'); ?></title>
        <link rel="icon" href="<?php echo base_url('favicon.png') ?>">
        <link rel="stylesheet" type="text/css" href="<?php echo assets_url() ?>css/bootstrap.css">
        <style type="text/css" media="screen">
            .promocode-list{
                border-bottom: 1px solid #ccc;
                height: 40px;
                padding: 10px 0px;
            }
            .promocode-list-title th{
                font-size: 14px;
                font-weight: 600;
                padding: 20px 25px 5px 25px;
                border-bottom: 1px solid #ccc;
            }
            .promocode-list td{
                font-size: 14px;
                padding: 0 25px;
            }
            .course-performance-wrapper{
                top: 10px;
                position: relative;
                padding:0 30px;
            }
        </style>
    </head>
    <body >
        
        <!-- Manin Iner container start -->
        <div class='bulder-content-inner add-question-block'>
            <div class="col-sm-12 bottom-line question-head promocode-userreport-header">
                <h3 class="question-title">Discount Coupon Usage Report - <?php echo $promocode_name; ?></h3>
                <div class="save-btn"><button class="pull-right btn btn-green selected" onclick="exportPromocodeUserReport(<?php echo $promocode_id ?>);">EXPORT<ripples></ripples></button></div>
                <?php $promocode_url = admin_url('promo_code'); ?>
                <span class="cb-close-qstn"><i class="icon icon-cancel-1" onclick="location.href='<?php echo $promocode_url ?>'"></i></span>
            </div>
            <div class="col-sm-12 question-block">
                <div class="rTableCell">
                    <div class="container-fluid course-performance-wrapper">
                    <?php if(!empty($users)){ ?>
                        <table style="width: 100%;">
                            <thead class="promocode-list-title">
                                <tr>
                                    <th>Sl.no</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Mobile</th>
                                    <th>Item Type</th>
                                    <th>Item Name</th>
                                    <th>Applied On</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            $i=1;
                            foreach($users as $user){
                            ?>
                                <tr class="promocode-list">
                                    <td><?php echo $i++;?></td>
                                    <td><?php echo isset($user['name'])?$user['name']:''; ?></td>
                                    <td><?php echo isset($user['email'])?$user['email']:''; ?></td> 
                                    <td><?php echo isset($user['phone'])?$user['phone']:''; ?></td>
                                    <td><?php echo isset($user['itemType'])?$user['itemType']:''; ?></td>
                                    <td><?php echo isset($user['itemName'])?$user['itemName']:''; ?></td>
                                    <td><?php echo isset($user['applied_on'])?$user['applied_on']:''; ?></td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    <?php } else { ?>
                    <p>No Users used this promocode</p>
                    <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </body>
    <!-- body end-->
    <script type="text/javascript" src="<?php echo assets_url() ?>js/jquery.min.js"></script>
    <script type="text/javascript" src="<?php echo assets_url() ?>js/bootstrap.min.js"></script>
    <script type="text/javascript" src="<?php echo assets_url() ?>js/layout.min.js"></script>
    <script type="text/javascript" src="<?php echo assets_url() ?>js/bootstrap-tagsinput.js"></script>
    <script type="text/javascript" src="<?php echo assets_url() ?>js/system.js"></script>
    <script type="text/javascript" src="<?php echo assets_url() ?>js/app.js"></script>
    <script type="text/javascript" src="<?php echo base_url() ?>/assets/js/ckeditor/ckeditor.js"></script>
    <script type="text/javascript" src="<?php echo base_url() ?>/assets/js/ckeditor/config.js"></script>
    <script type="text/javascript">
    function exportPromocodeUserReport( promocodeId ) {
        var param           = {
                                "promocodeId"   : promocodeId
                                };
        param               = JSON.stringify(param);
        var pathname        = '/admin/promo_code/export_promocode_user_report';
        var link            = window.location.protocol + "//" + window.location.host + pathname;
        window.location     = link + '/' + btoa(param);
    }
    </script>
</html>
