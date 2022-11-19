<?php include_once 'header.php'; ?>
<style>
.slimScrollDiv{top:110px;height:calc(100% - 120px) !important;}
</style>
<section class="courses-tab base-cont-top">
    <ol class="nav nav-tabs offa-tab">
        <!-- active tab start -->
        <?php if($this->auth->get_current_user_session('admin') || $this->auth->get_current_user_session('finance_manager')): ?>
        <li>
            <a href="<?php echo admin_url(); ?>finance">Monthly Earnings</a>
            <span class="active-arrow" style="background: rgb(255, 255, 255);"></span>
        </li>
        <?php endif; ?>
        <!-- active tab end -->
        <li class="active">
            <a href="<?php echo admin_url(); ?><?php echo $this->router->fetch_class() ?>/monthly_teachers_shares">Teacher Percentage</a>
            <span class="active-arrow" style="background: rgb(255, 255, 255);"></span>
        </li>
    </ol>
</section>
<section class="content-wrap create-group-wrap settings-top">
    <!-- LEFT CONTENT --> <!-- STARTS -->
    <!-- ===========================  -->
    <div class="col-sm-12 pad0 settings-left-wrap">
        <div class="container-fluid nav-content width-100p nav-js-height">
            <div class="row">
                <div class="rTable content-nav-tbl" style="">
                    <div class="rTableRow">
                        <div class="rTableCell">
                            January 2015
                        </div>                    
                        <div class="rTableCell">
                            <div class="input-group">
                                <input class="form-control srch_txt" placeholder="Search Group" id="group_keyword" type="text">
                                <a class="input-group-addon" id="search_group">
                                    <i class="icon icon-search"> </i>
                                </a>
                            </div> 
                        </div>

                    </div>
                </div>

            </div>
        </div>


        <!-- Group content section  -->
        <!-- ====================== -->
        <div class="container-fluid nav-content sales-nav">

            <div class="row">
                <div class="rTable content-nav-tbl" style="">
                    <div class="rTableRow">
                        <div class="rTableCell dropdown sales-archive">
                            <a href="#" id="sales_month_label" class="dropdown-toggle sales-filter-date" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"> <?php echo date("F Y")  ?> <span class="caret"></span></a>
                            <ul class="dropdown-menu white sales-drop sales_month_list">
                                <?php $count = 0; ?>
                                <?php while($count < 6): ?>
                                    <li><a href="javascript:void(0)" data-period="<?php echo date("Y-m", strtotime ( '-'.$count.' month' , strtotime ( 'm' ) ))  ?>"><?php echo date("F Y", strtotime ( '-'.$count.' month' , strtotime ( 'm' ) ))  ?></a></li>
                                    <?php $count++; ?>
                                <?php endwhile; ?>
                            </ul>

                        </div>

                        <div class="rTableCell sales-search">
                            <div class="input-group" style="visibility:<?php echo (($this->auth->get_current_user_session('admin') || $this->auth->get_current_user_session('finance_manager'))?'visible':'hidden') ?>">
                                <input type="text" id="teacher_keyword" class="form-control srch_txt" placeholder="Search by Name">
                                <a class="input-group-addon">
                                    <i class="icon icon-search"> </i>
                                </a>
                            </div> 
                        </div>
                        <div class="rTableCell sales-export">
                            <a class="btn btn-green selected mt8" onclick="exportMonthlyShares()"><i class="icon icon-upload"></i> EXPORT <ripples></ripples></a>
                        </div>

                    </div>
                </div>

            </div>
        </div>

        <div class="col-sm-12 group-content course-cont-wrap group-top sale-top">
            <div class="table course-cont rTable" id="monthly_sales_wrapper" style="">
            </div>
            <div class="center-block text-center" id="load_more_btn">
                <a onclick="getPurchases()" style="visibility: hidden;" class="btn btn-green selected"> LOAD MORE <ripples></ripples><ripples></ripples></a>
            </div>
        </div>
        <!-- ====================== -->
        <!-- Group content section  -->
    </div>

    <div class="col-sm-6 pad0 right-content">
        <!-- Nav section inside this wrap  --> <!-- START -->
        <!-- =========================== -->
        <div class="container-fluid nav-content no-nav-style pos-abslt nav-js-height width-100p" id="preview_wrapper" style="display: block;">
            <div class="col-sm-12">
                <div class="rTable content-nav-tbl" style="">
                    <div class="rTableRow">
                        <div class="rTableCell txt-left">
                            <a href="javascript:void(0)" class="select-all-style"><label> <input class="user-checkbox-parent" type="checkbox">  Select All</label><span id="selected_users_count"></span></a>
                        </div>
                        <div class="rTableCell">

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Nav section inside this wrap  --> <!-- END -->
        <!-- =========================== -->

        <div class="container-fluid right-box">
            <div class="row">
                <div class="col-sm-12 course-cont-wrap"> 
                    <div class="table course-cont rTable" id="monthly_purchase_details_wrapper">
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!-- ==========================  -->
    <!--  LEFT CONTENT--> <!-- ENDS -->
