var timeOut = '';
$(document).on('keyup', '#order_keyword', () =>  {
    clearTimeout(timeOut);
    timeOut = setTimeout(function () {
        __offset = 1;
        getOrders();
    }, 600);
});

var __orderObject = {};
$(document).ready(function () {
    var filter          = getQueryStringValue('filter');
    var keyword         = getQueryStringValue('keyword');
    var startdate       = getQueryStringValue('startdate');
    var enddate         = getQueryStringValue('enddate');
    var filter_type     = getQueryStringValue('type');

    if (filter != '') {
        __filter_dropdown = filter;
        $('#filter_dropdown_text').html($('#filer_dropdown_list_' + filter).text() + '<span class="caret"></span>');
    }

    if (keyword != '') {
        keyword = keyword.split('-').join(' ');
        $('#order_keyword').val(keyword);
    }

    if(startdate !=''){
        __report_date_start = startdate;
        $('#report_date_start').val(startdate);
    }

    if(enddate !=''){
        __report_date_end = enddate;
        $('#report_date_end').val(enddate);
    }

    if(filter_type !=''){
        __filter_type = filter_type;
        $('#filter_dropdown_types').html(lang(filter_type) + '<span class="caret"></span>');
    }
    
    var orders      = {};
    __orderObject   = $.parseJSON(__orders);
    orders.orders   = $.parseJSON(__orders);
    if (orders.orders.length > 0) {
        $('#order_row_wrapper').html(renderOrderHtml(JSON.stringify(orders)));
    } else {
        $('.order-count').html("No Orders");
        $('#order_row_wrapper').html(renderPopUpMessagePage('error', 'No Orders found.'));
        $('#popUpMessagePage .close').css('display', 'none');
    }
});

$(document).on('click', '#basic-addon2', () =>  {
    var order_keyword = $('#order_keyword').val().trim();
    if (order_keyword == '') {
        lauch_common_message('KEYWORD MISSING', 'Please enter a valid input to search.');
    } else {
        __offset = 1;
        getOrders();
    }
});

var __filter_dropdown = __controller == 'pending_order' ? 'pending':'completed';
var __gettingOrderInProgress = false;

function getOrders() {
    if (__gettingOrderInProgress == true) {
        return false;
    }
    __gettingOrderInProgress = true;
    var keyword = $('#order_keyword').val().trim();

    if (history.pushState) {
        var link = window.location.protocol + "//" + window.location.host + window.location.pathname;

        if (__filter_dropdown != '' || keyword != '') {
            link += '?';
        }
        if (__filter_dropdown != '') {
            link += '&filter=' + __filter_dropdown;
        }
        if (keyword != '') {
            var uSearch = keyword.split(' ').join('-');
            link += '&keyword=' + uSearch;
        }

        if (__report_date_start != '') {
            link += '&startdate=' + __report_date_start;
        }

        if (__report_date_end != '') {
            link += '&enddate=' + __report_date_end;
        }

        if (__filter_type != '') {
            link += '&type=' + __filter_type;
        }
//alert(link); //return; 
        __offset = (typeof __offset == 'undefined') ? 1 : __offset;
        link += '&offset=' + __offset;
        window.history.pushState({
            path: link
        }, '', link);
    }
    $.ajax({
        url: admin_url +__controller+'/orders_json',
        type: "POST",
        data: {
            "is_ajax": true,
            "filter": __filter_dropdown,
            "keyword": keyword,
            "startdate": __report_date_start,
            "enddate": __report_date_end,
            'limit': __limit,
            'type' : __filter_type,
            'offset': __offset
        },
        success: function (response) {
            $('.order-checkbox-parent').prop('checked', false);
            var data = $.parseJSON(response);
            var remainingOrder = 0;
            //console.log(data);
            renderPagination(__offset, data.total_orders);
            if (data.orders.length > 0) {
                __offset++;
                if (__offset == 2) {
                    __totalOrders = data.total_orders;
                    __shownOrders = data.orders.length;
                    remainingOrder = (data.total_orders - data.orders.length);
                    var totalOrdersHtml = data.total_orders + ' ' + ((data.total_orders == 1) ? "Order" : "Orders"); 
                    $('#order_row_wrapper').html(renderOrderHtml(response));
                    $('.order-count').html(totalOrdersHtml);
                    
                } else {
                    __totalOrders = data.total_orders;
                    __shownOrders = ((__offset - 2) * data.limit) + data.orders.length;
                    remainingOrder = (data.total_orders - (((__offset - 2) * data.limit) + data.orders.length));
                    var totalOrdersHtml = data.total_orders + ' Orders';
                    $('#order_row_wrapper').html(renderOrderHtml(response));
                    $('.order-count').html(totalOrdersHtml);
                    
                }
            } else {
                $('#order_row_wrapper').html(renderPopUpMessagePage('error', 'No Orders found.'));
                $('.order-count').html("No Orders");
                
                $('#popUpMessagePage .close').css('display', 'none');
            }
            remainingOrder = (remainingOrder > 0) ? '(' + remainingOrder + ')' : '';
            __gettingOrderInProgress = false;
        }
    });
}

