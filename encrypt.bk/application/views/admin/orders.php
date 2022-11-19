<?php include_once "header.php";?>
<link rel="stylesheet" type="text/css" href="<?php echo assets_url();?>css/datepicker.min.css">
<style type="text/css" media="screen">
 .rTableCell label.manage-stud-list{display: flex !important;} 
 .batch-carrot{top: 20px !important;}  
 .manage-stud-list .list-user-name{width:25%;}
 .list-institute-code, .list-register-number{width:25%;}
 .w20-align{width: 20%;text-align: center;}

    .rowdivision{
        width: 20%;
        text-align: left;
    }
    #order_row_wrapper{
        width: 100%;
        display: contents; 
    }
    .user-listing-row:after{
        content:'';
        background:#eee;
        position:absolute;
        left:10px;
        width: calc(100% - 30px);
        height: 1px;
    }
    .pb-20{padding-bottom:15px;}
    .course-container{overflow:hidden;}
    .overflow-scroll{
        max-height: calc(100vh - 80px);
        height: calc(100vh - 80px);
        overflow: auto;
    }
</style>
        <section class="content-wrap base-cont-top nopad">
            <div class="container-fluid nav-content nav-course-content">
                <div class="row">
                    <div class="rTable content-nav-tbl" style="">
                        <div class="rTableRow flex-space">
                        <?php /* ?><div class="rTableCell">
                                <a href="javascript:void(0)" class="select-all-style"><label> 
                                    <input class="user-checkbox-parent" type="checkbox"><?php echo lang('select_all') ?></label>
                                    <span id="selected_user_count"></span>
                                </a>
                            </div><?php */ ?>
                            <?php /*if($controller=='orders'): ?>
                                <div class="rTableCell dropdown">
                                    <a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" id="filter_dropdown_text"> <?php echo lang('completed_orders') ?> <span class="caret"></span></a>
                                        <ul class="dropdown-menu white">
                                            <li><a href="javascript:void(0)" id="filer_dropdown_list_all" onclick="filter_order_by('all')"><?php echo lang('all_orders') ?></a></li>
                                            <li><a href="javascript:void(0)" id="filer_dropdown_list_completed" onclick="filter_order_by('completed')"><?php echo lang('completed_orders') ?></a></li>
                                            <li><a href="javascript:void(0)" id="filer_dropdown_list_processing" onclick="filter_order_by('processing')"><?php echo lang('processing_orders') ?></a></li>
                                        </ul>
                                </div>
                            <?php endif;*/?>

                            <div class="rTableCell pos-relative">
                                    <!-- datetimepicker -->
                                <input id="report_date_start" class="report_date form-control" value="" type="text" autocomplete="off" name="" placeholder="Start date" readonly="readonly">
                                    <!-- datetime picker -->
                                <span id="date-clear-start" class="date-clear" style="">×</span>
                            </div>

                            <div class="rTableCell pos-relative">
                                    <!-- datetimepicker -->
                                <input id="report_date_end" class="report_date form-control" value="" type="text" autocomplete="off" name="" placeholder="End date" readonly="readonly">
                                    <!-- datetime picker -->
                                <span id="date-clear-end" class="date-clear" style="">×</span>
                            </div>

                            <div class="rTableCell dropdown" style="min-width: 260px;">
                                <a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" id="filter_dropdown_types"> <?php echo lang('all_types') ?> <span class="caret"></span></a>
                                    <ul class="dropdown-menu white">
                                        <li><a href="javascript:void(0)" id="filer_dropdown_listtype_all" onclick="filter_types('all')"><?php echo lang('all_types');?></a></li>
                                        <li><a href="javascript:void(0)" id="filer_dropdown_listtype_course" onclick="filter_types('course')"><?php echo lang('course');?></a></li>
                                        <li><a href="javascript:void(0)" id="filer_dropdown_listtype_bundle" onclick="filter_types('bundle')"><?php echo lang('bundle');?></a></li>
                                    </ul>
                            </div>

                            <div class="rTableCell">
                                <div class="input-group">
                                    <input type="text" class="form-control srch_txt" id="order_keyword" placeholder="Search" />
                                    <span id="searchclear">×</span>
                                    <a class="input-group-addon" id="basic-addon2">
                                        <i class="icon icon-search"> </i>
                                    </a>
                                </div>
                            </div>

                            <div class="rTableCell">
                                <div class="save-btn" style="margin-top: 3px;">
                                    <button class="pull-right btn btn-green" onclick="exportSalesReport();">EXPORT</button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="left-wrap overflow-scroll col-sm-12 pad0">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-12 course-cont-wrap" id="show_message_div" style="margin-bottom:100px">
                            <div>
                                <div class="pull-right">
                                    <h4 class="right-top-header order-count">
                                    <?php 
                                        $order_html  = '';
                                        if($total_orders < 1) {
                                            $order_html = 'No Orders';
                                        } else {
                                            $order_html .= ($total_orders>1)?$total_orders.' Orders':$total_orders.' Order';    
                                        }
                                        echo $order_html;
                                    ?>
                                    </h4>
                                </div>
                            </div>
                            
                            <div class="table rTable">
                                <div class="rTableRow">
                                    <div class="rTableCell pb-20">
                                        <span class="wrap-mail ellipsis-hidden">
                                            <div class="ellipsis-style fixed-col"><strong>Order ID</strong></div>
                                        </span>
                                    </div>
                                    <div class="rTableCell pb-20">
                                        <span class="wrap-mail ellipsis-hidden">
                                            <div class="ellipsis-style fixed-col">
                                                <span><strong>Payee name</strong></span>
                                            </div>
                                        </span>
                                    </div>
                                    <div class="rTableCell pb-20">
                                        <span class="wrap-mail ellipsis-hidden">
                                            <div class="ellipsis-style fixed-col">
                                                <span><strong>Payee phone</strong></span>
                                            </div>
                                        </span>
                                    </div>
                                    <div class="rTableCell pb-20">
                                        <span class="wrap-mail ellipsis-hidden">
                                            <div class="ellipsis-style fixed-col">
                                                <span><strong>Item</strong></span>
                                            </div>
                                        </span>
                                    </div>
                                    <div class="rTableCell pb-20">
                                        <span class="wrap-mail ellipsis-hidden">
                                            <div class="ellipsis-style fixed-col">
                                                <span><strong>Paid amount</strong></span>
                                            </div>
                                        </span>
                                    </div>
                                    <div class="rTableCell pb-20" style="padding-right: 20px;">
                                        <span class="wrap-mail ellipsis-hidden">
                                            <div class="ellipsis-style fixed-col">
                                                <span><strong>Payment Mode</strong></span>
                                            </div>
                                        </span>
                                    </div>
                                    <div class="rTableCell pb-20">
                                        <span class="wrap-mail ellipsis-hidden">
                                            <div class="ellipsis-style fixed-col">
                                                <span><strong>Date/Time</strong></span>
                                            </div>
                                        </span>
                                    </div>

                                    <div class="rTableCell pb-20" style="min-width: 100px;">
                                        <span class="wrap-mail ellipsis-hidden">
                                            <div class="ellipsis-style fixed-col">
                                                <span><strong></strong></span>
                                            </div>
                                        </span>
                                    </div>

                                    <div class="rTableCell pb-20 text-right">
                                        <span class="wrap-mail ellipsis-hidden">
                                            <div class="ellipsis-style fixed-col">
                                                <span><strong>Status</strong></span>
                                            </div>
                                        </span>
                                    </div>                          
                                </div>
                                <div id="order_row_wrapper"></div>
                                
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div id="pagination_wrapper"></div>
                    </div>
                </div>
            </div>
        </section>

        <?php include_once 'footer.php';?>
        <script src="<?php echo assets_url() ?>js/order.js"></script>
        <script type="text/javascript">
            const adminUrl            = '<?php echo admin_url() ?>';
            const __limit             = '<?php echo $limit; ?>';
            var __orders              = atob('<?php echo base64_encode(json_encode($orders)); ?>');
            var __offset              = Number('<?php echo isset($_GET['offset']) ? $_GET['offset'] : '1' ?>');
            var __totalOrders         = '<?php echo $total_orders; ?>';
            var __shownOrders         = '<?php echo sizeof($orders); ?>';
            var __report_date_start   = '';
            var __report_date_end     = '';
            var __filter_type         = '';
            const order_permissions   = '<?php echo json_encode($this->order_privilege); ?>';
            const __user_privilege    = new User(order_permissions);
            var __controller          = '<?php echo $controller;?>';

           $(function()
            {
                renderPagination(__offset, __totalOrders);
            });
        </script>
        
    <script>

        $(document).on('click', '#date-clear-start', function (){
            if($('#report_date_start').val()){
                $('#report_date_start').val('');
                __report_date_start = '';
                __offset = 1;
                getOrders();
            }
        });

        $(document).on('click', '#date-clear-end', function () {
            if($('#report_date_end').val()){
                $('#report_date_end').val('');
                __report_date_end   = '';
                __offset = 1;
                getOrders();
            }
        });