</section>
<script type="text/javascript" src="<?php echo assets_url() ?>js/jquery.slimscroll.min.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/multi-select/jquery.tokenize.js"></script>
<script src="<?php echo assets_url() ?>js/redactor/js/redactor.js"></script>
<script src="<?php echo assets_url() ?>js/redactor/js/table.js"></script>
<script src="<?php echo assets_url() ?>js/redactor/js/source.js"></script>
<script src="<?php echo assets_url() ?>js/redactor/js/alignment.js"></script>
<script src="<?php echo assets_url() ?>js/jquery.slimscroll.min.js"></script>
<script>
    var __controller      = '<?php echo $this->router->fetch_class() ?>';
    var __activeMonth     = '<?php echo date("Y-m")  ?>';
    var __adminUrl        = '<?php echo admin_url(); ?>';
    var __offset          = 2;
    var __perPage         = '<?php echo $per_page ?>';
    var __start           = true;
    var __requestTimeOut  = null;
    var __purchasesObject = atob('<?php echo base64_encode(json_encode($monthly_teachers_shares)) ?>');    
    $(document).ready(function (e) {
        $(function () {
            $('.right-box').slimScroll({
                height: '100%',
                width: '100%',
                wheelStep: 3,
                distance: '10px'
            });
        });
        __purchasesObject      = $.parseJSON(__purchasesObject);
        $('#monthly_sales_wrapper').html(renderMonthlyPurchasesHtml(__purchasesObject));
    });
    
    $(document).on('click', '.sales_month_list li a', function(){
         __activeMonth = $(this).attr('data-period');
         $('#sales_month_label').text($(this).text());
        __offset = 1;
        __start  = true;
        $('#load_more_btn a').html('Load More').css('visibility', 'hidden');
        getPurchases();
    });
    $(document).on('keyup', '#teacher_keyword', function(){
        initGetPurchases();
    });
    
    function getPurchases()
    {
        if(__start == true)
        {
            $('#monthly_sales_wrapper').html('');
        }
        var keyword  = $('#teacher_keyword').val();
        $.ajax({
            url: __adminUrl+__controller+'/monthly_teachers_shares_json',
            type: "POST",
            data:{"is_ajax":true, "keyword":keyword, "offset":__offset, 'period':__activeMonth},
            success: function(response) {
                var data = $.parseJSON(response);
                if(data['error']==false)
                {
                    if(__start == true)
                    {
                        $('#monthly_sales_wrapper').html('');
                        $('#monthly_sales_wrapper').html(renderMonthlyPurchasesHtml(data['monthly_teachers_shares']));
                    }
                    else
                    {
                        $('#monthly_sales_wrapper').append(renderMonthlyPurchasesHtml(data['monthly_teachers_shares']));
                    }
                    __start = false;
                    __offset++;
                }
            }
        });
    }
    
    function renderMonthlyPurchasesHtml(purchases)
    {
        $('#load_more_btn a').html('Load More').css('visibility', 'hidden');
        $('#monthly_purchase_details_wrapper').html('');
        var purchasesHtml  = '';
        var initialPurchaseDetail = true;
        var activeTabClass = 'active-table';
        if(Object.keys(purchases).length > 0 )
        {
            $.each(purchases, function(key, purchase )
            {
                if(initialPurchaseDetail == true && __start == true)
                {
                    initialPurchaseDetail = false;
                    getPurchasesDetails(purchase['ps_teacher_id'], __activeMonth);
                }
                purchasesHtml += '<div onclick="getPurchasesDetails('+purchase['ps_teacher_id']+', \''+ __activeMonth+'\')" class="rTableRow" data-item-id="'+purchase['ps_teacher_id']+'" id="purchase_row_'+purchase['ps_teacher_id']+'">';    
                purchasesHtml += '    <div class="rTableCell">';
                purchasesHtml += '        <span class="icon-wrap-round green pull-left"><i class="icon icon-user"></i></span>';
                purchasesHtml += '        <span class="earning-course-content pull-left">';
                purchasesHtml += '            <a href="javascript:void(0)" class="normal-base-color grp-click-fn"><span class="earning-course-name">'+purchase['us_name']+'</span></a>        ';
                purchasesHtml += '        </span>';
                purchasesHtml += '        <span class="label-active group-total sales-total pull-left">'+purchase['total_sales']+' Sales</span> ';
                purchasesHtml += '        <span class="group-total sales-total pull-left">'+parseFloat(purchase['total_amount']).toFixed(2)+'  &#8377;</span>    ';
                purchasesHtml += '    </div>    ';
                purchasesHtml += '    <div class="rTableCell pos-rel active-user-custom '+activeTabClass+'">        ';
                purchasesHtml += '        <span class="active-arrow sales-arrow" style="background: rgb(255, 255, 255);"></span>    ';
                purchasesHtml += '    </div>';
                purchasesHtml += '</div>';
                activeTabClass = '';
            });
            if( Object.keys(purchases).length == __perPage)
            {
                $('#load_more_btn a').css('visibility', 'visible');                
            }
        }
        __start = false;
        return purchasesHtml;
    }
    function loadMorePurchases()
    {
        $('#load_more_btn a').html('Loading...');
        getPurchases();
    }
    function initGetPurchases()
    {
        __offset = 1;
        __start  = true;
        clearTimeout(__requestTimeOut);
        __requestTimeOut = setTimeout(function(){
            getPurchases();
        }, 600);
    }
    
    function getPurchasesDetails(teacher_id, period)
    {
        $('#monthly_purchase_details_wrapper').html('<div class="rTableRow "><h3>Loading.....</h3></div>');
        $('.active-user-custom').removeClass('active-table');
        $('#purchase_row_'+teacher_id+' .active-user-custom').addClass('active-table');
        $.ajax({
            url: __adminUrl+__controller+'/monthly_teachers_shares_details_json',
            type: "POST",
            data:{"is_ajax":true, "teacher_id":teacher_id, 'period':period},
            success: function(response) {
                var data = $.parseJSON(response);
                $('#monthly_purchase_details_wrapper').html(renderMonthlyPurchasesDetailsHtml(data['monthly_teachers_shares_details']));
            }
        });
    }
    function renderMonthlyPurchasesDetailsHtml(purchasesDetails)
    {
        var purchasesDetailsHtml  = '';
        if(Object.keys(purchasesDetails).length > 0 )
        {
            $.each(purchasesDetails, function(key, purchaseDetail )
            {
                purchasesDetailsHtml += '<div onclick="generateInvoice('+purchaseDetail['share_id']+')" class="rTableRow sales-invoice-modal">    ';
                purchasesDetailsHtml += '    <div class="rTableCell sales-sl">'+Number(key+1)+'</div>';
                purchasesDetailsHtml += '    <div class="rTableCell sales-date">'+purchaseDetail['ph_payment_date']+'</div> ';
                purchasesDetailsHtml += '    <div class="rTableCell sales-course"><i class="icon teacher-icon icon-graduation-cap"></i>'+purchaseDetail['cb_title']+'</div>';
                purchasesDetailsHtml += '    <div class="rTableCell sales-course-amt">'+parseFloat(purchaseDetail['teacher_share']).toFixed(2)+'  &#8377;</div>     ';
                purchasesDetailsHtml += '</div>';
            });
        }
        return purchasesDetailsHtml;
    }
    
    function exportMonthlyShares()
    {
        var keyword  = $('#teacher_keyword').val();
       location.href = __adminUrl+__controller+'/export_monthly_teacher_shares/'+btoa(__activeMonth+'#'+keyword);
    }
    
    function generateInvoice(shareId)
    {
        $('#monthly-invoice').modal();
        $('#payment_modal_header').html('INVOICE');
        $('#invoice_body').html('<h3>Loading...</h3>');
        $.ajax({
            url: __adminUrl+__controller+'/payment_share_details',
            type: "POST",
            data:{"is_ajax":true, "share_id":shareId},
            success: function(response) {
                var data    = $.parseJSON(response);
                var payment = data['share'];
                var invoiceHtml = '';
                    invoiceHtml += '<div class="row">';
                    invoiceHtml += '    <div class="col-sm-12">';
                    invoiceHtml += '        <div class="row pb10">';
                    invoiceHtml += '            <div class="col-sm-4">Invoice ID :</div>';
                    invoiceHtml += '            <div class="col-sm-8">'+payment['ph_order_id']+'</div>';
                    invoiceHtml += '        </div>';
                    invoiceHtml += '        <div class="row pb10">';
                    invoiceHtml += '            <div class="col-sm-4">Invoice Date :</div>';
                    invoiceHtml += '            <div class="col-sm-8">'+payment['ph_payment_date']+'</div>';
                    invoiceHtml += '        </div>';
                    invoiceHtml += '        <div class="row pb10">';
                    invoiceHtml += '            <div class="col-sm-4">Purchased By :</div>';
                    invoiceHtml += '            <div class="col-sm-8">'+payment['student_name']+'</div>';
                    invoiceHtml += '        </div>  ';                              
                    invoiceHtml += '        <div class="row pb10">';
                    invoiceHtml += '            <div class="col-sm-4">Course Name :</div>';
                    invoiceHtml += '            <div class="col-sm-8">'+payment['cb_title']+'</div>';
                    invoiceHtml += '        </div>  ';                              
                    invoiceHtml += '        <div class="row pb10">';
                    invoiceHtml += '            <div class="col-sm-4">Amount Paid:</div>';
                    invoiceHtml += '            <div class="col-sm-8">'+payment['payed_amount']+'</div>';
                    invoiceHtml += '        </div>  ';                              
                    invoiceHtml += '        <div class="row pb10">';
                    invoiceHtml += '            <div class="col-sm-4">Course Price:</div>';
                    invoiceHtml += '            <div class="col-sm-8">'+payment['ph_course_price']+'</div>';
                    invoiceHtml += '        </div>  ';                              
                    invoiceHtml += '        <div class="row pb10">';
                    invoiceHtml += '            <div class="col-sm-4">Discount Price:</div>';
                    invoiceHtml += '            <div class="col-sm-8">'+payment['ph_course_discount']+'</div>';
                    invoiceHtml += '        </div>  ';                              
                    invoiceHtml += '        <div class="row pb10">';
                    invoiceHtml += '            <div class="col-sm-4">Teacher Share:</div>';
                    invoiceHtml += '            <div class="col-sm-8">'+parseFloat(payment['teacher_share']).toFixed(2)+'</div>';
                    invoiceHtml += '        </div>  ';       
                    invoiceHtml += '    </div>';
                    invoiceHtml += '</div>';
                $('#payment_modal_header').html('INVOICE FOR PAYMENT ID '+payment['ph_order_id']);
                $('#invoice_body').html(invoiceHtml);
                $('#export_invoice').unbind();
                $('#export_invoice').click({"share_id": shareId}, exportInvoice);  
            }
        });
    }
    
    function exportInvoice(param)
    {
        var a = document.createElement('a');
        a.href=__adminUrl+__controller+'/pdf_share/'+param.data.share_id;
        a.target = '_blank';
        document.body.appendChild(a);
        a.click();
    }
</script>
<?php include_once 'footer.php'; ?>

<!-- Modal pop up contents :: Invite Users -->
<div class="modal fade in" data-backdrop="static" data-keyboard="false" id="monthly-invoice" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-small" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                <h4 class="modal-title" id="payment_modal_header"></h4>
            </div>
            <div class="modal-body" id="invoice_body">
            </div>
            <div class="modal-footer">
                <a href="javascript:void(0)" type="button" id="export_invoice" class="btn btn-green">EXPORT</a>
                <a type="button" class="btn btn-red" data-dismiss="modal">CLOSE</a>
            </div>
        </div>
    </div>
</div>
<!-- Modal pop up contents :: Invite Users --> 