function renderOrderHtml(response) {
    var data = $.parseJSON(response);
    var orderHtml = '';
        
    if (data.orders.length > 0){
        
        for (var i = 0; i < data.orders.length; i++) {
            orderHtml += '<div class="rTableRow user-listing-row" id="order_row_' + data.orders[i].id + '">';
            orderHtml += renderOrderRow(data.orders[i]);
            orderHtml += '</div>';
        }
    }
    return orderHtml;
}

function renderOrderRow(data) {
    
    var orderHtml = '';
    var time = new Date(data.ph_payment_date);
    let formatted_date = time.getFullYear()+'/'+time.toLocaleDateString('en-US', {month: '2-digit', day: '2-digit'});
    
    console.log(data);
    if(!data.ph_user_details || data.ph_user_details == null || typeof data.ph_user_details == 'undefined' || data.ph_user_details ==''){
        var payee_phone = '';
    }else{
        var payee_phone = JSON.parse(data.ph_user_details).phone;
    }
        
    if (data) {

        // orderHtml += '<div class="rTableRow">'; 
        orderHtml += '  <div class="rTableCell rowdivision"><div class="ellipsis-style" title="">'+data.ph_order_id+'</div></div>';
        orderHtml += '  <div class="rTableCell rowdivision"><div class="ellipsis-style">'+data.us_name+'</div></div>';
        orderHtml += '  <div class="rTableCell rowdivision"><div class="ellipsis-style user-type">'+payee_phone+'</div></div>';
        orderHtml += '  <div class="rTableCell rowdivision"><div class="ellipsis-style user-type">'+data.ph_item_name+'</div></div>';
        //orderHtml += '  <div class="rTableCell rowdivision"><div class="ellipsis-style user-type">₹ '+data.ph_item_amount_received+'</div></div>';
        orderHtml += '  <div class="rTableCell rowdivision"><div class="ellipsis-style user-type" style=" min-width: 150px;">'+formatted_date+' '+time.toLocaleString('en-US', { hour: 'numeric', minute: 'numeric', hour12: true })+'</div></div>';
        //orderHtml += '  <div class="rTableCell rowdivision"><div class="ellipsis-style user-type"><label class="pull-right label label-info" style="margin: 1px 2px 0px 0px;"><a class="" style="color: #fff;" href="javascript:void(0)" onclick="orderDetails('+data.id+')">Order details </a></label></div></div>';

        //consider the record is deleted and set the value if record deleted
        var label_class = 'spn-delete';
        var action_class = 'label-danger';
        var item_deleted = 'item-deleted';
        var action = lang('deleted');
        //case if record is not deleted
        if (data.ph_status == 1) {
            action_class = 'label-success';
            label_class = 'spn-active';
            action = lang('complete');
        } else {
            action_class = 'label-warning';
            label_class = 'spn-inactive';
            action = lang('incomplete');
        }

        orderHtml += '    <div class="rTableCell"><div class="ellipsis-style user-type">';
        orderHtml += '            <div class="pull-right label ' + action_class + '" id="action_class_' + data.id + '" style="margin: 1px 0px 0px 0px;">';
        orderHtml += action;
        orderHtml += '            </div>';
        orderHtml += '            </div>';
        orderHtml += '    </div>';
        /*orderHtml += '    <div class="td-dropdown rTableCell" style="padding-bottom: 3px !important;width:40px;">';

        
        orderHtml += '        <div class="btn-group lecture-control">';
        orderHtml += '            <span class="dropdown-tigger" data-toggle="dropdown">';
        orderHtml += '                <span class="label-text">';
        orderHtml += '                  <i class="icon icon-down-arrow"></i>';
        orderHtml += '                </span>';
        orderHtml += '                <span class="tilder"></span>';
        orderHtml += '            </span>';
        orderHtml += '            <ul class="dropdown-menu pull-right" role="menu" id="order_action_' + data.id + '">';
        orderHtml += '                <li>';
        orderHtml += '                    <a href="'+adminUrl+'orders/order_info/'+data.id+'" >' + lang('order_details') + '</a>';
        orderHtml += '                    <a target="_blank" href="'+adminUrl+'orders/pdf/'+data.id+'" >' + lang('order_invoice') + '</a>';
        orderHtml += '                </li>';

        orderHtml += '           </ul>';
        orderHtml += '        </div>';
        orderHtml += '    </div>';*/
        // orderHtml += '</div>';
    }
    return orderHtml;
}