/** date picker starts, please dont remove the below code **/
$('#report_date_end').val('<?php echo $this->input->get('enddate') ?>');
$('#report_date_start').val('<?php echo $this->input->get('startdate') ?>');

$(document).on('click', '#report_date_start', function () {

    $("#report_date_start").datepicker({
                        language: 'en',
                        minDate: false,
                        maxDate: $('#report_date_end').val() ? new Date($('#report_date_end').val()) : false,
                        dateFormat: 'yyyy-mm-dd',
                        autoClose: true,
                        onSelect: function(dateText, inst) { 
                            __report_date_start = $('#report_date_start').val();
                            __report_date_end   = $('#report_date_end').val();
                            __offset = 1;
                            getOrders();
                        }
                    });
});

$(document).on('click', '#report_date_end', function () {
    
    $("#report_date_end").datepicker({
                        language: 'en',
                        minDate: $('#report_date_start').val() ? new Date($('#report_date_start').val()) : false,
                        maxDate: false,
                        dateFormat: 'yyyy-mm-dd',
                        autoClose: true,
                        onSelect: function(dateText, inst) { 
                            __report_date_start = $('#report_date_start').val();
                            __report_date_end   = $('#report_date_end').val();
                            __offset = 1;
                            getOrders();
                        }
                    });
});