function filter_order_by(filter) {
    if (filter == 'all') {
        $('#order_keyword').val('');
    }
    __filter_dropdown = filter;
    $('#filter_dropdown_text').html($('#filer_dropdown_list_' + filter).text() + '<span class="caret"></span>');
    __offset = 1;
    getOrders();
    $("#selected_order_count").html('');
}

function filter_types(type) {
    __filter_type = type;
    $('#filter_dropdown_types').html($('#filer_dropdown_listtype_' + type).text() + '<span class="caret"></span>');
    __offset = 1;
    getOrders();
}

$(document).on('click', '#searchclear', () =>  {
    __user_selected = new Array();
    $("#user_bulk").css('display', 'none');
    __offset = 1;
    getOrders();
});

function getQueryStringValue(key) {
    return decodeURIComponent(window.location.search.replace(new RegExp("^(?:.*[&\\?]" + encodeURIComponent(key).replace(/[\.\+\*]/g, "\\$&") + "(?:\\=([^&]*))?)?.*$", "i"), "$1"));
}

function renderPagination(offset, totalUsers) {
    offset = Number(offset);
    totalUsers = Number(totalUsers);
    var totalPage = Math.ceil(totalUsers / __limit);
    if (offset <= totalPage && totalPage > 1) {
        var paginationHtml = '';
        paginationHtml += '<ul class="pagination pagination-wrapper"  style="left:65px;">';
        paginationHtml += generatePagination(offset, totalPage);
        paginationHtml += '</ul>';
        $('#pagination_wrapper').html(paginationHtml);
        scrollToTopOfPage();
    } else {
        $('#pagination_wrapper').html('');
    }
}
$(document).on('click', '.locate-page', function (){
    __offset = $(this).attr('data-page');
    getOrders();
});

function clearUserCache() {
    __user_selected = new Array();
    __course_selected = new Array();
    $("#selected_user_count").html('');
}

function refreshListing() {
    if ($('.pagination li.active a').hasClass('pagination-last-page') == true) {
        if ($('.user-listing-row').length == 0) {
            __offset = $('.pagination li.active a').attr('data-page');
            __offset = __offset - 1;
            if (__offset == 0) {
                __offset = 1;
            }
            getOrders();
        }
    } else {
        if ($('.user-listing-row').length == 0) {
            __offset = $('.pagination li.active a').attr('data-page');
            getOrders();
        }
    }
}

$(document).ready(function () {
    $('[data-toggle="tooltip"]').tooltip();
});

function refreshListingLaunch() {
    refreshListing();
    $('#common_message_advanced').hide();
}

function exportSalesReport(){
     var keyword         = $('#order_keyword').val();
     var param           = {
                            "filter":       __filter_dropdown,
                            "keyword":      keyword,
                            "startdate":    __report_date_start,
                            "enddate":      __report_date_end,
                            'type' :        __filter_type
                        };
     param               = JSON.stringify(param);
     var pathname        = '/admin/'+__controller+'/export_sales_report';
     var link            = window.location.protocol + "//" + window.location.host + pathname;
     window.location     = link + '/' + btoa(param);
}

function orderDetails(orderId){
    $('#ordermodelcontent').html('Please wait...!');
    $("#order-details-model").modal();
    $.get(admin_url+__controller+'/order_info/'+orderId, function(data){
        $('#ordermodelcontent').html(data);
    });
    
}