$(function(){
    var today = new Date();
    $("#report_date_start").datepicker({
        language: 'en',
        minDate: false,
        maxDate: $('#report_date_end').val() ? new Date($('#report_date_end').val()) : false,
        dateFormat: 'yyyy-mm-dd',
        autoClose: true,
        onSelect: function(dateText, inst) { 
            __report_date_start = $('#report_date_start').val();
            __report_date_end   = $('#report_date_end').val();
            __offset = 1;
            getOrders();
        }
    });

    $("#report_date_end").datepicker({
        language: 'en',
        minDate: $('#report_date_start').val() ? new Date($('#report_date_start').val()) : false,
        maxDate: false,
        dateFormat: 'yyyy-mm-dd',
        autoClose: true,
        onSelect: function(dateText, inst) { 
            __report_date_start = $('#report_date_start').val();
            __report_date_end   = $('#report_date_end').val();
            __offset = 1;
            getOrders();
        }
    });
});

/** date picker ends, please dont remove the above code */
    </script>
<script src="<?php echo assets_url();?>js/datepicker.js"></script>
<script src="<?php echo assets_url();?>js/datepicker.en.js"></script>

<!-- Popup starts-->

<!-- Modal -->
<div id="order-details-model" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
        <div class="modal-header">
            <button id="closePopup" type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Order details</h4>
        </div>
        <div class="modal-body" id="ordermodelcontent"></div>
    </div>
  </div>
</div>

<script>

</script>
<!--Popup ends-->
